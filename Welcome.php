<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends CI_Controller {

	 function __construct()
  {

    parent::__construct();

    date_default_timezone_set('Europe/Warsaw');
		session_start();

    /* AĂąadimos el helper al controlador */
    $this->load->helper('url');
  }

	
	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */
	public function index()
	{
		
		$this->load->helper('auth_helper');
		
		$islogged = is_logged();
		
	
		
		
		
		//$this->Intranet->test();
		if(!$islogged)
		{
			redirect('/login/index/1');
		}
		
		$this->load->model('Intranet');
		
		//$this->Intranet->test();
		
		$this->load->helper('xcrud');
        
        $xcrud = xcrud_get_instance();
        $xcrud->table('tusers');
		  $xcrud->relation('dzialid','ttaxonomy','idtaxonomy','name','ttaxonomy.type = "dzial"');
		  $xcrud->relation('gniazdo','ttaxonomy','idtaxonomy','name','ttaxonomy.type = "gniazdo"');
		  $xcrud->relation('infoid','ttaxonomy','idtaxonomy','name','ttaxonomy.type = "infoprac"');	
		   $xcrud->label('dzialid','Dział');	
			$xcrud->label('gniazdo','Gniazdo');	
			$xcrud->label('infoid','Info pracownik');
			$xcrud->change_type('foto','image','',array('width'=>300)); // resize main image
			
			$data['content'] = $xcrud->render();
		
		
		
		$this->load->view('header');
		$this->load->view('sidebar');
		$this->load->view('topbar');
		$this->load->view('welcome_message',$data);
		$this->load->view('footer');
	}
}
