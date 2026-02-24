{{-- ressources/views/components/print-layout.blade.php --}}

@props([
    'title' => 'DOCUMENT OFFICIEL', 
    'reference' => '', 
    'size' => 'A4', 
    'filename' => null
])

<div class="print-container bg-white text-gray-900 mx-auto">
    <style>
        @media print {
            body * { visibility: hidden; }
            .print-container, .print-container * { visibility: visible; }
            .print-container {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                padding: 0.5cm !important; /* Réduit pour gagner de la place */
                display: flex;
                flex-direction: column;
                height: 100%;
                border: none !important;
                box-shadow: none !important;
            }
            .print-container main {
                flex: 1 0 auto; /* Prend l'espace disponible */
            }
            footer {
                page-break-inside: avoid; /* Empêche la coupure du footer */
            }
            @page { 
                size: {{ $size }}; 
                margin: 0; 
            }
        }

        @media screen {
            .print-container {
                border: 1px solid #e5e7eb;
                margin-top: 2rem;
                box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            }
        }
    </style>

    <script>
        function printDocument() {
            const originalTitle = document.title;
            const dynamicName = "{{ $filename ?? ($title . '_' . $reference) }}";
            document.title = dynamicName.replace(/[/\\?%*:|"<>]/g, '-');
            window.print();
            setTimeout(() => { document.title = originalTitle; }, 100);
        }
    </script>

    {{-- EN-TÊTE COMPACT --}}
    <header class="flex justify-between items-center border-b-4 border-gray-900 pb-2 mb-3">
        <div class="flex items-center gap-3">
            <img src="{{ asset('images/logo.png') }}" alt="Logo" class="h-12 w-auto object-contain">
            <div class="border-l-2 border-gray-200 pl-3">
                <h1 class="text-xl font-black uppercase tracking-tighter leading-none">{{ config('app.name') }}</h1>
                <p class="text-[8px] font-bold text-gray-500 uppercase tracking-[0.2em] mt-0.5">{{ config('settings.nom_entreprise') }}</p>
                <p class="text-[8px] text-gray-400 font-medium">Agrément N° : 123/XYZ/2024</p>
            </div>
        </div>
        <div class="text-right">
            <div class="bg-gray-900 text-white px-3 py-1 mb-1 inline-block">
                <h2 class="text-[10px] font-black uppercase tracking-widest">{{ $title }}</h2>
            </div>
            <p class="text-[9px] font-mono font-bold text-gray-800">{{ $reference }}</p>
            <p class="text-[8px] text-gray-400 italic">Émis le {{ now()->format('d/m/Y à H:i') }}</p>
        </div>
    </header>

    {{-- CORPS DU DOCUMENT (prend la hauteur restante) --}}
    <main class="flex-1">
        {{ $slot }}
    </main>

    {{-- PIED DE PAGE COMPACT --}}
    <footer class="pt-3 border-t border-gray-100 page-break-inside-avoid">
        <div class="grid grid-cols-3 gap-2 text-[7px] text-gray-500 uppercase font-semibold">
            <div>
                <p class="text-gray-900 mb-0.5">Siège Social</p>
                <p>AV. LUMUMBA N°141</p>
                <p>Kikwit, RDC</p>
            </div>
            <div class="text-center border-x border-gray-100">
                <p class="text-gray-900 mb-0.5">Contact</p>
                <p>+243 000 000 000</p>
                <p>contact@sysco.com</p>
            </div>
            <div class="text-right">
                <p class="text-gray-900 mb-0.5">Digital</p>
                <p>{{ config('app.url') }}</p>
                <p>Page 1 / 1</p>
            </div>
        </div>
        <div class="mt-2 text-center">
            <p class="text-[6px] text-gray-300 italic">Ce document électronique est certifié conforme et protégé par un QR Code.</p>
        </div>
    </footer>
</div>