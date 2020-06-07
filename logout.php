<?php include_once 'inc/top.php';?>

<?php

// Muutetaan navigointi-palkiston näyttöehtoja ottamalla 'kayttaja_id' 
// pois asetetuista sessiomuuttujista (top.php)
unset($_SESSION['kayttaja_id']);
// Kun login on false, kirjoitusten ja kommenttien tuhoaminen ei ole mahdollista
// eikä käyttäjä voi kirjoittaa viestejä tai kommentteja
$_SESSION['login'] = FALSE;

// Ohjataan näkymä sivulle index.php
header('Location: index.php');

?>

<?php include_once 'inc/bottom.php';?>