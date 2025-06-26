@extends('layouts.app')

@section('title', 'Ввод номера телефона')

@section('content')
    <h2 class="text-2xl font-bold text-center mt-12 mb-4">Оформление разрешения на перевозки</h2>

    <div class="max-w-md mx-auto mt-20 bg-white p-8 rounded shadow">
        <h1 class="text-xl font-semibold mb-6 text-center">Вход / Регистрация</h1>
        <p class="text-gray-600 text-sm mb-6 text-center">
            Введите свой номер телефона.<br>
            Если вы <strong>уже зарегистрированы</strong> — вы будете авторизованы.<br>
            Если вы <strong>впервые</strong> — аккаунт будет создан автоматически.
        </p>

        <form id="phone-form" method="POST" action="{{ url('/register-phone') }}">
            @csrf

            <label class="block mb-2 text-sm font-medium text-gray-700">Телефон</label>
            <div class="flex items-center border rounded p-2 mb-4">
                <span class="text-gray-700 mr-2">+7</span>
                <input type="text" name="phone" id="phone"
                       class="flex-1 focus:outline-none"
                       maxlength="10"
                       placeholder="___ ___ __ __"
                       required>
            </div>

            <p id="phone-error" class="text-red-600 text-sm mt-2 hidden"></p>

            <button type="submit"
                    class="w-full mt-6 bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                Продолжить
            </button>
        </form>

    </div>

    <p class="text-gray-600 text-sm mb-6 text-center mt-4">
        Печкин ЯндексПро<br>
        Гужевая 11, тел. <a href="tel:89200395155">8(920)039-51-55</a>
    </p>
@endsection

@section('scripts')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script>
        $(document).ready(function () {
            $('#phone').on('input', function () {
                this.value = this.value.replace(/\D/g, '').slice(0, 10);
            });

            $('#phone-form').on('submit', function (e) {
                e.preventDefault();

                const phone = $('#phone').val();
                const fullPhone = '7' + phone;

                if (phone.length !== 10) {
                    $('#phone-error').text('Введите 10 цифр номера телефона.').removeClass('hidden');
                    return;
                }

                $('#phone-error').addClass('hidden');

                $.post("{{ url('/register-phone') }}", {
                    _token: '{{ csrf_token() }}',
                    phone: fullPhone
                })
                    .done(function (data) {
                        if (data.step) {
                            window.location.href = '{{ url('step') }}-' + data.step;
                        }
                    })
                    .fail(function (xhr) {
                        const err = xhr.responseJSON?.message || 'Ошибка. Попробуйте снова.';
                        $('#phone-error').text(err).removeClass('hidden');
                    });
            });
        });
    </script>
@endsection
