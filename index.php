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
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    </head>
    <body>
        <div class="container text-center bg-primary-subtle mt-2 py-5 rounded">
            <h1>Interface de Test de la fonction de devis</h1>

            <form action="" method="get">
                <div class="row justify-content-center">

                    <div class="col-3">
                        <?= $formulaire->input('Nombre d\'adhérents', 'nombreAdherents', 'number', 'nombreAdherents',['min' => 0, 'class' => 'form-control']);?>
                        <?= $formulaire->input('Nombre de sections désirées', 'nombreSections', 'number', 'nombreSections',['min' => 0, 'class' => 'form-control']);?>
                        <?= $formulaire->select('Fédérations', 'federations', null,[
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
                                            <?= $calculAdherentsHT . $devis->getCurrency() ?>
                                        </div>
                                        <div class="col-8">
                                            Tarif avec Réduction fédération:
                                        </div>
                                        <div class="col-4">
                                            <?= $prixHTAvecReduction . $devis->getCurrency() ?>
                                        </div>
                                        <div class="col-8">
                                            Prix Section: 
                                        </div>
                                        <div class="col-4">
                                            <?= $prixSectionHT . $devis->getCurrency() ?>
                                        </div>
                                        <div class="col-8">
                                            Tarif HT: 
                                        </div>
                                        <div class="col-4">
                                            <?= $prixTotalHT . $devis->getCurrency() ?>
                                        </div>

                                        <hr class="mt-3">
                                        
                                        <div class="col-8">
                                            Tarif TTC:  
                                        </div>
                                        <div class="col-4">
                                            <?= $devis->prixTTC($prixTotalHT) . $devis->getCurrency()?>
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