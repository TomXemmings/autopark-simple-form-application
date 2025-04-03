<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Документы пользователя #{{ $user->user_code }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.4.120/pdf.min.js"></script>
    <style>
        @media print {
            .page-break { page-break-after: always; }
        }

        img, canvas {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
            display: block;
            margin: 0 auto;
        }

        body {
            font-family: sans-serif;
            padding: 2rem;
        }
    </style>
</head>
<body class="bg-white text-black">

<h1 class="text-2xl font-bold mb-8">Документы пользователя #{{ $user->user_code }}</h1>

@php
    $pdfQueue = [];
@endphp

@foreach ($user->documents as $doc)
    @php
        $ext = strtolower(pathinfo($doc->file_path, PATHINFO_EXTENSION));
        $isImage = in_array($ext, ['jpg', 'jpeg', 'png', 'gif']);
        $isPdf = $ext === 'pdf';
        $docId = 'doc-' . $doc->id;
    @endphp

    <div class="mb-12">
        <h2 class="text-lg font-semibold mb-4">{{ $doc->type }}</h2>

        @if ($isImage)
            <img src="{{ asset($doc->file_path) }}" alt="{{ $doc->type }}" class="page-break">
        @elseif ($isPdf)
            <div id="{{ $docId }}"></div>
            @php $pdfQueue[] = ['id' => $docId, 'url' => asset($doc->file_path)]; @endphp
        @else
            <p>Формат не поддерживается для предпросмотра.</p>
        @endif
    </div>
@endforeach

<script>
    const pdfsToRender = @json($pdfQueue);
    let totalPages = 0;
    let renderedPages = 0;

    async function renderAllPDFs() {
        for (const pdfDoc of pdfsToRender) {
            const container = document.getElementById(pdfDoc.id);
            const pdf = await pdfjsLib.getDocument(pdfDoc.url).promise;
            totalPages += pdf.numPages;

            for (let i = 1; i <= pdf.numPages; i++) {
                const page = await pdf.getPage(i);
                const viewport = page.getViewport({ scale: 1.5 });
                const canvas = document.createElement('canvas');
                const context = canvas.getContext('2d');

                canvas.width = viewport.width;
                canvas.height = viewport.height;
                canvas.classList.add('page-break', 'mb-12');

                await page.render({
                    canvasContext: context,
                    viewport: viewport
                }).promise;

                container.appendChild(canvas);
                renderedPages++;

                if (renderedPages === totalPages) {
                    setTimeout(() => window.print(), 500);
                }
            }
        }

        if (pdfsToRender.length === 0) {
            window.print();
        }
    }

    window.addEventListener('load', renderAllPDFs);
</script>

</body>
</html>
