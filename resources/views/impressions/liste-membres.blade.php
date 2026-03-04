@extends('impressions.layout')

@section('title', 'Liste Générale des Membres')

@section('content')
    <div class="text-center mb-6">
        <h3 class="text-xl font-bold uppercase underline decoration-1 tracking-wider">Liste Générale des Membres</h3>
        <p class="text-sm text-gray-600 italic">Document extrait du système le {{ now()->format('d/m/Y à H:i') }}</p>
    </div>

    {{-- Statistiques rapides en bas de page --}}
    <div class="mt-6 flex justify-end">
        <div class="border-2 border-gray-800 p-3 bg-gray-50 inline-block rounded-lg text-right">
            <p class="text-xs font-bold uppercase">Total Membres : <span class="text-lg ml-2">{{ count($membres) }}</span></p>
        </div>
    </div>
    
    <table class="w-full text-[11px] table-custom border-collapse">
        <thead>
            <tr class="bg-gray-100 text-gray-700 uppercase font-bold text-left">
                <th class="p-2 border">N° Ident.</th>
                <th class="p-2 border">Nom Complet</th>
                <th class="p-2 border">Sexe</th>
                <th class="p-2 border">Email</th>
                <th class="p-2 border text-center">Qualité</th>
                <th class="p-2 border text-center">Date Adhésion</th>
                <th class="p-2 border">Statut</th>
            </tr>
        </thead>
        <tbody>
            @forelse($membres as $membre)
                <tr class="hover:bg-gray-50">
                    <td class="p-2 border font-mono font-bold">{{ $membre->numero_identification }}</td>
                    <td class="p-2 border font-semibold">{{ $membre->nom }}</td>
                    <td class="p-2 border text-center">{{ $membre->sexe }}</td>
                    <td class="p-2 border italic text-gray-600">{{ $membre->user->email ?? '—' }}</td>
                    <td class="p-2 border text-center">{{ $membre->qualite }}</td>
                    <td class="p-2 border text-center">
                        {{ $membre->date_adhesion ? $membre->date_adhesion->format('d/m/Y') : '—' }}
                    </td>
                    <td class="p-2 border text-center">
                        <span class="px-2 py-0.5 rounded-full border text-[9px] uppercase font-bold">
                            {{ $membre->agent ? 'Agent' : 'Membre' }}
                        </span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="p-8 text-center text-gray-500 italic text-sm">
                        Aucun membre enregistré dans le système.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
@endsection