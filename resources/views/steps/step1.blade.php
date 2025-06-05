@extends('layouts.app')

@section('title', 'Шаг 1 — ФИО и ИНН')

@section('content')
    <div class="max-w-xl mx-auto bg-white p-8 rounded shadow">
        <h2 class="text-xl font-bold mb-6">Шаг 1: Введите ваши данные</h2>

        <form id="step1-form" method="POST" action="{{ url('/step-1') }}">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                <div>
                    <label for="last_name" class="block text-sm font-medium text-gray-700 mb-1">Фамилия *</label>
                    <input type="text" name="last_name" id="last_name"
                           class="form-input border rounded p-2 w-full"
                           placeholder="Фамилия" required>
                </div>

                <div>
                    <label for="first_name" class="block text-sm font-medium text-gray-700 mb-1">Имя *</label>
                    <input type="text" name="first_name" id="first_name"
                           class="form-input border rounded p-2 w-full"
                           placeholder="Имя" required>
                </div>

                <div>
                    <label for="middle_name" class="block text-sm font-medium text-gray-700 mb-1">Отчество</label>
                    <input type="text" name="middle_name" id="middle_name"
                           class="form-input border rounded p-2 w-full"
                           placeholder="Отчество (необязательно)">
                </div>
            </div>

            <div class="mb-4">
                <label for="inn" class="block text-sm font-medium text-gray-700 mb-1">ИНН (12 цифр) *</label>
                <input type="text" name="inn" id="inn"
                       class="form-input border rounded p-2 w-full"
                       placeholder="ИНН (12 цифр)" required maxlength="12">
            </div>

            <p id="step1-error" class="text-red-600 text-sm hidden mb-2"></p>

            <button type="submit"
                    class="w-full bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                Продолжить
            </button>
        </form>
    </div>
@endsection

@section('scripts')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script>
        $(document).ready(function () {
            $('#inn').on('input', function () {
                this.value = this.value.replace(/\D/g, '').slice(0, 12);
            });

            $('#step1-form').on('submit', function (e) {
                e.preventDefault();

                const last = $('#last_name').val().trim();
                const first = $('#first_name').val().trim();
                const inn = $('#inn').val().trim();

                if (!last || !first || inn.length !== 12) {
                    $('#step1-error').text('Проверьте, что все обязательные поля заполнены и ИНН состоит из 12 цифр.')
                        .removeClass('hidden');
                    return;
                }

                $('#step1-error').addClass('hidden');

                $.post("{{ url('/step-1') }}", {
                    _token: '{{ csrf_token() }}',
                    last_name: last,
                    first_name: first,
                    middle_name: $('#middle_name').val(),
                    inn: inn
                })
                    .done(function () {
                        window.location.href = '/step-2';
                    })
                    .fail(function (xhr) {
                        const err = xhr.responseJSON?.message || 'Ошибка. Попробуйте снова.';
                        $('#step1-error').text(err).removeClass('hidden');
                    });
            });
        });
    </script>
@endsection
