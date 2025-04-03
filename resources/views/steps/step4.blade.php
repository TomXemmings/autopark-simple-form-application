@extends('layouts.app')

@section('title', 'Шаг 4 — Загрузка документов')

@section('content')
    <div class="max-w-2xl mx-auto bg-white p-8 rounded shadow">
        <h2 class="text-xl font-bold mb-6">Шаг 4: Загрузите документы</h2>

        <form method="POST" action="{{ url('/step-4') }}" enctype="multipart/form-data">
            @csrf

            <div class="grid grid-cols-1 gap-4 mb-4">

                <div>
                    <label for="passport_main" class="block text-sm font-medium text-gray-700 mb-1">Паспорт (основная страница) *</label>
                    <input type="file" name="passport_main" accept=".jpg,.jpeg,.png"
                           class="form-input border rounded p-2 w-full" required>
                </div>

                <div>
                    <label for="passport_registration" class="block text-sm font-medium text-gray-700 mb-1">Паспорт (прописка) *</label>
                    <input type="file" name="passport_registration" accept=".jpg,.jpeg,.png"
                           class="form-input border rounded p-2 w-full" required>
                </div>

                <div>
                    <label for="license_front" class="block text-sm font-medium text-gray-700 mb-1">Вод. удостоверение (лицевая) *</label>
                    <input type="file" name="license_front" accept=".jpg,.jpeg,.png"
                           class="form-input border rounded p-2 w-full" required>
                </div>

                <div>
                    <label for="license_back" class="block text-sm font-medium text-gray-700 mb-1">Вод. удостоверение (обратная) *</label>
                    <input type="file" name="license_back" accept=".jpg,.jpeg,.png"
                           class="form-input border rounded p-2 w-full" required>
                </div>

                <div>
                    <label for="insurance_photo" class="block text-sm font-medium text-gray-700 mb-1">Фото страхового полиса *</label>
                    <input type="file" name="insurance_photo" accept=".jpg,.jpeg,.png"
                           class="form-input border rounded p-2 w-full" required>
                </div>

                <div>
                    <label for="court_certificate" class="block text-sm font-medium text-gray-700 mb-1">Справка из суда *</label>
                    <input type="file" name="court_certificate" accept=".jpg,.jpeg,.png,.pdf"
                           class="form-input border rounded p-2 w-full" required>
                </div>

                <button type="submit"
                        class="w-full bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                    Загрузить все документы
                </button>
            </div>
        </form>

        <hr class="my-6">

        <h3 class="text-lg font-semibold mb-2">Загруженные документы:</h3>
        <div class="space-y-2">
            @foreach ($user->documents as $doc)
                <div class="flex items-center justify-between border p-2 rounded bg-gray-50">
                    <div>
                        <strong>{{ $doc->type }}</strong> —
                        <a href="{{ asset($doc->file_path) }}" target="_blank" class="text-blue-600 underline">Просмотр</a>
                    </div>
                </div>
            @endforeach
        </div>

        @if ($user->documents->count() >= 6)
            <form method="POST" action="{{ route('user.complete') }}" class="mt-6">
                @csrf
                <button class="w-full bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                    Завершить регистрацию
                </button>
            </form>
        @endif
    </div>
@endsection


@section('scripts')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script>
        $('#upload-form').on('submit', function (e) {
            const fileInput = $('#file')[0];
            const type = $('#type').val();

            if (!type || !fileInput.files.length) {
                e.preventDefault();
                $('#upload-error').text('Заполните все поля.').removeClass('hidden');
            } else {
                $('#upload-error').addClass('hidden');
            }
        });
    </script>
@endsection
