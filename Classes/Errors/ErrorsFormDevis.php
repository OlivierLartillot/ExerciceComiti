<?php 
namespace Comiti\Errors;


class ErrorsFormDevis {

    private array $errors = [];

    public function getErrors(): array 
    {
        return $this->errors;
    }

    public function setErrors($error): array 
    {
        $this->errors[]= $error;
        return $this->errors;
    }


    /**
     * Retourne false si submit = true (si il n'y a pas d'erreur) ou insère l'erreur dans le tableau d'erreur de l'objet
     * 
     * @param $input Prend le $_GET['nomDuSubmit'] en paramètre pour comparer sa valeur qui doit être 'true' 
     */
    public function checkSubmit(string $input){
        if (!empty($input)) {
            // nettoyer le submit
            $input =filter_input(INPUT_GET,'submit', FILTER_SANITIZE_SPECIAL_CHARS, FILTER_FLAG_STRIP_HIGH);

            // on peut traiter la demande utilisateur
            if ($input == 'true') {
                return false;
            }
            return $this->setErrors('Il y a eu un problème avec le bouton "Voir mon Devis". Veillez à ne pas utiliser l\'Url mais seulement le formulaire. Si le problème persiste etc...');
        } 
            
        return $this->setErrors('Le submit ne doit pas être vide !');
    }

    /**
     * Retourne soit la valeur de l input, soit l erreur ajoutée dans le tableau des erreurs
     * 
     * @param string $input ex $_GET['maClef] 
     * @param string $keyName ex: 'maClef'
     * @param string $label Le label à utiliser dans les messages d'erreurs (personnalisation)
     * @param string $positive includeZero/excludeZero, défini si l'élément de formulaire accepte les chiffres positifs à partir de 1 ou de 0
     * 
     * @return mixed renvoie l'input ou ajoute l'erreur au tableau errors de l'objet
     */
    public function checkInputInteger(string $input, $keyName, $label, $positive ='includeZero'):mixed
    {
        $input = filter_input(INPUT_GET, $keyName, FILTER_SANITIZE_SPECIAL_CHARS, FILTER_FLAG_STRIP_HIGH);
        $validationNombre = filter_input(INPUT_GET, $keyName,FILTER_VALIDATE_INT);

        if ($input == null) {
            return $this->setErrors("Le {$label} ne doit pas être vide.");
        }
        else if ($validationNombre === false) {
            return $this->setErrors("Le {$label} doit être un entier.");
        }
        else if ($validationNombre !== false){
            if ($positive == "includeZero") { 
                if ($input >= 0) {return $input;} 
                else { return $this->setErrors("Le nombre pour '{$label}' doit être un entier positif supérieur ou égal à 0.");}
            } else if ($positive == "excludeZero") {
                if ($input > 0) {return $input;} 
                else { return $this->setErrors("Le nombre pour '{$label}' doit être un entier positif supérieur à 0.");}
            }
        } 
        
        return $this->setErrors("Un erreur inconnue est survenue.");
    }

    /**
     * Retourne soit la valeur de l input, soit l erreur ajoutée dans le tableau des erreurs
     * 
     * @param string $input ex le tableau des valeurs souhaitées 
     * @param array $arrayKeysToCheck Le tableau à passer pour la comparaison array_key_exists
     * @param string $keyName Le nom de la clé du tableau $_GET ou $_POST
     * @param string $label Le label à utiliser dans les messages d'erreurs (personnalisation)
     * 
     * @return mixed renvoie l'input ou ajoute l'erreur au tableau errors de l'objet
     */
    public function checkInputSelect(string $input, array $arrayKeysToCheck , $keyName, $label):mixed
    {
        $input = filter_input(INPUT_GET, $keyName, FILTER_SANITIZE_SPECIAL_CHARS, FILTER_FLAG_STRIP_HIGH);

        if ($input == null) {
            return $this->setErrors('La liste "' . $label . '" ne peut pas étre nulle');
        }
        else {
            if (!array_key_exists($input, $arrayKeysToCheck)) {
                return $this->setErrors('Veuillez choisir un élément de la liste "'  . $label . '"');
            }
            return $input;
        }
    }




}