<?php

namespace App\Models\Traits;

use App\Models\CloturesComptable;
use Exception;

trait VerifieClotureComptable
{
    /**
     * Boot du trait : Laravel l'appelle automatiquement 
     * lors de l'initialisation du modèle.
     */
    protected static function bootVerifieClotureComptable()
    {
        static::creating(function ($model) {
            $model->empecherActionSiCloturee($model->getAgenceId(), $model->getDateOperation());
        });

        static::updating(function ($model) {
            $model->empecherActionSiCloturee($model->getAgenceId(), $model->getDateOperation());
        });

        static::deleting(function ($model) {
            $model->empecherActionSiCloturee($model->getAgenceId(), $model->getDateOperation());
        });
    }

    /**
     * Vérifie le statut de la journée comptable
     */
    protected function empecherActionSiCloturee($agenceId, $date)
    {
        $cloture = CloturesComptable::where('agence_id', $agenceId)
            ->where('date_cloture', $date)
            ->first();

        // Cas 1 : La journée n'a même pas été ouverte par le directeur
        if (!$cloture) {
            throw new Exception("Action refusée : La journée comptable du {$date} n'est pas encore ouverte pour cette agence.");
        }

        // Cas 2 : La journée existe mais elle est déjà clôturée
        // On suppose que votre colonne statut utilise les valeurs 'ouverte' et 'cloturee'
        if ($cloture->statut === 'cloturee') {
            throw new Exception("Action impossible : La journée comptable du {$date} est déjà clôturée. Aucune modification n'est permise.");
        }
    }

    /**
     * Accesseurs par défaut (à surcharger dans le modèle si nécessaire)
     */
    public function getAgenceId() {
        return $this->agence_id;
    }

    public function getDateOperation() {
        // Ordre de priorité des colonnes de date selon le type de transaction
        return $this->date_operation //pour revenu et depense
            ?? $this->date_transaction //pour depot et retrait epargne
            ?? $this->date_credit //pour pret
            ?? $this->date_paiement //pour remboursement d'un pret
            ?? now()->format('Y-m-d');
    }
}