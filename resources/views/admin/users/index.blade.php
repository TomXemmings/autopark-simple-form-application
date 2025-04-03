@extends('layouts.admin')

@section('title', 'Пользователи')

@section('content')
    <h1 class="text-2xl font-bold mb-6">Список водителей</h1>

    <form method="GET" class="mb-6" id="filter-form">
        <div class="grid grid-cols-3 gap-4 mb-2">
            <input type="text" name="phone" value="{{ request('phone') }}" class="form-input border rounded p-2" placeholder="Телефон">
            <input type="text" name="fio" value="{{ request('fio') }}" class="form-input border rounded p-2" placeholder="ФИО">
            <input type="text" name="inn" value="{{ request('inn') }}" class="form-input border rounded p-2" placeholder="ИНН">
        </div>

        <div class="flex justify-end gap-3">
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Поиск</button>
            <a href="{{ route('admin.users') }}" class="bg-gray-300 text-gray-800 px-4 py-2 rounded hover:bg-gray-400">Сбросить</a>
        </div>
    </form>

    <form method="POST" action="{{ route('admin.users.export.selected.csv') }}" id="export-form">
        @csrf

        <div id="user-table" class="overflow-visible">
            @include('admin.users._table', ['users' => $users])
        </div>

        <div class="flex justify-end mt-4">
            <button type="submit"
                    class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                Экспортировать выбранных в CSV
            </button>
        </div>
    </form>

    <script>
        const tableContainer = document.getElementById('user-table');
        const filterForm = document.getElementById('filter-form');

        function loadTable(params = '') {
            fetch(`{{ route('admin.users.table') }}?${params}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
                .then(response => response.text())
                .then(html => tableContainer.innerHTML = html);
        }

        filterForm.addEventListener('submit', function (e) {
            e.preventDefault();
            const params = new URLSearchParams(new FormData(this)).toString();
            loadTable(params);
        });

        tableContainer.addEventListener('click', function (e) {
            if (e.target.tagName === 'A' && e.target.closest('.pagination')) {
                e.preventDefault();
                const url = new URL(e.target.href);
                loadTable(url.searchParams.toString());
            }
        });

        tableContainer.addEventListener('change', function (e) {
            if (e.target.id === 'check-all') {
                document.querySelectorAll('input[name="user_ids[]"]').forEach(cb => cb.checked = e.target.checked);
            }
        });
    </script>
@endsection
