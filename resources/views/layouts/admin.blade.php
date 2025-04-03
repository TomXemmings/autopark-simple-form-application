<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Админка')</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-800">
<div class="min-h-screen flex">
    <aside class="w-64 bg-white shadow-md px-6 py-8">
        <h2 class="text-xl font-bold mb-6">Админка</h2>
        <nav class="space-y-2">
            <a href="{{ route('admin.users') }}" class="block py-2 px-4 rounded hover:bg-gray-200 {{ request()->routeIs('admin.users') ? 'bg-gray-100 font-semibold' : '' }}">Пользователи</a>
            <form action="{{ route('admin.logout') }}" method="POST">
                @csrf
                <button class="w-full text-left py-2 px-4 mt-4 bg-red-100 hover:bg-red-200 text-red-700 rounded">Выйти</button>
            </form>
        </nav>
    </aside>

    <main class="flex-1 p-8">
        @yield('content')
    </main>
</div>
</body>
</html>
