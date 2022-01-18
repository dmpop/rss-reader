<?php
$theme = "dark";
$title = "RSS reader";
$footer = "Read the <a href='https://dmpop.gumroad.com/l/php-right-away'>PHP Right Away</a> book";
$file = "feeds.txt";
$feed_cache = __DIR__ . '/feed_cache.html';
$feed_cache_expire = 900; // 15 minutes
?>

<!DOCTYPE html>
<html lang="en" data-theme="<?php echo $theme; ?>">

<!-- Author: Dmitri Popov, dmpop@linux.com
	 License: GPLv3 https://www.gnu.org/licenses/gpl-3.0.txt -->

<head>
	<title><?php echo $title; ?></title>
	<meta charset="utf-8">
	<link rel="shortcut icon" href="favicon.png" />
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="css/classless.css" />
	<link rel="stylesheet" href="css/themes.css" />
	<!-- Suppress form re-submit prompt on refresh -->
	<script>
		if (window.history.replaceState) {
			window.history.replaceState(null, null, window.location.href);
		}
	</script>
</head>

<body>
	<div class="card text-center">
		<div style="margin-top: 1em; margin-bottom: 1em;">
			<img style="display: inline; height: 2.5em; vertical-align: middle;" src="favicon.svg" alt="logo" />
			<h1 style="display: inline; margin-top: 0em; vertical-align: middle; letter-spacing: 3px;"><?php echo $title; ?></h1>
		</div>
		<hr style="margin-bottom: 1em;">
		<?php
		$refresh = time() - $feed_cache_expire;
		$feeds = file($file);
		$array_length = count($feeds);
		if (!file_exists($feed_cache) || filemtime($feed_cache) < $refresh) {
			for ($i = 0; $i < $array_length; $i++) {
				$content .= "<details>";
				$xml = simplexml_load_file(str_replace(PHP_EOL, "", $feeds[$i]));
				$root_element_name = $xml->getName();
				if ($root_element_name  == 'rss') {
					$content .= '<summary>' . htmlspecialchars($xml->channel->title) . '</summary>';
					$content .= "<ul>";
					foreach ($xml->channel->item as $item) {
						$content .= '<li style="font-size: 85%"><a href="' . htmlspecialchars($item->link) . '" target="_blank">' . htmlspecialchars($item->title) . "</a></li>";
					}
				} else if ($root_element_name  == 'feed') {
					$content .= '<summary>' . htmlspecialchars($xml->title) . '</summary>';
					$content .= "<ul>";
					foreach ($xml->entry as $entry) {
						$content .= '<li style="font-size: 85%"><a href="' . htmlspecialchars($entry->link['href']) . '" target="_blank">' . htmlspecialchars($entry->title) . "</a></li>";
					}
				}
				$content .= "</ul>";
				$content .= "</details>";
				file_put_contents($feed_cache, $content);
			}
			echo $content;
			echo "<span style='color:gray; font-size: 85%;'>Last updated: </span>" . date("H:i:s", filemtime($feed_cache));
		} else {
			echo file_get_contents($feed_cache);
			echo "<span style='color:gray; font-size: 85%;'>Last updated: </span>" . date("H:i:s", filemtime($feed_cache));
		}
		?>
		<div style="margin-top:1em; margin-bottom: 1em;">
			<?php echo $footer; ?>
		</div>
	</div>
</body>

</html>