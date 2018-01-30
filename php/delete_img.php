<?php
require "install.php";

$img = $_POST[img];
echo $img;

try
{
	$bdd = new PDO("mysql:host=localhost;dbname=Camagru", "root", "root");
	array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION);
}
catch(exception $e)
{
	die('Erreur : '.$e->getMessage());
}
$sql = $bdd->prepare("DELETE FROM pictures WHERE path=?");
$sql->execute(array($img));
$sql->closeCursor();
unlink($img);
?>