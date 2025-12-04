<?php
/*
This data was originally stored in a tab-separated text file.  I decided I
didn’t want to reload such a file every time, or to mess with getting the data
in and out of a MySQL table.  So I’m just going to store it more or less
declaratively, for the easiest usage.  As a plus, this format is easier to edit.
And as far as I can see, the only available facility the host is offering to
cache any of this state in server RAM is via the engine’s cacheing of compiled
PHP code, so this may also be the most efficient means of loading it.
To use this, just go:
	require './MovieData.php';

The format is nested associative arrays.  The outer level ($movies) is indexed by
series or character nickname, a.k.a. "page name" -- that is, by a name such as
"spider-man" which in the old Cape jeer layout was the name of the .html file
that related movies appeared in, and which in the new is a base name to which a
year is appended to form the review’s url path.  (The year is optional in the url
for the oldest movie within a series.)  The second level is indexed by year.  So
far, the combination of page (which corresponds to a character, series, or franchise)
plus year has always been sufficient to uniquely specify an individual movie.
(If this ever changes, we might have to use year values with decimal points, or
something.  Or append the anchor, if we don’t just remove that field.)

The inner level, which is indexed by year, contains objects of class Movie.  We
initialize these objects with string-indexed arrays.  The fields are:

    integer: rank, capes, tents, heads, serious (not used yet), deconst,
	         category (pseudo-enum), group (pseudo-enum), year, sequelYear

    string:  title, page, anchor, boxImage, panelImage, quote, rating,
	         director, imdb (number with leading "tt")

    date:    published (initialize with ISO format string, YYYY-MM-DD)

    boolean: isRemake (calculated), mcu, isPrequel

    obj ref: series (a backlink to the containing inner dictionary),
	         *** ADD THIS:  altTitles (either null or an array of strings)

Strings in the title, altTitles, director, and quote fields must have all special
characters (quote marks, tag delimiters, ampersands, non-USASCII, etc) represented
in safe HTML entity form.  (These source files should use no literal non-USASCII
characters in general, except in comments like this one.)  The other string fields
should contain only basic characters, such as alphanumerics and hyphens and periods.
The boolean fields mcu and isPrequel can be omitted from the initialization and
default to false.  The rating field can also be omitted and defaults to "PG-13".
The published field is omitted for the earliest reviews and defaults to 1/1/05.
(In many later cases where the publication date is lost, I used 1/1/10.)

In the text file, the ordering was by rank, which made it easy to keep the
rankings straight.  We preserve that order here in the sequence that we add the
movies to the $movies nested array.

Positive rank values should be unique, but nonpositive ranks are not.  They have
special meaning.  Zero means "not seen", -1 means "not released yet", -2 means
"seen but not yet reviewed" (a value I will just continue to display as "not
seen", at least for now), and -3 means "never released" (a value not currently
in use for any film, though it has been used in the past for e.g. Roger Corman's
Fantastic Four).  And there’s a special behavior when rank is -10 or less: this
record is then an alternate name for a movie.  In this case, the year is zero,
the sequelYear is the year of the film pointed to, the page is likewise that of
the film pointed to, and the quote field contains the other film’s title.  (Note
that the code currently does not support having two alternate names for one
movie.  I should just support alternate titles within the Movie class itself.)

The category and group values might better be enums.  The meanings of the values
in the category field are: 1 = DC Comics, 2 = Marvel Comics, 3 = other adventure
comics, 4 = comedic comics, 5 = newspaper comics, and 6 = imitation comic book
characters.  The meanings of the group values are: 1 = good movies, 2 = okay
movies, 3 = lame movies, 4 = really bad movies, 5 = not seen yet (or not
reviewed yet), and 6 = pointless sequels not seen yet.

The anchor field is mainly for support of historical URLs.  It is always empty
for the first film with a given page value, and unique per page for others.
*/

define('DOMAIN',       'capejeer.com');
define('RELATIVEPATH', '');				        /* NO TRAILING SLASH; if at root of domain, use empty string */
define('SITE',         'https://' . DOMAIN . RELATIVEPATH);
define('SITENAME',     'Cape Jeer');
define('AUTHOR',       'Paul Kienitz');			/* if multiple authors on site, this will not suffice */
define('AUTHOR_ALT',   'Supersonic');			/* empty string if not needed */
define('DEBUG',        true );					/* emit debugging breadcrumbs for page rendering */
define('DEBUGLOAD',    true );					/* the same but for initial data loading (use 404 page to see report) */
define('VALIDATE',     true );					/* verify data load integrity; throw exceptions for anything bad */
define('PRODUCTION',   false);					/* allow search engines etc, and hide any debug output with display:none */

$debugLoad = "";

class Movie
{
	public $rank;
	public $capes;
	public $tents;
	public $heads;
	public $serious;			// future?
	public $deconst;
	public $category;
	public $group;
	public $year;
	public $sequelYear;
	public $imdb;
	public $title;
	public $director;
	public $page;
	public $anchor;
	public $boxImage;
	public $panelImage;
	public $quote;
	public $rating;				// optional, default 'PG-13'
	public $mcu;				// optional, default false
	public $published;			// optional -- ancient entries default to 2005-01-01
	public $isRemake;			// a computed value which is set later
	public $series;             // set when added to a page collection

	// We pass in an array instead of individual parameters because I want each value
	// to be explicitly named, and passing by name isn’t a thing yet as of PHP 7.
	function __construct(array &$arr)
	{
		$this->isRemake = false;
		$this->isPrequel = false;
		$this->mcu = false;
		$this->rating = 'PG-13';
		$this->serious = 0;
		$this->published = '2005-01-01';		// for now, don't bother converting to DateTime
		foreach ($arr as $property => &$value)
		{
			$this->$property = &$value;
		}
	}

	function validate()
	{
		foreach ($this as $field => &$value)
			if (!isset($this->$field))
				throw new InvalidArgumentException("Failed to initialize Movie->$field");
			else
				switch ($field)
				{
					case 'title': case 'page': case 'anchor': case 'boxImage': case 'panelImage':
					case 'director': case 'published': case 'quote': case 'rating': case 'imdb':
						if (!is_string($this->$field))
							throw new InvalidArgumentException("Field $field has nonstring value");
						break;
					case 'isRemake': case 'isPrequel': case 'mcu':
						break;
					case 'series':
						break;
					default:
						if (!is_int($this->$field))
							throw new InvalidArgumentException("Field $field has noninteger value '$value'");
				}
		if (strlen($this->title) == 0)
			throw new InvalidArgumentException("Title is empty for a movie in {$this->page}");
		if (strlen($this->page) == 0)
			throw new InvalidArgumentException("Page name is empty for {$this->title}");
		if ($this->capes < 0 || $this->capes > 8)
			throw new InvalidArgumentException("{$this->page}-{$this->year} has {$this->capes} half-capes.");
		if ($this->tents < 0 || $this->tents > 6)
			throw new InvalidArgumentException("{$this->page}-{$this->year} has {$this->tents} half-tents.");
		if ($this->heads < 0 || $this->heads > 2)
			throw new InvalidArgumentException("{$this->page}-{$this->year} has {$this->heads} half-heads.");
		if ($this->deconst < 0 || $this->deconst > 2)
			throw new InvalidArgumentException("{$this->page}-{$this->year} has {$this->deconst} half-balls.");
		if ($this->category < 0 || $this->category > 6)
			throw new InvalidArgumentException("{$this->page}-{$this->year} is in category {this->category}.");
		if ($this->group < 0 || $this->group > 6)
			throw new InvalidArgumentException("{$this->page}-{$this->year} is in group {this->group}.");
		if (strlen($this->anchor) > 0 && isset($movies[$this->page]))
			foreach ($movies[$this->page] as &$othermv)
				if ($this !== $othermv && $this->anchor == $othermv->anchor)
					throw new InvalidArgumentException("Anchor '$this->anchor' reused in $this->page");
	}

	// public methods

	function url()
	{
		return $this->page . ($this->anchor == "" ? "" : "-" . $this->year);       // this is a relative url
	}

	function jsonMetadata()							// *** THIS IS DEPRECATED
	{
		$imdbBase = 'https://www.imdb.com/title/';
		$data = [
		         '@context'      => 'http://schema.org/',
		         '@type'         => 'Review',
		         'url'           => SITE . '/' . $this->url(),		// go ahead and report prod (canonical) even if this is test
		         'description'   => html_entity_decode($this->quote, ENT_HTML5),
		         'datePublished' => $this->published,
		         'author'        => [ '@type'         => 'Person',
		                              'name'          => AUTHOR ],
		         'publisher'     => [ '@type'         => 'Organization',
		                              'name'          => SITENAME ],
		         'inLanguage'    => 'en',
		         'itemReviewed'  => [ '@type'         => 'Movie',
		                              'name'          => html_entity_decode($this->title, ENT_HTML5),
		                              'copyrightYear' => $this->year,
		                              'sameAs'        => $this->imdb ? $imdbBase . $this->imdb . '/'
		                                                             : SITE . '/' . $this->url(),	// fallback, required field
		                              'director'      => [ '@type' => 'Person',
		                                                   'name'  => html_entity_decode($this->director, ENT_HTML5) ],
		                              'image'         => SITE . '/box/' . $this->boxImage ],
		         'reviewRating'  => [ '@type'         => 'Rating',
		                              'worstRating'   => 0,
		                              'bestRating'    => 4,
		                              'ratingValue'   => ($this->capes / 2) ]   /* ideally we would append ' capes'  */
		        ];
		// *** XXX TODO:  if ($this->altTitles) $data['itemReviewed']['alternateName'] = $this->altTitles;
		return json_encode($data);
	}

	function capeRatingMarkup($wholeicon, $halficon, $classy)
	{
		return ratingIcons($this->capes, $wholeicon, $halficon, $classy, "Cape",
		                   ratingText($this->capes, "Rating: ", "cape", 8));
	}

	function tentRatingMarkup($wholeicon, $halficon, $classy)
	{
		return ratingIcons($this->tents, $wholeicon, $halficon, $classy, "Tent",
		                   ratingText($this->tents, "Camp value: ", "tent", 6));
	}

	function deconstRatingMarkup($wholeicon, $halficon, $classy)
	{
		return ratingIcons($this->deconst, $wholeicon, $halficon, $classy, "Wrecking ball",
		                   ratingText($this->deconst, "Deconstruction: ", "wrecking ball"));
	}

	function headRatingMarkup($wholeicon, $halficon, $classy)
	{
		return ratingIcons($this->heads, $wholeicon, $halficon, $classy, "Balding head",
		                   ratingText($this->heads, "Maturity: ", "balding head"));
	}

	function condenseTitle($acronymic)							// for sorting purposes
	{
		$name = strtolower($this->title);
		if (substr($name, 0, 4) == "the ")
			$name = substr($name, 4);
		if ($acronymic && $name == "tmnt")						// the only common acronym recognized so far
			$name = "teenage mutant ninja turtles";
		$name = str_replace("300", "three hundred", $name);		// these are the only numbers found so far
		$name = str_replace("30", "thirty", $name);
		$name = str_replace(" iv", " 4", $name);
		$name = str_replace(" iii", " 3", $name);
		$name = str_replace(" ii", " 2", $name);
		return preg_split('/\\s*[^a-z&\'. -]+\\s*/', $name)[0];
	}
}		// class Movie


function ratingText($halves, $prefix, $name, $maxhalves = 0, $plural = "")
{
	return $prefix . genericNumericText($halves) . ' ' .
	       (!isPlural($halves) ? $name : (strlen($plural) > 0 ? $plural : $name . 's')) .
	       ($halves > 0 && $maxhalves > 2 ? ' (out of ' . genericNumericText($maxhalves) . ')' : '');
}

function ratingIcons($halves, $wholeicon, $halficon, $classy, $name, $title)
{
	$ret = "";
	for ($h = $halves; $h >= 2; $h -= 2)
		$ret .= "<img alt='$name' title='$title' src='$wholeicon' class='$classy'\n          />";
	if ($h > 0)
		$ret .= "<img alt='Half a " . strtolower($name) . "' title='$title' src='$halficon' class='$classy'\n          />";
	return $ret;
}


function genericNumericText($halves)
{
	switch ($halves)
	{
		case 0: return "zero";
		case 1: return "half a";
		case 2: return "one";
		case 3: return "one and a half";
		case 4: return "two";
		case 5: return "two and a half";
		case 6: return "three";
		case 7: return "three and a half";
		case 8: return "four";
		case 10: return "five";
		case 12: return "six";
		case 14: return "seven";
		case 16: return "eight";
		case 18: return "nine";
		case 20: return "ten";
		default: return "" . ($halves / 2);
	}
}


function isPlural($halves)
{
	return $halves <= 0 || $halves > 2;
}


static $addedCount = 0;

/*static*/ function addMovie(array $moviePropertyArray)
{
	global $addedCount, $movies, $debugLoad;
	$addedCount++;
	$movie = new Movie($moviePropertyArray);
	if (DEBUGLOAD)
		$debugLoad .= '<br/>#' . $addedCount . ': ' . $movie->title;
	if (!isset($movies[$movie->page]))
		$movies[$movie->page] = [];
	$movie->series = &$movies[$movie->page];
	$movies[$movie->page][$movie->year] = $movie;
	if (VALIDATE)
		$movie->validate();
}

/*static*/ function DO_NOT_addMovie(array $moviePropertyArray)
{
	// do nothing
}


/* miscellany for navigation -- first, for internal nav menus, then for external links: */
function pageLink($tag, $idprefix, $currentPage, $page, $title, $extraBefore = "", $linkOverride = "")
{
    return "<$tag id='$idprefix" . strtolower($page) . "'" .
           ($page == $currentPage ? " class='currentpage'" : "") .
		   ">$extraBefore<a href='" . ($linkOverride ?: $page) . "'>$title</a></$tag>";
}

function externalLink($tag, $url, $iconImage, $title, $attrTitle = "", $attrIcon = "")
{
	return "<$tag><a href='$url'><img class='icon' src='$iconImage' alt='' $attrIcon/></a>\n" .
	       "                      <a class='foldy' href='$url' $attrTitle>$title</a></$tag>\n";
}


// ========================================================  HERE WE GO: the movie data.

if (!isset($movies))      // idempotency
{
	if (DEBUGLOAD)
		$debugLoad = '$movies was not set, creating empty array.';
	$movies = [];

	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~   THE BIG INCLUDES:

	require './MovieDataLoading_SAMPLE.php';
    // (the real Cape Jeer has six include files here)


	// detect remakes... if they ever start making remakes with nonmatching titles, we'll have to add some static data
	foreach ($movies as $pageName => &$years)
		foreach ($years as $year => &$movie)
			foreach ($years as $otherYear => &$otherMovie)
				if ($movie !== $otherMovie && $movie->sequelYear == 0 && $otherMovie->sequelYear == 0 && $otherMovie->rank >= -3
					                       && $movie->condenseTitle(true) == $otherMovie->condenseTitle(true))
					$movie->isRemake = true;

	if (DEBUGLOAD)
		$debugLoad .= "<br/>addMovie called $addedCount times, yielding " . count($movies, 0) . " pages in the top level array; validation was " . (VALIDATE ? '' : 'not ') . 'performed.';
}
else
	if (DEBUGLOAD)
		$debugLoad = 'isset($movies) returned ' . isset($movies);

// To make sure we preserve our ability to send HTTP headers in the event of an error,
// we have no newline after this closing angle bracket. ?>