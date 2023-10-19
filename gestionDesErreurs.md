# Liste des erreurs gérées

Pour gérer les erreurs, j'ai créé une classe dédiée \Classes\Errors\ErrorsFormDevis.php

## La démarche

On check dans la page uniquement **si le submit est envoyé** (présent)

- si tous les parametres dans l'url sont présents, sinon on demande de ne pas jouer avec l url ...
- si ils sont tous présents on va checker
    - si le submit == 'true' (string pas booleen !!!)
    - si le nombre d adhérents est conforme (de 0 à N)  
    - si le nombre de sections est conforme (de 1 à N) 
     - si la fédération est présente dans le tableau

## Exemple d'url non valides

- soumettre sans envoyer de données: http://localhost/exerciceComiti/?nombreAdherents=&nombreSections=&federations=nochoice&submit=true
- modifier l'url en conservant le submit=true : http://localhost/exerciceComiti/?nomderations=nochoice&submit=true
- ou encore modifier des parametres en mettant des caractères non attendus:
    - http://localhost/exerciceComiti/?nombreAdherents=<u>**-1**</u>&nombreSections=<u>**fgfhfhfhh**</u>&federations=<u>**\<b>B\</b>**</u>&submit=<u>**trueeeeeeee**</u>


### Nombre d'adhérents

Sont rejetés:
- côté client (formulaire)
    - min value a 0
    - type="number"
- côté serveur 
    - vide
    - une string
    - un chiffre < 0

### Nombre de sections

Sont rejetés:
- côté client (formulaire)
    - min value a 1
    - type="number"
- côté serveur 
    - vide
    - une string
    - un chiffre <= 0

### Choix fédération

Est rejeté:
- Tout caractère n'étant pas présent dans le tableau 
```php
    $federationsChoices = ['B', 'G', 'N', 'A'];
```

### Submit

Sont rejetés:
- Toute submit value != de la chaine de caractère "true"