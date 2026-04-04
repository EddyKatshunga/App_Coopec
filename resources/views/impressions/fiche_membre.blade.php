@extends('impressions.layout')

@section('content')
{{-- Conteneur principal avec classes spécifiques pour l'impression (print:...) --}}
<div class="max-w-5xl mx-auto bg-white rounded-xl shadow-sm border border-gray-300 print:border-none print:shadow-none overflow-hidden my-4">
    
    {{-- En-tête de la fiche (Style épuré, contraste élevé) --}}
    <div class="border-b-2 border-gray-900 px-6 py-5 flex flex-col md:flex-row justify-between items-start gap-4">
        <div class="flex items-center gap-5">
            {{-- Icône dynamique selon le genre --}}
            <div class="w-16 h-16 shrink-0 rounded-full border-2 border-gray-900 flex items-center justify-center bg-gray-50">
                @if($membre->sexe == 'M')
                    {{-- Icône Masculine --}}
                    <svg class="w-8 h-8 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                @else
                    {{-- Icône Féminine --}}
                    <svg class="w-8 h-8 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 11a4 4 0 100-8 4 4 0 000 8zM8 14v1a4 4 0 004 4v0a4 4 0 004-4v-1m-8 0a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                @endif
            </div>
            
            <div>
                <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1">Fiche Signalétique Membre</p>
                <h1 class="text-2xl font-black uppercase tracking-wide text-gray-900 flex items-center gap-3">
                    {{ $membre->nom }}
                    
                    {{-- Badge Agent dynamique --}}
                    @if($membre->agent)
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-gray-900 text-white tracking-widest uppercase print:border print:border-black print:text-black print:bg-white">
                            Agent Interne
                        </span>
                    @endif
                </h1>
                
                <div class="flex items-center gap-3 mt-1 text-sm font-medium text-gray-700">
                    <span>ID : <strong class="text-gray-900">#{{ $membre->numero_identification }}</strong></span>
                    <span class="text-gray-300">|</span>
                    <span class="capitalize">Qualité : <strong>{{ $membre->qualite }}</strong></span>
                </div>
            </div>
        </div>

        <div class="text-right">
            <p class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-0.5">Date d'adhésion</p>
            <p class="text-lg font-black text-gray-900">{{ $membre->date_adhesion?->format('d/m/Y') ?? 'N/A' }}</p>
        </div>
    </div>

    {{-- Contenu Principal --}}
    <div class="p-6 grid grid-cols-1 lg:grid-cols-3 gap-6 print:grid-cols-3 print:gap-4">
        
        {{-- Colonne de gauche : Identité & Pro (Prend 2/3 de l'espace) --}}
        <div class="col-span-2 space-y-6">
            
            {{-- Section Identité --}}
            <section>
                <h3 class="text-xs font-black bg-gray-100 text-gray-900 px-2 py-1 mb-3 uppercase tracking-widest border-l-4 border-gray-900 print:bg-transparent print:border-black">
                    I. Identité & État Civil
                </h3>
                
                <div class="border border-gray-200 rounded-lg p-4 print:border-gray-400">
                    <dl class="grid grid-cols-2 gap-x-4 gap-y-3">
                        <div>
                            <dt class="text-[9px] font-bold text-gray-500 uppercase tracking-wider">Genre / Sexe</dt>
                            <dd class="text-sm font-bold text-gray-900">{{ $membre->sexe == 'M' ? 'Masculin' : 'Féminin' }}</dd>
                        </div>
                        <div>
                            <dt class="text-[9px] font-bold text-gray-500 uppercase tracking-wider">Date & Lieu de naissance</dt>
                            <dd class="text-sm font-bold text-gray-900">
                                {{ $membre->date_de_naissance?->format('d/m/Y') }} à {{ $membre->lieu_de_naissance }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-[9px] font-bold text-gray-500 uppercase tracking-wider">Téléphone</dt>
                            <dd class="text-sm font-mono font-bold text-gray-900">{{ $membre->telephone }}</dd>
                        </div>
                        <div>
                            <dt class="text-[9px] font-bold text-gray-500 uppercase tracking-wider">Email</dt>
                            <dd class="text-sm font-semibold text-gray-900">{{ $membre->email ?? 'Non renseigné' }}</dd>
                        </div>
                        <div class="col-span-2 pt-2 border-t border-gray-100 print:border-gray-300">
                            <dt class="text-[9px] font-bold text-gray-500 uppercase tracking-wider">Adresse Domicile</dt>
                            <dd class="text-sm font-bold text-gray-900 leading-tight">{{ $membre->adresse }}</dd>
                        </div>
                    </dl>
                </div>
            </section>

            {{-- Section Professionnelle --}}
            <section>
                <h3 class="text-xs font-black bg-gray-100 text-gray-900 px-2 py-1 mb-3 uppercase tracking-widest border-l-4 border-gray-900 print:bg-transparent print:border-black">
                    II. Informations Professionnelles
                </h3>
                
                <div class="border border-gray-200 rounded-lg p-4 print:border-gray-400">
                    <dl class="grid grid-cols-2 gap-x-4 gap-y-3">
                        <div class="col-span-2">
                            <dt class="text-[9px] font-bold text-gray-500 uppercase tracking-wider">Activités principales</dt>
                            <dd class="text-sm font-bold text-gray-900">{{ $membre->activites }}</dd>
                        </div>
                        <div class="col-span-2">
                            <dt class="text-[9px] font-bold text-gray-500 uppercase tracking-wider">Adresse de l'activité</dt>
                            <dd class="text-sm font-bold text-gray-900 leading-tight">{{ $membre->adresse_activite }}</dd>
                        </div>
                        <div class="col-span-2 pt-2 border-t border-gray-100 print:border-gray-300">
                            <dt class="text-[9px] font-bold text-gray-500 uppercase tracking-wider">Parrainé par</dt>
                            <dd class="text-sm font-bold text-gray-900 italic">{{ $membre->agentParrain?->nom ?? 'Inscription Directe' }}</dd>
                        </div>
                    </dl>
                </div>
            </section>
        </div>

        {{-- Colonne de droite : Synthèse & Signature (Prend 1/3 de l'espace) --}}
        <div class="space-y-6 flex flex-col justify-between">
            
            {{-- Synthèse du dossier --}}
            <section>
                <h3 class="text-xs font-black bg-gray-100 text-gray-900 px-2 py-1 mb-3 uppercase tracking-widest border-l-4 border-gray-900 print:bg-transparent print:border-black">
                    III. Synthèse du Dossier
                </h3>
                
                <div class="border-2 border-gray-900 rounded-lg p-4 space-y-4 print:border-black">
                    
                    {{-- Résumé Comptes --}}
                    <div class="flex justify-between items-center border-b border-gray-200 pb-3 print:border-gray-400">
                        <div>
                            <p class="text-[10px] font-bold text-gray-500 uppercase">Comptes actifs</p>
                            <p class="text-xs text-gray-900 font-medium">Liés au profil</p>
                        </div>
                        <span class="text-2xl font-black text-gray-900 font-mono">{{ $membre->comptes->count() }}</span>
                    </div>

                    {{-- Résumé Crédits --}}
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-[10px] font-bold text-gray-500 uppercase">Total Crédits</p>
                            <p class="text-xs text-gray-900 font-medium">Historique global</p>
                        </div>
                        <span class="text-2xl font-black text-gray-900 font-mono">{{ $membre->credits->count() }}</span>
                    </div>

                    {{-- Statut Engagement Financier --}}
                    <div class="mt-2 pt-3 border-t-2 border-gray-900 print:border-black">
                        @if($membre->hasActiveCredit())
                            <div class="flex items-start gap-2 text-gray-900">
                                <svg class="w-4 h-4 mt-0.5 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                                <div>
                                    <p class="text-[11px] font-black uppercase">Crédit en cours</p>
                                    <p class="text-[9px] leading-tight font-medium mt-0.5">Le membre a un engagement financier actif.</p>
                                </div>
                            </div>
                        @else
                            <div class="flex items-start gap-2 text-gray-900">
                                <svg class="w-4 h-4 mt-0.5 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                <div>
                                    <p class="text-[11px] font-black uppercase">Libre d'engagement</p>
                                    <p class="text-[9px] leading-tight font-medium mt-0.5">Aucun crédit actif ou en retard.</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </section>
            
            {{-- Zone pour la signature / cachet (Positionnée en bas) --}}
            <div class="border border-gray-400 border-dashed rounded-lg p-4 text-center mt-auto min-h-[120px] flex flex-col justify-between print:border-black print:border-solid">
                <p class="text-[9px] font-bold text-gray-500 uppercase tracking-widest">Visa Administration</p>
                <div class="flex-grow"></div>
                <div>
                    <div class="w-32 h-px bg-gray-400 mx-auto print:bg-black"></div>
                    <p class="text-[9px] font-bold text-gray-500 mt-1 uppercase">Signature / Cachet</p>
                </div>
            </div>
            
        </div>
    </div>
</div>
@endsection