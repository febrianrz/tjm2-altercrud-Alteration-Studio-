<?php
defined('BASEPATH') OR exit('No direct script access allowed');
include "Super.php";
class Altercrud extends Super{

    public function __construct(){
        parent::__construct();
    }

    public function index(){
        $data = $this->data;
        $data['user_page'] = 'v_makemenucrud';
        $data['judul'] = 'Alteration Studio';
        $data['judul_2'] = 'Alteration Studio';
        $this->db->order_by('nama');
        $data['menus'] = $this->db->get('t_menu');
        $this->load->view($this->data['user_view'],$data);
    }
}