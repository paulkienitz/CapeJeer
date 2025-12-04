<?php

// It sure would be nice if this could be edited in a spreadsheet instead of in source code.
// I didn't do it that way because it would add database query or IO costs to every page load,
// to read data that is the same every time.  What I really want is to persist it in memory.

// ------------------- GOOD MOVIES

	addMovie([
			  'rank'       => 990,
			  'capes'      => 8,
			  'tents'      => 1,
			  'heads'      => 2,
			  'deconst'    => 2,
			  'category'   => 6,
			  'group'      => 1,
			  'year'       => 1982,
			  'sequelYear' => 0,
			  'imdb'       => 'tt0000000',
			  'title'      => 'The Interesting Four',
			  'director'   => 'Rinse Dream',
			  'page'       => 'interesting4',
			  'anchor'     => '',
			  'boxImage'   => 'interestin.jpg',
			  'panelImage' => '',
			  'quote'      => 'SNL does another full-length movie based on a skit, with Seiko, Weather Woman, The Human Stapler, and Mr. Wonderful facing the evil Council of Communist Polluters.&ensp;Amazingly, the result is a masterpiece.',
			  'published'  => '2010-01-01'
			]);
	if ($debugLoad) $debugLoad .= '<br/>first movie loaded.';		/* only happens if MovieData.php has already logged something */

	addMovie([
			  'rank'       => 800,
			  'capes'      => 7,
			  'tents'      => 1,
			  'heads'      => 2,
			  'deconst'    => 2,
			  'category'   => 5,
			  'group'      => 1,
			  'year'       => 2011,
			  'sequelYear' => 0,
			  'imdb'       => 'tt0000011',
			  'title'      => 'Albert Oop, Jr',
			  'director'   => '',
			  'page'       => 'alleyoop',
			  'anchor'     => '',
			  'boxImage'   => 'albertoop.jpg',
			  'panelImage' => '',
			  'quote'      => 'The premise here is that one of the caveman Alley Oop&rsquo;s children, transported to the present day by Professor Wonmug&rsquo;s time machine, has become a savvy New Yorker who gains an edge in the urban hustle from his hunter-gatherer upbringing.',
			  'published'  => '2013-06-27'
			]);

// ------------------- OKAY MOVIES

	addMovie([
			  'rank'       => 710,
			  'capes'      => 6,
			  'tents'      => 1,
			  'heads'      => 2,
			  'deconst'    => 0,
			  'category'   => 5,
			  'group'      => 2,
			  'year'       => 2013,
			  'sequelYear' => 0,
			  'imdb'       => 'tt0000009',
			  'title'      => 'Steve Roper',
			  'director'   => 'Paul Greengrass',
			  'page'       => 'roper',
			  'anchor'     => '',
			  'boxImage'   => 'steveroper.jpg',
			  'panelImage' => '',
			  'quote'      => 'Long ago, reporter Steve Roper was a newspaper comic stable, until his strip was taken over by side character Mike Nomad.&ensp;Nobody remembers Roper, but he&rsquo;s been revived here in a surprisingly mature and effective film, played completely straight, though with a certain stuffy artiness.',
			  'published'  => '2015-03-02'
			]);

	addMovie([
			  'rank'       => 630,
			  'capes'      => 5,
			  'tents'      => 1,
			  'heads'      => 1,
			  'deconst'    => 1,
			  'category'   => 1,
			  'group'      => 2,
			  'year'       => 2017,
			  'sequelYear' => 0,
			  'imdb'       => 'tt0000006',
			  'title'      => 'Ultimate Batman',
			  'director'   => 'Wes Anderson',
			  'page'       => 'batman',
			  'anchor'     => 'ult',
			  'boxImage'   => 'ultimatebat.jpg',
			  'panelImage' => '',
			  'quote'      => 'What is this, the fifth or sixth Batman continuity in movies now?&ensp;Through some kind of corporate IP brand tie-in nonsense, his arch-enemy here is Skeletor.',
			  'published'  => '2011-01-01'
			]);

// ------------------- LAME MOVIES

	addMovie([
			  'rank'       => 430,
			  'capes'      => 3,
			  'tents'      => 1,
			  'heads'      => 0,
			  'deconst'    => 1,
			  'category'   => 6,
			  'group'      => 3,
			  'year'       => 1984,
			  'sequelYear' => 1982,
			  'imdb'       => 'tt0000001',
			  'title'      => 'The Interesting Four Point Two',
			  'director'   => 'McG',
			  'page'       => 'interesting4',
			  'anchor'     => 'poin2',
			  'boxImage'   => 'interestinger.jpg',
			  'panelImage' => '',
			  'quote'      => 'SNL&rsquo;s astonishingly profound and layered Interesting Four feature gets a sequel every bit as lame as the original was expected to be.',
			  'published'  => '2011-01-01'
			]);

// ------------------- REALLY BAD MOVIES

	addMovie([
			  'rank'       => 230,
			  'capes'      => 2,
			  'tents'      => 1,
			  'heads'      => 0,
			  'deconst'    => 1,
			  'category'   => 1,
			  'group'      => 4,
			  'year'       => 2011,
			  'sequelYear' => 0,
			  'imdb'       => 'tt0000003',
			  'title'      => 'The Slouching Dead',
			  'director'   => 'Mikey Moore Jr.',
			  'page'       => 'slouchingdead',
			  'anchor'     => '',
			  'boxImage'   => 'slouch.jpg',
			  'panelImage' => '',
			  'quote'      => 'When the dead come back to life, they only want to sit in front of the TV with a beer.&ensp;One-joke comedy, or one-joke social satire, rather dull either way.&ensp;Certainly not what I expected from the pen of the legendary Alan Moore.',
			  'published'  => '2017-03-05'
			]);

	addMovie([
			  'rank'       => 50,
			  'capes'      => 1,
			  'tents'      => 4,
			  'heads'      => 1,
			  'deconst'    => 2,
			  'category'   => 6,
			  'group'      => 4,
			  'year'       => 1982,
			  'sequelYear' => 0,
			  'imdb'       => 'tt0000002',
			  'title'      => 'Art-Man',
			  'director'   => 'Portland People&rsquo;s Percipience Party, Picture Production Platoon',
			  'page'       => 'artman',
			  'anchor'     => '',
			  'boxImage'   => 'artman.jpg',
			  'panelImage' => '',
			  'quote'      => 'Is this an earnest attempt by a bunch of well-meaning fanatics to satirize commercial art, or a scathing parody of the art scene?&ensp;No one is telling &mdash; a grotesque failure regardless.',
			  'published'  => '2017-03-05'
			]);

// ------------------- NOT SEEN YET

	addMovie([
			  'rank'       => 0,
			  'capes'      => 0,
			  'tents'      => 0,
			  'heads'      => 0,
			  'deconst'    => 0,
			  'category'   => 1,
			  'group'      => 5,
			  'year'       => 1999,
			  'sequelYear' => 0,
			  'imdb'       => 'tt0000013',
			  'title'      => 'Yarasa-Adam',
			  'director'   => 'Joey Karacada&#287;',
			  'page'       => 'batman',
			  'anchor'     => '',
			  'boxImage'   => 'turkishbat.jpg',
			  'panelImage' => '',
			  'quote'      => 'Commonly known as &ldquo;Turkish Batman&rdquo;, this low budget ripoff may or may not be a laff riot.',
			  'published'  => '2015-03-30'
			]);

	addMovie([
			  'rank'       => 0,
			  'capes'      => 0,
			  'tents'      => 0,
			  'heads'      => 0,
			  'deconst'    => 0,
			  'category'   => 2,
			  'group'      => 5,
			  'year'       => 2012,
			  'sequelYear' => 0,
			  'imdb'       => 'tt0848228',
			  'title'      => 'The Avengers',
			  'director'   => 'Joss Whedon',
			  'page'       => 'avengers',
			  'anchor'     => '',
			  'boxImage'   => 'avengers.jpg',
			  'panelImage' => 'theavengers.jpg',
			  'quote'      => 'Apparently this team-up movie is supposed to be a big deal.',
			  'mcu'        => true,
			  'published'  => '2013-07-30'
		     ]);

// ------------------- POINTLESS SEQUELS NOT SEEN YET


	addMovie([
			  'rank'       => -2,
			  'capes'      => 0,
			  'tents'      => 0,
			  'heads'      => 0,
			  'deconst'    => 0,
			  'category'   => 6,
			  'group'      => 6,
			  'year'       => 1994,
			  'sequelYear' => 1982,
			  'imdb'       => 'tt0000022',
			  'title'      => 'Art-Man and Blue Period',
			  'director'   => 'Portland People&rsquo;s Percipience Party, Picture Production Platoon',
			  'page'       => 'artman',
			  'anchor'     => 'blue',
			  'boxImage'   => 'artman2.jpg',
			  'panelImage' => '',
			  'quote'      => 'Footage was shot for an Art-Man sequel, but the project was shelved midway.&ensp;Years later, someone cobbled together a semi-finished cut, but shows it only at private events.',
			  'published'  => '2017-03-17'
			]);
	if ($debugLoad) $debugLoad .= '<br/>the last movie is loaded.';


// We want to preserve our ability to send HTTP headers in the event of an error,
// so we have no newline after this closing angle bracket. ?>