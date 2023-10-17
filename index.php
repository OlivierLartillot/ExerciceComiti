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


        <?php if ((isset($_GET['nombreAdherents']) and ((!empty($_GET['nombreAdherents']) or $_GET['nombreAdherents'] == 0) 
                                                   and (!empty($_GET['nombreSections']) or $_GET['nombreSections'] == 0) 
                                                   and (!empty($_GET['federations'])) ) 
                              )):?>

            <?php 
                $calculAdherentsHT = $devis->calculPrixHTAdherents($_GET['nombreAdherents']) ;
                $prixAdherentsTTC = $devis->prixTTC($calculAdherentsHT);
                $prixHTAvecReduction = $devis->pourcentagesDeReduction($_GET['federations'] ,$calculAdherentsHT);
                $prixSectionHT = $devis->calculPrixHTSection( $_GET['federations'], $_GET['nombreSections'], $_GET['nombreAdherents']); 
                $prixTotalHT = $devis->calculPrixTotal([$prixHTAvecReduction, $prixSectionHT]);
                $prixTTC =  $devis->prixTTC($prixTotalHT) ;
            ?>

            <?php if($devis->getErrors() != null):?>
                <ul>
                    <?php foreach ($devis->getErrors() as $error):?>
                        <li><?= $error ?></li>                        
                    <?php endforeach ?>
                </ul>
            <?php else:?>
                <div>
                    <div>
                        Tarif Base Nombre d'adhérents: 
                            <?= $calculAdherentsHT ?>
                        </div>
                        <div> 
                            Réduction éventuelle due à la fédération:<?= $prixHTAvecReduction ?>e HT
                        </div>
                        <div>
                            Prix Section: <?= $prixSectionHT ?>e HT
                        </div>
                        <div>
                            Tarif HT: <?= $prixTotalHT ?>
                        </div>
                    <div>
                        Tarif TTC = <?= $devis->prixTTC($prixTotalHT) ?>
                    </div>
                </div>
            <?php endif ?>
        <?php endif ?>
</body>
</html>