<!-- Injecter une classe formulaire - pour utiliser du php :) ! -->
<?php 
    require './Classes/Form.php';
    $formulaire = new Form();
?>

<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Exercice Devis | Société Comiti</title>
    </head>
    <body>
        <h1>Interface de Test de la fonction de devis</h1>
            <form action="">

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