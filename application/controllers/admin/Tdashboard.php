<?php
defined('BASEPATH') OR exit('No direct script access allowed');
include "Super.php";
class Tdashboard extends Super{

    protected $alter;

    public function __construct(){
        parent::__construct();
        $this->data['menu_id'] = 26;
        $this->alter = new Altercrud();
        $this->alter->setTable('t_dashboard');
        $this->alter->detail_skip = array(1);
        $this->alter->tambah_skip = array('id');
        $this->alter->table_skip = array('id');
        $this->alter->detail_skip = array('id');
        $this->alter->display_as('id_kategori_user','Kategori');
        $this->alter->set_relation('id_kategori_user','t_kategori_user','nama','id');
        /**
         * Perintah generate CRUD di html
         */
        $this->generateTitle();
        $this->data['output'] = $this->alter->generate();
    }

    public function index(){
        $this->load->view($this->data['user_view'],$this->data);
    }
}