<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\AddressInfo;
use App\Models\Document;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Check phone number and register if don`t exist
     *
     * @param  Request      $request
     * @return JsonResponse
     */
    public function registerPhone(Request $request)
    {
        $request->validate([
            'phone' => ['required', 'regex:/^\d{10}$/'],
            'password' => ['required', 'min:6'],
        ]);

        $phone = $request->input('phone');

        $user = User::where('phone', $phone)->first();

        if ($user) {
            return response()->json([
                'message' => 'Пользователь уже существует',
                'user'    => $user,
                'step'    => $user->current_step,
            ]);
        }

        $userCode = User::generateUserCode();

        $newUser = User::create([
            'phone'     => $phone,
            'user_code' => $userCode,
            'password'  => Hash::make($request->password),
            'current_step' => 1,
        ]);

        Auth::login($newUser);

        return response()->json([
            'message' => 'Пользователь создан',
            'user'    => $newUser,
            'step'    => 1,
        ]);
    }

    /**
     * Login by password
     *
     * @param  Request      $request
     * @return JsonResponse
     */
    public function login(Request $request)
    {
        $request->validate([
            'phone'    => ['required', 'regex:/^\d{10}$/'],
            'password' => ['required'],
        ]);

        $user = User::where('phone', $request->phone)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Неверный телефон или пароль'], 401);
        }

        auth()->login($user);

        return response()->json([
            'message' => 'Успешный вход',
            'step'    => $user->current_step,
            'user'    => $user,
        ]);
    }


    /**
     * Check step by phone number
     *
     * @param  Request      $request
     * @return JsonResponse
     */
    public function checkPhone(Request $request)
    {
        $request->validate([
            'phone' => ['required', 'regex:/^\d{10}$/'],
        ]);

        $user = User::where('phone', $request->phone)->first();

        if ($user) {
            return response()->json([
                'exists'    => true,
                'step'      => $user->current_step,
                'user_code' => $user->user_code,
            ]);
        }

        return response()->json([
            'exists' => false,
            'step'   => 1,
        ]);
    }

    /**
     * First step - driver`s info
     *
     * @param  Request      $request
     * @return JsonResponse
     */
    public function stepOne(Request $request)
    {
        $request->validate([
            'last_name'   => 'required|string|max:255',
            'first_name'  => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'inn'         => ['required', 'digits:10'],
        ]);

        $user = auth()->user();

        if (!$user) {
            return response()->json(['message' => 'Пользователь не найден'], 404);
        }

        $user->update([
            'last_name'    => $request->last_name,
            'first_name'   => $request->first_name,
            'middle_name'  => $request->middle_name,
            'inn'          => $request->inn,
            'current_step' => 2,
        ]);

        return response()->json([
            'message' => 'Данные сохранены',
            'user'    => $user,
            'step'    => 2,
        ]);
    }

    /**
     * Second step - detail driver info
     *
     * @param  Request      $request
     * @return JsonResponse
     */
    public function stepTwo(Request $request)
    {
        $request->validate([
            'city'    => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'email'   => 'required|email|max:255',
        ]);

        $user = auth()->user();

        if (!$user) {
            return response()->json(['message' => 'Пользователь не найден'], 404);
        }

        $user->addressInfo()->updateOrCreate([], [
            'city'    => $request->city,
            'address' => $request->address,
            'email'   => $request->email,
        ]);

        $user->update(['current_step' => 3]);

        return response()->json([
            'message' => 'Адресная информация сохранена',
            'step'    => 3,
        ]);
    }

    /**
     * Third step - info about driver license
     *
     * @param  Request      $request
     * @return JsonResponse
     */
    public function stepThree(Request $request)
    {
        $request->validate([
            'policy_number' => 'required|string|max:255',
            'start_date'    => 'required|date',
            'end_date'      => 'required|date|after_or_equal:start_date',
            'company_name'  => 'required|string|max:255',
            'fgis_number'   => 'required|string|max:255',
            'fgis_date'     => 'required|date',
        ]);

        $user = auth()->user();

        if (!$user) {
            return response()->json(['message' => 'Пользователь не найден'], 404);
        }

        $user->insuranceInfo()->updateOrCreate([], [
            'policy_number' => $request->policy_number,
            'start_date'    => $request->start_date,
            'end_date'      => $request->end_date,
            'company_name'  => $request->company_name,
            'fgis_number'   => $request->fgis_number,
            'fgis_date'     => $request->fgis_date,
        ]);

        $user->update(['current_step' => 4]);

        return response()->json([
            'message' => 'Данные страховки сохранены',
            'step'    => 4,
        ]);
    }

    /**
     * Fourth step - download documents
     *
     * @param  Request      $request
     * @return JsonResponse
     */
    public function stepFour(Request $request)
    {
        $request->validate([
            'license_front'         => 'required|file|mimes:jpg,jpeg,png',
            'license_back'          => 'required|file|mimes:jpg,jpeg,png',
            'insurance_photo'       => 'required|file|mimes:jpg,jpeg,png',
            'court_certificate'     => 'required|file|mimes:jpg,jpeg,png,pdf',
            'passport_main'         => 'required|file|mimes:jpg,jpeg,png',
            'passport_registration' => 'required|file|mimes:jpg,jpeg,png',
        ]);

        $user = auth()->user();

        if (!$user) {
            return response()->json(['message' => 'Пользователь не найден'], 404);
        }

        $documents = [
            'license_front',
            'license_back',
            'insurance_photo',
            'court_certificate',
            'passport_main',
            'passport_registration'
        ];

        foreach ($documents as $docType) {
            $file = $request->file($docType);
            $path = $request->file($docType)->store('documents', 'public');

            $user->documents()->create([
                'type'      => $docType,
                'file_path' => Storage::url($path),
            ]);
        }

        $user->update(['current_step' => 5]);

        return redirect()->route('user.complete.success');
    }

    /**
     * Complete registration
     *
     * @param  Request          $request
     * @return RedirectResponse
     */
    public function completeRegistration(Request $request)
    {
        $user = auth()->user();

        $user->update(['current_step' => 5]);

        return redirect()->route('user.complete.success');
    }

}
