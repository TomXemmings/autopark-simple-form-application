@extends('layouts.app')

@section('title', 'Шаг 2 — Адрес')

@section('content')
    <div class="max-w-xl mx-auto bg-white p-8 rounded shadow">
        <h2 class="text-xl font-bold mb-6">Шаг 2: Адрес проживания</h2>

        <form id="step2-form" method="POST" action="{{ url('/step-2') }}">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-1 gap-4 mb-4">
{{--                <div>--}}
{{--                    <label for="city" class="block text-sm font-medium text-gray-700 mb-1">Город *</label>--}}
{{--                    <input type="text" name="city" id="city"--}}
{{--                           class="form-input border rounded p-2 w-full"--}}
{{--                           placeholder="Город" required>--}}
{{--                </div>--}}

                <div>
                    <label for="address" class="block text-sm font-medium text-gray-700 mb-1">Адрес по прописке (как в паспорте) *</label>
                    <input type="text" name="address" id="address"
                           class="form-input border rounded p-2 w-full"
                           placeholder="Адрес" required>
                </div>
            </div>

{{--            <div class="mb-4">--}}
{{--                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email *</label>--}}
{{--                <input type="email" name="email" id="email"--}}
{{--                       class="form-input border rounded p-2 w-full"--}}
{{--                       placeholder="Email" required>--}}
{{--            </div>--}}

            <p id="step2-error" class="text-red-600 text-sm hidden mb-2"></p>

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
            $('#step2-form').on('submit', function (e) {
                e.preventDefault();

                const address = $('#address').val().trim();

                if (!address) {
                    $('#step2-error').text('Заполните все поля и проверьте корректность Email.').removeClass('hidden');
                    return;
                }

                $('#step2-error').addClass('hidden');

                $.post("{{ url('/step-2') }}", {
                    _token: '{{ csrf_token() }}',
                    address: address,
                })
                    .done(function () {
                        window.location.href = '/step-3';
                    })
                    .fail(function (xhr) {
                        const err = xhr.responseJSON?.message || 'Ошибка. Попробуйте снова.';
                        $('#step2-error').text(err).removeClass('hidden');
                    });
            });
        });
    </script>
@endsection
