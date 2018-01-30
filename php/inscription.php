<?php
	require 'install.php';


	if ($_GET[pass] == 'invalide')
	{
		echo "<p id='errmess'>Votre mot de passe doit contenir une minuscule, une majuscule, un nombre et doit avoir au minimum 8 caractères</p><style>#errmess{ background-color: #cd5d67; color: white; }</style>";
		$_POST[login] = NULL;
	}
	if ($_POST[login] != NULL && !preg_match("/^[A-Za-z0-9]+$/", $_POST[login]))
	{
		echo "<p id='errmess'>Login invalide</p><style>#errmess{ background-color: #cd5d67; color: white; }</style>";
		$_POST[login] = NULL;
	}
	if ($_POST[login] != NULL && $_POST[pass] != NULL && $_POST[email] != NULL)
	{
		$login = htmlentities($_POST[login]);
		$pass = $_POST[pass];
		$email = $_POST[email];
		$uppercase = preg_match('@[A-Z]@', $pass);
		$lowercase = preg_match('@[a-z]@', $pass);
		$number = preg_match('@[0-9]@', $pass);
		if (!$uppercase || !$lowercase || !$number || strlen($pass) < 8)
		{	
			$_POST[login] = NULL;
			header("Location: inscription.php?pass=invalide");
		}
		else
		{
			$hpass = hash(whirlpool, $pass);
			try
			{
				$bdd = new PDO("mysql:host=localhost;dbname=Camagru", "root", "root");
	    		array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION);
			}
			catch (exception $e)
			{
	    		die('Erreur : ' . $e->getMessage());
			}
			$sql = $bdd->prepare("INSERT INTO users(login, pass, email, verif) VALUES(?, ?, ?, 'NON')");
			$sql->execute(array($login, $hpass, $email));
			$sql->closeCursor();
			$cle = hash(md5, $login).hash(md5, $email);
			$lien = "http://".$_SERVER[HTTP_HOST].$_SERVER[SCRIPT_NAME]."?user=$login&verif=$cle";
			$lien = str_replace("inscription", "montage", $lien);
			$message = "Vous êtes desormais un nouveau Camagruser !\r\n\r\nA conditions de confirmer votre adresse email via ce lien : ".$lien;
			$message = wordwrap($message, 70, "\r\n");
			mail($email, "Bienvenue sur Camagru", $message);
			header("Location: connexion.php?inscription=new");
		}
	}
?>
<html>
<head>
	<title>Camagru</title>
	<link rel="stylesheet" type="text/css" href="../css/inscription.css">
	<meta name="viewport" content="initial-scale=1.0,width=device-width">
</head>
<body>
<header>
	<h1 id="titre"><a id="lien_titre" href="index.php">Camagru</a></h1>
	<h2 id="titre_2">Inscription</h2>
</header>
<div id="content">
	<form method="post" action="inscription.php" id="formulaire">
		<div>
			<label for="input_log" id="label_log">Login</label>
			<input type="text" id="input_log" name="login">
		</div>
		<div>
			<label for="input_mp" id="label_mp">Mot de passe</label>
			<input type="password" id="input_mp" name="pass">
		</div>
		<div>
			<label for="input_email" id="label_email">Adresse email</label>
			<input type="email" id="input_email" name="email">
		</div>
		<div id="bouton">
			<input type="submit" value="OK">
		</div>
	</form>
	<div id="div_lien">
		<a id="lien_bas" href="connexion.php">Déjà inscrit ? Connectez-vous ici !</a>
	</div>
</div>
</body>
</html>