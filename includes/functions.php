<?php

// Connect to the database. Returns a PDO object
function getDb() {
    // Local deployment
    $server = "localhost";
    $username = "adcog_user";
    $password = "secret";
    $db = "adcog";

    return new PDO("mysql:host=$server;dbname=$db;charset=utf8", "$username", "$password",array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
}

// Check if a user is connected
function isUserConnected() {
    return isset($_SESSION['login']);
}

// Check if a user is an administrator
function isUserAdmin() {
    if (isUserConnected()) {
        return ($_SESSION['role']=='administrateur');
    }
    else  {
        return false;
    }
}

// Check if the offer was posted by the user
function isMyOffer($offerId){
    if (isUserConnected()){
        $result = getDb()->query("SELECT * FROM `personne` WHERE `login` = '".$_SESSION['login']."'");
        $personne = $result->fetch();
        $stmt = getDb()->prepare('SELECT * FROM `creer` WHERE `offre_id`= ? AND `personne_id`= ?');
        $stmt->execute(array($offerId, $personne['personne_id']));
        if($stmt->rowCount() >=1) {
            return true;
        }
        else{
            return false;
        }
    }
    else {
        return false;
    }
}

// Redirect to a URL
function redirect($url) {
    header("Location: $url");
}

// Escape a value to prevent XSS attacks
function escape($value) {
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8', false);
}

// Transform a timestamp into a date 
function timestampToDate($t) {
    return date("d-m-Y",$t);
}

// Generate offer's code
function generateCode(){
    $chaine = 'azertyuiopqsdfghjklmwxcvbn123456789';
    $nb_lettres = strlen($chaine) - 1;
    do{
        $generation = '';
        for($i=0; $i < 15; $i++)
        {
            $pos = mt_rand(0, $nb_lettres);
            $car = $chaine[$pos];
            $generation .= $car;
        }
        $result = getDb()->query("SELECT * FROM `offre` WHERE `offre_code` = '".$generation."'");
    }while($result->rowCount() >=1);

    return $generation;
}

// Limit the number of characters 
function truncate($text)
{
    $max_char=500;
    // Text length is over the limit
    if (strlen($text)>$max_char){
        // Define maximum char number
        $text = substr($text, 0, $max_char);
        // Get position of the last space
        $position_space = strrpos($text, " ");
        $text = substr($text, 0, $max_char);
        // Add "..."
        $text = $text." ...";
    }
    // Return the text
    return $text;
}

function pagination($nbPages,$page){
    // Pagination
    if ($nbPages > 1) // We don't need pagination for 1 page
    {
        // Previous page
        if ( $page > 1){
            echo '<a href="?page='.($page-1).'">&laquo; Précédent - </a>';
        }
        // Page numbers
        for($a = 1; $a <= $nbPages; $a++){
            if ($a == $page) // No link for the current page
                echo ' ['.$a.'] - ';
            else
                echo '<a href="?page='.$a.'">'.$a.'</a> - ';
        }
        // Next page
        if ($page < $nbPages){
            echo '<a href="?page='.($page +1).'">Suivant &raquo;</a>';
        }
    }
}

// Check if an offer is available
function isOfferAvailable($id)
{
    $stmt = getDb()->prepare('SELECT `est_indispo_id` FROM `est_indispo` WHERE `offre_id`= ?');
    $stmt->execute(array($id));
    return($stmt->rowCount() == 0);
}