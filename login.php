<?php include_once 'inc/top.php';?>

<?php
$id = 1;    //kovakoodaus
$tunnus = "";
$salasana = "";

$viesti = "";

$tietokanta = new PDO('mysql:host=localhost;dbname=blogi;charset=utf8','root','');
//Oletuksena PDO ei näytä mahdollisia virheitä, joten asetetaan "virhemoodi" päälle.
$tietokanta->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); 

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    if($tietokanta != NULL) {
        try {
            $tunnus = filter_input(INPUT_POST, 'tunnus', FILTER_SANITIZE_STRING);
            $salasana = md5(filter_input(INPUT_POST, 'salasana', FILTER_SANITIZE_STRING));
            
            $sql ="SELECT * FROM kayttaja WHERE tunnus='$tunnus' AND salasana='$salasana'";
            
            $kysely = $tietokanta->query($sql);
            
            if($kysely->rowCount() === 1){
                $tietue = $kysely->fetch();
                $_SESSION['login'] = TRUE;
                $_SESSION['kayttaja_id'] = $tietue['id'];
                header('Location: index.php');
            } else {
                $viesti = "Väärä tunnus tai salasana!";
            }
            
        } catch (PDOException $pdoex) {
            print "Käyttäjän tietojen hakeminen epäonnistui." . $pdoex->getMessage();
        }
    }
}
?>

<div class="container">
    <div class="some-space">
        <form action="<?php print $_SERVER['PHP_SELF'];?>" method="post">
            <input type="hidden" name="id" value="<?php print($id);?>">

            <div class="form-group">
                <label for="tunnus">Tunnus</label>
                <input class="form-control" id="tunnus" name="tunnus" placeholder="Kirjoita tunnus" value="<?php print($tunnus);?>">
            </div>
            <div class="form-group">
                <label for="salasana">Salasana</label>
                <input class="form-control" id="salasana" type="password" name="salasana" placeholder="Kirjoita salasana" value="<?php print($salasana);?>">
            </div>

            <button type="submit" name="kirjaudu" class="btn btn-default">Kirjaudu</button>

        </form>
    </div>
</div>

<?php include_once 'inc/bottom.php';?>