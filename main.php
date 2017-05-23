<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Main extends CI_Controller {

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

		if(!$islogged) 	{redirect('/login/index/1');}
		
		$this->load->model('Intranet');
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
		$this->load->view('homepage',$data);
		$this->load->view('footer');
	}

    public function pracownicy ()
    {

        $this->load->helper('auth_helper');
        $islogged = is_logged();

        if(!$islogged) 	{redirect('/login/index/1');}

        $this->load->model('Intranet');
        $this->load->helper('xcrud');
        $xcrud = xcrud_get_instance();

        $data['arrOldNumbers'] = $this->Intranet -> getOldNumbers();

        $xcrud->table('tusers');

        if(!$_SESSION['has_finance_hr'])
        {
            $xcrud->columns('imie,nazwisko,foto,stanowisko,email,telefon,dzialid,gniazdo,skype');
        }

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
        $this->load->view('page',$data);
        $this->load->view('footer');
    }
    
    public function profil()
	{
		
		$this->load->helper('auth_helper');
		$islogged = is_logged();

		if(!$islogged)
		{
			redirect('/login/index/1');
		}
		
		$this->load->model('Intranet');
		$this->load->helper('xcrud');
        
        $xcrud = xcrud_get_instance();
        $xcrud->table('tusers');
        $xcrud->table_name('Mój profil ', 'Edycja profilu użytkownika','&#xe05b;');
        $xcrud->where('uid=',$_SESSION['logged_uid']);
        
		  $xcrud->relation('dzialid','ttaxonomy','idtaxonomy','name','ttaxonomy.type = "dzial"');
		  $xcrud->relation('gniazdo','ttaxonomy','idtaxonomy','name','ttaxonomy.type = "gniazdo"');
		  $xcrud->relation('infoid','ttaxonomy','idtaxonomy','name','ttaxonomy.type = "infoprac"');	
		   $xcrud->label('dzialid','Dział');	
			$xcrud->label('gniazdo','Gniazdo');	
			$xcrud->label('infoid','Info pracownik');
			$xcrud->change_type('foto','image','',array('width'=>300)); // resize main image
			
			$data['content'] = $xcrud->render("view",$_SESSION['logged_uid']);
            $xcrud->change_type('haslo', 'password', 'sha1');

        $data['isprofil'] = true;
		
		
		$this->load->view('header');
		$this->load->view('sidebar');
		$this->load->view('topbar');
		$this->load->view('page',$data);
		$this->load->view('footer');
	}
	
	//
	public function planer()
	{
		
		$this->load->helper('auth_helper');
		$this->load->helper('intranet_helper');
		
		$islogged = is_logged();
		
	
		
		
		
		//$this->Intranet->test();
		if(!$islogged)
		{
			redirect('/login/index/1');
		}
		
		$this->load->model('Intranet');
		
		
	  //	$data['query'] = $this->Intranet->getUrlopy(0);
		
		$this->load->helper('xcrud');
        
        $xcrud = xcrud_get_instance();
        $xcrud->table('turlopywnioski');
		  $xcrud->table_name('Twoje wnioski ', 'Wnioski zapisane w bazie','&#xe05b;');	
		  		
		  $xcrud->relation('rodzaj','ttaxonomy','idtaxonomy','name','ttaxonomy.type = "urlop"');
		  $xcrud->relation('uid','tusers','uid',array('nazwisko','imie'));
		  $xcrud->relation('zastepstwo','tusers','uid',array('nazwisko','imie'));	
		  $xcrud->relation('status','ttaxonomy','idtaxonomy','name','ttaxonomy.type = "statuswurlop"');	
			
		  //$xcrud->columns('uid','rodzaj','start','stop','zastepstwo','status');	
			
		  $xcrud->disabled('status');		
		  
		   $xcrud->change_type('start', 'date', '', array('range_end'=>'stop')); // this is start date field and it points to end date field		
        $xcrud->change_type('stop', 'date', '', array('range_start'=>'start')); // this is end of range date and it points to the start date
		  //$xcrud->relation('infoid','ttaxonomy','idtaxonomy','name','ttaxonomy.type = "infoprac"');	
		   $xcrud->label('rodzaj','Rodzaj wniosku');	
			$xcrud->label('hdzien','Godzin / dzień');
			$xcrud->label('uid','Pracownik');
			//$xcrud->label('gniazdo','Gniazdo');	
		//$xcrud->label('infoid','Info pracownik');
			//$xcrud->change_type('foto','image','',array('width'=>300)); // resize main image
			
			$xcrud->change_type('hdzien', 'int', '8', array('maxlength'=>2)); // v1.6
			$xcrud->validation_pattern('hdzien', 'numeric');
			//$xcrud->change_type('uid','hidden');
			$xcrud->pass_default('status', 69);
		  $xcrud->pass_default('uid', 1);	
			
		  $xcrud->create_action('Anuluj', 'cancel_action');
			$xcrud->create_action('Odrzuć', 'deny_action'); // action callback, function publish_action() in functions.php		
    		$xcrud->create_action('Zatwierdź', 'accept_action');
    		$xcrud->button('http://155.168.5.5/intranet2/index.php/Main/wniosekurlop/{idtwniosku}/76', 'ODRZUĆ', 'icon-cancel glyphicon glyphicon-remove');//$xcrud->button('http://155.168.5.5/intranet2/index.php/Main/wniosekurlop/{idtwniosku}/70', 'ANULUJ', 'icon-close glyphicon glyphicon-remove');
    		$xcrud->button('http://155.168.5.5/intranet2/index.php/Main/wniosekurlop/{idtwniosku}/71', 'ZATWIERDŹ', 'icon-checkmark glyphicon glyphicon-ok');
		  
		  $xcrud->highlight_row('status', '=', 48, '#cccccc');	
		  $xcrud->highlight('status', '=', 49, '#ff9999');		
			
			
			
			
			$data['content'] = $xcrud->render(); // Tabela główna
			
			
			$xcrud2 = xcrud_get_instance();
			$xcrud2->table('vkadrybradford');
		  	$xcrud2->table_name('Wskaźniki Bradforda ', 'Tabela chorobowego','&#xe05b;');
		  	$xcrud2->highlight_row('wskbradforda', '>', 100, '#ff9999');	
			
			
		   $data['content2'] = $xcrud2->render();		
		
		
		$this->load->view('header-cal');
		$this->load->view('sidebar');
		$this->load->view('topbar');
		$this->load->view('calendar',$data);
		//$this->load->view('footer');
	}
	
	//
	public function wniosekurlop($id=0,$status=70)
	{
		
		$this->load->helper('auth_helper');
		
		$islogged = is_logged();
		
	
		
		
		
		//$this->Intranet->test();
		if(!$islogged)
		{
			redirect('/login/index/1');
		}
		
		$this->load->model('Intranet', '', TRUE);
		
		$this->Intranet->ustawStatusWniosku($id,$status);
		
	  
			
			
			
			
			
		
		redirect('/Main/planer');
		
	  /*	$this->load->view('header-cal');
		$this->load->view('sidebar');
		$this->load->view('topbar');
		$this->load->view('calendar',$data);*/
		//$this->load->view('footer');
	}
	
	
	public function zakupystatus($id=0,$status=77)
	{
		
		$this->load->helper('auth_helper');
		
		$islogged = is_logged();
		
	
		
		
		
		//$this->Intranet->test();
		if(!$islogged)
		{
			redirect('/login/index/1');
		}
		
		$this->load->model('Intranet', '', TRUE);
		
		$this->Intranet->ustawStatusZakupu($id,$status);
		
  	
			
			
		
		redirect('/Main/zakupy');
		
	  /*	$this->load->view('header-cal');
		$this->load->view('sidebar');
		$this->load->view('topbar');
		$this->load->view('calendar',$data);*/
		//$this->load->view('footer');
	}
	
	//
	public function projekty()
	{
		
		$this->load->helper('auth_helper');
		$islogged = is_logged();

		if(!$islogged) {redirect('/login/index/1');	}
		
		$this->load->model('Intranet');
		$this->load->helper('xcrud');
        
        $xcrud = xcrud_get_instance();
        $xcrud->table('tprojekty');
		$xcrud->table_name('BAZA PROJEKTÓW ', 'Projekty zapisane w bazie','&#xe05b;');	

		$xcrud->relation('status','ttaxonomy','idtaxonomy','name','ttaxonomy.type = "statusproj"');
		$xcrud->relation('grupa','ttaxonomy','idtaxonomy','name','ttaxonomy.type = "grupaproj"');
		$xcrud->relation('klient','tklienci','idklienta','oznaczenie');
		$xcrud->relation('typproj','ttaxonomy','idtaxonomy','name','ttaxonomy.type  = "typproj"');
			

	     
			
		  $xcrud->change_type('datastart', 'date', '', array('range_end'=>'datastop')); // this is start date field and it points to end date field	
        $xcrud->change_type('datastop', 'date', '', array('range_start'=>'datastart')); // this is end of range date and it points to the start date
			
		  $xcrud->label('oznaczenie','Oznaczenie / numer projektu');	
		  $xcrud->label('nazwa','Tytuł projektu');
		  $xcrud->label('oznaczenie','Oznaczenie / numer projektu');
		  $xcrud->label('klient','Oznaczenie klienta');
		  $xcrud->label('datastart','Data rozpoczęcia');		
		  $xcrud->label('datastop','Termin zakończenia');
		  $xcrud->label('grupa','Grupa');
		  $xcrud->label('topicid','Ref (baza):');			
		  //$xcrud->change_type('status','text','',20);		
		  $xcrud->disabled('topicid,status');	
		  $xcrud->pass_default('status', 46);



			
		  //$xcrud->button('/main/projekty/status', 'Zmień status');	
		  $xcrud->create_action('W toku', 'back_action'); // action callback, function publish_action() in functions.php	
    		$xcrud->create_action('Zakończ', 'end_action');
    		$xcrud->button('#', 'unpublished', 'icon-close glyphicon glyphicon-remove', 'xcrud-action',
        array(  // set action vars to the button
            'data-task' => 'action',
            'data-action' => 'back',
            'data-primary' => '{topicid}'), 
        array(  // set condition ( when button must be shown)
            'status',
            '=',
            '47')
    );
    		$xcrud->button('#', 'published', 'icon-checkmark glyphicon glyphicon-ok', 'xcrud-action', array(
        'data-task' => 'action',
        'data-action' => 'end',
        'data-primary' => '{topicid}'), array(
        'status',
        '=',
        '48'));
		  $xcrud->highlight_row('status', '=', 48, '#cc');	
		  $xcrud->highlight('status', '=', 49, '#ff9999');		
			
		  $xcrud->default_tab('Dane projektu');	
		  $projektyosint = $xcrud->nested_table('Projekt - osoby firma','topicid','tprojektyosoby','projid'); // 2nd level	
		  $projektyosint->table_name('Przyporządkowanie osób do projektów ');	
    	  $projektyosint->columns('osobaid');
    	  $projektyosint->fields('osobaid');
		  $projektyosint->relation('osobaid','tusers','uid',array('nazwisko','imie','stanowisko'),'','nazwisko desc');
			
    	  $projektyosint->default_tab('Projekt - osoby firma');
		  $projektyosint->unset_csv();	
    	  $projektyosint->unset_limitlist();
    	  $projektyosint->unset_numbers();
		  $projektyosint->unset_pagination();
    	  $projektyosint->unset_print();
			
		  $projektyosext = $xcrud->nested_table('Projekt - osoby klient','topicid','tprojektyosoby','projid'); // 2nd level		
		  $projektyosext->table_name('Przyporządkowanie kontaktów klienta do projektów ');	
    	  $projektyosext->columns('intex');
    	  $projektyosext->fields('intex');
			
		  $projektyosext->columns('osobaid');	
    	  $projektyosext->fields('osobaid');
    	  $temp = '';
		  //$temp = 'tklienciosoby.klientid='.$xcrud->row('klient');
		  $projektyosext->relation('osobaid','tklienciosoby','id',array('nazwisko','imie'),$temp,'nazwisko desc');

		  //&xcrud->
			
    	  $projektyosext->default_tab('Projekt - osoby klient');
		  $projektyosext->unset_csv();		
    	  $projektyosext->unset_limitlist();
    	  $projektyosext->unset_numbers();
		  $projektyosext->unset_pagination();
    	  $projektyosext->unset_print();
	
				
		  //$xcrud->relation('infoid','ttaxonomy','idtaxonomy','name','ttaxonomy.type = "infoprac"');	
		   //$xcrud->label('rodzaj','Rodzaj wniosku');	
			//$xcrud->label('gniazdo','Gniazdo');	
		//$xcrud->label('infoid','Info pracownik');
			//$xcrud->change_type('foto','image','',array('width'=>300)); // resize main image

        $data['content'] = $xcrud->render();
        $data['isprojekty'] = true;
		
		
		$this->load->view('header');
		$this->load->view('sidebar');
		$this->load->view('topbar');
		$this->load->view('page',$data);
		$this->load->view('footer');
	}
	
	public function zakupy()
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
      $xcrud->table('tzakupy');
		$xcrud->columns('idzakup,data,uidzakup,dozakupu,opis,koszt,status');
		$xcrud->sum('koszt');
		$xcrud->table_name('BAZA ZAKUPÓW ', 'Wypełnia osoba zgłaszająca zapotrzebowanie. Opis możliwie szczegółowy aby zakupy wiedziały CO, OD KOGO, NA KIEDY	 ','&#xe05b;');	
		$xcrud->relation('status','ttaxonomy','idtaxonomy','name','ttaxonomy.type = "statuszakup"');
		 $xcrud->relation('uidzakup','tusers','uid',array('nazwisko','imie'));
		 // $xcrud->relation('grupa','ttaxonomy','idtaxonomy','name','ttaxonomy.type = "grupaproj"');	
		  //$xcrud->relation('klient','tklienci','idklienta','oznaczenie');
			
		  $xcrud->change_type('data', 'date'); // this is start date field and it points to end date field	
		  $xcrud->change_type('koszt', 'price','0',array('suffix'=>' zł'));	
        //$xcrud->change_type('datastop', 'date', '', array('range_start'=>'datastart')); // this is end of range date and it points to the start date
			
		  $xcrud->label('idzakup','Id zakupu');	
		  $xcrud->label('data','Data zgłoszenia');
		  $xcrud->label('uidzakup','Zgłaszający');
		  $xcrud->label('dozakupu','Co do zakupu');
		  $xcrud->label('opis','Opis');		
		  $xcrud->label('koszt','Koszt');
		  $xcrud->label('status','Status zgłoszenia');
		  $xcrud->label('komentarz','Komentarz');			
		  //$xcrud->change_type('status','text','',20);		
		  $xcrud->disabled('idzakup,status,uidzakup');	
		  $xcrud->pass_default('status', 77);	
		  $xcrud->pass_default('uidzakup', $_SESSION['logged_uid']);
		  $xcrud->pass_var('uidzakup', $_SESSION['logged_uid']);
		  $xcrud->pass_default('status', 77);	
		 $xcrud->pass_var('status', 77);
		 ///$xcrud->pass_default('data', date("d.m.Y"));	
			
		  //$xcrud->bu
		  //$xcrud->button('/main/projekty/status', 'Zmień status');	
		  //$xcrud->create_action('dozakupu', 'zakupok_action','http://155.168.5.5/intranet2/xcrud/functions.php'); // action callback, function publish_action() in functions.php	
    	  //$xcrud->create_action('odrzuc', 'zakupnot_action','http://155.168.5.5/intranet2/xcrud/functions.php');
		  //$xcrud->create_action('wstrzymaj', 'zakupdelay_action','http://155.168.5.5/intranet2/xcrud/functions.php');
			
			$xcrud->button('http://155.168.5.5/intranet2/index.php/Main/zakupystatus/{idzakup}/79', 'ODRZUĆ', 'icon-cancel glyphicon glyphicon-remove','action',array(  // set action vars to the button
            'data-task' => 'action'), 
        array(  // set condition ( when button must be shown)
            'status',
            '!=',
            '79'));
			$xcrud->button('http://155.168.5.5/intranet2/index.php/Main/zakupystatus/{idzakup}/78', 'DO KUPIENIA', 'icon-ok glyphicon glyphicon-ok','action',array(  // set action vars to the button
            'data-task' => 'action'), 
        array(  // set condition ( when button must be shown)
            'status',
            '!=',
            '78'));
			$xcrud->button('http://155.168.5.5/intranet2/index.php/Main/zakupystatus/{idzakup}/80', 'WSTRZYMAJ', 'icon-clock glyphicon glyphicon-clock','action',array(  // set action vars to the button
            'data-task' => 'action'), 
        array(  // set condition ( when button must be shown)
            'status',
            '!=',
            '80'));
    		
		  
		  $xcrud->highlight('status', '=', 79, '#ff9999');
		  $xcrud->highlight('status', '=', 78, '#99ff99');		
		  $xcrud->highlight('status', '=', 80, '#ffcc00');		
	
				
		  //$xcrud->relation('infoid','ttaxonomy','idtaxonomy','name','ttaxonomy.type = "infoprac"');	
		  //$xcrud->label('rodzaj','Rodzaj wniosku');	
		  //$xcrud->label('gniazdo','Gniazdo');	
			//$xcrud->label('infoid','Info pracownik');
		  //$xcrud->change_type('foto','image','',array('width'=>300)); // resize main image
			
			
		  $data['content'] = $xcrud->render();
		
		
		
		$this->load->view('header');
		$this->load->view('sidebar');
		$this->load->view('topbar');
		$this->load->view('page',$data);
		$this->load->view('footer');
	}

    // ZAKKLADKA RAPORTÓW
	public function raporty($type="projekty",$id="0")
    {

        $this->load->helper('auth_helper');
        $this->load->helper('xcrud');
        $islogged = is_logged();
        if(!$islogged) {redirect('/login/index/1');}
        $this->load->model('Intranet');
        $this->Intranet->connectMSSQL();
        $xcrud = xcrud_get_instance();$xcrud->table('tprojekty');
        $xcrud2 = xcrud_get_instance();$xcrud2->table('vkadrybradford');
        $arrPost= array();

        if($type=="projekty")
        {
            //$postReptype = 5;
            $postReptype = $this->input->post('reptype', TRUE);
            //echo $postReptype;

            $postStart = "2017-05-01";
            $postStop = "2017-05-31";
            $postNazwisko = "";
            $postWydzial = "";
            $postProjekt = "";
            //$postReptype = $this->input->post('reptype', TRUE);
            if (isset($postReptype)) {

                $postStart = $this->input->post('start', TRUE);
                $postStop = $this->input->post('stop', TRUE);
                $postNazwisko = $this->input->post('nazwisko', TRUE);
                $postWydzial = $this->input->post('wydzial', TRUE);
                $postProjekt = $this->input->post('projekt', TRUE);


                //$postReptype = $this->input->post('reptype', TRUE);
                $arrPost= $this->input->post(NULL, TRUE);
            }
            else
            {
                $postReptype = 5;
            }
            //echo $postReptype;
            $arrPost['start'] = $postStart;
            $arrPost['stop'] = $postStop;
            $arrPost['nazwisko'] = $postNazwisko;
            $arrPost['wydzial'] = $postWydzial;
            $arrPost['projekt'] = $postProjekt;
            $arrPost['reptype'] = $postReptype;
            //$arrPost= $this->input->post(NULL, TRUE);
            if($postReptype == 0 || $postReptype == 1) {$postStart="2017-05-01";$postStop="2017-05-31";}
            if($postReptype == 2) {$postWydzial="";$postProjekt="";}
            if($postReptype == 4) {$postWydzial="";$postNazwisko="";}
            if($postReptype == 5) {$postWydzial="";$postNazwisko="";}
            if($postReptype == 6) {$postNazwisko="";$postProjekt="";}
            //$arrProjekty = $this->Intranet->getMSSQLProjects($id);
            $arrRaport1 = $this->Intranet->getMSSQLReport($postReptype,$postStart,$postStop,$postWydzial,$postNazwisko,$postProjekt);
            //$arrRaport2 = $this->Intranet->getMSSQLReport(1);
            //print_r($arrProjekty);


            //echo "<br><br>";
            //print_r($arrRaport1);
           // echo "<br><br>";
            //print_r($arrRaport2);
            //echo "<br><br>"; //$xcrud = xcrud_get_instance();


            $xcrud->table_name('BAZA PROJEKTÓW ', 'Projekty zapisane w bazie','&#xe05b;');
            //echo 'lll';
            $xcrud->relation('status','ttaxonomy','idtaxonomy','name','ttaxonomy.type = "statusproj"');
            $xcrud->relation('grupa','ttaxonomy','idtaxonomy','name','ttaxonomy.type = "grupaproj"');
            $xcrud->relation('klient','tklienci','idklienta','oznaczenie');
            $xcrud->relation('typproj','ttaxonomy','idtaxonomy','name','ttaxonomy.type  = "typproj"');



            $xcrud->change_type('datastart', 'date', '', array('range_end'=>'datastop')); // this is start date field and it points to end date field
            $xcrud->change_type('datastop', 'date', '', array('range_start'=>'datastart')); // this is end of range date and it points to the start date

            $xcrud->label('oznaczenie','Oznaczenie / numer projektu');
            $xcrud->label('nazwa','Tytuł projektu');
            $xcrud->label('oznaczenie','Oznaczenie / numer projektu');
            $xcrud->label('klient','Oznaczenie klienta');
            $xcrud->label('datastart','Data rozpoczęcia');
            $xcrud->label('datastop','Termin zakończenia');
            $xcrud->label('grupa','Grupa');
            $xcrud->label('topicid','Ref (baza):');
            //$xcrud->change_type('status','text','',20);
            $xcrud->disabled('topicid,status');
            $xcrud->pass_default('status', 46);




            //$xcrud->button('/main/projekty/status', 'Zmień status');
            $xcrud->create_action('W toku', 'back_action'); // action callback, function publish_action() in functions.php
            $xcrud->create_action('Zakończ', 'end_action');
            $xcrud->button('#', 'unpublished', 'icon-close glyphicon glyphicon-remove', 'xcrud-action',
                array(  // set action vars to the button
                    'data-task' => 'action',
                    'data-action' => 'back',
                    'data-primary' => '{topicid}'),
                array(  // set condition ( when button must be shown)
                    'status',
                    '=',
                    '47')
            );
            $xcrud->button('#', 'published', 'icon-checkmark glyphicon glyphicon-ok', 'xcrud-action', array(
                'data-task' => 'action',
                'data-action' => 'end',
                'data-primary' => '{topicid}'), array(
                'status',
                '=',
                '48'));
            $xcrud->highlight_row('status', '=', 48, '#cc');
            $xcrud->highlight('status', '=', 49, '#ff9999');

            $xcrud->default_tab('Dane projektu');
            $projektyosint = $xcrud->nested_table('Projekt - osoby firma','topicid','tprojektyosoby','projid'); // 2nd level
            $projektyosint->table_name('Przyporządkowanie osób do projektów ');
            $projektyosint->columns('osobaid');
            $projektyosint->fields('osobaid');
            $projektyosint->relation('osobaid','tusers','uid',array('nazwisko','imie','stanowisko'),'','nazwisko desc');

            $projektyosint->default_tab('Projekt - osoby firma');
            $projektyosint->unset_csv();
            $projektyosint->unset_limitlist();
            $projektyosint->unset_numbers();
            $projektyosint->unset_pagination();
            $projektyosint->unset_print();

            $projektyosext = $xcrud->nested_table('Projekt - osoby klient','topicid','tprojektyosoby','projid'); // 2nd level
            $projektyosext->table_name('Przyporządkowanie kontaktów klienta do projektów ');
            $projektyosext->columns('intex');
            $projektyosext->fields('intex');

            $projektyosext->columns('osobaid');
            $projektyosext->fields('osobaid');
            $temp = '';
            //$temp = 'tklienciosoby.klientid='.$xcrud->row('klient');
            $projektyosext->relation('osobaid','tklienciosoby','id',array('nazwisko','imie'),$temp,'nazwisko desc');

            //&xcrud->

            $projektyosext->default_tab('Projekt - osoby klient');
            $projektyosext->unset_csv();
            $projektyosext->unset_limitlist();
            $projektyosext->unset_numbers();
            $projektyosext->unset_pagination();
            $projektyosext->unset_print();
        }
        elseif ($type=="kadry")
        {


            $xcrud2->table_name('Wskaźniki Bradforda ', 'Tabela chorobowego','&#xe05b;');
            $xcrud2->highlight_row('wskbradforda', '>', 100, '#ff9999');


            $data['content2'] = $xcrud2->render();
        }


        $data['content'] = $xcrud->render();
        $data['content2'] = $xcrud2->render();
        $data['reporttype'] = $type;
        $data['reportArray'] = $arrRaport1;
        $data['postArray'] = $arrPost;




        $this->load->view('header');
        $this->load->view('sidebar');
        $this->load->view('topbar');
        $this->load->view('page',$data);
        $this->load->view('footer');
    }

    public function godziny($date="0", $uid=0)
    {

        $this->load->helper('auth_helper');
        $islogged = is_logged();
        echo $date;

        //$this->Intranet->test();
        if(!$islogged)
        {
            redirect('/login/index/1');
        }

        $this->load->model('Intranet');

        /* Pobranie i sformatowanie zdarzeń do wdioku kalendarza */
        $arrEvents = $this->Intranet->getEventsForCalendar($uid);
        $ile = sizeof($arrEvents);
        $data['arrEvents'] = $arrEvents;

        $events = "";
        for($i=0;$i<$ile;$i++)
        {
            $startparse = date_parse($arrEvents[$i]-> start);
            $stopparse = date_parse($arrEvents[$i]-> stop );

            $events=$events."\n{ title:'".$arrEvents[$i]->projekt."-".$arrEvents[$i]->nazwa."',";
            $events=$events."\nstart: new Date(".$startparse['year'].",".($startparse['month']-1).",".$startparse['day']."),";
            $events=$events."\nend: new Date(".$stopparse['year'].",".($stopparse['month']-1).",".$stopparse['day'].")";
            $events=$events."\n}";

            if($i<$ile-1) $events=$events.",";

        }

        $data['events'] = $events;

        $this->load->helper('xcrud');

        $xcrud = xcrud_get_instance();
        $xcrud->table('tkartypracy');
        $xcrud->columns('idkarty,start,stop,godzin,projid,zadanieid,delegacja,status');
        if ($uid>0) {$xcrud->where('tkartypracy.uid =', $uid);} // karty pracy dla okreslonego uzytkownika
        if ($date!="0") {$xcrud->where('tkartypracy.start =', $date);$xcrud->or_where('tkartypracy.stop =', $date);} // karty pracy dla okreslonego dnia - daty
        $xcrud->order_by('start','desc');

        $xcrud->table_name('  ', ' ');
        $xcrud->relation('status','ttaxonomy','idtaxonomy','name','ttaxonomy.type = "statuskartypracy"');
        $xcrud->relation('projid','tprojekty','topicid',array('projekt','klient','nazwa'));
        $xcrud->change_type('godziny', 'int', '0', 4);


        /*  <?php
    $xcrud = Xcrud::get_instance();
    $xcrud->table('employees');
    $xcrud->join('officeCode','offices','officeCode'); // ... INNER JOIN offices ON employees.officeCode = offices.officeCode ...
    echo $xcrud->render();
?>	*/
        $today = date("Y-m-d");
        $xcrud->pass_default('uwagi', $today);

        $xcrud->change_type('start', 'date', $today, array('range_end'=>'stop')); // this is start date field and it points to end date field
        $xcrud->change_type('stop', 'date', $today, array('range_start'=>'start')); // this is end of range date and it points to the start date
       //$xcrud->pass_default('start', $today);
        //$xcrud->pass_default('stop', $today);

        $xcrud->label('start','Od (data)');
        $xcrud->label('stop','Do (data)');
        $xcrud->label('godzin','Ile godzin');
        $xcrud->label('projid','Projekt');
        $xcrud->label('zadanieid','Zadanie projektu');
        $xcrud->disabled('idkarty,uid,dzialid');
        $xcrud->pass_default('uid', $uid);
        $xcrud->pass_default('godzin',8);
        $xcrud->pass_var('uid', $uid);




        //$xcrud->default_tab('Dodawanie / edycja karty pracy');



        //$xcrud->relation('infoid','ttaxonomy','idtaxonomy','name','ttaxonomy.type = "infoprac"');
        //$xcrud->label('rodzaj','Rodzaj wniosku');
        //$xcrud->label('gniazdo','Gniazdo');
        //$xcrud->label('infoid','Info pracownik');
        //$xcrud->change_type('foto','image','',array('width'=>300)); // resize main image

        $data['content'] = $xcrud->render();

        $this->load->view('header-cal');
        $this->load->view('sidebar');
        $this->load->view('topbar');
        $this->load->view('page-godziny',$data);
        //$this->load->view('footer');
    }

}
