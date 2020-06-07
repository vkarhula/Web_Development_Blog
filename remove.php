<?php include_once 'inc/top.php';?>

<div class="container">

    <div class="some-space">
        
    <?php
    $id = filter_input(INPUT_GET,'id',FILTER_SANITIZE_NUMBER_INT);

    try {

        // Avataan tietokantayhteys.
        $tietokanta = new PDO('mysql:host=localhost;dbname=blogi;charset=utf8','root','');
        //Oletuksena PDO ei näytä mahdollisia virheitä, joten asetetaan "virhemoodi" päälle.
        $tietokanta->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Poistetaan ensin kirjoitukseen liittyvät kommentit
        $sql1 = "DELETE FROM kommentti WHERE kirjoitus_id=:kirjoitus_id";
        
        $kysely1 = $tietokanta->prepare($sql1);
        
        $kysely1->bindValue(':kirjoitus_id', $id,PDO::PARAM_INT);
        
        if($kysely1->execute()){
            print "<p>Kommentit poistettu</p>";

            // Kommenttien poistamisen jälkeen poistetaan kirjoitus
            // Muodostetaan parametroitu sql-kysely tiedon poistamista varten.
            $kysely2 = $tietokanta->prepare("DELETE FROM kirjoitus WHERE id=:id");

            $kysely2->bindValue(':id',$id,PDO::PARAM_INT);

            // Suoritetaan kysely ja tarkastetaan samalla mahdollinen virhe.
            if ($kysely2->execute()) {
                print('<p>Kirjoitus poistettu.</p>');
            }
            else {
                print '<p>';
                print_r($tietokanta->errorInfo());
                print '</p>';
            }
            print("<a href='index.php'>Takaisin etusivulle</a>");
        
        } else {
            print '<p>';
            print_r($tietokanta->errorInfo());
            print '</p>';           
        }

    } catch (PDOException $pdoex) {
        print '<p>Tietokannan avaus epäonnistui.' . $pdoex->getMessage(). '</p>';
    }
    ?>
        
    </div>   
</div><!-- /.container -->

<?php include_once 'inc/bottom.php';?>