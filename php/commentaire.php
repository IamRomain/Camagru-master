<?php

require "install.php";

$login = $_SESSION[login];
$path = $_POST[path];
$comment = htmlentities($_POST[comment]);
try
{
	$bdd = new PDO("mysql:host=localhost;dbname=Camagru", "root", "root");
  	array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION);
}
catch (exception $e)
{
  	die('Erreur : ' . $e->getMessage());
}
$sql = $bdd->prepare("INSERT INTO comments(comment, user, path) VALUES(?, ?, ?)");
$sql->execute(array($comment, $login, $path));
$sql->closeCursor();
$tab = explode("/", $path);
$i = count($tab);
$photo = $tab[$i - 1];
$tab2 = explode(".", $photo);
$photo = $tab2[0];
$login_photo = $tab[$i - 2];
try
{
	$bdd2 = new PDO("mysql:host=localhost;dbname=Camagru", "root", "root");
  	array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION);
}
catch (exception $e)
{
  	die('Erreur : ' . $e->getMessage());
}
$sql2 = $bdd2->prepare("SELECT email FROM users WHERE login = ?");
$sql2->execute(array($login_photo));
$ret = $sql2->fetch();
$email = $ret[email];
$sql2->closeCursor();
$message = "Votre photo ".$photo." a été commentée par ".$login." allez regarder quel mot il vous a dit ;)";
$message = wordwrap($message, 120, "\r\n");
mail($email, "Un nouveau mot sur votre photo !", $message);
?>