<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Nollej_model extends CI_Model {


	function __construct()
	{
		parent::__construct();
		$userAgent = "nollej/1.0 (http://seancheatham.com/nollej)";
		$this->load->library('wikipedia', $userAgent);
	}

    function get_links2($q,$limit){
        $links = $this->wikipedia->getLinksByTitle($q);
        $pages = $links['query']['pages'];
        shuffle($pages);
        $result = array();
        $i = 0;
        foreach($pages as $page){
            if($i >= $limit) break;
            if(mb_stripos($page['title'],":")) continue;
            $result[] = $page['title'];
            $i++;
        }
        return $result;
        die(print_r($result));
    }

	function get_links($q, $limit){
        $connection = new MongoClient();
        $db = $connection->nollej;
        $collection = $db->relations;
        $r = $collection->find(array("parent" => ucfirst($q)));
        $r->sort(array("order" => 1));
        $r->limit($limit);

        if(sizeof($r) == $limit) {
            $result = array();
            foreach($r as $doc) $result[]=$doc['child'];
            return $result;
        }

		$page = $this->wikipedia->getPageByTitle($q);
		$pages = array_values($page['query']['pages']);
		$regex = "\[\[([a-z ]*)\]\]";
		$results = array();
		if(!isset($pages[0]['revisions'])) return $results;
		if(preg_match_all("/$regex/siU", $pages[0]['revisions'][0]['*'], $matches, PREG_SET_ORDER)) {
			$i = 0;
			//shuffle($matches);
            foreach($matches as $match) {
                try {
                    $collection->insert(array("parent" => $q, "child" => ucfirst($match[1]), "order" => $i));
                }
                catch(MongoCursorException $e){

                }
                $i++;
				if($i>=$limit) continue;
				$results[] = ucfirst($match[1]);
			}
		}
		return $results;
	}
    function get_words($q, $limit){
        $stopWords = array(
            " ",
            "",
            "a",
            "about",
            "above",
            "after",
            "again",
            "against",
            "all",
            "am",
            "an",
            "and",
            "any",
            "are",
            "aren't",
            "as",
            "at",
            "be",
            "because",
            "been",
            "before",
            "being",
            "below",
            "between",
            "both",
            "but",
            "by",
            "can't",
            "cannot",
            "could",
            "couldn't",
            "did",
            "didn't",
            "do",
            "does",
            "doesn't",
            "doing",
            "don't",
            "down",
            "during",
            "each",
            "few",
            "for",
            "from",
            "further",
            "had",
            "hadn't",
            "has",
            "hasn't",
            "have",
            "haven't",
            "having",
            "he",
            "he'd",
            "he'll",
            "he's",
            "her",
            "here",
            "here's",
            "hers",
            "herself",
            "him",
            "himself",
            "his",
            "how",
            "how's",
            "i",
            "i'd",
            "i'll",
            "i'm",
            "i've",
            "if",
            "in",
            "into",
            "is",
            "isn't",
            "it",
            "it's",
            "its",
            "itself",
            "let's",
            "me",
            "more",
            "most",
            "mustn't",
            "my",
            "myself",
            "no",
            "nor",
            "not",
            "of",
            "off",
            "on",
            "once",
            "only",
            "or",
            "other",
            "ought",
            "our",
            "ours",
            "ourselves",
            "out",
            "over",
            "own",
            "same",
            "shan't",
            "she",
            "she'd",
            "she'll",
            "she's",
            "should",
            "shouldn't",
            "so",
            "some",
            "such",
            "than",
            "that",
            "that's",
            "the",
            "their",
            "theirs",
            "them",
            "themselves",
            "then",
            "there",
            "there's",
            "these",
            "they",
            "they'd",
            "they'll",
            "they're",
            "they've",
            "this",
            "those",
            "through",
            "to",
            "too",
            "under",
            "until",
            "up",
            "very",
            "was",
            "wasn't",
            "we",
            "we'd",
            "we'll",
            "we're",
            "we've",
            "were",
            "weren't",
            "what",
            "what's",
            "when",
            "when's",
            "where",
            "where's",
            "which",
            "while",
            "who",
            "who's",
            "whom",
            "why",
            "why's",
            "with",
            "won't",
            "would",
            "wouldn't",
            "you",
            "you'd",
            "you'll",
            "you're",
            "you've",
            "your",
            "yours",
            "yourself",
            "yourselves"

        );

        $page = $this->wikipedia->getPageByTitle($q);
        $pages = array_values($page['query']['pages']);
        $regex = "\b\[*([a-z ]*)\*?\b";
        $results = array();
        if(!isset($pages[0]['revisions'])) return $results;
        if(preg_match_all("/$regex/siU", $pages[0]['revisions'][0]['*'], $matches, PREG_SET_ORDER)) {
            $count_values = array();
            foreach($matches as $match){
                foreach($match as $a){
                    if(!in_array(strtolower($a),$stopWords)) $count_values[$a] = (isset($count_values[$a])) ? $count_values[$a]+1 : 1;
                }
            }
            arsort($count_values);
            $result = array();
            $i = 6;
            //die(print_r($count_values));
            foreach($count_values as $key => $val){
                if($i>=$limit+6) break;
                $result[] = $key;
                $i++;
            }
            return $result;
            return array_slice($count_values,6,$limit+6);
        }
        return null;
    }
}

?>