<?php
	require 'install.php';

	if ($_GET[user] != "" && $_GET[verif] != "")
	{
		$login = $_GET[user];
		try
		{
    		$bdd = new PDO("mysql:host=localhost;dbname=Camagru", "root", "root");
    		array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION);
		}
		catch (exception $e)
		{
    		die('Erreur : ' . $e->getMessage());
		}
		$sql = $bdd->prepare("SELECT * FROM users WHERE login = ?");
		$sql->execute(array($login));
		$ret = $sql->fetch();
		if ($ret)
		{
			$cle = hash(md5, $ret[login]).hash(md5, $ret[email]);
			if ($cle == $_GET[verif])
			{
				$sql = $bdd->prepare("UPDATE users SET verif = 'OUI' WHERE login = ?");
				$sql->execute(array($login));
				$_SESSION[login] = $login;
			}
		}
	}
	if ($_SESSION[login] == "")
		header("Location: index.php");
	$login = $_SESSION[login];
?>
<html>
<head>
	<title>Camagru</title>
	<link rel="stylesheet" type="text/css" href="../css/montage.css">
</head>
<body>
	<header>
		<h1 id="titre"><a id="lien_titre" href="index.php">Camagru</a></h1>
		<a id="Deconnexion" href="index.php?user=deco"><img src="../img/Deconnexion.png"></a>
		<h2 id="titre_2"><a id="lien_titre_2" href="galerie.php">Galerie</a></h2>
		<h3 id="titre_login"><?php echo $login ?></h3>
	</header>
	<div id="content">
	<div id="midlane">
		<div id="div_menu_gauche">
			<div id="menu_gauche">
				<label>
					<input id="chapeau" type="radio" name="filtre" onclick="stick_func(this)">
					<div><img src="../img/chapeau.png"></div>
				</label>
				<label>
					<input id="lunettes" type="radio" name="filtre" onclick="stick_func(this)">
					<div><img src="../img/lunettes.png"></div>
				</label>
				<label>
					<input id="pokeball" type="radio" name="filtre" onclick="stick_func(this)">
					<div><img src="../img/pokeball.png"></div>
				</label>
				<label>
					<input id="razengan" type="radio" name="filtre" onclick="stick_func(this)">
					<div><img src="../img/razengan.png"></div>
				</label>
			</div>
			<form id="form_upload_file" method="post" action="upload.php" enctype="multipart/form-data">
				<input id="input_titre_2" type="text" name="titre_upload" placeholder="Titre de la photo" onchange="func_title(this)">
				<br><input id="input_upload_file" type="file" accept="image/png" name="upload_file" onchange="openFile(event)">
			</form>
		</div><div id="div_video">
			<div id="cam">
				<input type="text" name="Titre" placeholder="Inscrivez ici le titre de la photo" id="input_titre" onchange="func_title(this)">
				<video id="video"></video>
				<button id="snapbutton" onclick="snapfunction()">Snap</button>
			</div>
		</div><div id="div_menu_droit">
			<div id="menu_droit">
				<?php
					try
					{
						$bdd = new PDO("mysql:host=localhost;dbname=Camagru", "root", "root");
						array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION);
					}
					catch (exception $e)
					{
						die('Erreur : ' . $e->getMessage());
					}
					$sql = $bdd->prepare("SELECT path FROM pictures WHERE user=? ORDER BY id DESC");
					$sql->execute(array($login));
					while ($images = $sql->fetch())
					{
						echo "<label><input id='".$images[path]."' type='radio' name='filtre2' onclick='select_img(this)'><div><img src='".$images[path]."'></div></label>";
					}
					$sql->closeCursor();
				?>
			</div>
			<div id="div_del_button"><button id="delete_button" onclick="del_but_func()">Supprimer</button></div>
		</div>
		</div>
		<div id="div_canvas">
			<canvas id="canvas"></canvas>
			<img src="" id="photo"></div>
			<img id="output">
		</div>
	</div>
	<script>
	document.getElementById("video").style.width = (document.body.clientWidth / 2)+"px";
	document.getElementById("div_video").style.width = (document.body.clientWidth / 2)+"px";
	document.getElementById("video").removeAttribute("height");
	var sticker;
	(function() {
		var streaming = false,
			video		= document.querySelector('#video'),
			cover		= document.querySelector('#cover'),
			canvas		= document.querySelector('#canvas'),
			photo		= document.querySelector('#photo'),
			startbutton	= document.querySelector('#snapbutton'),
			width = (document.body.clientWidth) / 2,
			height = 0;

 			if (navigator.mozGetUserMedia)
 			{
 				navigator.mediaDevices.getUserMedia(
 				{
 					audio: false, video: true
 				}).then(function(stream)
 					{
 						video.src = window.URL.createObjectURL(stream);
 						video.onloadedmetadata = function(e)
 						{
 							video.play();
 						};
 					}
 				).catch(function(err)
 				{
 					// cleanContainer();
 					// cleanCanvas();
 					alert('Webcam indisponible.');
 				});
 				return ;
 			}
 			else
 			{
				navigator.getMedia = ( navigator.getUserMedia || navigator.webkitGetUserMedia || navigator.mozGetUserMedia || navigator.msGetUserMedia);
			}
		navigator.getMedia(
		{
			video: true,
			audio: false
		},
		function(stream) {
			if (navigator.mozGetUserMedia) {
				video.mozSrcObject = stream;
			}
			else {
				var vendorURL = window.URL || window.webkitURL;
				video.src = vendorURL.createObjectURL(stream);
			}
			video.play();
		},
		function(err) {
			console.log("An error occured : " + err);
		}
		);
		video.addEventListener('canplay', function(ev){
			if (!streaming)	{
				height = video.videoHeight / (video.videoWidth/width);
				video.setAttribute('width', width);
				video.setAttribute('height', height);
				canvas.setAttribute('width', width);
				canvas.setAttribute('height', height);
				streaming = true;
			}
		}, false);
		function takepicture() {
			canvas.width = width;
			canvas.height = height;
			canvas.getContext('2d').drawImage(video, 0, 0, width, height);
			var data = canvas.toDataURL('image/png');
			var tof = photo.setAttribute('src', data);
			var ajax = new XMLHttpRequest();
			titre_photo = verif_titre(titre_photo);
			if (titre_photo != "" && sticker)
			{
				ajax.open("POST", "upload.php", true);
				ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
				ajax.send("photo=" + data + "&sticker=" + sticker + "&titre=" + titre_photo);
    			ajax.onreadystatechange = function ()
    			{
        			if (ajax.readyState == 4 && ajax.status == 200)
        			{
        			    // console.log(ajax.responseText);
						location.reload();
			        }
    			}
				document.getElementById("input_titre").value = "";
				titre_photo = "";
			}
			else
			{
				alert("N'oubliez pas de selectionner un sticker, de donnez un titre a votre photo, celui ci ne doit pas depasser les 20 caractères et ne pas utiliser de caractères spéciaux. Merci :)");
				location.reload();
			}
		}
		
		snapbutton.addEventListener('click', function(ev){
			takepicture();
			ev.preventDefault();
		}, false);
		document.getElementById("video").style.width = (document.body.clientWidth / 2)+"px";
		document.getElementById("div_video").style.width = (document.body.clientWidth / 2)+"px";
		document.getElementById("video").removeAttribute("height");
	})();
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
	function rs()
	{	
		document.getElementById("video").style.width = (document.body.clientWidth / 2)+"px";
		document.getElementById("div_video").style.width = (document.body.clientWidth / 2)+"px";
		document.getElementById("video").removeAttribute("height");
	}
	window.onresize = rs;
	function snapfunction()
	{
		var canvas = document.getElementById('canvas');
		var dataURL = canvas.toDataURL();
	}
	var titre_photo = "";
	function func_title(form_element)
	{
		titre_photo = form_element.value;
	}
	function stick_func(form_element)
	{
		sticker = form_element.id;
	}
	var del_img = "";
	function select_img(form_element)
	{
		del_img = form_element.id;
	}
	function del_but_func()
	{
		if (del_img != "")
		{
			var ajax = new XMLHttpRequest();
			ajax.open("POST", "delete_img.php", true);
			ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
			ajax.send("img=" + del_img);
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
			alert("Selectionnez une image a supprimer avant de cliquer sur le bouton")
	}
	var openFile = function(event) {
		var input = event.target;
		var reader = new FileReader();
		reader.onload = function()
		{
			var dataURL = reader.result;
			var output = document.getElementById("output");
			output.src = dataURL;
			var ajax = new XMLHttpRequest();
			titre_photo = verif_titre(titre_photo);
			if (titre_photo != "" && sticker)
			{
				ajax.open("POST", "upload.php", true);
				ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
				ajax.send("photo=" + dataURL + "&sticker=" + sticker + "&titre=" + titre_photo);
	    		ajax.onreadystatechange = function ()
	    		{
	        		if (ajax.readyState == 4 && ajax.status == 200)
	        		{
	    	  		    // console.log(ajax.responseText);
						location.reload();
			        }
	    		}
				document.getElementById("input_titre_2").value = "";
				titre_photo = "";
				location.reload();
			}
			else
			{
				alert("N'oubliez pas de selectionner un sticker, de donnez un titre a votre photo, celui ci ne doit pas depasser les 20 caractères et ne pas utiliser de caractères spéciaux. Merci :)");
				location.reload();
			}
		};
		reader.readAsDataURL(input.files[0]);
	};
	</script>
</body>
</html>