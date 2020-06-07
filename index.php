<?php include_once 'inc/top.php';?>

<div class="container">
    <div class="some-space">
        <?php
        $asiakas_id = 1; //kovakoodaus
        
        try {
            // Avataan tietokantayhteys.
            $tietokanta = new PDO('mysql:host=localhost;dbname=blogi;charset=utf8','root','');
            //Oletuksena PDO ei näytä mahdollisia virheitä, joten asetetaan "virhemoodi" päälle.
            $tietokanta->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                       
            // Muodostetaan suoritettava sql-lause.
            $sql = 'SELECT kirjoitus.*, kayttaja.tunnus FROM kirjoitus '
                    . 'INNER JOIN kayttaja ON kirjoitus.kayttaja_id = kayttaja.id ORDER BY paivays DESC';
           
            // Suoritetaan kysely tietokantaan.
            $kysely = $tietokanta->query($sql);
            
            if ($kysely) {
                print "<h2>Blogi</h2>";
                
                while ($tietue = $kysely->fetch()) {
                    print '<div class="my-border">';
                    $muotoiltu_paivays = strtotime($tietue['paivays']);
                    print  $tietue['tunnus'] . '<span style="color: #aaa;">' 
                            . ' kirjoitti ' . date("d.m.Y H.i", $muotoiltu_paivays) . '</span>';
                   
                    // Huom! Siirretään id selvyyden vuoksi kirjoitus_id:nä, 
                    // mikä on kommentti-taulun kirjoitus-taulua vastaava id
                    print '<p>&nbsp &nbsp' . '<span class="text-bold"><a href="blog_text.php?kirjoitus_id=' 
                            . $tietue['id'] . '">' . $tietue['otsikko'] . '</a> ' . '</span>';
                    
                    // Kirjoitusten poistaminen on mahdollista vain, kun käyttäjä on kirjautunut
                    if ($_SESSION['login'] === TRUE){
                        print '&nbsp &nbsp';
                        print '<a href="remove.php?id=' . $tietue['id'] . 
                                '" onclick="return confirm(\'Poistetaanko kirjoitus?\');">'
                                . '<span class="glyphicon glyphicon-trash"></span></a>';
                    }
                    print '<p></p>';
                    print '</div>';
                }
                
                if($kysely->rowCount() === 0) {
                    //print "<h2>Tervetuloa blogiin!</h2>";
                    print "<p>Blogissa ei ole vielä kirjoituksia.</p>"
                    . "<p>Kirjauduttuasi voit aloittaa kirjoittamisen.</p>"
                    . "<p>Tervetuloa!</p>";
                } 

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
</div>

<?php include_once 'inc/bottom.php';?>