<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Регистрация')</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @yield('head')
</head>
<body class="bg-gray-100 text-gray-800 min-h-screen">

<div class="container mx-auto px-4 py-8">
    @yield('content')
</div>

@yield('scripts')
</body>
</html>
