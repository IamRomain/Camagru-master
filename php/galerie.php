<?php
	require 'install.php';

	if ($_SESSION[login] == "")
		header("Location: index.php");
	$login = $_SESSION[login];
	$page = $_GET[page];
	$limite = 10;
	try
	{
		$bdd = new PDO("mysql:host=localhost;dbname=Camagru", "root", "root");
		array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION);
	}
	catch (exception $e)
	{
		die('Erreur : ' . $e->getMessage());
	}
	$sql = $bdd->prepare("SELECT count(*) FROM pictures");
	$sql->execute();
	while ($ret = $sql->fetch())
		$count = $ret['count(*)'];
	$nbpage = ceil($count/10);
	if (isset($_GET[page]))
	{
		$page = intval($_GET[page]);
		if ($page > $nbpage)
			$page = $nbpage;
	}
	else
		$page = 1;
?>
<html>
<head>
	<title>Camagru</title>
	<link rel="stylesheet" type="text/css" href="../css/galerie.css">
</head>
<body>
	<header>
		<h1 id="titre"><a id="lien_titre" href="index.php">Camagru</a></h1>
		<a id="Deconnexion" href="index.php?user=deco"><img src="../img/Deconnexion.png"></a>
		<h2 id="titre_2"><a id="lien_titre_2" href="montage.php">Montage</a></h2>
		<h3 id="titre_login"><?php echo $login ?></h3>
	</header>
	<div id="content">
		<?php
			try
			{
				$bdd2 = new PDO("mysql:host=localhost;dbname=Camagru", "root", "root");
				array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION);
			}
			catch (exception $e)
			{
				die('Erreur : ' . $e->getMessage());
			}
			$page = $_GET[page];
			if ($page == 0)
				$page = 1;
			$limite = 10;
			$debut = ($page - 1) * $limite;
			$sql = $bdd->prepare("SELECT * FROM pictures ORDER BY id DESC LIMIT 10 OFFSET $debut");
			$sql->execute();
			while ($pictures = $sql->fetch())
			{
				echo "<div id='div_img' ><h4 id='titre_photo'>".$pictures[title]." by ".$pictures[user]."</h4><img id='img' src='".$pictures[path]."'><button id='like_button' name='".$pictures[path]."' onclick='like_function(this)'>Like or Unlike</button>";
				$sql2 = $bdd2->prepare("SELECT likes FROM pictures WHERE path=?");
				$sql2->execute(array($pictures[path]));
				$ret2 = $sql2->fetch();
				$likes = $ret2[likes];
				try
				{
					$bdd3 = new PDO("mysql:host=localhost;dbname=Camagru", "root", "root");
					array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION);
				}
				catch (exception $e)
				{
					die('Erreur : ' . $e->getMessage());
				}
				if ($likes != "")
				{
					$nb_like = count(explode(";", $likes)) - 1;
					$people_like = str_replace(";", "<br>", $likes);
					echo "<p id='nb_like'>".$nb_like." like for this picture <3</p>";
				}
				echo "<div id='div_input_com'><input type='text' id='input_for_".$pictures[path]."' name='commentaire' maxlength='25' placeholder='Votre mot'></div>
					<button id='com_button' name='".$pictures[path]."' onclick='com_function(this)'>Poster un mot</button>";
				$sql3 = $bdd3->prepare("SELECT * FROM comments WHERE path=?");
				$sql3->execute(array($pictures[path]));
				while ($com = $sql3->fetch())
				{	
					echo "<p id='mot'>".$com[user]." : ".$com[comment]."</p>";
				}
				echo "</div>";
			}
			$sql->closeCursor();
		?></div>
		<?php
			if ($page > 1)
				echo "<a id='lien_precedent' href='galerie.php?page=".($page - 1)."'>Page précédente</a>";
			if (($page * 10) < $count)
				echo "<a id='lien_suivant' href='galerie.php?page=".($page + 1)."'>Page suivante</a>";
		?>
		<br>
		<br>
		<br>
		<br>
	</div>
	<script type="text/javascript">
		function verif_titre(vtitre)
		{
			var index = 0;
			if (vtitre == "")
				return (vtitre);
			if (vtitre.length > 20)
			{
				vtitre = "";
				return(vtitre);
			}
			while (vtitre[index])
			{
				if (vtitre[index] >= '0' && vtitre[index] <= '9')
					index++;
				else if (vtitre[index] >= 'a' && vtitre[index] <= 'z')
					index++;
				else if (vtitre[index] >= 'A' && vtitre[index] <= 'Z')
					index++;
				else
				{
					vtitre = "";
					return(vtitre);
				}
			}
			return(vtitre);
		}
		function com_function(bouton_com)
		{
			var path = bouton_com.name;
			var comment = document.getElementById("input_for_" + path).value;
			comment = verif_titre(comment);
			if (comment != "")
			{
				var ajax = new XMLHttpRequest();
				ajax.open("POST", "commentaire.php", true);
				ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
				ajax.send("path=" + path + "&comment=" + comment);
				ajax.onreadystatechange = function ()
				{
	    			if (ajax.readyState == 4 && ajax.status == 200)
	    			{
	    			    // console.log(ajax.responseText);
						location.reload();
			        }
				}
				location.reload();
			}
			else
			{
				alert("Votre commentaire doit être un mot unique sans caractères spéciaux");
				location.reload();
			}
		}
		function like_function(bouton)
		{
			var ajax = new XMLHttpRequest();
			var appel = document.getElementById("titre_login");
			var login = appel.innerText || appel.textContent;
			var path = bouton.name;
			ajax.open("POST", "like.php", true);
			ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
			ajax.send("login=" + login + "&path=" + path);
			ajax.onreadystatechange = function ()
			{
    			if (ajax.readyState == 4 && ajax.status == 200)
    			{
    			    console.log(ajax.responseText);
					location.reload();
		        }
			}
			location.reload();
		}
	</script>
</body>
</html>