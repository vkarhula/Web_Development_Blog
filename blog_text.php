<?php include_once 'inc/top.php';?>

<div class="container">
    <div class="some-space">
        <?php
        $kayttaja_id = 1; //kovakoodaus
        $id = 0;
        $teksti = "";
        $paivays = "";
        $kirjoitus_id = 0;
        $tunnus = "";
        
        date_default_timezone_set('Europe/Helsinki');
        //$paivays = date('d.m.Y H.i', time());  //tämä muoto ei toimi tallennuksessa tietokantaan
        $paivays = date('Y-m-d H:i:s', time());  //täytyy tallentaa tässä muodossa, ei muotoilua       
        
        // Avataan tietokantayhteys.
        $tietokanta = new PDO('mysql:host=localhost;dbname=blogi;charset=utf8','root','');
        //Oletuksena PDO ei näytä mahdollisia virheitä, joten asetetaan "virhemoodi" päälle.
        $tietokanta->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        if ($_SERVER['REQUEST_METHOD']==='GET') {
            if (isset($_GET['kirjoitus_id'])) {
                // $id <=> kirjoitus.id
                $kirjoitus_id = filter_input(INPUT_GET,'kirjoitus_id',FILTER_SANITIZE_NUMBER_INT);
                print '<p><a href="index.php">Takaisin etusivulle</a></p>';

                try {
                    // Muodostetaan suoritettava sql-lause.
                    $sql = "SELECT kirjoitus.*, kayttaja.tunnus FROM kirjoitus "
                            . "INNER JOIN kayttaja ON kirjoitus.kayttaja_id = kayttaja_id "
                            . "WHERE kirjoitus.id = $kirjoitus_id";

                    // Suoritetaan kysely tietokantaan.
                    $kysely = $tietokanta->query($sql);

                    if ($kysely) {
                        $tietue = $kysely->fetch();
                            print '<h2>' . $tietue['otsikko'] . '</h2>';
                            print '<p><span style="color: #aaa;">' . $tietue['tunnus'] . ' kirjoitti ';
                            $muotoiltu_paivays = strtotime($tietue['paivays']);
                            print date("d.m.Y H.i", $muotoiltu_paivays) . '</span></p>';
                            print '<p>' . $tietue['teksti'] . '</p>';
                            print '<p class=text-bold>Kommentit</p>';

                            // Tulostetaan kommentit listassa
                            $sql2 = "SELECT kommentti.id,paivays,teksti,kayttaja.tunnus FROM kommentti INNER JOIN kayttaja ON kommentti.kayttaja_id = kayttaja.id WHERE kirjoitus_id = $kirjoitus_id ORDER BY paivays ASC";

                            $kysely2 = $tietokanta->query($sql2);
                            if ($kysely2) {
                                print '<ul>';
                                while ($tietue2 = $kysely2->fetch()) {
                                    $muotoiltu_paivays2 = strtotime($tietue2['paivays']);
                                    print '<li>' . '<span style="color: #aaa;">' . $tietue2['tunnus'] . ' kirjoitti' 
                                            . ' ' .  date("d.m.Y H.i", $muotoiltu_paivays2) . '</span>';
                                    print "<p>" . $tietue2['teksti'];
                                    // Kommenttien poistaminen on mahdollista vain, kun käyttäjä on kirjautunut
                                    if ($_SESSION['login'] === TRUE){
                                        print '&nbsp &nbsp';
                                        print '<a href="remove_comment.php?id=' . $tietue2['id'] . "&kirjoitus_id=" . $kirjoitus_id .
                                            '" onclick="return confirm(\'Poistetaanko kommentti?\');">'
                                            . '<span class="glyphicon glyphicon-trash"></span></a>';
                                    }
                                    print "</p>";
                                    print '<p></p>';
                                }
                                print '</ul>';
                            }
                            /*if ($kysely2) {
                                print '<ul>';
                                while ($tietue2 = $kysely2->fetch()) {
                                    $muotoiltu_paivays2 = strtotime($tietue2['paivays']);
                                    print '<li>' . $tietue2['teksti'] . ' ' .  date("d.m.Y H.i", $muotoiltu_paivays2);
                                    print '<span style="color: #aaa;">' . ' by ' . $tietue2['tunnus'] . '</span>';
                                    // Kommenttien poistaminen on mahdollista vain, kun käyttäjä on kirjautunut
                                    if ($_SESSION['login'] === TRUE){
                                        print '&nbsp &nbsp';
                                        print '<a href="remove_comment.php?id=' . $tietue2['id'] . "&kirjoitus_id=" . $kirjoitus_id .
                                            '" onclick="return confirm(\'Poistetaanko kommentti?\');">'
                                            . '<span class="glyphicon glyphicon-trash"></span></a>';
                                    }
                                    print '<p></p>';
                                }
                                print '</ul>';
                            }*/
                            print '<p></p>';
                             
                            if ($_SESSION['login'] === TRUE){
                                print "<p style='padding-left:2em;'>Lisää kommentti:</p>";
                                ?>
    
                                <form action="<?php print $_SERVER['PHP_SELF'];?>" method="post" style="padding-left:2em;">
                                    <input type="hidden" name="id" value="<?php print($id);?>">  <!-- id <=> kommentti.id --> <!-- <?//php print $tietue->id;?>">  -->
                                    <input type="hidden" name="paivays" value="<?php print $paivays;?>">   
                                    <input type="hidden" name="kirjoitus_id" value="<?php print $kirjoitus_id;?>">
                                    <input type="hidden" name="kayttaja_id" value="<?php print $kayttaja_id;?>">
                                    <textarea name="teksti" id="tekstiarea" rows="2" cols="50" value="<?php print $tietue2['teksti'];?>"></textarea>
                                    <p></p>
                                    <!-- Kommentin tallennus toteutettu enterin painalluksella textareassa jQuery:llä (script.js). -->
                                     <input type="submit" id="submit" style="display: none;"/> 
                                    <!-- Alla buttonin submit-tallennus -->
                                    <!-- <button type="submit" name="Tallenna" id="submit" class="btn btn-primary active">Tallenna</button>  -->
                                </form>
                            <?php
                            }                           
                            
                            
                    } else {
                        print '<p>';
                        print_r($tietokanta->errorInfo());
                        print '</p>';
                    }
            
                } catch (PDOException $pdoex) {
                    print '<p>Tietokannan avaus epäonnistui.' . $pdoex->getMessage(). '</p>';
                }
            }
            
        } else if ($_SERVER['REQUEST_METHOD']==='POST') {
            try {
                // Luetaan tiedot lomakkeelta.
                $id = filter_input(INPUT_POST, 'id',FILTER_SANITIZE_NUMBER_INT);    //$id = kommentti.id
                $teksti = filter_input(INPUT_POST, 'teksti',FILTER_SANITIZE_STRING);
                $paivays = filter_input(INPUT_POST, 'paivays',FILTER_SANITIZE_STRING);
                $kirjoitus_id = filter_input(INPUT_POST, 'kirjoitus_id',FILTER_SANITIZE_NUMBER_INT);
                $kayttaja_id = filter_input(INPUT_POST, 'kayttaja_id',FILTER_SANITIZE_NUMBER_INT);
              
                // Muodostetaan parametroitu sql-kysely tiedon päivittämistä varten. Piilokentässä oleva id on 0 lisäystilanteessa ja muuten
                // kenttä sisältää päivitettävän tietueen id:n.
                if ($id == 0) {
                    $kysely3 = $tietokanta->prepare("INSERT INTO kommentti(teksti,paivays,kirjoitus_id,kayttaja_id) VALUES (:teksti,:paivays,:kirjoitus_id,:kayttaja_id)");
                    print "INSERT INTO kommentti(teksti,paivays,kirjoitus_id,kayttaja_id) VALUES (:teksti,:paivays,:kirjoitus_id,:kayttaja_id)";
                }
                else {
                    $kysely3 = $tietokanta->prepare("UPDATE kommentti SET teksti=:teksti,paivays=:paivays,kirjoitus_id=:kirjoitus_id,kayttaja_id=:kayttaja_id WHERE id=:id");
                    $kysely3->bindValue(':id',$id,PDO::PARAM_INT);
                }
                
                $kysely3->bindValue(':teksti',$teksti,PDO::PARAM_STR);
                $kysely3->bindValue(':paivays',$paivays,PDO::PARAM_STR);
                $kysely3->bindValue(':kirjoitus_id',$kirjoitus_id,PDO::PARAM_INT);
                $kysely3->bindValue(':kayttaja_id',$kayttaja_id,PDO::PARAM_INT);

                // Suoritetaan kysely ja tarkastetaan samalla mahdollinen virhe.
                if ($kysely3->execute()) {
                    // Haetaan lisätyn tietueen id-muuttujaan. Jos käyttäjä muuttaa tietoja vielä tässä näkymässä, niin id sisältää sitten
                    // äskettäin lisätyn uuden tietueen id:n ja tietoja muutettaessa päivitetään (update).
                    $id = $tietokanta->lastInsertId();

                    // Siirrytään toiselle sivulle näyttämään kommentin tallennuksen onnistuminen.
                    // Näin vältetään mahdollinen sivun päivittämisen aiheuttama kommentin tuplatallennus
                    header("Location: comment_saved.php?kirjoitus_id=$kirjoitus_id"); 
                }
                else {
                    print '<p>';
                    print_r($tietokanta->errorInfo());
                    print '</p>';
                }
                print("<a href='index.php'>Etusivulle</a>");

            } catch (PDOException $pdoex) {
                print '<p>Tietokannan avaus epäonnistui.' . $pdoex->getMessage(). '</p>';
            }
        }

        ?>
    </div>
</div>

<?php include_once 'inc/bottom.php';?>