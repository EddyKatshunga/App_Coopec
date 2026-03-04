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
    public ?Agence $agence = null;
    public ?Zone $zone = null; // Optionnel, présent uniquement en édition
    

    public $nom, $code, $gerant_id;

    public function mount($agenceUuid = null, $zoneUuid = null)
    {
        // Mode édition : on a un UUID de zone
        if ($zoneUuid) {
            $this->zone = Zone::where('uuid', $zoneUuid)->firstOrFail();
            $this->nom = $this->zone->nom;
            $this->code = $this->zone->code;
            $this->gerant_id = $this->zone->gerant_id;
            $this->agence = $this->zone->agence;
        } 
        // Mode création : on a un UUID d'agence
        elseif ($agenceUuid) {
            $this->agence = Agence::where('uuid', $agenceUuid)->firstOrFail();
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