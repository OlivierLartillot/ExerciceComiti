# Documentation du projet

## Détail de l'avancée du projet

| cette documentation me servira de fil conducteur au projet et sera remise à jour régulièrement (idéalement au fur et à mesure de l'avancée)

- :heavy_check_mark: initialisation du projet via git et github
- :heavy_check_mark: création d'une branche "develop" (branche de développement)
- :heavy_check_mark: création d'une branche "autour-du-formulaire" : L'objectif est de définir avant de commencer la fonction, qu'elles sont les données intéressantes à entrer dans le formulaire pour obtenir un résultat. Cela peut permettre de réfléchir aux attentes de la fonction avant même de commencer a réfléchir à comment la réaliser ! De plus si ma réflexion est bonne, je connaitrai tous les champs (et leur type associé) du formulaire à réaliser.
- :heavy_check_mark: création d'une branche "calcul-ttc" et réalisation du corps de l'exercice
- :heavy_check_mark: test de l'exercice dans le même temps pour chaque fonctionnalité
- :heavy_check_mark: ajout d'un peu de beauté à cette vilaine page ! Utilisation de bootstrap
- :heavy_check_mark: Tests "manuels"

# L'exercice:

Calcul du prix :

- Le nombre d’adhérents du club
- Le nombre de sections désirées (la section est un découpage du club en plus petites entités pour
faciliter la gestion, séparer les responsables ou encore séparer les paiements)
- La fédération dont le club est membre

### 1. Le nombre d’adhérents du club 

- De 0 à 100 -> 10€/mois HT
- De 101 à 200 -> 0.10€/adhérent/mois HT
- De 201 à 500 -> 0.09€/adhérent/mois HT
- De 501 à 1000 -> 0.08€/adhérent/mois HT
- A partir de +1000 -> 70€ HT par tranche de 1000 adhérents (une tranche entamée est une tranche
comptée)
- Au-dessus de 10000 -> 1000€/mois HT

| **FORMULAIRE**: nombre d'adhérents, Integer, input

### 2. Le nombre de sections désirées
- 5€/section/mois HT
- une section est offerte si le club possède plus de 1000 adhérents
| **FORMULAIRE**: nombre de sections désirées, Integer, input 


### 3. La fédération dont le club est membre
- Fédération de Natation (“N”) -> 3 sections offertes
- Fédération de Gymnastique (“G”) -> 15% de réduction sur le cout des adhérents
- Fédération de Basketball (“B”) -> 30% de réduction sur le cout des sections
- Autre fédération -> aucun avantage


| **FORMULAIRE**: nombre de sections désirées, String, select  
