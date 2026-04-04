<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Document')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @media print {
            @page { 
                size: A4; 
                margin: 15mm; 
            }
            .no-print { display: none !important; }
            body { background-color: white !important; -webkit-print-color-adjust: exact; }
        }
        
        /* Styles partagés par tous les documents */
        .print-container { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .border-double-bottom { border-bottom: 3px double #374151; }
        .table-custom th, .table-custom td { padding: 4px 8px; border: 1px solid #e5e7eb; }
    </style>
</head>
<body class="bg-gray-100 print:bg-white" onload="window.print()">
    
    <div class="no-print bg-gray-800 text-white p-4 flex justify-between items-center shadow-lg mb-6">
        <span class="font-bold">Mode Aperçu avant Impression</span>
        <div class="space-x-2">
            <button onclick="window.close()" class="bg-gray-600 px-4 py-2 rounded">Fermer</button>
            <button onclick="window.print()" class="bg-blue-500 px-4 py-2 rounded font-bold">Lancer l'impression</button>
        </div>
    </div>

    <div class="print-container max-w-4xl mx-auto bg-white p-8 shadow-sm print:shadow-none print:p-0">
        
        <div class="flex justify-between items-center mb-8 border-b-2 border-gray-800 pb-4">
            <div class="flex items-center space-x-4">
                <img src="{{ asset('images/logo.png') }}" class="h-16">
                <div>
                    <h2 class="text-xl font-black text-gray-800 uppercase">{{config('app.nom_entreprise')}} - LA FINANCE SAINE</h2>
                    <p class="text-xs text-gray-500 italic">Solidarité et développement</p>
                </div>
            </div>
            <div class="text-right text-xs">
                <p>{{config('app.nom_entreprise')}}</p>
                <p>Contact : +243 819 197 885</p>
                <p>Email : coopeckwilu6@gmail.com</p>
                <p>Adresse : 1464, boulevard nat. Q/Lunia C/Lukolela
                    <br>Réf. Marché Batetela, Immeuble Kasongo </p>
            </div>
        </div>

        <main>
            @yield('content')
        </main>

        <footer class="mt-12 pt-4 border-t border-gray-200 text-[10px] text-gray-400 text-center">
            <p>Document généré le {{ now()->format('d/m/Y H:i') }} par {{ auth()->user()->name }} - {{config('app.name')}}</p>
        </footer>
    </div>

</body>
</html>