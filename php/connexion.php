<?php
	require 'install.php';

	if ($_SESSION[login] != "")
		header("Location: montage.php");
	if ($_GET[inscription] == "new")
		echo "<p id='okmess'>Votre compte a bien été crée, vérfiez votre boite mail.</p><style>#okmess{ background-color: #4aad52; color: white; }</style>";
	if ($_GET[newmp] == "change")
		echo "<p id='okmess'>Votre mot de passe a bien été mis a jour</p><style>#okmess{ background-color: #4aad52; color: white; }</style>";
	if ($_POST[login] != NULL && $_POST[pass] != NULL)
	{
		$login = htmlentities($_POST[login]);
		$pass = $_POST[pass];
		$hpass = hash(whirlpool, $pass);
		try
		{
    		$bdd = new PDO("mysql:host=localhost;dbname=Camagru", "root", "root");
    		array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION);
    	}
    	catch (Exception $e)
    	{
    		die('Erreur : ' . $e->getMessage());
    	}
		$sql = $bdd->prepare("SELECT * FROM users WHERE login = ? and pass = ?");
		$sql->execute(array($login, $hpass));
		$ret = $sql->fetch();
		if ($ret)
		{
			if ($ret[verif] == "OUI")
			{
				$_SESSION[login] = $ret[login];
				header("Location: montage.php");
			}
			else
			{	
				echo "<p id='errmess'>Compte non verifié</p><style>#errmess{ background-color: #cd5d67; color: white; }</style>";
			}
		}
		else
		{
			echo "<p id='errmess'>Entrées invalides</p><style>#errmess{ background-color: #cd5d67; color: white; }</style>";
		}
		$sql->closeCursor();
	}
	if ($_POST[email] != "")
	{
		try
		{
    		$bdd = new PDO("mysql:host=localhost;dbname=Camagru", "root", "root");
    		array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION);
    	}
    	catch (Exception $e)
    	{
    		die('Erreur : ' . $e->getMessage());
    	}
    	$sql = $bdd->prepare("SELECT * FROM users WHERE email = ?");
    	$sql->execute(array($_POST[email]));
    	$ret = $sql->fetch();
    	if ($ret)
    	{
    		$cle = hash(md5, $ret[email]).hash(md5, $ret[login])."JhKZ";
    		$getuser = str_rot13("user");
    		$user = str_rot13($ret[login]);
			$lien = "http://".$_SERVER[HTTP_HOST].$_SERVER[SCRIPT_NAME]."?$getuser=$user&cle=$cle";
			$lien = str_replace("connexion", "newmp", $lien);
    		$message = "Pour créer un nouveau mot de passe veuillez cliquer sur ce lien : ".$lien." (Si vous n'avez pas demandé a changer de mot de passe ne prenez pas compte de ce mail.)";
    		$message = wordwrap($message, 70, "\r\n");
    		mail($ret[email], "Changement de mot de passe sur Camagru", $message);
			echo "<p id='okmess'>Votre demande de changement de mot de passe a bien été prise en compte, vérfiez votre boite mail.</p><style>#okmess{ background-color: #4aad52; color: white; }</style>";
    	}
		else
		{
			echo "<p id='errmess'>Entrées invalides</p><style>#errmess{ background-color: #cd5d67; color: white; }</style>";
		}
		$sql->closeCursor();
	}
?>
<html>
<head>
	<title>Camagru</title>
	<link rel="stylesheet" type="text/css" href="../css/connexion.css">
	<meta name="viewport" content="initial-scale=1.0,width=device-width">
</head>
<body>
<header>
	<h1 id="titre"><a id="lien_titre" href="index.php">Camagru</a></h1>
	<h2 id="titre_2">Connexion</h2>
</header>
<div id="content">
	<form method="post" action="connexion.php" id="formulaire">
		<div>
			<label for="input_log" id="label_log">Login</label>
			<input type="text" id="input_log" name="login">
		</div>
		<div>
			<label for="input_mp" id="label_mp">Mot de passe</label>
			<input type="password" id="input_mp" name="pass">
		</div>
		<div id="bouton">
			<input type="submit" value="OK">
		</div>
	</form>
	<div id="div_lien">
		<a id="lien_bas" href="inscription.php">Pas encore inscrit ? Inscrivez-vous ici !</a>
		<br>
		<br>
		<?php
		if ($_GET[form] != "newmp")
		{
			echo "<a id='lien_bas' href='connexion.php?form=newmp'>Mot de passe oublié ?</a>";
		}
		else
		{
			echo "<form method='post' action='connexion.php?form=newmp' id='formulaire_2'>
				<div>
					<label for='input_mpo' id='label_mpo'>Email</label>
					<input type='text' id='input_mpo' name='email'>
				</div>
				<div id='bouton'>
					<input type='submit' value='OK'>
				</div>
			</form>";
		}
		?>
	</div>
</div>
</body>
</html>