<?php

use Carbon\Carbon;

if (! function_exists('to_kinshasa')) {
    /**
     * Convertit une date UTC en Africa/Kinshasa pour l'affichage
     * 
     * @param string|Carbon|null $date
     * @param string $format
     * @return string|null
     */
    function to_kinshasa($date, $format = 'd/m/Y H:i')
    {
        if (!$date) {
            return null;
        }
        
        return Carbon::parse($date)
            ->setTimezone('Africa/Kinshasa')
            ->format($format);
    }
}

if (! function_exists('date_kinshasa')) {
    /**
     * Convertit une date UTC en Africa/Kinshasa (format date seulement)
     * 
     * @param string|Carbon|null $date
     * @param string $format
     * @return string|null
     */
    function date_kinshasa($date, $format = 'd/m/Y')
    {
        if (!$date) {
            return null;
        }
        
        return Carbon::parse($date)
            ->setTimezone('Africa/Kinshasa')
            ->format($format);
    }
}

if (! function_exists('datetime_kinshasa')) {
    /**
     * Alias de to_kinshasa pour plus de clarté
     */
    function datetime_kinshasa($date, $format = 'd/m/Y H:i')
    {
        return to_kinshasa($date, $format);
    }
}

if (! function_exists('date_fr_long')) {
    /**
     * Format long en français (ex: 15 janvier 2024)
     * 
     * @param string|Carbon|null $date
     * @return string|null
     */
    function date_fr_long($date)
    {
        if (!$date) {
            return null;
        }
        
        $mois = [
            'January' => 'janvier',
            'February' => 'février',
            'March' => 'mars',
            'April' => 'avril',
            'May' => 'mai',
            'June' => 'juin',
            'July' => 'juillet',
            'August' => 'août',
            'September' => 'septembre',
            'October' => 'octobre',
            'November' => 'novembre',
            'December' => 'décembre'
        ];
        
        $date = Carbon::parse($date)->setTimezone('Africa/Kinshasa');
        $moisFr = $mois[$date->format('F')];
        
        return $date->format('d') . ' ' . $moisFr . ' ' . $date->format('Y');
    }
}

if (! function_exists('date_fr_complet')) {
    /**
     * Format complet en français (ex: lundi 15 janvier 2024)
     */
    function date_fr_complet($date)
    {
        if (!$date) {
            return null;
        }
        
        $jours = [
            'Monday' => 'lundi',
            'Tuesday' => 'mardi',
            'Wednesday' => 'mercredi',
            'Thursday' => 'jeudi',
            'Friday' => 'vendredi',
            'Saturday' => 'samedi',
            'Sunday' => 'dimanche'
        ];
        
        $mois = [
            'January' => 'janvier',
            'February' => 'février',
            'March' => 'mars',
            'April' => 'avril',
            'May' => 'mai',
            'June' => 'juin',
            'July' => 'juillet',
            'August' => 'août',
            'September' => 'septembre',
            'October' => 'octobre',
            'November' => 'novembre',
            'December' => 'décembre'
        ];
        
        $date = Carbon::parse($date)->setTimezone('Africa/Kinshasa');
        $jourFr = $jours[$date->format('l')];
        $moisFr = $mois[$date->format('F')];
        
        return $jourFr . ' ' . $date->format('d') . ' ' . $moisFr . ' ' . $date->format('Y');
    }
}

if (! function_exists('heure_kinshasa')) {
    /**
     * Format heure seulement (ex: 14:30)
     */
    function heure_kinshasa($date, $format = 'H:i')
    {
        if (!$date) {
            return null;
        }
        
        return Carbon::parse($date)
            ->setTimezone('Africa/Kinshasa')
            ->format($format);
    }
}

if (! function_exists('difference_pour_humains')) {
    /**
     * Affiche la différence en français (ex: il y a 2 jours)
     */
    function difference_pour_humains($date, $reference = null)
    {
        if (!$date) {
            return null;
        }
        
        $date = Carbon::parse($date)->setTimezone('Africa/Kinshasa');
        $reference = $reference ? Carbon::parse($reference) : Carbon::now('Africa/Kinshasa');
        
        return $date->diffForHumans($reference, [
            'syntax' => Carbon::DIFF_RELATIVE_TO_NOW,
            'parts' => 1,
            'short' => false
        ]);
    }
}

if (! function_exists('est_aujourd_hui')) {
    /**
     * Vérifie si la date est aujourd'hui (fuseau Kinshasa)
     */
    function est_aujourd_hui($date)
    {
        if (!$date) {
            return false;
        }
        
        return Carbon::parse($date)
            ->setTimezone('Africa/Kinshasa')
            ->isToday();
    }
}

if (! function_exists('age_depuis')) {
    /**
     * Calcule l'âge ou le temps écoulé depuis une date
     */
    function age_depuis($date, $unite = 'ans')
    {
        if (!$date) {
            return null;
        }
        
        $date = Carbon::parse($date)->setTimezone('Africa/Kinshasa');
        $maintenant = Carbon::now('Africa/Kinshasa');
        
        switch ($unite) {
            case 'ans':
                return $date->diffInYears($maintenant);
            case 'mois':
                return $date->diffInMonths($maintenant);
            case 'jours':
                return $date->diffInDays($maintenant);
            case 'heures':
                return $date->diffInHours($maintenant);
            default:
                return $date->diffInYears($maintenant);
        }
    }
}