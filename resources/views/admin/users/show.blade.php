@extends('layouts.admin')

@section('title', 'Карточка пользователя')

@section('content')
    <h1 class="text-2xl font-bold mb-6">Карточка пользователя #{{ $user->user_code }}</h1>

    @if(session('success'))
        <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">
            {{ session('success') }}
        </div>
    @endif

    <form method="POST" action="{{ route('admin.users.update', $user) }}" class="space-y-6">
        @csrf

        <div>
            <h2 class="text-lg font-semibold mb-2">ФИО и ИНН</h2>
            <div class="grid grid-cols-4 gap-4">
                <input type="text" name="last_name" value="{{ $user->last_name }}" class="form-input border rounded p-2" placeholder="Фамилия">
                <input type="text" name="first_name" value="{{ $user->first_name }}" class="form-input border rounded p-2" placeholder="Имя">
                <input type="text" name="middle_name" value="{{ $user->middle_name }}" class="form-input border rounded p-2" placeholder="Отчество">
                <input type="text" name="inn" value="{{ $user->inn }}" class="form-input border rounded p-2" placeholder="ИНН">
            </div>
        </div>

        <div>
            <h2 class="text-lg font-semibold mb-2">Серия, дата выдачи и окончания водительских прав</h2>
            <div class="grid grid-cols-4 gap-4">
                <input type="text" name="driver_license_number" value="{{ $user->driver_license_number ?? '' }}" class="form-input border rounded p-2" placeholder="Серия прав">
                <input type="date" name="driver_license_start_date" value="{{ $user->driver_license_start_date ?? '' }}" class="form-input border rounded p-2" placeholder="Дата выдачи">
                <input type="date" name="driver_license_end_date" value="{{ $user->driver_license_end_date ?? '' }}" class="form-input border rounded p-2" placeholder="Дата окончания">
            </div>
        </div>

        <div>
            <h2 class="text-lg font-semibold mb-2">Адрес</h2>
            <div class="grid grid-cols-3 gap-4">
                <input type="text" name="address" value="{{ $user->addressInfo->address ?? '' }}" class="form-input border rounded p-2" placeholder="Адрес">
            </div>
        </div>

        <div>
            <h2 class="text-lg font-semibold mb-2">ОСГОП</h2>
            <div class="grid grid-cols-6 gap-4">
                <input type="text" name="policy_number" value="{{ $user->insuranceInfo->policy_number ?? '' }}" class="form-input border rounded p-2" placeholder="Номер ОСГОП">
                <input type="date" name="start_date" value="{{ $user->insuranceInfo->start_date ?? '' }}" class="form-input border rounded p-2">
                <input type="date" name="end_date" value="{{ $user->insuranceInfo->end_date ?? '' }}" class="form-input border rounded p-2">
                <input type="text" name="company_name" value="{{ $user->insuranceInfo->company_name ?? '' }}" class="form-input border rounded p-2" placeholder="Компания">
            </div>
        </div>

        <div>
            <h2 class="text-lg font-semibold mb-2">Договор с Яндексом</h2>
            <div class="grid grid-cols-3 gap-4">
                <input type="text" name="service_agreement_number"
                       value="{{ $user->service_agreement_number }}"
                       class="form-input border rounded p-2"
                       placeholder="Номер договора">

                <input type="date" name="service_agreement_start_date"
                       value="{{ $user->service_agreement_start_date }}"
                       class="form-input border rounded p-2">
            </div>
        </div>


        <button class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Сохранить</button>
    </form>

    <hr class="my-8">


    <h2 class="text-lg font-semibold mb-4">Подпись</h2>

    <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-6">
        <img src="{{ asset($user->signature) }}" class="rounded shadow mx-auto max-h-40 object-contain">
    </div>

    <hr class="my-8">

    <h2 class="text-lg font-semibold mb-4">Документы</h2>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-6">
        @foreach ($user->documents as $doc)
            <div class="text-center">
                <div class="text-sm font-medium mb-2">{{ $doc->type }}</div>

                @php
                    $ext = strtolower(pathinfo($doc->file_path, PATHINFO_EXTENSION));
                    $isImage = in_array($ext, ['jpg', 'jpeg', 'png', 'gif']);
                    $isPdf = $ext === 'pdf';
                @endphp

                @if ($isImage)
                    <img src="{{ asset($doc->file_path) }}" class="rounded shadow mx-auto max-h-40 object-contain">
                @elseif ($isPdf)
                    <div class="flex flex-col items-center">
                        <svg class="w-12 h-12 text-red-600 mb-2" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M6 2a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8l-6-6H6z"/>
                        </svg>
                        <a href="{{ asset($doc->file_path) }}" target="_blank" class="text-blue-600 underline">Открыть PDF</a>
                    </div>
                @else
                    <div class="flex flex-col items-center">
                        <svg class="w-12 h-12 text-gray-500 mb-2" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M6 2a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8l-6-6H6z"/>
                        </svg>
                        <a href="{{ asset($doc->file_path) }}" target="_blank" class="text-blue-600 underline">Скачать файл</a>
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.users.document.delete', [$user, $doc]) }}" class="mt-2">
                    @csrf
                    @method('DELETE')
                    <button class="text-sm bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600">Удалить</button>
                </form>
            </div>
        @endforeach
    </div>

    <hr class="my-8">

    <h2 class="text-lg font-semibold mb-2">Добавить новый документ</h2>
    <form method="POST" action="{{ route('admin.users.document.upload', $user) }}" enctype="multipart/form-data" class="grid grid-cols-3 gap-4 mb-6">
        @csrf
        <input type="text" name="type" class="form-input border rounded p-2" placeholder="Тип (например passport_main)">
        <input type="file" name="file" class="form-input border rounded p-2">
        <button class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700">Загрузить</button>
    </form>

    <div class="flex gap-4">
        <a href="{{ route('admin.users.export.csv', $user) }}" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">Экспорт CSV</a>
        <a href="{{ route('admin.users.print', $user) }}" target="_blank" class="bg-indigo-500 text-white px-4 py-2 rounded hover:bg-indigo-600">Печать документов</a>
    </div>
@endsection
