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
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    </head>
    <body>
        <div class="container text-center bg-primary-subtle mt-2 py-5 rounded">
            <h1 class="mb-5">Votre devis en ligne pour le mois en cours <br> (<?= $devis->getfrenchCurrentMonthInLetter()?>)</h1>

            <form action="" method="get">
                <div class="row justify-content-center">

                    <div class="col-3">
                        <?= $formulaire->input('Nombre d\'adhérents', 'nombreAdherents', 'number', 'nombreAdherents',['min' => 0, 'class' => 'form-control']);?>
                        <?= $formulaire->input('Nombre de sections désirées', 'nombreSections', 'number', 'nombreSections',['min' => 1, 'class' => 'form-control']);?>
                        <?= $formulaire->select('Fédérations', 'federations', null, 'De quelle fédération dépendez-vous ?',[
                                'B' => 'Basketball',
                                'G' => 'Gymnastique',
                                'N'=>'Natation',
                                'A' => 'Autres Fédérations',
                            ]);?>

                        <?= $formulaire->submit('Voir mon Devis', ["class" => "btn btn-primary mt-3"]);?>
                    </div>
                    
                </div>
            </form>
            <?php if ((isset($_GET['nombreAdherents']) and ((!empty($_GET['nombreAdherents']) or $_GET['nombreAdherents'] == 0) 
                                                    and (!empty($_GET['nombreSections'])) 
                                                    and (!empty($_GET['federations'])) ) 
                                )):?>

                <?php 
                    $calculAdherentsHT = $devis->calculPrixHTAdherents($_GET['nombreAdherents']) ;
                    $prixAdherentsTTC = $devis->prixTTC($calculAdherentsHT);
                    $prixHTAvecReduction = $devis->pourcentagesDeReduction($_GET['federations'] ,$calculAdherentsHT);
                    $calculPrixHTSection = $devis->calculPrixHTSection( $_GET['federations'], $_GET['nombreSections'], $_GET['nombreAdherents']); 
                    $prixSectionHT = $calculPrixHTSection['prixTotalSection'];
                    $tarifPleinSectionHT =  $calculPrixHTSection['tarifPleinSection'];
                    $nombretarifPleinSection = $calculPrixHTSection['nombretarifPleinSection'];
                    $tarifReduitSectionHT =  $calculPrixHTSection['tarifReduitSection'];
                    $nombretarifReduitSection = $calculPrixHTSection['nombretarifReduitSection'];
                    $prixTotalHT = $devis->calculPrixTotal([$prixHTAvecReduction, $prixSectionHT]);
                    $prixTTC =  $devis->prixTTC($prixTotalHT) ;
                    $devis->setCurrency('€');
                ?>

                <div class="row justify-content-center mt-3">
                    <div class="card col-lg-4 rounded">
                        <div class="card-body">
                            <h5 class="card-title mb-3">Votre Devis</h5>
                            
                            <?php if($devis->getErrors() != null):?>
                                <div class="p-4 rounded bg-warning">
                                    <ul>
                                        <?php foreach ($devis->getErrors() as $error):?>
                                            <li><?= $error ?></li>                        
                                            <?php endforeach ?>
                                        </ul>
                                    </div>
                            <?php else:?>
                                <p class="text-start fst-italic ">*Prix HT</p>
                                <div class="row justify-content-between">
                                    <div class="col-8">
                                        Tarif Base Nombre d'adhérents: 
                                    </div>
                                    <div class="col-4">
                                        <?= $devis->prixParAnnee($calculAdherentsHT) . $devis->getCurrency() ?>
                                    </div>
                                    <div class="col-8">
                                        Tarif avec Réduction fédération:
                                    </div>
                                    <div class="col-4">
                                        <?= $devis->prixParAnnee($prixHTAvecReduction) . $devis->getCurrency() ?>
                                    </div>

                                    <hr class="mt-3">

                                    <div class="col-12 text-start">
                                        Sections plein tarif
                                    </div>
                                    <div class="col-8 text-start"> 
                                        - <?= $nombretarifPleinSection ?> section(s) à 5€
                                    </div>
                                    <div class="col-4">       
                                        <?= $devis->prixParAnnee($tarifPleinSectionHT)  . $devis->getCurrency() ?>
                                    </div>
                                    <div class="col-12 text-start">
                                        Sections tarif réduit
                                    </div>
                                    <div class="col-8 text-start"> 
                                        - <?= $nombretarifReduitSection ?> section(s) à 3€
                                    </div>
                                    <div class="col-4">       
                                        <?= $devis->prixParAnnee($tarifReduitSectionHT)  . $devis->getCurrency() ?>
                                    </div>
                                    <div class="col-8 text-start mt-3">
                                        Prix Total Section: 
                                    </div>
                                    <div class="col-4 mt-3">
                                        <?= $devis->prixParAnnee($prixSectionHT) . $devis->getCurrency() ?>
                                    </div>
                                    <hr class="mt-3">
                                    <div class="col-8">
                                        Tarif HT: 
                                    </div>
                                    <div class="col-4">
                                        <?= $devis->prixParAnnee($prixTotalHT) . $devis->getCurrency() ?>
                                    </div>
                                    <div class="col-8">
                                        Tarif TTC:  
                                    </div>
                                    <div class="col-4">
                                        <?= $devis->prixParAnnee($devis->prixTTC($prixTotalHT)) . $devis->getCurrency()?>
                                    </div>
                                </div>
                            <?php endif ?>
                        </div>
                    </div>
                </div>
            <?php endif ?>
        </div>
    </body>
</html>