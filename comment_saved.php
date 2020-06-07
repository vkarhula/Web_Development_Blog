<?php include_once 'inc/top.php';?>

<div class="container">
      <div class="some-space">

            <?php
            $kirjoitus_id = 0;
            if ($_SERVER['REQUEST_METHOD']==='GET') {
               if (isset($_GET['kirjoitus_id'])) {
                    $kirjoitus_id = filter_input(INPUT_GET,'kirjoitus_id',FILTER_SANITIZE_NUMBER_INT);
                    //print "kirjoitus_id = " . $kirjoitus_id;
                    ?>
                    <p>Kommenttisi on tallennettu.</p>
                    <?php
                    print '<p>' . '<a href="blog_text.php?kirjoitus_id=' . $kirjoitus_id . '">'
                            . "Takaisin kirjoitukseen" . '</a>';
                 } 
            }
            ?>
                    
      </div>
    </div><!-- /.container -->

<?php include_once 'inc/bottom.php';?>