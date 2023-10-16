<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exercice Devis | Société Comiti</title>
</head>
<body>
    <h1>Interface de Test de la fonction de devis</h1>

    prix t.t.c à l'année de l'abonnement
 
    <!-- Injecter une classe formulaire - pour utiliser du php :) ! -->

    <form action="">

        <?php 
            require './Classes/Form.php';
            $formulaire = new Form();
        ?>
        <div>
            <?= $formulaire->input('Nombre d\'adhérents', 'nombreAdherents', 'number', 'nombreAdherents',['min' => 0]);?>
        </div>
        <div>
            <?= $formulaire->input('Nombre de sections désirées', 'nombreSections', 'number', 'nombreSections',['min' => 0]);?>
        </div>
        <div>
            <?= $formulaire->select('Fédération', 'federations', null,[
                    'B' => 'Basketball',
                    'G' => 'Gymnastique',
                    'N'=>'Natation',
                    'A' => 'Autres Fédérations',
                ]);?>
        </div>
        <div>
            <?= $formulaire->submit('Voir mon Devis');?>
        </div>




    </form>



</body>
</html>