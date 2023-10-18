# ExerciceComiti

Petit Exercice de Devis avec conditions

- Utilisation d'un maximum de php pour l'exercice. Ainsi le formulaire aurait pu être fait entièrement en html, mais je trouvais ça beaucoup plus intéressant, compte tenu du temps que j'avais, de le faire en php.
- Utililsation de bootstrap pour donner rapidement un côté **joli** à cet exercice.


Améliorations futures identifiées:

- Sécuriser les données reçues du formulaire
- Utilisation de contrôlleur si le projet grandit pour "épurer" la vue
- Dans la méthode Devis->calculPrixHTSection():
    - renomer la méthode
    - séparer le code dans de nouvelles méthodes si nécesaire, par exemple:
    ```php
        private function countIsMultiple(){}
        private function countIsNotMultiple(){}
    ```
    - UX: renvoyer le nombre de sections offertes et le prix associés (ex: "3 sections à 5€ offertes") ainsi que le prix économisé pour le client (ex: -15€)