<?php

namespace App\Models\Traits;

use App\Models\CloturesComptable;
use Exception;
use Illuminate\Database\Eloquent\Model;

trait ManageClotureComptable
{
    /**
     * Boot du trait.
     */
    public static function bootManageClotureComptable()
    {
        // Lors de la création : assignation automatique date et journee_comptable_id
        static::creating(function (Model $model) {
            $model->assignJourneeEtDate();
            $model->ensureJourneeEstOuverte();
        });

        // Lors de la mise à jour : vérifier que la journée n'est pas clôturée
        static::updating(function (Model $model) {
            $model->ensureJourneeNonCloturee();
        });

        // Lors de la suppression : vérifier que la journée n'est pas clôturée
        static::deleting(function (Model $model) {
            $model->ensureJourneeNonCloturee();
        });
    }

    /**
     * Assigne automatiquement journee_comptable_id et la date
     * en fonction de la journée comptable ouverte du jour.
     */
    protected function assignJourneeEtDate(): void
    {
        $agenceId = auth()->user()->agence_id;
        if (!$agenceId) {
            throw new Exception("Impossible de déterminer l'agence pour cette transaction.");
        }

        // Récupérer la journée ouverte pour l'agence de cette opération
        $journee = auth()->user()->journee_ouverte;

        if (!$journee) {
            throw new Exception("Aucune journée comptable ouverte pour aujourd'hui. Veuillez contacter le directeur.");
        }

        // Assigner la clé étrangère
        $this->journee_comptable_id = $journee->id;

        // Assigner l'agence
        $this->agence_id = $agenceId;

        // Assigner la date selon le modèle
        $dateColumn = $this->getDateColumnName();
        $this->$dateColumn = $journee->date_cloture;
    }

    /**
     * Vérifie que la journée associée à l'enregistrement est bien ouverte.
     * (Utilisé principalement en création, mais peut servir ailleurs)
     */
    protected function ensureJourneeEstOuverte(): void
    {
        if (!$this->journeeComptable) {
            throw new Exception("La transaction n'est liée à aucune journée comptable.");
        }
        if ($this->journeeComptable->statut !== 'ouverte') {
            throw new Exception("La journée comptable du {$this->journeeComptable->date_cloture} n'est pas ouverte.");
        }
    }

    /**
     * Vérifie que la journée associée n'est pas clôturée.
     * Utilisé en update/delete.
     */
    protected function ensureJourneeNonCloturee(): void
    {
        // Recharger la relation pour être sûr d'avoir le dernier statut
        if ($this->relationLoaded('journeeComptable')) {
            $this->load('journeeComptable');
        }

        if (!$this->journeeComptable) {
            throw new Exception("La transaction n'est liée à aucune journée comptable.");
        }

        if ($this->journeeComptable->statut !== 'ouverte') {
            throw new Exception(
                "Action impossible : la journée comptable du {$this->journeeComptable->date_cloture} est soit vérouillée, soit clôturée. Veuillez contactez le Chef d'Agence."
            );
        }
    }

    /**
     * Retourne le nom de la colonne de date propre au modèle.
     * Chaque modèle doit implémenter cette méthode.
     *
     * @return string
     */
    abstract public function getDateColumnName(): string;
}