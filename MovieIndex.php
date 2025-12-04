<?php
/*
This produces an HTML fragment within the context of the CapeJeer.php master page.
It displays an index of all movies according to one of four sort criteria.  The
particular format used is determined by the preset variable $whichPage.
*/


function sortAlpha($a, $b)
{
	$ret = strcmp($a->condenseTitle(false), $b->condenseTitle(false));
	return $ret == 0 ? $a->year - $b->year : $ret;
}

function sortAlphaClumpSequels($a, $b)
{
	global $movies;
	$aparent = $a->sequelYear > 0 && $a->year > 0 ? $movies[$a->page][$a->sequelYear] : $a;
	$bparent = $b->sequelYear > 0 && $b->year > 0 ? $movies[$b->page][$b->sequelYear] : $b;
	if ($aparent !== $bparent)
	{
    	$ret = strcmp($aparent->condenseTitle(false), $bparent->condenseTitle(false));
    	return $ret == 0 ? $aparent->year - $bparent->year : $ret;
	}
	else
		return $a->year - $b->year;
}

function sortCategories($a, $b)
{
	if ($a->category == $b->category)
		return sortAlphaClumpSequels($a, $b);
	else
		return $a->category - $b->category;
}

function sortRankDesc($a, $b)
{
	if ($a->rank > 0 || $b->rank > 0)
		return $b->rank - $a->rank;						// positive ranks should be unique
	else if ($a->sequelYear == 0 && $b->sequelYear > 0)
		return -1;
	else if ($b->sequelYear == 0 && $a->sequelYear > 0)
		return 1;
	else
		return sortAlpha($a, $b);
}

function sortYearDesc($a, $b)
{
	if ($a->year == $b->year)
		return sortAlpha($a, $b);
	else
		return $b->year - $a->year;
}


function subheaderText(&$movie, $subheaderField)
{
	switch ($subheaderField)
	{
		case 'category':
			switch ($movie->category)
			{
				case 1: $subheaderText = "DC Comics<sup>&reg;</sup>"; break;
				case 2: $subheaderText = "Marvel Comics<sup>&reg;</sup>"; break;
				case 3: $subheaderText = "Other adventure comics"; break;
				case 4: $subheaderText = "Comedic comics"; break;
				case 5: $subheaderText = "Newspaper comic strips"; break;
				case 6: $subheaderText = "Imitation comic book characters<br/><small>(including parodies)</small>"; break;
			}
			break;
		case 'group':
			switch ($movie->group)
			{
				case 1: $subheaderText = "Good movies"; break;
				case 2: $subheaderText = "Okay movies"; break;
				case 3: $subheaderText = "Lame movies"; break;
				case 4: $subheaderText = "Really bad movies"; break;
				case 5: $subheaderText = "Not seen yet"; break;
				case 6: $subheaderText = "Not seen yet &mdash; pointless sequels"; break;
			}
			break;
		case 'year':
			$subheaderText = (string) $movie->year;
			break;
	}
	return $subheaderText;
}


function iconMarkup(&$movie)
{
	echo "        <a href='{$movie->url()}'>";
	echo $movie->tentRatingMarkup("img/tent.png", "img/halftent.png", "iconlet");

	if ($movie->tents > 0 && $movie->deconst > 0)
		echo "<span class='iconlet_gap'>&nbsp;</span>";
    echo $movie->deconstRatingMarkup("img/wreckingball.png", "img/halfwreckingball.png", "iconlet");

	if ($movie->heads > 0 && ($movie->tents + $movie->deconst) > 0)
		echo "<span class='iconlet_gap'>&nbsp;</span>";
    echo $movie->headRatingMarkup("img/baldhead.png", "img/halfbaldhead.png", "iconlet");

	if ($movie->capes > 0 && ($movie->heads + $movie->tents + $movie->deconst > 0))
		echo "<span class='iconlet_gap'>&nbsp;</span>";
    echo $movie->capeRatingMarkup("img/cape.png", "img/halfcape.png", "iconlet");
	echo "</a>\n";
}


function htmlIndexTitle(&$movie)
{
    global $whichPage;
    echo "        <a title='{$movie->quote}'\n            href='{$movie->url()}' class='" .
	     ($movie->rank > 0 ? 'reviewed' : 'unreviewed') . "'>{$movie->title}</a>";
    if ($whichPage != 'YEAR' && $whichPage != 'MCU' && $movie->isRemake && strpos($movie->title, ':') === false)
        echo "&ensp;({$movie->year})";
}


// ==============================


switch ($whichPage)
{
	case 'ALPHABETIC':
		$sorter = 'sortAlpha';
		$subheaderField = NULL;
		$description = '&ensp;Below is a list of reviewed movies, ordered by title.&ensp;For
            alternate views, such as a list ordered by rating from best to worst, select
            one of the links on the left.';
		break;
	case 'CATEGORIES':
		$sorter = 'sortCategories';
		$subheaderField = 'category';
		$description = '&ensp;Below is a list of reviewed movies, divided up by where
            the characters came from.&ensp;For alternate views, such as a list ordered by
            name or by release date, select one of the links on the left.';
		break;
	case 'RANK':
		$sorter = 'sortRankDesc';
		$subheaderField = 'group';
		$description = '&ensp;Below is a list of reviewed movies, ordered with the best
            movies at the top and the worst at the bottom.&ensp; If you would prefer a list
            ordered by name or by year of release, select one of the links on the left.';
		break;
	case 'YEAR':
		$sorter = 'sortYearDesc';
		$subheaderField = 'year';
		$description = '&ensp;Below is a list of reviewed movies, ordered by the year
            the movie came out.&ensp;For alternate views, such as a list ordered by title,
            select one of the links on the left.';
		break;
	case 'MCU':
		$sorter = 'sortYearDesc';
		$subheaderField = 'year';
		$description = '&ensp;Below is a list of the movies that constitute the Marvel Cinematic Universe,
            ordered by the year the movie came out.&ensp;To see all films reviewed on this site,
            rather than just those in the Marvel continuity, select one of the links on the left.';
		break;
	default:
		throw new ArgumentException("No such index page as $whichPage");
}

echo "<div class='index_page'>\n";
echo "  <p style='margin-top: 0'><b>Cape Jeer</b> is a review site for movies about superheroes and other\n";
echo "            comic book characters.<span class=wideonly>$description</span></p>\n\n";

require './newest.html-content';

?>
  <div class='catalog_hdr' style='padding-bottom: 0.4em'>
	<?php echo $title; ?>
  </div>

  <table>
<?php
$sortedMovies = array();
foreach ($movies as $pageName => &$years)
	foreach ($years as $year => &$movie)
		if ($movie->year > 0 || $whichPage == 'ALPHABETIC' || /* not in old CJ: */ $whichPage == 'CATEGORIES')
		    if ($whichPage != 'MCU' || $movie->mcu)
			    $sortedMovies[] = $movie;
usort($sortedMovies, $sorter);
//if (strlen($debugLoad) > 0) echo $debugLoad;

$prevSubheader = NULL;
foreach ($sortedMovies as &$movie)
{
	if (isset($subheaderField) && $movie->$subheaderField != $prevSubheader)
	{
		$subheaderText = subheaderText($movie, $subheaderField);
		echo "    <tr>\n      <td>&nbsp;</td>\n      <td class='section'>$subheaderText</td>\n    </tr>\n";
		$prevSubheader = $movie->$subheaderField;
	}

	echo "    <tr class='movies'>\n      <td class='rank_icons'>\n";
	if ($movie->rank <= 0)
		echo "        <span class='iconlet_gap'>&nbsp;</span>\n";
	else
		iconMarkup($movie);

	echo "      </td>\n      <td class='index_title'>\n";
	if ($movie->rank <= -10)
	{
	    $referee = $movies[$movie->page][$movie->sequelYear];
	    echo "        <span class='" . ($referee->rank > 0 ? 'reviewed' : 'unreviewed');
		echo "'\n            >$movie->title</span> &mdash; <small>SEE</small> ";
		htmlIndexTitle($referee);
	}
	else
	{
	    if ($whichPage == 'CATEGORIES' && $movie->sequelYear > 0)
    	    echo "        &emsp;&ndash;&ensp;\n";
		htmlIndexTitle($movie);
	    if ($whichPage != 'CATEGORIES' && $movie->category == 6)
    	    echo "<span class='asterisk'>&nbsp;*</span>\n";
		else
			echo "\n";
	}
	echo "      </td>\n    </tr>\n";
}
?>
  </table>
<?php
if ($whichPage != 'CATEGORIES' && $whichPage != 'MCU')
{
?>
  <p class='index_note'>
    <span class='asterisk'>&nbsp;*</span> =&ensp;imitation comic book character, not from an actual comic book
  </p>
<?php
}
?>
</div>
