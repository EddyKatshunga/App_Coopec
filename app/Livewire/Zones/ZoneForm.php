<?php

namespace App\Livewire\Zones;

use App\Models\Zone;
use App\Models\Agence;
use App\Models\Agent;
use App\Services\AgentService;
use Livewire\Component;
use Illuminate\Validation\Rule;

class ZoneForm extends Component
{
    public Agence $agence;
    public AgentService $service;
    public ?Zone $zone = null; // Optionnel, présent uniquement en édition

    public $nom, $code, $gerant_id;

    public function mount(Agence $agence, ?Zone $zone = null)
    {
        $this->agence = $agence;
        
        if ($zone && $zone->exists) {
            $this->zone = $zone;
            $this->nom = $zone->nom;
            $this->code = $zone->code;
            $this->gerant_id = $zone->gerant_id;
            // On s'assure que l'agence est celle de la zone
            $this->agence = $zone->agence;
        }
    }

    protected function rules()
    {
        return [
            'nom' => [
                'required', 'string', 'min:2',
                Rule::unique('zones', 'nom')->ignore($this->zone?->id)
            ],
            'code' => [
                'required', 'string', 'max:10',
                Rule::unique('zones', 'code')->ignore($this->zone?->id)
            ],
            'gerant_id' => 'required|exists:agents,id',
        ];
    }

    public function save()
    {
        $validated = $this->validate();

        if ($this->zone) {
            // Mode Édition
            $this->zone->update($validated);
            session()->flash('message', 'Zone mise à jour avec succès.');
        } else {
            // Mode Création
            $validated['agence_id'] = $this->agence->id;
            Zone::create($validated);

            session()->flash('message', 'Zone créée avec succès.');
        }

        return redirect()->route('agences.zones.index', $this->agence->id);
    }

    public function render()
    {
        $eligibleGerants = Agent::where('agence_id', $this->agence->id)
            // On exclut l'agent déjà gérant de la zone actuelle si on est en mode édition
            ->whereDoesntHave('zone_dirige', function($query) {
                if ($this->zone) {
                    $query->where('id', '!=', $this->zone->id);
                }
            })
            ->get();

        return view('livewire.zones.zone-form', [
            'gerants' => $eligibleGerants
        ])->layout('layouts.app');
    }
}