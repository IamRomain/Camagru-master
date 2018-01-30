<?php
require "database.php";

try
{
	$bdd = new PDO($DB_DSN, $DB_USER, $DB_PASSWORD);
    array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION);
	$sql = "CREATE DATABASE Camagru";
	$bdd->exec($sql);
	$sql = "CREATE TABLE Camagru.users
			(
		  		id int NOT NULL AUTO_INCREMENT PRIMARY KEY,
		  		login text NOT NULL,
		  		pass text NOT NULL,
		  		email text NOT NULL,
		  		verif text NOT NULL
			)";
	$bdd->exec($sql);
	$sql = "CREATE TABLE Camagru.pictures
			(
				id int NOT NULL AUTO_INCREMENT PRIMARY KEY,
				title text NOT NULL,
				user text NOT NULL,
				path text NOT NULL,
				likes text NOT NULL
			)";
	$bdd->exec($sql);
	$sql = "CREATE TABLE Camagru.comments
			(
				id int NOT NULL AUTO_INCREMENT PRIMARY KEY,
				comment text NOT NULL,
				user text NOT NULL,
				path text NOT NULL
			)";
	$bdd->exec($sql);
	echo "Tables created successfully\n";
}
catch(PDOException $e)
{
    die('Erreur : ' . $e->getMessage());
}
?>