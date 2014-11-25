<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Testing_model extends CI_Model {

	function __construct()
	{
		parent::__construct();
		$userAgent = "nollej/1.0 (http://seancheatham.com/nollej)";
		$this->load->library('wikipedia', $userAgent);
		//$this->load->database();
	}
	
	function get_links($q){
		$results = $this->wikipedia->getLinksByTitle($q);
		return $results;
	}
	
	function new_object($name, $pageid){
		if($this->db->from('objects')->where('name', $name)->count_all_results() > 0){
			return false;
		}
		$data = array(
			'name' => $name,
			'pageid' => $pageid
		);
		$query = $this->db->insert('objects', $data);
		return $query;
	}
	
	function new_association($parent, $child){
		$data = array(
			'parent' => $parent,
			'child' => $child
		);
		$query = $this->db->insert('relations', $data);
		return $query;
	}
	
	function get_object_by_name($name){
		$query = $this->db->get('objects')->where('name', $name);
		if ($query->num_rows() > 0)
		{
		   $row = $query->row_array();
		   return $row;
		}
		return false;
	}
    function get_words($q, $limit){
        $page = $this->wikipedia->getPageByTitle($q);
        $pages = array_values($page['query']['pages']);
        $regex = "\[\[([a-z ]*)\]\]";
        $results = array();
        if(!isset($pages[0]['revisions'])) return $results;
        if(preg_match_all("/$regex/siU", $pages[0]['revisions'][0]['*'], $matches, PREG_SET_ORDER)) {
            $count_values = array();
            foreach($matches as $match){
                foreach($match as $a) (isset($count_values[$a]))? $count_values[$a]++ : $count_values[$a] = 1;
            }
            arsort($count_values);
            return array_slice($count_values,0,$limit);
        }
        return null;
    }

}

?>