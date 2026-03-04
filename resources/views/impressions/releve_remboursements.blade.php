@extends('impressions.layout')

@section('title', 'Relevé des Remboursements')

@section('content')
    <div class="text-center mb-6">
        <h1 class="text-xl font-black border-b-2 border-indigo-800 inline-block px-4 pb-1 uppercase text-indigo-900">RELEVÉ DES REMBOURSEMENTS</h1>
        <p class="text-sm text-gray-600 mt-2">Agence : <strong>{{ $cloture->agence->nom }}</strong> | Date : <strong>{{ $cloture->date_cloture->format('d/m/Y') }}</strong></p>
    </div>

    <table class="w-full text-[10px] table-custom">
        <thead class="bg-indigo-50 uppercase text-indigo-900">
            <tr>
                <th class="text-left">Heure</th>
                <th class="text-left">Client / Dossier</th>
                <th class="text-left">Zone</th>
                <th class="text-right text-green-700">Principal</th>
                <th class="text-right text-blue-700">Intérêt</th>
                <th class="text-right font-bold">Total Perçu</th>
                <th class="text-center">Devise</th>
            </tr>
        </thead>
        <tbody>
            @forelse($items as $remb)
            <tr>
                <td>{{ $remb->created_at->format('H:i') }}</td>
                <td>
                    <div class="font-bold">{{ $remb->credit->user->name }}</div>
                    <div class="text-[8px] text-gray-500">Réf : {{ $remb->credit->code }}</div>
                </td>
                <td class="uppercase">{{ $remb->zone->nom ?? 'N/A' }}</td>
                <td class="text-right font-mono">{{ number_format_fr($remb->montant_capital ?? $remb->montant) }}</td> {{-- Ajustez selon votre logique de calcul --}}
                <td class="text-right font-mono text-blue-600">{{ number_format_fr($remb->montant_interet ?? 0) }}</td>
                <td class="text-right font-mono font-bold bg-gray-50">{{ number_format_fr($remb->montant) }}</td>
                <td class="text-center font-bold">{{ $remb->monnaie }}</td>
            </tr>
            @empty
            <tr><td colspan="7" class="text-center py-4 italic text-gray-400">Aucun remboursement perçu ce jour.</td></tr>
            @endforelse
        </tbody>
        <tfoot class="bg-gray-800 text-white font-bold">
            <tr>
                <td colspan="3" class="text-right uppercase p-2">Cumul perçu (USD)</td>
                <td colspan="3" class="text-right font-mono text-lg p-2">${{ number_format_fr($items->where('monnaie', 'USD')->sum('montant')) }}</td>
                <td></td>
            </tr>
            <tr class="bg-gray-600">
                <td colspan="3" class="text-right uppercase p-2">Cumul perçu (CDF)</td>
                <td colspan="3" class="text-right font-mono text-lg p-2">{{ number_format_fr($items->where('monnaie', 'CDF')->sum('montant')) }} FC</td>
                <td></td>
            </tr>
        </tfoot>
    </table>

@endsection