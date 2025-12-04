<?php
/*
TODO:	make sure comments are float-cleared past the panel image, dang it
		convert alternate titles to string array property (Argoman has four titles)
		time to start using flexbox instead of tables? (see https://github.com/philipwalton/flexbugs for issues to avoid)
		stop using forty-nothings in layout
		capejeer.com//foo/ fails to 404... looks like $_SERVER gets misinitialized!  (maybe newer PHP version could fix?)
		remove separate index.php for legacy urls? nah

(TODO list for content: see MovieData.php)

This page implements all the movie review pages on capejeer.com, via a "FallbackResource"
rule in .htaccess which uses this to handle all paths that don’t match a physical file.
We cover the following URL paths, most being compatible with older versions of the site.
These paths are divided into three categories: indexes, supplements, and movies.
After this master page takes care of all the common headers and navigation, the main
content area is passed off to various PHP sub-pages according to the URL received:

	These are simple static includes of html content into our template:
		introduction
		bestworst
		ratings
		serials
		tv
		...any of the above followed by .html, for compatibility
		...and for deeper historical compatibility, the same names preceded by underscores.

	MovieIndex.php generates the content for these urls:
		ALPHABETIC
		CATEGORIES
		RANK
		YEAR
		MCU                        (later addition)
		...any of the above followed by .html, for compatibility
		...and for deeper historical compatibility, the same names preceded by underscores.
		index.html or index.php    (synonym for RANK, which is also our default page if no path is given)

	and Review.php handles these, by looking for a movie that matches the URL:
		<pagename>                 (where <pagename> is alphanumeric with hyphens, and found in $movies)
		<pagename>-<year>          (where <year> is four digits, and found in $movies[pagename] -- newer syntax)
		<pagename>#anchor          (outdated syntax handled by writing out javascript to redirect)
		...any of the above followed by .html (before the anchor in the last case)

Any other URL that lands on this page will produce a 404 (with our site banner on it).
*/


$dumpty = "";

require './MovieData.php';


function earliestInSeries(&$movieSeries)
{
	$minYear = 9999999;
    $return = null;
	foreach ($movieSeries as &$movie)
		if ($movie->year > 0 && $movie->year < $minYear)
		{
			$minYear = $movie->year;
			$return = $movie;
		}
    return $return;
}

function depunct($str)
{
    return str_replace('  ', ' ', preg_replace('/[^A-Za-z0-9 -]/', ' ', $str));
}


$title = "";
$keywords = "";
$isIndex = false;
$show404 = false;
$redirect = null;
$pageName = null;
$year = 0;
$theMovie = null;
$commentsType = 'inline';                   // which style of comments to support... other than inline, most have shortcomings
$cmtx_folder = '/commentix/';               // this is a relative path serverside for inline, clientside for iframe
$commentsHost = SITE;                       // can leave blank if inline or if on same domain (the usual case)

/* Notes on types of comments:
    -  'inline' requires that Commentics be hosted on the current domain; when available it's the best option
    -  'iframe' allows Commentics to be hosted elsewhere, but has some problems not resolved yet
    -  'proxy' gets around the current-domain issue by wrapping someone else's Commentics install, but has CORS issues
    - ('hash' would use HashOver 2 instead of Commentics, but it's not implemented here yet as it's feature-poor)
    -  'none', or any unrecognized string, deactivates comments.  */

$reqUrl = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$whichPage = strtolower(substr(strrchr($reqUrl, '/'), 1));
$subfolder = $whichPage ? substr($reqUrl, 0, -strlen($whichPage)) : $reqUrl;
if (DEBUG) $dumpty .= "Url is '{$_SERVER['REQUEST_URI']}', so subfolder is '$subfolder' and whichPage is '$whichPage'.\n";
if (!is_dir($_SERVER['DOCUMENT_ROOT'] . $subfolder))
    $show404 = true;
if (DEBUG) $dumpty .= "Is '" . $_SERVER['DOCUMENT_ROOT'] . $subfolder . "' a directory?  " . !$show404 . "\n";

if ($whichPage == 'index.html' || $whichPage == 'index.php' || $whichPage == '')
	$whichPage = 'rank';						// our default page

if (strlen($whichPage) > 5 && substr_compare($whichPage, '.html', -5) === 0)
	$whichPage = substr($whichPage, 0, -5);		// discard the optional .html
else if (strrchr($whichPage, '.'))
	$show404 = true;

if (!$show404)
{
	switch ($whichPage)
	{
		// Is it a known index or supplement name?
		case 'alphabetic':
		case '_alphabetic':
			$isIndex = true;
			$title = 'Index of Movies by Name';
			break;
		case 'categories':
		case '_categories':
			$isIndex = true;
			$title = 'Index of Movies by Category of Origin';
			break;
		case 'rank':
		case '_rank':
			$isIndex = true;
			$title = 'Index of Movies by Rank';
			break;
		case 'year':
		case '_year':
			$isIndex = true;
			$title = 'Index of Movies by Release Date';
			break;
		case 'mcu':
		case '_mcu':
			$isIndex = true;
			$title = 'Index of Marvel<sup>&reg;</sup> Cinematic Universe movies by Release Date';
			break;
		case 'introduction':
		case '_introduction':
			$title = 'Introduction';
			break;
		case 'bestworst':
		case '_bestworst':
			$title = 'Bests and Worsts';
			break;
		case 'ratings':
		case '_ratings':
			$title = 'The Rating System';
			break;
		case 'serials':
		case '_serials':
			$title = 'Old Serials';
			break;
		case 'tv':
		case '_tv':
			$title = 'TV Movies and Shows';
			break;
		// It's not a known static page.  See if it's a movie.
		default:
			if (preg_match('/^([A-Za-z0-9_-]+?)(|-\d\d\d\d)$/', $whichPage, $parsedMatches) == 1)
			{
				$pageName = $parsedMatches[1];
				if (isset($movies[$pageName]))
				{
					if (strlen($parsedMatches[2]) == 0)				// no year specified
					{
						// When given a bare page name, we show the start of the series.
						$theMovie = earliestInSeries($movies[$pageName]);
						if (!$theMovie)
							$show404 = true;			// should not happen: page contains no years
						else
							$year = $theMovie->year;
					}
					else
					{
						$year = -(int) $parsedMatches[2];	// matched substring includes hyphen, so negate
						if ($year > 0 && isset($movies[$pageName][$year]))
							$theMovie = $movies[$pageName][$year];
						else
						{
							if ($pageName == 'x-men' && isset($movies['wolvie'][$year]))	// some of these got moved to a new page
								$redirect = 'wolvie-' . $year;
							else
								$show404 = true;		// no such year for this page
						}
					}
				}
				else
					$show404 = true;					// not a known page name
			}
			else
				$show404 = true;						// unrecognized url format
	}
	if ($isIndex)
		$whichPage = strtoupper($whichPage);
	else if (isset($theMovie))
		$title = $theMovie->title;
	if (!$show404 && $whichPage[0] == '_' && !isset($theMovie))
		$whichPage = substr($whichPage, 1);
}
if (DEBUG) $dumpty .= "After trying to recognize whichPage ('$whichPage'), isIndex is $isIndex and the title is '$title'.\n";
if ($whichPage == 'RANK' || $whichPage == '_RANK')
	$canonical = SITE . '/';
else if (isset($theMovie) && $theMovie == earliestInSeries($movies[$pageName]))
	$canonical = SITE . '/' . $pageName;
else
	$canonical = SITE . '/' . $whichPage;
// we have to use https: because Gloogle now refuses to accept http: as valid for indexing!  dumbasses...

// Time to start emitting our response.
header('Content-Type: text/html; charset=utf-8');
if (!PRODUCTION) header('X-Robots-Tag: noindex');
if ($show404)
{
	$title = 'page not found';
	http_response_code(404);				// see also sitemap.php
}
else if ($redirect)
{
	$absolute = parse_url($_SERVER['REQUEST_URI']);
	$redirect = $absolute['scheme'] . $absolute['host'] . $subfolder . $redirect;
	header('Location: ' . $redirect);
	die();
}
else if (isset($theMovie))
{
	if ($commentsType === 'inline')
		session_start();
	$keywords = ", " . depunct($theMovie->title);    // todo: add director keyword
	$parent = earliestInSeries($movies[$theMovie->page]);
	if ($parent && $parent !== $theMovie)
		$keywords .= ", " . depunct($parent->title);
	// XXX  add a keywords member to the movie struct?  while at it, add starring field?
}
$doValidation = $isIndex;

// =====================   Here comes our master page template:
?><!DOCTYPE html>
<html>
<head>
  <meta charset='utf-8' />
  <meta name='viewport' content='width=device-width' />  <!-- keep in sync with CSS @viewport, if used -->
<?php if (!$show404) {
    echo "  <link rel='canonical' href='$canonical' />\n";
	if (isset($theMovie) && $commentsType === 'iframe')
    	echo "  <script src='{$commentsHost}{$cmtx_folder}embed.js'></script>\n";
} ?>
  <link rel='stylesheet' type='text/css' href='https://fonts.googleapis.com/css?family=News+Cycle:700%7CFredoka+One%7CSniglet%7CSpectral:400,400i,600i,700&amp;subset=latin,latin-ext'>
  <link rel='stylesheet' type='text/css' href='<?= file_exists($_SERVER['DOCUMENT_ROOT'] . $subfolder . 'capejeer.css') ? '' : '/' ?>capejeer.css' />
  <script src='sequelae.js'></script>
  <title>CAPE JEER: <?= preg_replace('/<sup>&reg;<\/sup>/', '', $title) ?></title>
  <meta name='keywords' content='cape jeer, paul kienitz, superhero movies, superheroes, comics, comic boox movies, film, dc, marvel<?=$keywords?>' />
  <link rel="icon" type="image/png" href="/img/favicon-96x96.png" sizes="96x96" />
  <link rel="icon" type="image/svg+xml" href="/img/favicon.svg" />
  <link rel="shortcut icon" href="/img/favicon.ico" />
  <link rel="apple-touch-icon" sizes="180x180" href="/img/apple-touch-icon.png" />
  <meta name="apple-mobile-web-app-title" content="Cape Jeer" />
  <link rel="manifest" href="/img/site.webmanifest" />
</head>

<body>
  <header>
<?php if (strlen($subfolder) > 1 && !$show404) { ?>
    <div style='position: absolute; left: 100px; top: 20px; z-index: 1; font-size: 70px; color:#e7ffe7; text-shadow: 2px 2px 12px #000000;'>
        <?=$subfolder?>    <!-- so I can always see if I'm looking at test or prod -->
    </div>
<?php } ?>
    <div class='banner-back'>
      <img id='thebanner' style='display: block;' src='/img/capejeerbanner.png' width='700' height='177'
           alt='Cape Jeer: a cynical survey of superhero cinema' title='' />
    </div>
    <div class='banner-under'>
      <img style='display: block;' src='/img/banner-background-down.png' alt='' />
    </div>
  </header>

<?php if ($show404) { ?>
  <div class="columns review" style="padding: 3em 0 15em 0;">
    <div style="padding: 0 40px 0 40px;">
      <h1>404 - Page Not Found</h1>
      <p>The movie review or other page that you have attempted to reach,
         &ldquo;<?=$reqUrl?>&rdquo;, does not exist on this
         site.&ensp;To see a list of available content pages at <?=SITE?>,
         <a href='<?=RELATIVEPATH ?: '/'?>'>click here</a>.</p>

<?php
// DEBUG output is usable in prod if needed, without being visible; you need to use browser dev tools to see it
if ($debugLoad) echo "<p class=debug" . (PRODUCTION ? " style='display: none'" : "") . ">$debugLoad</p>\n";
if ($dumpty) echo "<p class=debug" . (PRODUCTION ? " style='display: none'" : "") . ">$dumpty</p>\n";
?>
    </div>
  </div>

<?php } else {  // Here comes the body of the page, with nav and content: ?>

<?php require './NavMenuBar.php'; ?>

  <table id='nonheader' class='columns'>
    <tr class='tr_columns'>   <!-- hashtag giveupandusetables -->
      <td id='collapsar' class='indexx'> <!-- onclick='navvyOneWay(event)' -->

<?php require './NavColumn.php'; ?>

      </td>
      <td class='td_hardbody'>
        <main>
          <div id='includer' class='hardbody'>

<!-- ================ BEGIN BODY OF PAGE CONTENT... -->

<?php

	if ($isIndex)
		require './MovieIndex.php';
	else if (isset($theMovie))
		require './Review.php';
	else
		require "./$whichPage.html-content";
?>

<!-- ================ END BODY OF PAGE CONTENT... -->

<?php
// DEBUG output is usable in prod if needed, without being visible; you need to use browser dev tools to see it
// (WE omit $debugLoad from normal output... use the 404 page to view it)
if ($dumpty) echo "<p class=debug" . (PRODUCTION ? " style='display: none'" : "") . ">$dumpty</p>\n";
?>
          </div>
        </main>
      </td>
    </tr>
  </table>
<?php } ?>

</body>
</html>
