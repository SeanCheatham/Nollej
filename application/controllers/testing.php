<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Testing extends CI_Controller {

	public function index()
	{
		$data['title'] = "Testing";
		$this->load->view('testing', $data);
	}
	
	public function map($q){
		//$query = $this->input->post('query');
		if($q == FALSE) show_error("Invalid query");
		$data['title']="Nollej &bull; Map";
		$userAgent = "nollej/1.0 (http://seancheatham.com/nollej)";
		$this->load->library('wikipedia', $userAgent);
		$data['query'] = $q;
		$data['search'] = $this->wikipedia->getPageByTitle2($q);
		$this->load->view('testing/parsingTest', $data);
	}
	
	public function tree($q){
		$this->load->model('testing_model');
		$data['links'] = $this->testing_model->get_links($q);
		$links = $data['links']['query']['pages'];
		if(count($links) > 0){
			$this->testing_model->new_object($q, NULL);
		}
		$data['title'] = "Nollej &bull; Map";
		$data['query'] = rawurldecode($q);
		$this->load->view('testing/tree', $data);
	
	
	}

    public function getAllWords($q){
        $this->load->model('testing_model');
        $this->testing_model->get_words($q,0);
    }
}
