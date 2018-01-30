<?php

require "install.php";

$login = $_POST[login];
$path = $_POST[path];

try
{
	$bdd = new PDO("mysql:host=localhost;dbname=Camagru", "root", "root");
	array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION);
}
catch (exception $e)
{
	die('Erreur : ' . $e->getMessage());
}
$sql = $bdd->prepare("SELECT likes FROM pictures WHERE path=?");
$sql->execute(array($path));
$ret = $sql->fetch();
$likes = $ret[likes];
$sql = $bdd->prepare("UPDATE pictures SET likes = ? WHERE path = ?");
if (stripos($ret[likes], $login) === FALSE)
	$sql->execute(array($likes.$login.";", $path));
else
{
	$likes = str_replace($login.";", "", $likes);
	$sql->execute(array($likes, $path));
}
$sql->closeCursor();
//POUR LINSTANT CA REMPLACE TOUTE LA COLONNE LIKE, A REPARER POUR QUE SA AJOUTE JUSTE LE LOGIN AU LIEU DE TOUT REMPLACER BISOUS XOXO MA BICHE COURAGE DEMAIN MOTIVE TOI DES LARRIVEE STP A CE RYTHME TOUTE LA JOURNEE ON FINI LE BAY TMTC SALOPE ALLER COURAGE GROS JE CROIS EN TOI !!!!!
?>