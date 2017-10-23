<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta charset="utf-8" />
		<link rel="stylesheet" type="text/css" href="/scc/icsf/resources/style.css" />
		<link href="//fonts.googleapis.com/css?family=Lora|Alice" rel="stylesheet" type="text/css" />
		<link rel="icon" href="/scc/icsf/resources/logo.png" />
		<title>New Library Items - ICSF</title>
	</head>
	<body>

		<h1>
			<a id="logo" href="/scc/icsf/">
				<img src="/scc/icsf/resources/logo.png" alt="ICSF Logo" width="93" height="60" />
			</a>
			ICSF
			<span id="subtitle">
			New Items in the Library
			</span>
		</h1>

		<nav>
			<a href="/scc/icsf/">Home</a>
			<a href="/scc/icsf/events/">Events</a>
			<a href="/scc/icsf/library/">Library</a>
			<a href="/scc/icsf/committee/">Committee</a>
			<a href="/scc/icsf/publications/">Publications</a>
			<a href="/scc/icsf/picocon/">Picocon</a>
			<a href="/scc/icsf/social/quotes/">Quotes</a>
			<a href="/scc/icsf/social/gallery/">Gallery</a>
			<a href="/scc/icsf/history/">History</a>
			<a href="/scc/icsf/steampunk/">Steampunk</a>			<hr/>
			<h3>Library</h3>
			<a href="/scc/icsf/library/getting-to.html">Finding Us</a>
			<a href="/scc/icsf/library/search.php">Database Search</a>
			<a href="/scc/icsf/library/new-items.php">New Items</a>
			<a href="/scc/icsf/library/request-list.php">Request List</a>
			<a href="/scc/icsf/library/bookshops.html">Bookshops</a>
		</nav>
<?php

	if ('UTC' === date_default_timezone_get()
	{
		date_default_timezone_set('UTC');
	}

	function pageLinks($format, $curr, $max)
	{
		$max   = (int)$max;
		if ($max <= 1) return;
		$curr  = (int)$curr;
		$links = array(1, 2, 3, $max, $max - 1, $max - 2, $curr, $curr - 1, $curr + 1);
		$links = array_unique($links);
		$links = array_filter($links, function ($i) use($max) { return (0 < $i) && ($i <= $max); });

		sort($links, SORT_NUMERIC);
		$last = 0;

		foreach ($links as $link)
		{
			echo (($link - $last) !== 1) ? ' ... ' : ' ';
			printf($format, $link);
			$last = $link;
		}
	}

	require('database/database.inc');

	$page = (isset($_GET['page']) ? (int)$_GET['page'] : 1);

	$types = Database::getItemTypes();
	$items = Database::latest($page);

	$total = 0;
	foreach ($types as $type)
		$total += $type->count;

	// TODO: get total pages from sum of item types
	pagelinks('<a href="/scc/icsf/library/new-items.php?page=%1$d">%1$d</a>', $page, ceil($total / 50));

	$month = '';
	$cutoff = mktime(0,0,0,1,1,2005);

	foreach ($items as $item)
	{
		$item->AcquireDate = strtotime($item->AcquireDate);
		if (date('Ym', $item->AcquireDate) !== $month)
		{
			$header = ($item->AcquireDate < $cutoff ?
	            'Back in the mists of time... (pre-Apr 05)' :
				date('F Y', $item->AcquireDate)
			);

			if ($month != '')
				echo tabs(2) . '</ul>' . PHP_EOL;

			echo PHP_EOL . tabs(2) . '<h2>' . $header . '</h2>' . PHP_EOL . tabs(2) . '<ul>' . PHP_EOL;

	        $month = date('Ym', $item->AcquireDate);
	    }

		echo tabs(3) . '<li>' . $item->Author . ' &nbsp;&mdash; &nbsp;<em>&ldquo;' . $item->Title .
			'&rdquo;</em>';

		if (!empty($item->Series))
			echo ' &nbsp;&mdash; &nbsp;' . $item->Series . ': ' . $item->SeriesNum;

		if ((int)$item->TypeKey !== 1)
			echo ' &nbsp;(<em>' . $types[$item->TypeKey]->name . '</em>)';

		echo '</li>' . PHP_EOL;
	}
	echo tabs(2) . '</ul>'. PHP_EOL;

	function tabs($count)
	{
		return str_repeat("\t", $count);
	}

	pagelinks('<a href="/scc/icsf/library/new-items.php?page=%1$d">%1$d</a>', $page, ceil($total / 50));

?>
		<footer>
			<p class="copyright">
			Imperial College Science Fiction Society. Please report issues to
			<a href="mailto:techpriest@icsf.org.uk">techpriest@icsf.org.uk</a>
			</p>
		</footer>

	</body>
</html>
