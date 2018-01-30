<?php

require "install.php";

$photo = $_POST[photo];
$user = $_SESSION[login];
$name = htmlspecialchars($_POST[titre]);
$sticker = $_POST[sticker];
$i = 2;

list($type, $data) = explode(';', $photo);
list(, $data) = explode(',', $data);

$data = str_replace(' ', '+', $data);
$data = base64_decode($data);

if (!file_exists("../img_database/".$user))
	mkdir("../img_database/".$user);
if (file_exists("../img_database/".$user."/".$name.".png"))
{
	while (file_exists("../img_database/".$user."/".$name.$i.".png"))
		$i++;
	$name = $name.$i;
}
file_put_contents("../img_database/".$user."/".$name.".png", $data);

$source = imagecreatefrompng("../img/".$sticker.".png");
$largeur_source = imagesx($source);
$hauteur_source = imagesy($source);
imagealphablending($source, true);
imagesavealpha($source, true);

$destination = imagecreatefrompng("../img_database/".$user."/".$name.".png");
$largeur_destination = imagesx($destination);
$hauteur_destination = imagesy($destination);

$destination_x = ($largeur_destination - $largeur_source) / 2;
$destination_y = ($hauteur_destination - $hauteur_source) / 2;

imagecopy($destination, $source, $destination_x, $destination_y, 0, 0, $largeur_source, $hauteur_source);
imagepng($destination, "../img_database/".$user."/".$name.".png");
imagedestroy($destination);
imagedestroy($source);

try
{
	$bdd = new PDO("mysql:host=localhost;dbname=Camagru", "root", "root");
  	array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION);
}
catch (exception $e)
{
  	die('Erreur : ' . $e->getMessage());
}
$sql = $bdd->prepare("INSERT INTO pictures(title, user, path, likes) VALUES(?, ?, ?, ?)");
$sql->execute(array($name, $user, "../img_database/".$user."/".$name.".png", ""));
$sql->closeCursor();
?>