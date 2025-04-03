@extends('layouts.app')

@section('title', 'Заявка отправлена')

@section('content')
    <div class="max-w-xl mx-auto mt-20 bg-white p-8 rounded shadow text-center">
        <h1 class="text-2xl font-bold mb-4">Спасибо!</h1>
        <p class="text-lg mb-6">
            Все необходимые документы и данные успешно загружены.
        </p>

        <p class="text-gray-700 mb-6">
            Ваша заявка будет обработана в ближайшее время. Мы свяжемся с вами по указанному номеру телефона или email.
        </p>
    </div>
@endsection
