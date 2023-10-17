<!-- Injecter une classe formulaire - pour utiliser du php :) ! -->
<?php 
    require './Classes/Autoloader.php';
    use Comiti\Autoloader;
    use Comiti\Devis;
    use Comiti\Form;
    
    Autoloader::register();
    $formulaire = new Form($_GET);
    $devis = new Devis();
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
            <form action="" method="get">

                <div>
                    <?= $formulaire->input('Nombre d\'adhérents', 'nombreAdherents', 'number', 'nombreAdherents',['min' => 0]);?>
                </div>
                <div>
                    <?= $formulaire->input('Nombre de sections désirées', 'nombreSections', 'number', 'nombreSections',['min' => 0]);?>
                </div>
                <div>
                    <?= $formulaire->select('Fédérations', 'federations', null,[
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


            <?php 
            if ((isset($_GET) and ((!empty($_GET['nombreAdherents'])) and 
                                    (!empty($_GET['nombreSections'])) and
                                    (!empty($_GET['federations']))) 
                              or ($_GET['nombreAdherents'] == 0))):?>
                <div>
                    <div>
                        Tarif Base Nombre d'adhérents: <?= $calculAdherentsHT = $devis->calculPrixHTAdherents($_GET['nombreAdherents']) ?>e HT
                        soit <?= $devis->prixTTC($calculAdherentsHT) ?>e TTC 
                    </div>
                    <div> 
                        Réduction éventuelle due à la fédération:<?= $prixHTAvecReduction = $devis->pourcentagesDeReduction($_GET['federations'] ,$calculAdherentsHT) ?>e HT
                    </div>
                    <div>
                        Prix Section: <?= $prixSectionHT = $devis->calculPrixHTSection( $_GET['federations'], $_GET['nombreSections'], $_GET['nombreAdherents'])?>e HT
                    </div>
                    <div>
                        Tarif HT: <?= $prixTotalHT = $devis->calculPrixTotal([$prixHTAvecReduction, $prixSectionHT])?>
                    </div>
                    <div>
                        Tarif TTC = <?= $devis->prixTTC($prixTotalHT) ?>
                    </div>
                </div>
            <?php endif ?>
    </body>
</html>