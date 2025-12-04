<?php
/*
This produces an HTML fragment within the context of the CapeJeer.php master page.
It displays the review of a single movie.  The movie in question is specified by
the $pageName and $year variables, and the object is preloaded into $theMovie.
*/

$ensp = html_entity_decode('&ensp;', ENT_HTML5, 'UTF-8');  // no unicode literals in older PHP versions


function imageWidthHeightString($imagePath, $squashy)      // returns HTML attributes
{
	$reporting = error_reporting(E_ERROR | E_PARSE);
	$sizes = getimagesize($_SERVER['DOCUMENT_ROOT'] . $imagePath);
	error_reporting($reporting);
	if (!$sizes)
		return "";
	else if ($squashy)
		// It would be nice to limit it to the physical display pixels instead of CSS pixels...
		// though only in cases where the ratio between CSS and physical is 1.5 or less.
		return "style='max-width: {$sizes[0]}px; max-height: {$sizes[1]}px;'";
	else
		return "width='$sizes[0]' height='$sizes[1]'";     // use quotes for xhtml compatibility
}


function writeTitleReviewHeader($movie)
{
	if ($movie->rank > 0) {
//		echo "  <script type='application/ld+json'>\n" .
//		     $movie->jsonMetadata() . "\n" .
//		     "  </script>\n";				// JSON-formatted rich snippet can't enclose comments, so use microdata:
		echo "  <meta itemprop='url' content='" . SITE . "/{$movie->url()}' />\n";
		echo "  <meta itemprop='datePublished' content='{$movie->published}T00:00:00-08:00' />\n";
		echo "  <meta itemprop='description' content='{$movie->quote}' />\n";
		echo "  <meta itemprop='inLanguage' content='en' />\n";
		echo "  <div class='meta' itemprop='author' itemscope itemtype='https://schema.org/Person'>\n";
		echo "    <meta itemprop='name' content='" . AUTHOR . "' />\n";
		if (AUTHOR_ALT)
			echo "    <meta itemprop='alternateName' content='" . AUTHOR_ALT . "' />\n";
		echo "  </div>\n";
		echo "  <div class='meta' itemprop='publisher' itemscope itemtype='https://schema.org/Organization'>\n";
		echo "    <meta itemprop='name' content='" . SITENAME . "' />\n";
		echo "  </div>\n";
		echo "  <div class='meta' itemprop='reviewRating' itemscope itemtype='https://schema.org/Rating'>\n";
		echo "    <meta itemprop='worstRating' content='0' />\n";
		echo "    <meta itemprop='bestRating' content='4' />\n";
		echo "    <meta itemprop='ratingValue' content='" . ($movie->capes / 2) . "' />\n";
		echo "  </div>\n";
		echo "  <header itemprop='itemReviewed' itemscope itemtype='https://schema.org/Movie'>\n";
		echo "    <div class='meta' itemprop='director' itemscope itemtype='https://schema.org/Person'>\n";
		echo "      <meta itemprop='name' content='{$movie->director}' />\n";
		echo "    </div>\n";
		if ($movie->imdb)
			echo "    <meta itemprop='sameAs' content='https://www.imdb.com/title/{$movie->imdb}'>\n";
		else						// gloogle treats sameAs as a required field, so:
			echo "    <meta itemprop='sameAs' content='" . SITE . "/{$movie->url()}'>\n";
	} else
		echo "  <header>\n";
	echo "    <table><tr class='taiteltab'>      <!-- #giveupandusetables   ...todo: I think we can use grid now -->\n";
	if (strlen($movie->boxImage) > 0)
		echo "      <td><img class='box' alt='' itemprop='image' src='/box/{$movie->boxImage}' " .
		     imageWidthHeightString("/box/{$movie->boxImage}", false) . " /></td>\n";
	echo "      <td>\n";
	echo "        <h2 class='taitel'><span class='theetaitel' itemprop='name'>{$movie->title}</span>" .
	                                "&ensp;(<span itemprop='copyrightYear'>{$movie->year}</span>)";
	if ($movie->rating && $movie->rating != 'PG-13')
		echo "<span class=rating style='color: #666666; border-color: #666666;'>{$movie->rating}</span>";
	if ($movie->rank == -3)
		echo "<span class='unreleased'>&emsp;&mdash;&nbsp;never released</span></h2>\n";
	else if ($movie->rank == -2)
		echo "<span class='unseen'>&emsp;&mdash;&nbsp;not seen yet</span></h2>\n";  // "not reviewed yet", actually
	else if ($movie->rank == -1)
		echo "<span class='unreleased'>&emsp;&mdash;&nbsp;not released yet</span></h2>\n";
	else if ($movie->rank <= 0)
		echo "<span class='unseen'>&emsp;&mdash;&nbsp;not seen yet</span></h2>\n";
	else
	{
		echo "</h2>\n        <div class='taitel_icons'\n           >";
		echo $movie->capeRatingMarkup("img/cape.png", "img/halfcape.png", "star icon");
		if ($movie->heads > 0)
		{						// ** XXX TODO: stop using forty-nothings.gif as a spacer
			echo "<img alt='' src='img/forty-nothings.gif' class='star icon'\n          />";
			echo $movie->headRatingMarkup("img/baldhead.png", "img/halfbaldhead.png", "star icon");
		}

		if ($movie->deconst > 0)
		{
			echo "<img alt='' src='img/forty-nothings.gif' class='star icon'\n          />";
			echo $movie->deconstRatingMarkup("img/wreckingball.png", "img/halfwreckingball.png", "star icon");
		}

		if ($movie->tents > 0)
		{
			echo "<img alt='' src='img/forty-nothings.gif' class='star icon'\n          />";
			echo $movie->tentRatingMarkup("img/tent.png", "img/halftent.png", "star icon");
		}
		echo "</div>\n";
	}
	echo "      </td>\n";
	echo "    </tr></table>\n";
	echo "  </header>\n";
}


function offsetBy($steps)
{
	return round($steps * -0.8, 1) . "em";
}


function writeMoreMenu($count, $isLater)
{
	echo "          <td style='width: 5em; visibility: " . ($count > 0 ? "visible" : "hidden") . ";'>\n";
	echo "            <div class='liner'>\n";
	$LorR = $isLater ? 'R' : 'L';
	echo "              <div id='" . ($isLater ? 'rightmenu' : 'leftmenu') . "' class='additional{$LorR}'>\n";
	echo "                <img src='img/hamburger{$LorR}.png' class='burger' alt='' />\n";
	echo "                <div class='additionalabel'>" . genericNumericText($count * 2) . "</div>\n";
	echo "                <div class='additionalabel' style='position: relative; top: -0.5em;'>more</div>\n";
	echo "              </div>\n";
	echo "              <img src='img/moremenu.png' alt='' />\n";
	echo "            </div>\n";
	echo "          </td>\n";
}


function writeSequelBarArrow($movie, $isLater, $isReboot, $isPrequel)
{
	$LorR = $isLater ? 'R' : 'L';
	echo "              <div class='moviename{$LorR}'>\n";
	echo "                <div class='relatelabel'>&mdash; " .
	     ($isReboot ? ($isLater ? "reboot" : "reboot of")
	                : ($isPrequel ? ($isLater ? "prequel" : "prequel to")
	                              : ($isLater ? "sequel" : "sequel to"))) . " &mdash;</div>\n";
	echo "                <div class='taitel'><a href='" . $movie->url() . "'>$movie->title" . ($movie->isRemake ? " ($movie->year)" : "") . "</a></div>\n";
	echo "              </div>\n";
	echo "              <img src='img/signpost{$LorR}.png' alt='' />\n";
}


function sortYear($a, $b)
{
	return $a->year - $b->year;
}


function writeSequelNavBar($movie, $series)
{
	// don't show see-as records (note that $series was passed to us by value)
	foreach ($series as $x => $m)
		if ($m->rank <= -10)
			unset($series[$x]);
	// this normalizes the indexes if there are any gaps now...
	// we formerly used sortYearClumpSequels, but the extra complexity had no benefit
	usort($series, "sortYear");

	if (count($series) > 1)
	{
		$thisPage = array_search($movie, $series, true);
		echo "\n  <nav>\n";
		echo "    <div class='relatedbar2'>\n";
		if ($thisPage > 0)
		{
			echo "      <div class='relatedleft'>\n";
			echo "        <table style='width: 100%'><tr>\n";
			writeMoreMenu($thisPage - 1, false);
			echo "          <td class='additionalgap'></td><td>\n";
			echo "            <div class='liner'>\n";
			writeSequelBarArrow($series[$thisPage - 1], false, $series[$thisPage]->sequelYear <= 0, $series[$thisPage]->isPrequel);
			echo "              <div id='leftplop' class='plopup' style='display: none'>\n";
			for ($preq = $thisPage - 2; $preq >= 0; $preq--)
			{
				echo "                <div class='stepquel' style='left: " . offsetBy($thisPage - $preq - 1) . ";'>\n";
				writeSequelBarArrow($series[$preq], false, $series[$preq + 1]->sequelYear <= 0, $series[$preq + 1]->isPrequel);
				echo "                </div>\n";
			}
			echo "              </div>\n";
			echo "            </div>\n";
			echo "          </td>\n";
			echo "        </tr></table>\n";
			echo "      </div>\n";
		}
		if ($thisPage < count($series) - 1)
		{
			echo "      <div class='relatedright'>\n";
			echo "        <table style='width: 100%'><tr>\n";
			echo "          <td>\n";
			echo "            <div class='liner'>\n";
			writeSequelBarArrow($series[$thisPage + 1], true, $series[$thisPage + 1]->sequelYear <= 0, $series[$thisPage + 1]->isPrequel);
			echo "              <div id='rightplop' class='plopup' style='display: none'>\n";
			for ($seq = $thisPage + 2; $seq < count($series); $seq++)
			{
				echo "                <div class='stepquel' style='right: " . offsetBy($seq - $thisPage - 1) . ";'>\n";
				writeSequelBarArrow($series[$seq], true, $series[$seq]->sequelYear <= 0, $series[$seq]->isPrequel);
				echo "                </div>\n";
			}
			echo "              </div>\n";
			echo "            </div>\n";
			echo "          </td><td class='additionalgap'></td>\n";
			writeMoreMenu(count($series) - $thisPage - 2, true);
			echo "        </tr></table>\n";
			echo "      </div>\n";
		}
		echo "      <div class='clear'></div>\n";
		echo "    </div>\n";
		echo "  </nav>\n\n";
	}
}


function writeAnchorTranslator(&$series)
{
	$done = false;
	foreach ($series as $movie)
		if (strlen($movie->anchor) > 0)
		{
			if (!$done)
				echo "  <script type='text/javascript'>\n";
			echo "    if (window.location.hash == '#$movie->anchor')\n";
			echo "        window.location = '" . $movie->url() . "';\n";
			$done = true;
		}
	if ($done)
		echo "  </script>\n";
}


function emitReview($sourceFile)
{
	global $ensp;
	$text = file_get_contents('./reviews/' . $sourceFile);
	$text = preg_replace('/^\xEF\xBB\xBF/', '', $text);		// BOM causes extra vertical space
	// Our rule: you can use plain double spaces between sentences, but use &nbsp;
	// if the break you want double-spaced is at the end of a line.
	echo preg_replace('/((?<!\s)  |&nbsp;\s+)/', $ensp, $text);		// want better handling of line-end cases... for now just avoid them in source
}

?>
<?php
if ($theMovie->rank > 0)
	echo "<article itemscope itemtype='https://schema.org/Review'>\n";
else
	echo "<article>\n";
// This itemscope encloses the Commentics section as well as the review, so its itemscope
// is a child to the article one.  To make this work for Gloogle, I edited the Commentics
// files frontend/view/default/template/comment/layout_one.tpl and layout_two.tpl so the
// outer div at the top includes the attribute itemprop="comment" right before itemscope.

if (strlen($theMovie->anchor) == 0)
	writeAnchorTranslator($movies[$pageName]);		// phase this out?
writeTitleReviewHeader($theMovie);
writeSequelNavBar($theMovie, $movies[$pageName]);

if ($theMovie->rank > 0)
	echo "  <div class='review' itemprop='text'>\n";
else
	echo "  <div class='preeeview'>\n";

if (strlen($theMovie->panelImage) > 0)
	echo "    <img alt='' src='/panel/$theMovie->panelImage' class='floater' " .
	     imageWidthHeightString("/panel/$theMovie->panelImage", true) . " />\n";
echo "\n";

if (DEBUG && file_exists('./reviews/!' . $pageName . ' ' . $year . ".html-content"))
	emitReview('!' . $pageName . ' ' . $year . ".html-content");  // for testing alternate versions of review content
else
	emitReview($pageName . ' ' . $year . ".html-content");        // originally a simple "require" was used here
echo "  </div>\n";

if ($commentsType === 'inline')
{
	$cmtx_identifier = $pageName . '-' . $year;
	$cmtx_reference = $theMovie->title;
	echo "<div class=commentation>";
	$serverLocation = $_SERVER['DOCUMENT_ROOT'] . $cmtx_folder;
	if (DEBUG) $dumpty .= "Inline commentics ID is $cmtx_identifier, reference is '$cmtx_reference', path is '$serverLocation'.\n";
	require($serverLocation . 'frontend/index.php');
	echo '<div class=termination><a href="#" data-cmtx-target-modal="#cmtx_terms_modal">terms and conditions for commenters</a>';
	echo '&emsp;|&emsp;<a href="#" data-cmtx-target-modal="#cmtx_privacy_modal">privacy policy</a></div>';
	echo "\n</div>\n";
}
else if ($commentsType === 'iframe')
{
	echo "<script> var commentics_config = { 'identifier': '{$pageName}-{$year}', 'reference': '{$theMovie->title}' }; </script>\n";
	echo "<div class=commentation>\n<div id=commentics>\n</div>\n";
	if (DEBUG) $dumpty .= "Rendering commentics iframe parent.\n";
	echo "ARRGH, FIX THIS!  THESE LINKS NEED TO BE INSIDE THE IFRAME TO WORK:\n";
	echo '<div class=termination><a href="#" data-cmtx-target-modal="#cmtx_terms_modal">terms and conditions for commenters</a>';
	echo '&emsp;|&emsp;<a href="#" data-cmtx-target-modal="#cmtx_privacy_modal">privacy policy</a></div>';
	echo "\n</div>\n";
}
else if ($commentsType === 'proxy')				// a failed experiment
{
	$proxy = 'https://paulkienitz.net/bm/cj-comment-proxy.php';
	$encoded = urlencode($theMovie->title);
	$curly = curl_init("$proxy?whodat=Manter!_ooz%2bt&pageid=$pageName-$year&title=$encoded");
	if (DEBUG) $dumpty .= "Rendering commentics iframe parent proxywise.\n";
	curl_exec($curly);      // render direct to client
}
else
	if (DEBUG) $dumpty .= "Comment method is $commentsType, so comments are not active.\n";
?>
</article>
<div class='underpad'>&nbsp;</div>
