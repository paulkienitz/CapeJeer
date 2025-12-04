<?php
/*
This implements capejeer.com’s left column navigation.  It is all static
content, except for highlighting selected by $whichPage.
*/
?>
        <!-- ====== NAVIGATION SIDEBAR, for desktop-sized windows: -->

        <!-- at least the inside of our nav column is table-free... -->
        <div class='widenavv'>        <!-- old firefox doesn't like position relative on TD -->
<?php /*
          <div id='collapsed' style='display: none;'>
            <div class='navbutton' onclick='navvy(event)'>
              <b>&gt;&gt; <small>SHOW NAVIGATION</small> &gt;&gt;</b>
            </div>
            <div>&nbsp;</div>
          </div>
*/ ?>

          <div id='expanded' style='display: block;'>
<?php /*
            <div class='navbutton' onclick='navvy(event)'>
              <b>&lt;&lt; <small>HIDE NAVIGATION</small> &lt;&lt;</b>
            </div>
*/ ?>
            <div class='navv'>
              <nav>

                <div class='tophint'>&bull; &bull; &bull; select a page &bull; &bull; &bull;</div>

                <?php echo pageLink('h1', 'nav_s_', $whichPage, 'introduction', "Introduction"); ?>

                <?php echo pageLink('h1', 'nav_s_', $whichPage, 'ratings', "The Rating System"); ?>

                <?php echo pageLink('h1', 'nav_s_', $whichPage, 'bestworst', "Bests and Worsts"); ?>

                <h1><span class='sectionlabel'>INDEX OF MOVIES:</span></h1>
                <div class='blockage'>
                  <ul>
                    <li><?php echo pageLink('h2', 'nav_s_', $whichPage, 'RANK', "by <span class='kind'>Rank</span><br/>
                            <span class='kindnote'>(best to worst)</span>", "", "."); ?></li>
                    <li><?php echo pageLink('h2', 'nav_s_', $whichPage, 'CATEGORIES', "by <span class='kind'>Origin</span><br/>
                            <span class='kindnote'>(DC, Marvel, etc)</span>"); ?></li>
                    <li><?php echo pageLink('h2', 'nav_s_', $whichPage, 'ALPHABETIC', "by <span class='kind'>Name</span><br/>
                            <span class='kindnote'>(A to Z)</span>"); ?></li>
                    <li><?php echo pageLink('h2', 'nav_s_', $whichPage, 'YEAR', "by&nbsp;<span class='kind'>Release&nbsp;Date</span><br/>
                            <span class='kindnote'>(newest to oldest)</span>"); ?>

                    <hr style='width: 50%; height: 2px; background-color: #666666; border: none; margin: 1em auto;'/></li>
                    <li><?php echo pageLink('h2', 'nav_s_', $whichPage, 'MCU', "the<br/>Marvel&nbsp;Cinematic<br/>
                            Universe&nbsp;by&nbsp;date", "<i class='kindnote'>special sub-index:</i> "); ?></li>
                  </ul>
                </div>

                <?php echo pageLink('h1', 'nav_s_', $whichPage, 'serials', "<i>supplement:</i><br/>Old Serials"); ?>

                <?php echo pageLink('h1', 'nav_s_', $whichPage, 'tv', "<i>supplement:</i><br/>Television"); ?>


                <div class='barre'><img src='img/blue-dividey.jpg' width='182' height='15' alt='' /></div>  <!-- hr -->

				<!-- **** THESE LINKS ARE SAMPLES, PUT YOUR OWN BELOW **** -->

                <div class='tophint'>
                  &bull; &bull; &bull;&nbsp;
                  <span class='foldy'>we also offer:</span>
                  &nbsp;&bull; &bull; &bull;
                </div>

                <div class='otherz'>
				  <?php echo externalLink('h1', 'https://site0.cow/fooooo/', 'img/near1.png', "SAMPLE Near Link One"); ?>

				  <?php echo externalLink('h1', 'https://site0.cow/baaaaaaaar/', 'img/near2.png', "SAMPLE Near Link Two"); ?>

				  <?php echo externalLink('h1', 'https://site0.cow/quuuuuuuuuuuuux/', 'img/near3.png', "Sample Near Link Three"); ?>

				  <?php echo externalLink('h1', 'mailto:webmaster@' . DOMAIN, 'img/mailbox.png', "Send Mail to<br/>" . AUTHOR); ?>
                </div>

                <div class='tophint'>
                  &bull; &bull; &bull;&nbsp;
                  <span class='foldy'>recommended:</span>
                  &nbsp;&bull; &bull; &bull;
                </div>

                <div class='otherz'>
				  <?php echo externalLink('h1', 'https://site1.cow/', 'img/site1.png', "SAMPLE Faraway Site One",
				                          "style='font-size: 80%' ", "style='margin-right: 1.8em;' "); ?>

				  <?php echo externalLink('h1', 'https://site2.cow/', 'img/site2.png', "SAMPLE Faraway Site Two<br/>And Three Sevenths",
				                          "style='font-size: 80%' ", "style='margin-right: 1.8em' "); ?>

				  <?php echo externalLink('h1', 'http://site3.cow/', 'img/site3.png', "SAMPLE Faraway Site 33&#8531",
				                          "style='font-size: 80%' ", "style='margin-right: 1.8em' "); ?>
                </div>

              </nav>
            </div>
          </div>
        </div>
        <!-- ====== END NAVIGATION SIDEBAR -->
