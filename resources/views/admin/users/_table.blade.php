<table class="w-full bg-white rounded shadow overflow-hidden overflow-visible">
    <thead class="bg-gray-100 text-left">
    <tr>
        <th class="p-3"><input type="checkbox" id="check-all"></th>
        <th class="p-3">Код</th>
        <th class="p-3">Телефон</th>
        <th class="p-3">ФИО</th>
        <th class="p-3">ИНН</th>
        <th class="p-3">Статус</th>
        <th class="p-3">Действия</th>
    </tr>
    </thead>
    <tbody class="overflow-visible">
    @forelse ($users as $user)
        <tr class="border-t hover:bg-gray-50">
            <td class="p-3"><input type="checkbox" name="user_ids[]" value="{{ $user->id }}"></td>
            <td class="p-3">{{ $user->user_code }}</td>
            <td class="p-3">{{ $user->phone }}</td>
            <td class="p-3">{{ $user->last_name }} {{ $user->first_name }} {{ $user->middle_name }}</td>
            <td class="p-3">{{ $user->inn }}</td>
            <td class="p-3">
                @switch($user->current_step)
                    @case(1)
                        <span class="text-gray-500">Не начал регистрацию</span>
                        @break

                    @case(2)
                        <span class="text-blue-600">Шаг 2 — анкета</span>
                        @break

                    @case(3)
                        <span class="text-indigo-600">Шаг 3 — проверка документов</span>
                        @break

                    @case(4)
                        <span class="text-green-600">Шаг 4 — загрузка документов</span>
                        @break

                    @case(5)
                        <span class="text-emerald-600 font-semibold">Регистрация завершена</span>
                        @break

                    @default
                        <span class="text-red-600">Неизвестный статус</span>
                @endswitch
            </td>
            <td class="p-3 relative text-left">
                <button onclick="toggleDropdown(this)" type="button"
                        class="bg-gray-200 text-gray-800 text-sm px-3 py-1 rounded hover:bg-gray-300">
                    Действия
                </button>

                <div class="dropdown absolute z-10 mt-2 w-36 bg-white border border-gray-200 rounded shadow-lg text-sm hidden">
                    <a href="{{ route('admin.users.show', $user) }}"
                       class="block px-4 py-2 hover:bg-gray-100 text-blue-600">Открыть</a>

                    <a href="{{ route('admin.users.print', $user) }}"
                       target="_blank"
                       class="block px-4 py-2 hover:bg-gray-100 text-indigo-600">Печать</a>

                    <a href="{{ route('admin.users.export.csv', $user) }}"
                       class="block px-4 py-2 hover:bg-gray-100 text-green-600">CSV</a>
                </div>
            </td>
        </tr>
    @empty
        <tr>
            <td colspan="6" class="p-4 text-center text-gray-500">Нет данных</td>
        </tr>
    @endforelse
    </tbody>
</table>

<div class="mt-4">
    {{ $users->withQueryString()->links() }}
</div>

<script>
    document.getElementById('check-all')?.addEventListener('change', function () {
        document.querySelectorAll('input[name="user_ids[]"]').forEach(cb => cb.checked = this.checked);
    });

    function toggleDropdown(button) {
        const dropdown = button.nextElementSibling;
        const isVisible = !dropdown.classList.contains('hidden');
        document.querySelectorAll('.dropdown').forEach(el => el.classList.add('hidden'));
        if (!isVisible) dropdown.classList.remove('hidden');
    }

    document.addEventListener('click', function (e) {
        if (!e.target.closest('td')) {
            document.querySelectorAll('.dropdown').forEach(el => el.classList.add('hidden'));
        }
    });
</script>
