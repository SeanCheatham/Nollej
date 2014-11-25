<pre><code>
<?php
	$pages = array_values($search['query']['pages']);
	//var_dump($pages[0]['revisions'][0]['*']);
	
	
	$regex = "\[\[(.*)\]\]";
	$regex2 = "\[\[([a-z ]*)\]\]";
	$limit=10;
	if(preg_match_all("/$regex2/siU", $pages[0]['revisions'][0]['*'], $matches, PREG_SET_ORDER)) {
		$i = 0;
		foreach($matches as $match) {
			if($i>$limit) break;
			//$match[2] = link address
			echo $match[1]."<br />";
			$i++;
		}
	}
	
	//var_dump($search['parse']['text']['*']);
	
	//preg_match_all('/href=[\'"]?([^\s\>\'"]*)[\'"\>]/', $search['parse']['text']['*'], $matches);
	
	
	
	/*$regex = "<a\s[^>]*href=(\"??)([^\" >]*?)\\1[^>]*>(.*)<\/a>";
	
	if(preg_match_all("/$regex/siU", $search['parse']['text']['*'], $matches, PREG_SET_ORDER)) {
		foreach($matches as $match) {
		  // $match[2] = link address
			echo $match[3]."<br />";
		}
	}*/
	
?>
</code></pre>