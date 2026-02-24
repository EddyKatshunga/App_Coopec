<?php

namespace App\Livewire\Zones;

use App\Models\Zone;
use App\Models\Agence;
use App\Models\Agent;
use App\Services\AgentService;
use App\Services\ZoneService;
use Livewire\Component;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
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

    // Livewire injecte automatiquement le service si passé en paramètre de la méthode
    public function save(ZoneService $zoneService)
    {
        $validated = $this->validate();

        try {
            if (!$this->zone) {
                $validated['agence_id'] = $this->agence->id;
            }

            $zoneService->saveZone($validated, $this->zone);

            session()->flash('message', $this->zone ? 'Zone mise à jour.' : 'Zone créée.');
            return redirect()->route('agences.zones.index', $this->agence->id);
            
        } catch (\Exception $e) {
            session()->flash('error', "Une erreur est survenue : " . $e->getMessage());
        }
    }

    public function render()
    {
        $eligibleGerants = Agent::where('agence_id', $this->agence->id) // 1. Même agence
                            // 2. N'est PAS Chef de Zone (sauf éventuellement pour la zone en cours d'édition)
                            ->whereDoesntHave('zone_dirige', function($query) {
                                if ($this->zone) {
                                    $query->where('id', '!=', $this->zone->id);
                                }
                            })
                            // 3. N'est PAS Chef d'Agence (on enchaîne avec un AND implicite)
                            ->whereDoesntHave('agence_dirige') 
                            ->get();

        return view('livewire.zones.zone-form', [
            'gerants' => $eligibleGerants
        ]);
    }
}