<?php include_once 'inc/top.php';?>

        <?php
        $id=0;
        $otsikko="";
        $teksti="";
        $kayttaja_id=1;  //kovakoodaus
        
        date_default_timezone_set('Europe/Helsinki');
        //$paivays = date('d.m.Y H.i', time());  //tämä muoto ei toimi tallennuksessa tietokantaan
        $paivays = date('Y-m-d H:i:s', time());  //täytyy tallentaa tässä muodossa, ei muotoilua
        //print $paivays;
        
        // Avataan tietokantayhteys. Nyt tietokantayhteys avataan aina, kun tämä sivu näytetään. Tähän voisi toki lisätä if-lauseen, joka
        //estää tietokannan avaamisen, jos käyttäjä avaan vain asiakkaan lisäyslomakkeen esiin (jolloin tietokantayhteyttä ei vielä tarvita).
        $tietokanta = new PDO('mysql:host=localhost;dbname=blogi;charset=utf8','root','');
        //Oletuksena PDO ei näytä mahdollisia virheitä, joten asetetaan "virhemoodi" päälle.
        $tietokanta->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); 

        if ($_SERVER['REQUEST_METHOD']==='POST') {
            try {
                // Luetaan tiedot lomakkeelta.
                $id = filter_input(INPUT_POST, 'id',FILTER_SANITIZE_NUMBER_INT);
                $otsikko = filter_input(INPUT_POST, 'otsikko',FILTER_SANITIZE_STRING);
                $teksti = filter_input(INPUT_POST, 'teksti',FILTER_SANITIZE_STRING);
                $kayttaja_id = filter_input(INPUT_POST, 'kayttaja_id',FILTER_SANITIZE_NUMBER_INT);
                $paivays = filter_input(INPUT_POST, 'paivays',FILTER_SANITIZE_STRING);
                                
                // Muodostetaan parametroitu sql-kysely tiedon päivittämistä varten. Piilokentässä oleva id on 0 lisäystilanteessa ja muuten
                // kenttä sisältää päivitettävän tietueen id:n.
                if ($id == 0) {
                    $kysely = $tietokanta->prepare("INSERT INTO kirjoitus(otsikko,teksti,kayttaja_id,paivays) VALUES (:otsikko,:teksti,:kayttaja_id,:paivays)");
                }
                else {
                    $kysely = $tietokanta->prepare("UPDATE kirjoitus SET otsikko=:otsikko,teksti=:teksti,kayttaja_id=:kayttaja_id,paivays=:paivays WHERE id=:id");
                    //$kysely = $tietokanta->prepare("UPDATE kirjoitus SET otsikko=:otsikko,teksti=:teksti,kayttaja_id=:kayttaja_id,paivays=GETDATE() WHERE id=:id");
                    $kysely->bindValue(':id',$id,PDO::PARAM_INT);
                }
                
                $kysely->bindValue(':otsikko',$otsikko,PDO::PARAM_STR);
                $kysely->bindValue(':teksti',$teksti,PDO::PARAM_STR);
                $kysely->bindValue(':kayttaja_id',$kayttaja_id,PDO::PARAM_INT);
                $kysely->bindValue(':paivays',$paivays,PDO::PARAM_STR);
                
                // Suoritetaan kysely ja tarkastetaan samalla mahdollinen virhe.
                if ($kysely->execute()) {
                    print('<p>Kirjoitus tallennettu</p>');
                    // Haetaan lisätyn tietueen id-muuttujaan. Jos käyttäjä muuttaa tietoja vielä tässä näkymässä, niin id sisältää sitten
                    // äskettäin lisätyn uuden tietueen id:n ja tietoja muutettaessa päivitetään (update).
                    $id = $tietokanta->lastInsertId();
                }
                else {
                    print '<p>';
                    print_r($tietokanta->errorInfo());
                    print '</p>';
                }
                  
                // Ohjataan tallennuksen jälkeen index.php-sivulle
                header('Location: index.php');

            } catch (PDOException $pdoex) {
                print '<p>Tietokannan avaus epäonnistui.' . $pdoex->getMessage(). '</p>';
            }
        }
?>

        <div class="container">
        <h3><?php print "Lisää kirjoitus"; ?></h3>
        <form action="<?php print $_SERVER['PHP_SELF'];?>" method="post">
            <input type="hidden" name="id" value="<?php print($id);?>">
            
            <div class="form-group">
                <label for="otsikko">Otsikko</label>
                <input class="form-control" id="otsikko" name="otsikko" placeholder="Kirjoita otsikko" value="<?php print($otsikko);?>">
            </div>
            <div class="form-group">
                <label for="teksti">Teksti</label>
                <input class="form-control" id="teksti" name="teksti" placeholder="Kirjoita teksti" value="<?php print($teksti);?>">
            </div>
            
            <input type="hidden" name="kayttaja_id" value="<?php print($kayttaja_id);?>">
            <input type="hidden" name="paivays" value="<?php print($paivays);?>">
            
            <button type="submit" name="tallenna" class="btn btn-primary active">Tallenna</button>
            <button type="button" name="peruuta" class="btn btn-default" onclick="window.location='index.php';">Peruuta</button>
        </form>
        </div>

<?php include_once 'inc/bottom.php';?>