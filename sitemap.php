<?php

require './MovieData.php';

function earliestInSeries(&$movieSeries)
{
	$minYear = 9999999;
    $return = null;
	foreach ($movieSeries as &$movie)
		if ($movie->year > 0 && $movie->year < $minYear && $movie->rank > -10)
		{
			$minYear = $movie->year;
			$return = $movie;
		}
    return $return;
}

header('Content-Type: text/plain; charset=utf-8');
?><?=SITE?>/
<?=SITE?>/ALPHABETIC
<?=SITE?>/CATEGORIES
<?=SITE?>/YEAR
<?=SITE?>/MCU
<?=SITE?>/introduction
<?=SITE?>/bestworst
<?=SITE?>/ratings
<?=SITE?>/serials
<?=SITE?>/tv
<?php
	foreach ($movies as &$series) {
		$first = earliestInSeries($series);
		echo SITE . "/{$first->page}\n";
		foreach ($series as &$movie)
			if ($movie != $first && $movie->rank > -10)
				echo SITE . "/{$movie->page}-{$movie->year}\n";
	}
?>