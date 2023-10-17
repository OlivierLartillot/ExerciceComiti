<?php
namespace Comiti;

/**
 * Class Form
 * Permet de générer un formulaire très simplement
 */
class Form {

    /**
     * @var array données récupérées par le formulaire (généralement les tableaux post ou get)
     */
    private array $data;
    
    public function __construct(array $data = [])
    {
        $this->data = $data;
    }


    /**
     * Retourne la valeur de l'index courant du formulaire
     * 
     * @param string L'index de la valeur du tableau que l'on veut récupérer
     * @return string
     */
    private function getValue(string $index) 
    {
        return isset($this->data[$index]) ? $this->data[$index] : null;
    }


    /**
     * Retourne un élément de formulaire input
     * 
     * @param string $labelName Défini le label de l'input
     * @param string $name Défini le nom de l'input
     * @param string $type Défini le type de l'input (ex:text), pas de type si null
     * @param int $id Défini l'id html de l'input, name si null
     * @param array $otherAttributes Possibilité d'ajouter d'autres attributs html plus spécifiques sous forme clé => valeur
     * @return string 
     */
    public function input(string $labelName, string $name, string $type=null, string $id=null, array $otherAttributes = [])
    {
        // je laisse l'id optionnel, s'il ,n'est pas défini, il prendra la valeur du nom
        $id == ($id == null) ? $name : $id ;
        $htmlLabel = '<p><label for="'. $id .'">'. $labelName .': </label><br>';
        $htmlType = 'type="'. $type .'"';
        $htmlInput = '<input '. $htmlType .' name="'. $name .'" id="'. $id.'" value="'. $this->getValue($name) .'"'  ;

        // code d'ajout d'autres attributs
        if (!empty($otherAttributes)) {
            foreach ($otherAttributes as $key=>$attribute) {
                $htmlInput .= ''.$key. ' = "' .$attribute .'" '; 
            }
        }
        // fin de la balise html de l'input
        $htmlInput .= ' ></p>';
        
        return $htmlLabel . ' ' .$htmlInput;
    }


    /**
     * Retourne un élément de formulaire select 
     * 
     * @param string $labelName Défini le label du select
     * @param string $name Défini le nom du select
     * @param string $id Défini l'id html du select, name si null
     * @param array $federations Options du select sous forme [value => texte, value => texte...]
     * @return string
     */
    public function select(string $labelName, string $name, string $id=null, array $federations = [])
    {
        // je laisse l'id optionnel, s'il ,n'est pas défini, il prendra la valeur du nom
        $id == ($id == null) ? $name : $id ;

        // Construction du select/options html
        $htmlLabel = '<p><label for="'. $id .'">'. $labelName .': </label><br>';
        $htmlOpenSelect = '<select name="'. $name .'" id="'. $id.'"'.' >';
        $htmlCloseSelect = '</select>';
        $htmlOptions =  '<option value="">De quelle fédération dépendez vous ?</option>';

        $select = $this->getValue($name);
        // si le tableau des fédérations n'est pas vide on récupère les options et on les ajoute à la liste
        if(!empty($federations)) {
            foreach ($federations as $key => $federation) {
                    $selected = ($select == $key)? 'selected' : '';
                    $htmlOptions .= '<option value="'. $key .'" '. $selected.' >'. $federation .'</option>';
            }
        }

        $htmlSelect = $htmlOpenSelect . $htmlOptions . $htmlCloseSelect;
        return $htmlLabel . ' ' .$htmlSelect;
    }


    /**
     * Retourne l'input submit du formulaire 
     * 
     * @param string $value Défini le nom du bouton
     * @return string
     */
    public function submit(string $buttonName=null, array $otherAttributes = [])
    {
        $button = '<button type="submit"';
        // code d'ajout d'autres attributs
        if (!empty($otherAttributes)) {
            foreach ($otherAttributes as $key=>$attribute) {
                $button .= ''.$key. ' = "' .$attribute .'" '; 
            }
        }
        $button .= '>'. $buttonName . '</button>';

        return $button; 
    }    

}