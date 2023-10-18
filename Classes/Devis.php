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

    /**
     * Renvoie le mois en toute lettre en francais (ex Octobre)
     * @return string
     */
    public function getfrenchCurrentMonthInLetter(): string
    { 
        return $this->frenchCurrentMonthInLetter;
    }

    /**
     * Renvoie simplement le chiffre du mois en cours
     * @return string
     */
    public function getFrenchCurrentMonthInNumber(): string
    {
        return $this->frenchCurrentMonthInNumber;
    }

    /**
     * Obtenir le prix HT
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

    /**
     * Avoir accès plus facilement aux erreurs  rencontrées dans les méthodes
     * @return array
     */
    public function getErrors():array
    {
        return $this->errors;
    }

    /**
     * Permet de mettre à jour les erreurs recontrées adns les méthodes
     * @param array $errors une entrée = texte de l'erreur (sans clé)
     * @return array
     */
    public function setErrors($errors =[]):array
    {
        foreach ($errors as $error){
            $this->errors[] = $error;
        }
        return $this->errors;
    }

    /**
     * Permet de récupérer le sigle de la monnaie définie
     * @return string
     */
    public function getCurrency():string
    {
        return $this->currency;
    }

    /**
     * Permet de mettre à jour le sigle de la monnaie si jamais nous devons traiter une autre monnaie
     * 
     * @param string $newCurrency Le sigle de la monnaie (ex: $)
     * @return string
     */
    public function setCurrency($newCurrency):string
    {
        $this->currency = $newCurrency;
        return $this->currency;
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
     * Si le club à droit a cette réduction nous l'appliquons sinon on ressort avec le même cout
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
    
    /**
     * Cette méthode renvoie le prix total du en fonction du nombre de sections mais aussi des infos associées
     * Le tarif Plein à payer pour les Sections + le nombre de tarif pleins 
     * Le tarif Reduit à payer pour les Sections + le nombre de tarif réduits 
     * 
     * @param string $federation
     * @param int $nbreDeSections
     * @param int $nombreAdherents
     * @return array
     */
    function calculPrixHTSection(string $federation, int $nbreDeSections, int $nombreAdherents):array
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
            ($i % $currentMonth  == 0) ? $isMultiple ++ : $isNotMultiple ++;
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

    /**
     * Retourne le prix TTC à 20% d'un prix HT entré en input
     * 
     * @param int $prixHT Le prix HT à convertir
     * @return float prix arrondi à 2 decimales
     */
    public function prixTTC($prixHT) :float
    {
        return round($prixHT + ($prixHT*self::TVA), 2); 
    }  

    /**
     * Renvoie le total HT de plusieurs tarif de prestations entrées dans le tableau
     * 
     * @param array chaque entrée représente la somme d'une prestation
     * @return int prix total HT 
     */
    public function calculPrixTotal($totalPrestations = []):int
    {
        $prixTotalHT = 0;
        foreach ($totalPrestations as $prixPrestation) {
            $prixTotalHT += $prixPrestation;
        }

        return $prixTotalHT;
    }






}