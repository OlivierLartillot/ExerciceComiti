<?php 
    require './Classes/Autoloader.php';
    require_once './data/federations.php';
    use Comiti\Autoloader;
    use Comiti\Devis;
    use Comiti\Form;
    use Comiti\Errors\ErrorsFormDevis;
    
    Autoloader::register();
    $formulaire = new Form($_GET);
    $devis = new Devis();
    // gestion des erreurs et validation
    $errorsFormDevisObject = new ErrorsFormDevis();

    // si on soumet le formulaire
    if (isset($_GET['submit'])){

        // on s'assure que personne n'essaie de modifier l url
        if ((!isset($_GET['nombreAdherents'])) or
            (!isset($_GET['nombreSections'])) or
            (!isset($_GET['federations'])) 
            ) {
            $errorsFormDevisObject->setErrors('SVP, veuillez utiliser le formulaire sans modifier l\'url !');

        } else {
            // on regarde que le submit soit bien a true
            $errorsFormDevisObject->checkSubmit($_GET['submit']);
            // on regarde les erreurs eventuelles  
            $checknombreAdherentsErrors = $errorsFormDevisObject->checkInputInteger($_GET['nombreAdherents'], 'nombreAdherents', 'nombre d\'adhérents');           
            $checknombreSectionsErrors = $errorsFormDevisObject->checkInputInteger($_GET['nombreSections'], 'nombreSections', 'nombre de sections', 'excludeZero');  
            $checkFederationsErrors = $errorsFormDevisObject->checkInputSelect($_GET['federations'],$federationsChoices, 'federations', 'Fédérations');
            //si il y a des erreurs, nous n'aurons accès à aucune variable $_GET mais au tableau d'erreur !!!
        }
    }

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
                        <?= $formulaire->select('Fédérations', 'federations', null, 'De quelle fédération dépendez-vous ?',$federationsChoices);?>

                        <?= $formulaire->submit('submit', 'Voir mon Devis', ["class" => "btn btn-primary mt-3"]);?>
                    </div>
                    
                </div>
            </form>
            
            <!-- On affiche uniquement si on a cliqué sur submit -->
            <?php if (isset($_GET['submit'])):?>
                <!-- Si il y a des erreurs dans le formulaire on les affiches -->
                <?php if (!empty($errorsFormDevisObject->getErrors())):?> 
                    <div class="row justify-content-center">
                        <div class="bg-danger-subtle col-8 text-center p-4 rounded shadow border border-black">
                            <h3 class="mb-3">Votre demande contient des erreurs :</h3>
                            <?php foreach ($errorsFormDevisObject->getErrors() as $error):?>
                                <span class="fw-bold">- <?= $error ?></span>  <br>
                            <?php endforeach ?>
                        </div>
                    </div>
                <!-- Si il y n'a pas d'erreur on récupère et affiche les résultats -->
                <?php else:
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
                                
                                <p class="text-start fst-italic ">*Prix HT</p>
                                <div class="row justify-content-between">

                                    <!-- zone adhérents -->
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

                                    <!-- zone sections -->
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

                                    <!-- zone sommes Tarifs -->
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
                            </div>
                        </div>
                    </div>
                <!-- END IF ELSE erreur/affichage -->
                <?php endif ?>
            <!-- END IF submit affiche les données -->
            <?php endif ?>
        </div>
    </body>
</html>