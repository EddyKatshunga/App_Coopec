@extends('impressions.layout')

@section('title', 'Relevé de compte - ' . $account->nom)

@section('content')
<div class="text-center mb-6">
    <h1 class="text-xl font-black uppercase">RELEVÉ DE COMPTE</h1>
    <p class="text-base font-semibold">{{ $account->nom }} <span class="text-sm font-normal">({{ $account->numero }})</span> – Type : {{ strtoupper($account->type) }}</p>
    <p class="text-sm">Agence : {{ $agence ? $agence->nom : 'Toutes' }} | Période du {{ \Carbon\Carbon::parse($dateDebut)->format('d/m/Y') }} au {{ \Carbon\Carbon::parse($dateFin)->format('d/m/Y') }}</p>
</div>

{{-- Cartes récapitulatives par devise --}}
<div class="grid grid-cols-2 gap-4 mb-6">
    @foreach(['USD', 'CDF'] as $devise)
    @php $s = $stats[$devise]; @endphp
    <div class="border-2 border-gray-200 rounded p-3 bg-gray-50">
        <h3 class="text-sm font-bold border-b border-gray-300 pb-1 mb-2">Compte {{ $devise }}</h3>
        <div class="text-xs space-y-1">
            <div class="flex justify-between">
                <span>Solde initial (avant période) :</span>
                <span class="font-mono">{{ number_format($s['soldeInitial'], $devise=='USD'?2:0) }} {{ $devise }}</span>
            </div>
            <div class="flex justify-between">
                <span>Total débits :</span>
                <span class="font-mono">{{ number_format($s['periodDebit'], $devise=='USD'?2:0) }} {{ $devise }}</span>
            </div>
            <div class="flex justify-between">
                <span>Total crédits :</span>
                <span class="font-mono">{{ number_format($s['periodCredit'], $devise=='USD'?2:0) }} {{ $devise }}</span>
            </div>
            <div class="flex justify-between font-bold pt-1 border-t border-gray-300 mt-1">
                <span>Solde final :</span>
                <span class="font-mono">{{ number_format($s['soldeFinal'], $devise=='USD'?2:0) }} {{ $devise }}</span>
            </div>
        </div>
    </div>
    @endforeach
</div>

{{-- Tableau des mouvements --}}
<table class="w-full text-xs table-custom">
    <thead class="bg-gray-800 text-white">
        <tr>
            <th class="p-2 text-left">Date</th>
            <th class="p-2 text-left">Agence</th>
            <th class="p-2 text-left">Libellé</th>
            <th class="p-2 text-right">Débit</th>
            <th class="p-2 text-right">Crédit</th>
            <th class="p-2 text-center">Devise</th>
        </tr>
    </thead>
    <tbody>
        @forelse($mouvements as $line)
        <tr class="{{ $loop->even ? 'bg-gray-50' : '' }}">
            <td class="p-2">{{ $line->journalEntry->date_operation->format('d/m/Y') }}</td>
            <td class="p-2">{{ $line->journalEntry->agence->nom ?? '-' }}</td>
            <td class="p-2">{{ $line->journalEntry->libelle }}</td>
            <td class="p-2 text-right font-mono">{{ $line->debit ? number_format($line->debit, 2) : '-' }}</td>
            <td class="p-2 text-right font-mono">{{ $line->credit ? number_format($line->credit, 2) : '-' }}</td>
            <td class="p-2 text-center font-bold">
                <span class="px-2 py-0.5 rounded {{ $line->monnaie == 'USD' ? 'bg-blue-100' : 'bg-emerald-100' }}">{{ $line->monnaie }}</span>
            </td>
        </tr>
        @empty
        <tr><td colspan="6" class="p-4 text-center text-gray-400">Aucun mouvement trouvé pour cette période</td></tr>
        @endforelse
    </tbody>
</table>

<div class="mt-6 text-[10px] text-gray-500 text-right">
    Arrêté à la date du {{ now()->format('d/m/Y H:i') }}
</div>
@endsection