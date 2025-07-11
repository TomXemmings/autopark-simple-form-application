@extends('layouts.app')

@section('title', 'Шаг 3 — ОСГОП и ФГИС')

@section('content')
    <div class="max-w-2xl mx-auto bg-white p-8 rounded shadow">
        <h2 class="text-xl font-bold mb-6">Шаг 3: Страховка ОСГОП и лицензия ФГИС</h2>

        <form id="step3-form" method="POST" action="{{ url('/step-3') }}">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Номер ОСГОП *
                        <a href="https://trk.ppdu.ru/click/4BzCl8OO?erid=2SDnjdcvCRG"
                           target="_blank"
                           class="text-blue-600 text-xs ml-1 underline">
                            Нет ОСГОП?
                        </a>
                    </label>
                    <input type="text" name="policy_number" id="policy_number"
                           class="form-input border rounded p-2 w-full"
                           placeholder="Номер ОСГОП" required>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Страховая компания *
                    </label>
                    <input type="text" name="company_name" id="company_name"
                           class="form-input border rounded p-2 w-full"
                           placeholder="Компания" required>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Дата начала ОСГОП *</label>
                    <input type="date" name="start_date" id="start_date"
                           class="form-input border rounded p-2 w-full" required>
                </div>

                <div>
                    <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">Дата окончания ОСГОП *</label>
                    <input type="date" name="end_date" id="end_date"
                           class="form-input border rounded p-2 w-full" required>
                </div>
            </div>


            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-5 flex items-center">
                        Номер договора с яндекс *
                        <span onclick="toggleHint('yandex-contract-hint')"
                              class="ml-2 w-5 h-5 flex items-center justify-center bg-gray-300 text-white rounded-full text-xs font-bold cursor-pointer"
                              title="Показать подсказку">
                            ?
                        </span>
                    </label>
                    <input type="text" name="yandex_contract_number" id="yandex_contract_number"
                           class="form-input border rounded p-2 w-full"
                           placeholder="Номер договора с яндекс" required>
                    <div id="yandex-contract-hint" class="hidden mt-2">
                        Договор можно скачать в приложении яндекс про в разделе профиль - раздел документы
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-5 flex items-center">
                        Дата начала договора с Яндекс *
                    </label>
                    <input type="date" name="yandex_contract_start_date" id="yandex_contract_start_date"
                           class="form-input border rounded p-2 w-full" required>
                </div>
            </div>

            <p id="step3-error" class="text-red-600 text-sm hidden mb-2"></p>

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
            $('#step3-form').on('submit', function (e) {
                e.preventDefault();

                const policy = $('#policy_number').val().trim();
                const company = $('#company_name').val().trim();
                const start = $('#start_date').val();
                const end = $('#end_date').val();
                const yandex_contract_start_date = $('#yandex_contract_start_date').val();
                const yandex_contract_number = $('#yandex_contract_number').val();

                if (!policy || !company || !start || !end) {
                    $('#step3-error').text('Пожалуйста, заполните все поля.').removeClass('hidden');
                    return;
                }

                $('#step3-error').addClass('hidden');

                $.post("{{ url('/step-3') }}", {
                    _token: '{{ csrf_token() }}',
                    policy_number: policy,
                    start_date: start,
                    end_date: end,
                    company_name: company,
                    yandex_contract_start_date: yandex_contract_start_date,
                    yandex_contract_number: yandex_contract_number
                })
                    .done(() => window.location.href = '/step-4')
                    .fail(xhr => {
                        const err = xhr.responseJSON?.message || 'Ошибка при сохранении данных.';
                        $('#step3-error').text(err).removeClass('hidden');
                    });
            });
        });

        function toggleHint(id) {
            const el = document.getElementById(id);
            el.classList.toggle('hidden');
        }
    </script>
@endsection
