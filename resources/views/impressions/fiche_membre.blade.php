@extends('impressions.layout')

@section('content')
<div class="p-4 border-2 border-gray-900 rounded-3xl">
    <div class="flex justify-between items-start border-b-4 border-gray-900 pb-6 mb-8">
        <div>
            <h1 class="text-3xl font-black uppercase italic text-gray-900">Fiche Membre</h1>
            <p class="text-xl font-bold text-blue-700">ID : #{{ $membre->numero_identification }}</p>
        </div>
        <div class="text-right">
            <p class="text-sm font-bold uppercase tracking-widest text-gray-500">Date d'adhésion</p>
            <p class="text-lg font-black">{{ $membre->date_adhesion?->format('d/m/Y') }}</p>
        </div>
    </div>

    <div class="grid grid-cols-2 gap-10 mb-10">
        {{-- État Civil --}}
        <div>
            <h3 class="text-xs font-black bg-gray-900 text-white px-3 py-1 mb-4 uppercase inline-block">I. Identité & État Civil</h3>
            <table class="w-full text-sm">
                <tr class="border-b"><td class="py-2 font-bold text-gray-500 uppercase text-[10px]">Nom Complet</td><td class="py-2 text-right font-black uppercase">{{ $membre->nom }}</td></tr>
                <tr class="border-b"><td class="py-2 font-bold text-gray-500 uppercase text-[10px]">Genre / Sexe</td><td class="py-2 text-right">{{ $membre->sexe == 'M' ? 'Masculin' : 'Féminin' }}</td></tr>
                <tr class="border-b"><td class="py-2 font-bold text-gray-500 uppercase text-[10px]">Né(e) le</td><td class="py-2 text-right">{{ $membre->date_de_naissance?->format('d/m/Y') }} à {{ $membre->lieu_de_naissance }}</td></tr>
                <tr class="border-b"><td class="py-2 font-bold text-gray-500 uppercase text-[10px]">Téléphone</td><td class="py-2 text-right font-mono font-bold">{{ $membre->telephone }}</td></tr>
                <tr class="border-b"><td class="py-2 font-bold text-gray-500 uppercase text-[10px]">Email</td><td class="py-2 text-right italic">{{ $membre->email }}</td></tr>
                <tr><td class="py-2 font-bold text-gray-500 uppercase text-[10px]">Adresse domicile</td><td class="py-2 text-right leading-tight">{{ $membre->adresse }}</td></tr>
            </table>
        </div>

        {{-- Professionnel --}}
        <div>
            <h3 class="text-xs font-black bg-gray-900 text-white px-3 py-1 mb-4 uppercase inline-block">II. Infos Professionnelles</h3>
            <table class="w-full text-sm">
                <tr class="border-b"><td class="py-2 font-bold text-gray-500 uppercase text-[10px]">Activités</td><td class="py-2 text-right font-bold">{{ $membre->activites }}</td></tr>
                <tr class="border-b"><td class="py-2 font-bold text-gray-500 uppercase text-[10px]">Adresse Activité</td><td class="py-2 text-right leading-tight">{{ $membre->adresse_activite }}</td></tr>
                <tr class="border-b"><td class="py-2 font-bold text-gray-500 uppercase text-[10px]">Qualité</td><td class="py-2 text-right uppercase font-black text-blue-700">{{ $membre->qualite }}</td></tr>
                <tr class="border-b"><td class="py-2 font-bold text-gray-500 uppercase text-[10px]">Parrainage</td><td class="py-2 text-right italic">{{ $membre->agentParrain?->nom ?? 'Direct' }}</td></tr>
            </table>
        </div>
    </div>

    {{-- III. Comptes Épargne --}}
    <div class="mb-10">
        <h3 class="text-xs font-black bg-gray-900 text-white px-3 py-1 mb-4 uppercase inline-block">III. Comptes d'Épargne</h3>
        <table class="w-full text-xs border-collapse">
            <thead>
                <tr class="bg-gray-100 border-b border-gray-900">
                    <th class="p-2 text-left">N° COMPTE</th>
                    <th class="p-2 text-left">INTITULÉ</th>
                    <th class="p-2 text-right">SOLDE USD</th>
                    <th class="p-2 text-right">SOLDE CDF</th>
                </tr>
            </thead>
            <tbody>
                @foreach($membre->comptes as $compte)
                <tr class="border-b border-gray-200">
                    <td class="p-2 font-mono">{{ $compte->numero_compte }}</td>
                    <td class="p-2 font-bold italic">{{ $compte->intitule }}</td>
                    <td class="p-2 text-right">{{ number_format($compte->solde_usd, 2) }} $</td>
                    <td class="p-2 text-right">{{ number_format($compte->solde_cdf, 0, ',', ' ') }} FC</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection