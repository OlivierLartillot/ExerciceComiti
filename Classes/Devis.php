<?php 
namespace Comiti;

class Devis {

    private float $prixHT;
    private array $errors = [];
    const TVA = 20/100;
    private string $currency = '€';

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
    
    function calculPrixHTSection($federation, $nbreDeSections, $nombreAdherents)
    {

        if ($nbreDeSections < 0) {
            $this->setErrors(['Le nombre de sections doit être positif']);
        }

        $prixSection = 5;

        // Si ton club est natation tu as 3 sections offertes
        $nbreDeSections = ($federation == "N") ? $nbreDeSections-3 : $nbreDeSections;
        // au dessus de 1000 une section est offerte
        $nbreDeSections = ($nombreAdherents>1000) ? $nbreDeSections-1 : $nbreDeSections;

        $prixTotalSection = ($nbreDeSections > 0) ? $nbreDeSections*$prixSection : 0; 
        


        return $prixTotalSection;
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