<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Nollej extends CI_Controller {
	//$_results = 12;
	

	public function index()
	{
		$data['title'] = "Nollej &bull; Home";
		$this->load->view('templates/header', $data);
		$this->load->view('home', $data);
		$this->load->view('templates/footer', $data);
	}
	
	public function query(){
		$this->load->helper('url');
		$q = rawurlencode($this->input->post('query'));
		redirect("nollej/map/{$q}");
	}
	/*public function map(){
		$query = $this->input->post('query');
		if($query == FALSE) show_error("Invalid query");
		$data['title']="Nollej &bull; Map";
		$userAgent = "nollej/1.0 (http://seancheatham.com/nollej)";
		$this->load->library('wikipedia', $userAgent);
		$data['query'] = $query;
		$data['search'] = $this->wikipedia->getLinksByTitle($query);
		$this->load->view('templates/header', $data);
		$this->load->view('map', $data);
		$this->load->view('templates/footer', $data);
	}*/
	
	public function map2($q){
		/*$userAgent = "nollej/1.0 (http://seancheatham.com/nollej)";
		$this->load->library('wikipedia', $userAgent);
		$data['query'] = $q;
		$page = $this->wikipedia->getPageByTitle2($q);
		//die(print_r($page['query']['pages']));
		$pages = array_values($page['query']['pages']);
		$regex = "\[\[([a-z ]*)\]\]";
		$limit=10;
		$data['results'] = array();
		//die($data);
		if(preg_match_all("/$regex/siU", $pages[0]['revisions'][0]['*'], $matches, PREG_SET_ORDER)) {
			$i = 0;
			foreach($matches as $match) {
				if($i>$limit) break;
				$data['results'][] = $match[1];
				$i++;
			}
		}*/
		//$q = $this->input->post('query');
		$this->load->model('nollej_model');
		$data['links'] = $this->nollej_model->get_links($q,12);
		$data['title'] = "Nollej &bull; Map";
		$data['query'] = rawurldecode($q);
		$this->load->view('templates/header', $data);
		$this->load->view('map', $data);
		$this->load->view('templates/footer', $data);
	}
	
	/*public function getLinks($id){
		$userAgent = "nollej/1.0 (http://seancheatham.com/nollej)";
		$this->load->library('wikipedia', $userAgent);
		$data['id'] = $id;
		$data['search'] = $this->wikipedia->getLinksById($id);
		$this->load->view('row', $data);
	}*/
	
	public function getLinks($q){
		//$this->output->enable_profiler(TRUE);
		$this->load->model('nollej_model');
		$data['links'] = $this->nollej_model->get_links($q,12);
		$data['query'] = rawurldecode($q);
		$this->load->view('row', $data);
	}

    public function map($q){
        $this->load->model('nollej_model');
        //$data['links'] = $this->nollej_model->get_links($q,6);
        $data['title'] = "Nollej &bull; Map";
        $data['query'] = rawurldecode($q);
        $this->load->view('templates/header', $data);
        $this->load->view('map', $data);
        $this->load->view('templates/footer', $data);
    }

    public function getLinksJSON($q){
        $this->load->model('nollej_model');
        $data['links'] = $this->nollej_model->get_links($q,8);
        $data['query'] = rawurldecode($q);
        header('Content-Type: application/json');
        echo json_encode( $data );
    }

}
