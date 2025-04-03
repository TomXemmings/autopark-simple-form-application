<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Document;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Support\Facades\Date;

class AdminController extends Controller
{
    /**
     * Admin login page
     *
     * @return Factory|View|Application|object
     */
    public function showLogin()
    {
        return view('admin.login');
    }

    /**
     * Login admin by password
     *
     * @param  Request          $request
     * @return RedirectResponse
     */
    public function login(Request $request)
    {
        $request->validate(['password' => 'required']);

        if ($request->password === config('app.admin.password')) {
            session(['admin_logged_in' => true]);
            return redirect()->route('admin.users');
        }

        return back()->withErrors(['password' => 'Неверный пароль']);
    }

    /**
     * Logout admin
     *
     * @return RedirectResponse
     */
    public function logout()
    {
        session()->forget('admin_logged_in');
        return redirect()->route('admin.login');
    }

    /**
     *  Load drivers page
     *
     * @param  Request                         $request
     * @return Factory|View|Application|object
     */
    public function index(Request $request)
    {
        $users = User::orderBy('created_at', 'desc')->paginate(10);

        return view('admin.users.index', compact('users'));
    }

    /**
     * For ajax
     *
     * @param  Request                 $request
     * @return RedirectResponse|string
     */
    public function table(Request $request)
    {
        $query = User::query();

        if ($request->filled('phone')) {
            $query->where('phone', 'like', '%' . $request->phone . '%');
        }

        if ($request->filled('fio')) {
            $query->where(function ($q) use ($request) {
                $q->where('last_name', 'like', '%' . $request->fio . '%')
                    ->orWhere('first_name', 'like', '%' . $request->fio . '%')
                    ->orWhere('middle_name', 'like', '%' . $request->fio . '%');
            });
        }

        if ($request->filled('inn')) {
            $query->where('inn', 'like', '%' . $request->inn . '%');
        }

        $users = $query->orderByDesc('created_at')->paginate(10);

        if ($request->ajax()) {
            return view('admin.users._table', compact('users'))->render();
        }

        return redirect()->route('admin.users');
    }


    /**
     * Show driver`s detail
     *
     * @param  User                            $user
     * @return Factory|View|Application|object
     */
    public function show(User $user)
    {
        $user->load(['addressInfo', 'insuranceInfo', 'documents']);

        return view('admin.users.show', compact('user'));
    }

    /**
     * Update driver`s info
     *
     * @param  Request          $request
     * @param  User             $user
     * @return RedirectResponse
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'last_name'   => 'required|string|max:255',
            'first_name'  => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'inn'         => 'required|digits:10',

            'city'    => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'email'   => 'required|email|max:255',

            'policy_number' => 'required|string|max:255',
            'start_date'    => 'required|date',
            'end_date'      => 'required|date|after_or_equal:start_date',
            'company_name'  => 'required|string|max:255',
            'fgis_number'   => 'required|string|max:255',
            'fgis_date'     => 'required|date',

            'service_agreement_number'      => 'nullable|string|max:255',
            'service_agreement_start_date'  => 'nullable|date',
        ]);

        $user->update([
            'last_name'                    => $validated['last_name'],
            'first_name'                   => $validated['first_name'],
            'middle_name'                  => $validated['middle_name'] ?? null,
            'inn'                          => $validated['inn'],
            'service_agreement_number'     => $request->service_agreement_number,
            'service_agreement_start_date' => $request->service_agreement_start_date,
        ]);


        $user->addressInfo()->updateOrCreate([], [
            'city'    => $request->city,
            'address' => $request->address,
            'email'   => $request->email,
        ]);

        $user->insuranceInfo()->updateOrCreate([], [
            'policy_number' => $request->policy_number,
            'start_date'    => $request->start_date,
            'end_date'      => $request->end_date,
            'company_name'  => $request->company_name,
            'fgis_number'   => $request->fgis_number,
            'fgis_date'     => $request->fgis_date,
        ]);

        return redirect()->route('admin.users.show', $user)->with('success', 'Данные обновлены');
    }

    /**
     * Delete driver`s document
     *
     * @param  User             $user
     * @param  Document         $document
     * @return RedirectResponse
     */
    public function deleteDocument(User $user, Document $document)
    {
        if ($document->user_id !== $user->id) {
            abort(403, 'Документ не принадлежит пользователю');
        }

        if (Storage::exists($document->file_path)) {
            Storage::delete($document->file_path);
        }

        $document->delete();

        return redirect()->back()->with('success', 'Документ удалён');
    }

    /**
     * Upload driver`s document
     *
     * @param  Request          $request
     * @param  User             $user
     * @return RedirectResponse
     */
    public function uploadDocument(Request $request, User $user)
    {
        $request->validate([
            'type' => 'required|string',
            'file' => 'required|file|mimes:jpg,jpeg,png,pdf',
        ]);

        $path = $request->file('file')->store('documents', 'public');

        $user->documents()->create([
            'type'      => $request->type,
            'file_path' => Storage::url($path),
        ]);

        return redirect()->back()->with('success', 'Документ загружен');
    }

    /**
     *  Export one driver`s to csv
     *
     * @param  User             $user
     * @return StreamedResponse
     */
    public function exportSingleCsv(User $user): StreamedResponse
    {
        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="user_' . $user->id . '_export.csv"',
        ];

        $fgisDate       = optional($user->insuranceInfo->fgis_date) ? Carbon::parse($user->insuranceInfo->fgis_date) : null;
        $insuranceStart = optional($user->insuranceInfo->start_date) ? Carbon::parse($user->insuranceInfo->start_date) : null;
        $insuranceEnd   = optional($user->insuranceInfo->end_date) ? Carbon::parse($user->insuranceInfo->end_date) : null;
        $serviceDate    = optional($user->service_agreement_start_date) ? Carbon::parse($user->service_agreement_start_date) : null;

        $columns = [
            'LicenseNumber'                => $user->user_code ?? '',
            'LicenseStartDate'             => optional($user->created_at)?->format('d.m.Y') ?? '',
            'OrgForm'                      => '' ?? '',
            'FullName'                     => '' ?? '',
            'ShortName'                    => '' ?? '',
            'SecondName'                   => $user->first_name ?? '',
            'FirstName'                    => $user->last_name ?? '',
            'MiddleName'                   => $user->middle_name ?? '',
            'Ogrn'                         => '' ?? '',
            'INN'                          => $user->inn ?? '',
            'LegalAddress'                 => $user->addressInfo->city ?? '',
            'Address'                      => $user->addressInfo->address ?? '',
            'TelephoneNumber'              => $user->phone ?? '',
            'Email'                        => $user->addressInfo->email ?? '',
            'AdditionalRequirement'        => 'Дополнительное требование' ?? '',
            'ServiceMark'                  => 'НЕТ' ?? '',
            'CommercialDesignation'        => 'НЕТ' ?? '',
            'WithoutInvolvement'           => '' ?? '',
            'MedicalExaminationAddress'    => '' ?? '',
            'LicenseStatus'                => 1 ?? '',
            'LicenseDecision'              => '+' ?? '',
            'LicenseDecisionDate'          => optional($user->created_at)?->format('d.m.Y') ?? '',
            'LicenseEndDate'               => optional($user->created_at)?->addYears(5)->format('d.m.Y') ?? '',
            'InsuranceContractNumber'      => $user->insuranceInfo->policy_number ?? '',
            'InsuranceContractStartDate'   => $insuranceStart?->format('d.m.Y') ?? '',
            'InsuranceContractEndDate'     => $insuranceEnd?->format('d.m.Y') ?? '',
            'InsuranceCompanyName'         => $user->insuranceInfo->company_name ?? '',
            'ContractOfCarriageNumber'     => '' ?? '',
            'ContractOfCarriageStartDate'  => '' ?? '',
            'ContractOfCarriageEndDate'    => '' ?? '',
            'OrderAgreementOgrn'           => 5157746192731 ?? '',
            'DriverLicenseSeriesAndNumber' => $user->insuranceInfo->fgis_number ?? '',
            'DriverLicenseStartDate'       => $fgisDate?->format('d.m.Y') ?? '',
            'DriverLicenseEndDate'         => $fgisDate?->addYears(5)->format('d.m.Y') ?? '',
            'ServiceAgreementNumber'       => $user->service_agreement_number ?? '',
            'ServiceAgreementStartDate'    => $serviceDate?->format('d.m.Y') ?? '',
            'ServiceAgreementEndDate'      => $serviceDate?->addYears(5)->format('d.m.Y') ?? '',
        ];

        return response()->streamDownload(function () use ($columns) {
            $output = fopen('php://output', 'w');
            fputcsv($output, array_keys($columns), ';');
            fputcsv($output, array_values($columns), ';');
            fclose($output);
        }, "user_export_{$user->id}.csv", $headers);
    }

    /**
     * Export selected drivers to csv
     *
     * @param  Request            $request
     * @return BinaryFileResponse
     */
    public function exportSelectedCsv(Request $request)
    {
        $request->validate([
            'user_ids' => 'required|array',
        ]);

        $filename = 'selected_users.csv';
        $path = storage_path("app/{$filename}");
        $handle = fopen($path, 'w');

        $headerWritten = false;

        foreach ($request->user_ids as $id) {
            $user = User::with(['addressInfo', 'insuranceInfo'])->find($id);
            if (!$user) continue;

            $fgisDate       = optional($user->insuranceInfo->fgis_date) ? Carbon::parse($user->insuranceInfo->fgis_date) : null;
            $insuranceStart = optional($user->insuranceInfo->start_date) ? Carbon::parse($user->insuranceInfo->start_date) : null;
            $insuranceEnd   = optional($user->insuranceInfo->end_date) ? Carbon::parse($user->insuranceInfo->end_date) : null;
            $serviceDate    = optional($user->service_agreement_start_date) ? Carbon::parse($user->service_agreement_start_date) : null;

            $columns = [
                'LicenseNumber'                => $user->user_code ?? '',
                'LicenseStartDate'             => optional($user->created_at)?->format('d.m.Y') ?? '',
                'OrgForm'                      => '',
                'FullName'                     => '',
                'ShortName'                    => '',
                'SecondName'                   => $user->first_name ?? '',
                'FirstName'                    => $user->last_name ?? '',
                'MiddleName'                   => $user->middle_name ?? '',
                'Ogrn'                         => '',
                'INN'                          => $user->inn ?? '',
                'LegalAddress'                 => $user->addressInfo->city ?? '',
                'Address'                      => $user->addressInfo->address ?? '',
                'TelephoneNumber'              => $user->phone ?? '',
                'Email'                        => $user->addressInfo->email ?? '',
                'AdditionalRequirement'        => 'Дополнительное требование',
                'ServiceMark'                  => 'НЕТ',
                'CommercialDesignation'        => 'НЕТ',
                'WithoutInvolvement'           => '',
                'MedicalExaminationAddress'    => '',
                'LicenseStatus'                => 1,
                'LicenseDecision'              => '+',
                'LicenseDecisionDate'          => optional($user->created_at)?->format('d.m.Y') ?? '',
                'LicenseEndDate'               => optional($user->created_at)?->addYears(5)->format('d.m.Y') ?? '',
                'InsuranceContractNumber'      => $user->insuranceInfo->policy_number ?? '',
                'InsuranceContractStartDate'   => $insuranceStart?->format('d.m.Y') ?? '',
                'InsuranceContractEndDate'     => $insuranceEnd?->format('d.m.Y') ?? '',
                'InsuranceCompanyName'         => $user->insuranceInfo->company_name ?? '',
                'ContractOfCarriageNumber'     => '',
                'ContractOfCarriageStartDate'  => '',
                'ContractOfCarriageEndDate'    => '',
                'OrderAgreementOgrn'           => 5157746192731,
                'DriverLicenseSeriesAndNumber' => $user->insuranceInfo->fgis_number ?? '',
                'DriverLicenseStartDate'       => $fgisDate?->format('d.m.Y') ?? '',
                'DriverLicenseEndDate'         => $fgisDate?->copy()->addYears(5)->format('d.m.Y') ?? '',
                'ServiceAgreementNumber'       => $user->service_agreement_number ?? '',
                'ServiceAgreementStartDate'    => $serviceDate?->format('d.m.Y') ?? '',
                'ServiceAgreementEndDate'      => $serviceDate?->copy()->addYears(5)->format('d.m.Y') ?? '',
            ];

            if (!$headerWritten) {
                fputcsv($handle, array_keys($columns), ';');
                $headerWritten = true;
            }

            fputcsv($handle, array_values($columns), ';');
        }

        fclose($handle);

        return response()->download($path)->deleteFileAfterSend(true);
    }

    /**
     * Print documents page
     *
     * @param  User                            $user
     * @return Factory|View|Application|object
     */
    public function printDocuments(User $user)
    {
        $user->load('documents');
        return view('admin.users.print', compact('user'));
    }
}
