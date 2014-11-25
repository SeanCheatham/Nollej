<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Pages extends CI_Controller {

	public function index(){
		$data['title'] = "Nollej &bull; About";
		$this->load->view('templates/header', $data);
		$this->load->view('about', $data);
		$this->load->view('templates/footer', $data);
	}

		public function about(){
		$data['title'] = "Nollej &bull; About";
		$this->load->view('templates/header', $data);
		$this->load->view('about', $data);
		$this->load->view('templates/footer', $data);
	}
}