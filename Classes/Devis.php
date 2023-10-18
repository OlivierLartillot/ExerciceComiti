<?php 
namespace Comiti;

use IntlDateFormatter;

class Devis {

    private float $prixHT;
    private array $errors = [];
    const TVA = 20/100;
    private string $currency = '€';
    private string $frenchCurrentMonthInLetter;
    private string $frenchCurrentMonthInNumber;


    public function __construct()
    {
        $fmt = new IntlDateFormatter(
            'fr_FR',
            IntlDateFormatter::FULL,
            IntlDateFormatter::NONE,
            'Europe/Paris',
            IntlDateFormatter::GREGORIAN,
            'MMMM'
        );
       
        $this->frenchCurrentMonthInLetter = ucfirst($fmt->format(new \DateTime()));
        $frenchCurrentMonthInNumber = new \DateTime('now');
        $this->frenchCurrentMonthInNumber = $frenchCurrentMonthInNumber->format('m');
    }

    public function getfrenchCurrentMonthInLetter(): string
    { 
        return $this->frenchCurrentMonthInLetter;
    }

    public function getFrenchCurrentMonthInNumber(): string
    {
        return $this->frenchCurrentMonthInNumber;
    }

    /**
     * Obtenir le prix HT
     * 
     * @return float
     */
    public function getPrixHT()
    {
        return $this->prixHT;
    }

    /**
     * Modifier le prix HT
     * 
     * @param float $nouveauxPrix 
     * @return float nouveau prix HT
     */
    public function setPrixHT(float $nouveauxPrix)
    {
        $this->prixHT = $nouveauxPrix;
        return $this->prixHT;
    }
    public function getErrors()
    {
        return $this->errors;
    }
    public function setErrors($errors =[])
    {
        foreach ($errors as $error){
            $this->errors[] = $error;
        }
        return $this->errors;
    }

    public function getCurrency()
    {
        return $this->currency;
    }

    public function setCurrency($newCurrency)
    {
        $this->currency = $newCurrency;
        return $this;
    }
    
    /**
     * Prix HT à payer en fonction du nombre d'adhérents en prenant en compte les avantages dus a la fédération
     * 
     * @param int $nombre d'adhérents
     * @param string $federation: value HTML liée à la fédération ex:"N" pour natation
     * @return mixed prix HT a payer
     */
    function calculPrixHTAdherents($nombreAdherents) :mixed
    {
        if ($nombreAdherents < 0) {
            $this->setErrors(['Le nombre d\'adhérents doit être positif']);
        }

        if ( ($nombreAdherents >= 0) and ($nombreAdherents < 101) ) {
            $nouveauPrix = 10;
        } 
        else if ($nombreAdherents < 201) {
            $tarif = 0.10;
            $nouveauPrix = $nombreAdherents*$tarif;
        }
         else if ($nombreAdherents < 501) {
             $tarif = 0.09;
            $nouveauPrix = $nombreAdherents*$tarif;
         }
         else if ($nombreAdherents < 1001) {
             $tarif = 0.08;
             $nouveauPrix = $nombreAdherents*$tarif;    
            }
            else if ($nombreAdherents < 10001) {
                $tarif = 70;
                //1001 à 2000 == tranche de 1000 adhérents Mais on ne veut pas garder le 2 pour le substr .. d ou 2000 - 1 = 1999 on peut garder le 1*70
                $nombreAdherentsPourSubs = $nombreAdherents - 1;
                $nouveauPrix = (int)substr($nombreAdherentsPourSubs,0,1)*$tarif;
            }
            else if ($nombreAdherents > 10000) {
                $tarif = 1000;
                $nouveauPrix = $tarif;
         }
         
         return round($nouveauPrix, 2);    
        }
        
        /**
         * Application de la réduction pour les ayants droits
         * Nous insérons le cout adhérents précédemment calculé en HT
         * et si l'entité à droit a cette réduction nous l'appliquons sinon on ressort avec le même cout
         * 
         * @param string $federation value HTML liée à la fédération ex:"N" pour natation
     * @param int $coutAdherents
     * @return float prix HT a payer arrondi à 2 chiffres après la virgule
     */

    public function pourcentagesDeReduction($federation, $coutAdherents)
    {
        if ($federation == "G") {
            $prix = $coutAdherents - ($coutAdherents * 15 / 100);
        } else if ($federation == "B"){
            $prix = $coutAdherents - ($coutAdherents * 30 / 100);
        } else {
            $prix = $coutAdherents;
        }
        return round($prix, 2);
    }
    
    function calculPrixHTSection($federation, $nbreDeSections, $nombreAdherents):array
    {

        if ($nbreDeSections < 0) {
            $this->setErrors(['Le nombre de sections doit être positif']);
        }

        // voici le mois en cours ex: 10 (pour octobre)
        $currentMonth = $this->getFrenchCurrentMonthInNumber();

        // calcul le nombre de sections multiples et non multiples
        // isMultiple - isNotmultiple
        $isMultiple = 0; $isNotMultiple = 0;
        for ($i = 1; $i <= $nbreDeSections; $i++) {
            ($currentMonth % $i == 0) ? $isMultiple ++ : $isNotMultiple ++;
        }

       // *** Initialisation du nombre de sections offertes: ***
        $nbreDeSectionsOffertes = 0;
        // Si ton club est natation tu as 3 sections offertes
        $nbreDeSectionsOffertes = ($federation == "N") ? 3 : 0;
        // au dessus de 1000 une section est offerte
        $nbreDeSectionsOffertes = ($nombreAdherents>1000) ? $nbreDeSectionsOffertes+1 : $nbreDeSectionsOffertes;

        var_dump('nombre de sections choisies: ' . $nbreDeSections);
        var_dump('nombre de sections offertes: ' .$nbreDeSectionsOffertes);
        var_dump('Nombre de sections multiples à 3€: '. $isMultiple .' + nombre de sections non multiples à 5€: ' . $isNotMultiple);

        // offre en priorité les "notMultiple" à 5e
        // si le nbre total de sections offerte >= nombre de section non multiple, tu les deduis
        if ($nbreDeSectionsOffertes <= $isNotMultiple) {
            $isNotMultiple = $isNotMultiple-$nbreDeSectionsOffertes;
        } 
        // si y a plus de sections offertes que les notMultiple, il faut offrir les multiples
        else {
            // on déduit les multiples et comme nbreDeSectionsOffertes >, $isMultiple == 0
            $nbreDeSectionsOffertesRestantes = $nbreDeSectionsOffertes - $isNotMultiple;
            $isNotMultiple = 0;
            // on récupère le nbreDeSectionsOffertes restantes et si y a plus de sections offertes que de sections choisies, on remet à 0
            $isMultiple = $isMultiple - $nbreDeSectionsOffertesRestantes;
            $isMultiple = ($isMultiple<=0) ? 0 : $isMultiple;
        }

        var_dump('Apres deductions:');
        var_dump('Nombre de sections multiples à 3€: '. $isMultiple .' + nombre de sections non multiples à 5€: ' . $isNotMultiple);

        // calculons le tarif en fonction des sections restantes
        $tarifPleinSection = $isNotMultiple * 5;
        $tarifReduitSection = $isMultiple * 3; 

        $prixTotalSection = $tarifPleinSection + $tarifReduitSection; 
        
        return [
            'prixTotalSection' => $prixTotalSection, 
            'tarifPleinSection' => $tarifPleinSection, 
            'nombretarifPleinSection' => $isNotMultiple,
            'tarifReduitSection' => $tarifReduitSection,
            'nombretarifReduitSection' => $isMultiple,
            
        ];
    }

    public function prixTTC($prixHT) {
        return round($prixHT + ($prixHT*self::TVA), 2); 
    }  

    public function calculPrixTotal($totalPrestations = [])
    {
        $prixTotalHT = 0;
        foreach ($totalPrestations as $prixPrestation) {
            $prixTotalHT += $prixPrestation;
        }

        return $prixTotalHT;
    }






}