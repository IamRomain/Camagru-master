<?php
	if ($_GET[mp] == "fail")
	{
		echo "<p id='errmess'>Votre mot de passe doit contenir une minuscule, une majuscule, un nombre et doit avoir au minimum 8 caractères</p><style>#errmess{ background-color: #cd5d67; color: white; }</style>";
	}
	$getuser = str_rot13("user");
	if ($_SESSION[login] != "")
		header("Location: montage.php");
	if (!$_GET[str_rot13("user")])
		header("Location: montage.php");
	try
	{
		$bdd = new PDO("mysql:host=localhost;dbname=Camagru", "root", "root");
		array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION);
	}
	catch (Exception $e)
	{
		die('Erreur : ' . $e->getMessage());
	}
	$sql = $bdd->prepare("SELECT * FROM users WHERE login = ?");
	$sql->execute(array(str_rot13($_GET[str_rot13("user")])));
	$ret = $sql->fetch();
	if ($ret)
	{
		if ($_GET[cle] == hash(md5, $ret[email]).hash(md5, $ret[login])."JhKZ")
		{
			if ($_POST[newmp] != "")
			{
				$pass = $_POST[newmp];
				$uppercase = preg_match('@[A-Z]@', $pass);
				$lowercase = preg_match('@[a-z]@', $pass);
				$number = preg_match('@[0-9]@', $pass);
				if (!$uppercase || !$lowercase || !$number || strlen($pass) < 8)
				{	
					$getuser = str_rot13("user");
					$user = $_GET[$getuser];
					$cle = $_GET[cle];
					$lien = "http://".$_SERVER[HTTP_HOST].$_SERVER[SCRIPT_NAME]."?$getuser=$user&cle=$cle";
					header("Location: ".$lien."&mp=fail");
				}
				else
				{
					$login = str_rot13($_GET[str_rot13("user")]);
					$sql = $bdd->prepare("UPDATE users SET pass = ? WHERE login = '$login'");
					$sql->execute(array(hash(whirlpool, $_POST[newmp])));
					$sql->closeCursor();
					header("Location: connexion.php?newmp=change");
				}
			}
		}
		else
			header("Location: montage.php");
	}
	else
		header("Location: montage.php");
	$sql->closeCursor();
?>
<html>
<head>
	<title>Camagru</title>
	<link rel="stylesheet" type="text/css" href="../css/newmp.css">
	<meta name="viewport" content="initial-scale=1.0,width=device-width">
</head>
<body>
<header>
	<h1 id="titre"><a id="lien_titre" href="index.php">Camagru</a></h1>
	<h2 id="titre_2">Changement de mot de passe</h2>
</header>
<div id="content">
	<form method="post" action="newmp.php?<?php echo $getuser; ?>=<?php echo $_GET[$getuser]; ?>&cle=<?php echo $_GET[cle]; ?>" id="formulaire">
		<div>
			<label for="input_newmp" id="label_newmp">Nouveau mot de passe</label>
			<input type="password" id="input_newmp" name="newmp">
		</div>
		<div id="bouton">
			<input type="submit" value="OK">
		</div>
	</form>
</div>
</body>
</html>