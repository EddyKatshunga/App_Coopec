<?php

namespace App\Models\Traits;

trait AffectsCoffre
{
    // Détermine si le mouvement est une entrée ou une sortie
    // À surcharger dans les modèles si nécessaire
    public function isAddition(): bool
    {
        if (isset($this->type)) {
            return in_array($this->type, ['DEPOT', 'REVENU', 'REMBOURSEMENT']);
        }
        // Par défaut, on définit selon la classe (exemple simple)
        return in_array(class_basename($this), ['Revenu', 'CreditRemboursement']);
    }

    public function getAmountValue(): float
    {
        // Gestion du cas particulier 'Credit' qui utilise 'capital'
        return (float) ($this->capital ?? $this->montant);
    }
}