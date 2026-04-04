@extends('impressions.layout')

@section('title', 'Liste Générale des Membres')

@section('content')
    <div class="text-center mb-6">
        <h3 class="text-xl font-black uppercase tracking-[0.2em] text-gray-900">Liste Générale des Membres</h3>
        <p class="text-[10px] text-gray-500 font-medium mt-1">Édité le {{ now()->format('d/m/Y à H:i') }}</p>
    </div>

    {{-- Barre de Statistiques (Rendu ultra pro pour impression) --}}
    <div class="mb-6 flex flex-wrap gap-4 items-center justify-between border-y-2 border-gray-900 py-3">
        <div class="flex gap-8">
            <div class="flex flex-col">
                <span class="text-[9px] font-bold uppercase text-gray-500 tracking-wider">Total Membres</span>
                <span class="text-lg font-black text-gray-900">{{ $membres->count() }}</span>
            </div>
            <div class="flex flex-col border-l border-gray-300 pl-8">
                <span class="text-[9px] font-bold uppercase text-blue-600 tracking-wider">Hommes (M)</span>
                <span class="text-lg font-black text-gray-900">{{ $nbHommes }}</span>
            </div>
            <div class="flex flex-col border-l border-gray-300 pl-8">
                <span class="text-[9px] font-bold uppercase text-pink-600 tracking-wider">Femmes (F)</span>
                <span class="text-lg font-black text-gray-900">{{ $nbFemmes }}</span>
            </div>
        </div>
        
        {{-- Pourcentage (Optionnel mais pro) --}}
        <div class="text-[10px] font-mono font-bold text-gray-400 italic">
            Ratio H/F : {{ $membres->count() > 0 ? round(($nbHommes / $membres->count()) * 100) : 0 }}% / {{ $membres->count() > 0 ? round(($nbFemmes / $membres->count()) * 100) : 0 }}%
        </div>
    </div>
    
    <table class="w-full text-[10px] border-collapse">
        <thead>
            <tr class="bg-gray-900 text-white uppercase font-bold text-left print:bg-gray-100 print:text-black">
                <th class="p-2 border border-gray-900">N° Ident.</th>
                <th class="p-2 border border-gray-900">Nom Complet</th>
                <th class="p-2 border border-gray-900 text-center">Sexe</th>
                <th class="p-2 border border-gray-900">Email</th>
                <th class="p-2 border border-gray-900 text-center">Qualité</th>
                <th class="p-2 border border-gray-900 text-center">Date Adhésion</th>
                <th class="p-2 border border-gray-900 text-center">Statut</th>
            </tr>
        </thead>
        <tbody>
            @forelse($membres as $membre)
                <tr class="even:bg-gray-50">
                    <td class="p-2 border border-gray-300 font-mono font-bold">{{ $membre->numero_identification }}</td>
                    <td class="p-2 border border-gray-300 font-bold uppercase">{{ $membre->nom }}</td>
                    <td class="p-2 border border-gray-300 text-center font-bold {{ $membre->sexe == 'M' ? 'text-blue-700' : 'text-pink-700' }}">
                        {{ $membre->sexe }}
                    </td>
                    <td class="p-2 border border-gray-300 italic text-gray-600">{{ $membre->user->email ?? '—' }}</td>
                    <td class="p-2 border border-gray-300 text-center uppercase">{{ $membre->qualite }}</td>
                    <td class="p-2 border border-gray-300 text-center">
                        {{ $membre->date_adhesion ? $membre->date_adhesion->format('d/m/Y') : '—' }}
                    </td>
                    <td class="p-2 border border-gray-300 text-center">
                        @if($membre->agent)
                            <span class="px-1.5 py-0.5 border border-black bg-gray-900 text-white text-[8px] font-black uppercase rounded">Agent</span>
                        @else
                            <span class="px-1.5 py-0.5 border border-gray-400 text-gray-500 text-[8px] font-bold uppercase rounded">Membre</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="p-10 text-center text-gray-400 italic text-sm border">
                        Aucun membre enregistré dans le système.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Pied de page discret pour l'impression --}}
    <div class="mt-8 flex justify-between items-center text-[9px] text-gray-400 uppercase font-bold tracking-widest">
        <span>Institution Microfinance</span>
        <span>Page 1 / 1</span>
        <span class="border-b border-gray-300 pb-1 w-32 text-center text-gray-300 font-normal italic">Signature Autorisée</span>
    </div>
@endsection