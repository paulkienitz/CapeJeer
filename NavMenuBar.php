<?php
/*
This implements capejeer.com’s top menu-bar navigation.  It is all essentially
static content, except for highlighting selected by $whichPage.
*/
?>  <!-- ====== NAVIGATION DROPDOWN MENUS, for phone-sized windows: -->

  <nav>
    <div id='navvbar' class='narrnavv'>
      <div class='navvmenu'>
        <a class='navvitem' id='openindex'>Index of Movies <img src='img/menudrop.png' class='navvicon' alt='Menu: Index of Movies' /></a>
        <div id='indexmenu' class='indexx plopnavv' style='display: none; left: 0;'>
          <div class='navv'>
            <?php echo pageLink('h2', 'nav_b_', $whichPage, 'RANK', "by <span class='kind'>Rank</span><br/>
                <span class='kindnote'>(best to worst)</span>", "", "."); ?>

            <?php echo pageLink('h2', 'nav_b_', $whichPage, 'CATEGORIES', "by <span class='kind'>Origin</span><br/>
                <span class='kindnote'>(DC, Marvel, etc)</span>"); ?>

            <?php echo pageLink('h2', 'nav_b_', $whichPage, 'ALPHABETIC', "by <span class='kind'>Name</span><br/>
                <span class='kindnote'>(A to Z)</span>"); ?>

            <?php echo pageLink('h2', 'nav_b_', $whichPage, 'YEAR', "by&nbsp;<span class='kind'>Release&nbsp;Date</span><br/>
                <span class='kindnote'>(newest to oldest)</span>"); ?>

            <hr />
            <?php echo pageLink('h2', 'nav_b_', $whichPage, 'MCU', "the<br/>Marvel&nbsp;Cinematic<br/>
                Universe&nbsp;by&nbsp;date", "<i class='kindnote'>special sub-index:</i> "); ?>

           </div>
        </div>
      </div>

      <div class='navvmenu'>
        <a class='navvitem' id='openintro'>Introduction, etc <img src='img/menudrop.png' class='navvicon' alt='Menu: Introduction, etc' /></a>
        <div id='intromenu' class='indexx plopnavv' style='display: none; right: 0;'>
          <div class='navv'>
            <?php echo pageLink('h2', 'nav_b_', $whichPage, 'introduction', "Introduction"); ?>

            <?php echo pageLink('h2', 'nav_b_', $whichPage, 'ratings', "The Rating System"); ?>

            <?php echo pageLink('h2', 'nav_b_', $whichPage, 'bestworst', "Bests and Worsts"); ?>

            <?php echo pageLink('h2', 'nav_b_', $whichPage, 'serials', "<i>supplement:</i><br/>Old Serials"); ?>

            <?php echo pageLink('h2', 'nav_b_', $whichPage, 'tv', "<i>supplement:</i><br/>Television"); ?>

            <!--hr/-->
            <div class='tophint'>&bull; &bull; &bull; links &bull; &bull; &bull;</div>

			<!-- **** THESE LINKS ARE SAMPLES, PUT YOUR OWN BELOW **** -->
            <div class='otherz'>
			  <?php echo externalLink('h2', 'https://site0.cow/fooooo/', 'img/near1.png', "SAMPLE Near Link One"); ?>

			  <?php echo externalLink('h2', 'https://site0.cow/baaaaaaaar/', 'img/near2.png', "SAMPLE Near Link Two"); ?>

			  <?php echo externalLink('h2', 'https://site0.cow/quuuuuuuuuuuuux/', 'img/near3.png', "Sample Near Link Three"); ?>

			  <?php echo externalLink('h2', 'mailto:webmaster@' . DOMAIN, 'img/mailbox.png', "Send Mail to<br/>" . AUTHOR); ?>
            </div>
          </div>
        </div>
      </div>
    </div>
  </nav>

  <!-- ====== END NAVIGATION DROPDOWN MENUS -->
