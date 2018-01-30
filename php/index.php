<?php
	require 'install.php';
	
	if ($_GET['user'] == 'deco')
		$_SESSION[login] = "";
	if ($_SESSION[login] != "")
		header('Location: montage.php');
?>
<html>
<head>
	<title>Camagru</title>
	<link rel="stylesheet" type="text/css" href="../css/index.css">
	<meta name="viewport" content="initial-scale=1.0,width=device-width">
</head>
<body>
<header>
	<h1 id="titre"><a id="lien_titre" href="index.php">Camagru</a></h1>
	<a class="lien_header" href="inscription.php">Inscription</a>
	<a class="lien_header" href="connexion.php">Connexion</a>
</header>
<div id="content">
	<p id="BSC">Bienvenue sur Camagru</p>
	<p id="by_cmarin">by cmarin</p>
	<?php
		$page = $_GET[page];
		if ($page == 0)
			$page = 1;
		$limite = 10;
		$debut = ($page - 1) * $limite;
		try
		{
			$bdd = new PDO("mysql:host=localhost;dbname=Camagru", "root", "root");
			array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION);
		}
		catch (exception $e)
		{
			die('Erreur : ' . $e->getMessage());
		}
		$sql = $bdd->prepare("SELECT * FROM pictures ORDER BY id DESC LIMIT 10 OFFSET $debut");
		$sql->execute();
		while ($pictures = $sql->fetch())
		{
			echo "<div id='div_img' ><h4 id='titre_photo'>".$pictures[title]." by ".$pictures[user]."</h4><img id='img' src='".$pictures[path]."'></div>";
		}
		$sql->closeCursor();
		echo $count;
	?></div>
	<?php
		if ($page > 1)
			echo "<a href='galerie.php?page=".($page - 1)."'>Page précédente</a>";
		if (($page * 10) < $count)
			echo "<a href='galerie.php?page=".($page + 1)."'>Page suivante</a>";
	?>
</div>
</body>
</html>