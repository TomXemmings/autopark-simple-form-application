@extends('layouts.admin-plain')

@section('title', 'Вход в админку')

@section('content')
    <h2 class="text-xl font-bold mb-6 text-center">Вход администратора</h2>

    @if ($errors->any())
        <div class="bg-red-100 text-red-700 p-3 rounded mb-4">
            {{ $errors->first('password') }}
        </div>
    @endif

    <form method="POST" action="{{ route('admin.login.submit') }}">
        @csrf
        <div class="mb-4">
            <input type="password" name="password" class="w-full p-3 border rounded focus:outline-none focus:ring" placeholder="Пароль">
        </div>
        <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700">Войти</button>
    </form>
@endsection
