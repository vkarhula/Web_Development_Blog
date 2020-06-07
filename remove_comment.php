<?php include_once 'inc/top.php';?>

<div class="container">

    <div class="some-space">
        
    <?php
    $id = filter_input(INPUT_GET,'id',FILTER_SANITIZE_NUMBER_INT);
    $kirjoitus_id = filter_input(INPUT_GET,'kirjoitus_id',FILTER_SANITIZE_NUMBER_INT);

    try {

        // Avataan tietokantayhteys.
        $tietokanta = new PDO('mysql:host=localhost;dbname=blogi;charset=utf8','root','');
        //Oletuksena PDO ei näytä mahdollisia virheitä, joten asetetaan "virhemoodi" päälle.
        $tietokanta->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Muodostetaan parametroitu sql-kysely tiedon poistamista varten.
        $kysely = $tietokanta->prepare("DELETE FROM kommentti WHERE id=:id");

        $kysely->bindValue(':id',$id,PDO::PARAM_INT);

        // Suoritetaan kysely ja tarkastetaan samalla mahdollinen virhe.
        if ($kysely->execute()) {
            print('<p>Kommentti poistettu.</p>');
        }
        else {
            print '<p>';
            print_r($tietokanta->errorInfo());
            print '</p>';
        }
        print("<a href='blog_text.php?kirjoitus_id=$kirjoitus_id'>Takaisin kirjoitukseen</a>");

    } catch (PDOException $pdoex) {
        print '<p>Tietokannan avaus epäonnistui.' . $pdoex->getMessage(). '</p>';
    }
    ?>
        
    </div>   
</div><!-- /.container -->

<?php include_once 'inc/bottom.php';?>