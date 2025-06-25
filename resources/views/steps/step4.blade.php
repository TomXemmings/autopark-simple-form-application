@extends('layouts.app')

@section('title', 'Шаг 4 — Загрузка документов')

@section('content')
    <div class="max-w-2xl mx-auto bg-white p-8 rounded shadow">
        <h2 class="text-xl font-bold mb-6">Шаг 4: Загрузите документы</h2>

        <form method="POST" action="{{ url('/step-4') }}" enctype="multipart/form-data" id="upload-form">
            @csrf

            <div class="grid grid-cols-1 gap-4 mb-4">

                <div>
                    <label for="passport_main" class="block text-sm font-medium text-gray-700 mb-1">Паспорт (основная страница) *</label>
                    <input type="file" name="passport_main" accept=".jpg,.jpeg,.png,.pdf"
                           class="form-input border rounded p-2 w-full" required>
                </div>

                <div>
                    <label for="passport_registration" class="block text-sm font-medium text-gray-700 mb-1">Паспорт (прописка) *</label>
                    <input type="file" name="passport_registration" accept=".jpg,.jpeg,.png,.pdf"
                           class="form-input border rounded p-2 w-full" required>
                </div>

                <div>
                    <label for="license_front" class="block text-sm font-medium text-gray-700 mb-1">Вод. удостоверение (лицевая) *</label>
                    <input type="file" name="license_front" accept=".jpg,.jpeg,.png,.pdf"
                           class="form-input border rounded p-2 w-full" required>
                </div>

                <div>
                    <label for="license_back" class="block text-sm font-medium text-gray-700 mb-1">Вод. удостоверение (обратная) *</label>
                    <input type="file" name="license_back" accept=".jpg,.jpeg,.png,.pdf"
                           class="form-input border rounded p-2 w-full" required>
                </div>

                <div>
                    <label for="insurance_photo" class="block text-sm font-medium text-gray-700 mb-1">Страховой полис ОСГОП *</label>
                    <input type="file" name="insurance_photo" accept=".jpg,.jpeg,.png,.pdf"
                           class="form-input border rounded p-2 w-full" required>
                </div>

                <div>
                    <label for="court_certificate" class="block text-sm font-medium text-gray-700 mb-1">
                        Справка об отсутствии судимости *
                    </label>
                    <input type="file" name="court_certificate" accept=".jpg,.jpeg,.png,.pdf"
                           class="form-input border rounded p-2 w-full" required>
                </div>

                <div>
                    <label for="yandex_contract" class="block text-sm font-medium text-gray-700 mb-1">
                        Договор с Яндекс *
                    </label>
                    <input type="file" name="yandex_contract" accept=".jpg,.jpeg,.png,.pdf"
                           class="form-input border rounded p-2 w-full" required>
                </div>


                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700">
                        Подпись как в паспорте <span class="text-xs text-gray-500">(нарисуйте пальцем или мышью)</span> *
                    </label>

                    {{-- холст для рисования --}}
                    <canvas id="signature-pad"
                            class="border border-gray-300 rounded w-full h-40 touch-none"></canvas>

                    {{-- кнопка очистки холста --}}
                    <button type="button"
                            id="clear-signature"
                            class="mt-2 inline-block bg-gray-200 hover:bg-gray-300 text-gray-700 text-sm px-3 py-1 rounded">
                        Очистить подпись
                    </button>

                    {{-- скрытый input, сюда JS запишет base64 перед отправкой --}}
                    <input type="hidden" name="signature" id="signature-input" required>
                </div>

                <button type="submit"
                        class="w-full bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                    Загрузить все документы
                </button>
            </div>
        </form>


        <hr class="my-6">

{{--        <h3 class="text-lg font-semibold mb-2">Загруженные документы:</h3>--}}
{{--        <div class="space-y-2">--}}
{{--            @foreach ($user->documents as $doc)--}}
{{--                <div class="flex items-center justify-between border p-2 rounded bg-gray-50">--}}
{{--                    <div>--}}
{{--                        <strong>{{ $doc->type }}</strong> —--}}
{{--                        <a href="{{ asset($doc->file_path) }}" target="_blank" class="text-blue-600 underline">Просмотр</a>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            @endforeach--}}
{{--        </div>--}}

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
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.6/dist/signature_pad.umd.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script>
        const canvas   = document.getElementById('signature-pad');
        // подгоняем размер под плотность пикселей, чтобы подпись была чёткой
        function resizeCanvas() {
            const ratio = Math.max(window.devicePixelRatio || 1, 1);
            canvas.width  = canvas.offsetWidth  * ratio;
            canvas.height = canvas.offsetHeight * ratio;
            canvas.getContext('2d').scale(ratio, ratio);
        }
        window.addEventListener('resize', resizeCanvas);
        resizeCanvas();

        const signaturePad = new SignaturePad(canvas, {
            backgroundColor: 'rgba(0,0,0,0)',       // прозрачный фон
            penColor: 'rgb(14, 165, 233)'           // голубой (tailwind sky-500)
        });

        // ---------- очистка подписи ----------
        $('#clear-signature').on('click', () => signaturePad.clear());

        $('#upload-form').on('submit', function (e) {
            if (signaturePad.isEmpty()) {
                e.preventDefault();
                alert('Пожалуйста, подпишитесь, прежде чем продолжить.');
                return;
            }

            $('#signature-input').val(signaturePad.toDataURL('image/png'));
        });
    </script>
@endsection
