<?php
require_once "includes/functions.php";
session_start();

if(isUserAdmin()){
    if(isset($_GET["action"])){
        if($_GET["action"]=='remove'){
            // Remove offer
            $stmt = getDb()->prepare('DELETE FROM `offre` WHERE `offre_id`= ?');
            $stmt->execute(array($_GET["offre_id"]));
        }
    }
    // Retrieve offers
    $offers = getDb()->query('SELECT * FROM `offre` WHERE `valide` = 1 ORDER BY `date_validation` DESC');
}

?>

<!doctype html>
<html>

    <?php 
    $pageTitle = "Offres (Admin)";
    require_once "includes/head.php";
    require_once "includes/confirm.php";
    ?>

    <body>
        <div class="container pushFooter">
            <?php require_once "includes/header.php"; ?>
            <?php if(isUserAdmin()){ ?>
            <div>
                <div>
                    <form class="navbar-form" role="search" method="post">
                        <div class="col-sm-3">
                        </div>
                        <div class="input-group col-sm-6">
                            <input type="text" class="form-control" placeholder="Rechercher une offre" name="search">
                            <div class="input-group-btn">
                                <button class="btn btn-default" type="submit"><i class="glyphicon glyphicon-search"></i></button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover table-bordered  text-center">
                        <thead class="bg-primary">
                            <tr>
                                <th class="text-center">#</th>
                                <th class="text-center">Type</th>
                                <th class="text-center">Titre</th>
                                <th class="text-center">Secteur</th>
                                <th class="text-center">Entreprise</th>
                                <th class="text-center">Date création</th>
                                <th class="text-center">Lieu</th>
                                <th class="text-center"><span class="glyphicon glyphicon-user"></span></th>
                                <th class="text-center">Dispo</th>
                                <th class="text-center col-md-1">Modifier</th>
                                <th class="text-center col-md-1" >Supprimer</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($offers as $offer) { ?>
                            <tr style="cursor:pointer">
                                <th class="text-center" scope="row" onclick="document.location='details_offer.php?id=<?= $offer['offre_id'] ?>'"><?= $offer['offre_id'] ?></th>
                                <td onclick="document.location='details_offer.php?id=<?= $offer['offre_id'] ?>'"><?= $offer['type'] ?></td>
                                <td onclick="document.location='details_offer.php?id=<?= $offer['offre_id'] ?>'"><?= $offer['titre'] ?></td>
                                <td onclick="document.location='details_offer.php?id=<?= $offer['offre_id'] ?>'"><?= $offer['secteur'] ?></td>
                                <td onclick="document.location='details_offer.php?id=<?= $offer['offre_id'] ?>'"><?= $offer['entreprise'] ?></td>
                                <td onclick="document.location='details_offer.php?id=<?= $offer['offre_id'] ?>'"><?= timestampToDate($offer['date_creation'])?></td>
                                <td onclick="document.location='details_offer.php?id=<?= $offer['offre_id'] ?>'"><?= $offer['lieu'] ?></td>
                                <td onclick="document.location='details_offer.php?id=<?= $offer['offre_id'] ?>'"></td>
                                <?php if(isOfferAvailable($offer['offre_id'])){ ?>
                                        <td onclick="document.location='details_offer.php?id=<?= $offer['offre_id'] ?>'"><span class="glyphicon glyphicon-ok"></span></td>
                                <?php }else{ ?>
                                        <td>
                                            <a href="#" class="btn btn-xs btn-default btn-block"><span class="glyphicon glyphicon-flag" style="color:red"></span></a>
                                        </td>
                                    <?php } ?>
                                
                                <td>
                                    <a href="update_offer.php?offre_id=<?= $offer['offre_id'] ?>" class="btn btn-xs btn-warning btn-block" ><i class="glyphicon glyphicon-pencil"></i></a>
                                </td>
                                <td>
                                    <a href="#" data-href="admin_offers.php?offre_id=<?= $offer['offre_id'] ?>&action=remove" class="btn btn-xs btn-danger btn-block" data-toggle="modal" data-target="#confirm-alert"><i class="glyphicon glyphicon-remove"></i></a>
                                </td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php }else{ ?>
            <div class="alert alert-danger">
                <p><strong> Attention !</strong> Vous n'avez pas accès aux outils d'administration.</p>
            </div>
            <div>
                <center><a href="index.php">Revenir à l'accueil.</a></center>
            </div>
            <?php } ?>
        </div>

        <?php require_once "includes/footer.php";?>
        <?php require_once "includes/scripts.php"; ?>
    </body>

</html>