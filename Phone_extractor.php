<?php
session_start();
$url= !empty($_POST['url']) ? $_POST['url'] : NUll;

//Message d'erreur lorsque le champ n'est pas rempli
if($_POST['url'] == NULL){
    header("Location:index.php");
    $_SESSION['error'] = "Merci de renseigner une URL dans le champ correspondant.";
    exit;
}

// $url = 'https://www.lagrandemotte.fr/activites-loisirs/associations/annuaire-des-associations/';
$numbers = scrape_phone($url);
if($numbers == null){
    header("Location:index.php");
    $_SESSION['error'] = "Aucun numéro trouvé.";
    exit;
}

$fp=fopen("Tel.txt", "w+");
foreach($numbers[0] as $number){
    fwrite($fp, $number.PHP_EOL);
}
//Affichage de fichier txt
header('Content-Type: application/txt');
//nom du fichier txt
header('Content-Disposition: attachment; filename="Liste.txt');
//source du PDF original
readfile("Tel.txt");
fclose($fp);

function scrape_phone($url) {
    if ( !is_string($url) ) {
        return '';
    }
    //$result = @file_get_contents($url);
    // $result = @curl_get_contents($url);
    //Se faire passer pour un navigateur web : 
    $ckfile = tempnam("/tmp", "CURLCOOKIE");
    $useragent = 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US) AppleWebKit/533.2 (KHTML, like Gecko) Chrome/5.0.342.3 Safari/533.2';

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_COOKIEJAR, $ckfile);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, $useragent);

    $result = curl_exec($ch);

    curl_close($ch);
    
    if ($result === FALSE) {
        return '';
    }
    
    preg_match_all("#(?:(?:\+|00)33|0)\s*[1-9](?:[\s.-]*\d{2}){4}#", $result, $numbers);
    
//SUpprime les doublons en même temps    
return array_unique($numbers);
}


function curl_get_contents($url) {
//initialisation nouvelle session cURL
    $curl = curl_init($url);
//Options de transmission à définir
    curl_setopt($curl, CURLOPT_HEADER, FALSE);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
    // pour les connexions https, pas de vérifications SSL
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 20);
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, TRUE);
    curl_setopt($curl, CURLOPT_MAXREDIRS, 5);
    curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
    $content = curl_exec($curl);
    //$error = curl_error($ch);
    //$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($curl);
    return $content;
}
?>