@php
    $config = match($credit->statut) {
        'en_cours'          => ['css' => 'bg-blue-100 text-blue-800', 'label' => 'En cours'],
        'en_retard'         => ['css' => 'bg-orange-100 text-orange-800', 'label' => 'En retard'],
        'termine'           => ['css' => 'bg-green-100 text-green-800', 'label' => 'Soldé'],
        'termine_en_retard' => ['css' => 'bg-purple-100 text-purple-800', 'label' => 'Soldé (retard)'],
        'termine_negocie'   => ['css' => 'bg-gray-800 text-white', 'label' => 'Clôturé / Négocié'],
        default             => ['css' => 'bg-gray-100 text-gray-800', 'label' => $credit->statut],
    };
@endphp

<span class="px-3 py-1 rounded-lg text-[10px] font-black uppercase tracking-widest {{ $config['css'] }}">
    {{ $config['label'] }}
</span>