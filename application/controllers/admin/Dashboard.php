<?php
defined('BASEPATH') OR exit('No direct script access allowed');
include "Super.php";
class Dashboard extends Super{

    public function __construct(){
        parent::__construct();
        $this->data['menu_id'] = 13;
        $this->generateTitle();
    }

    public function index(){
        $this->data['user_page'] = 'v_dashboard';
        $this->data['rows_dashboard'] = $this->M_altercrud->getWhere('t_dashboard',array('id_kategori_user'=>$this->session->userdata('id_kategori_user')));
        $this->load->view($this->data['user_view'],$this->data);
    }
}