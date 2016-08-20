<?php
defined('BASEPATH') or exit('No direct script access allowed');
include "Super.php";
class Kategori extends Super
{
    protected $alter;

    public function __construct()
    {
        parent::__construct();
        $this->data['menu_id'] = 35 ;
        $this->alter = new Altercrud();
        $this->alter->setTable("kategori");
        $this->alter->edit_skip = array('id');
        $this->alter->tambah_skip = array('id_kategori');
        $this->alter->table_skip = array('id_kategori');
        $this->alter->detail_skip = array('id_kategori');
        /**
         * Perintah generate CRUD di html
         */
        $this->generateTitle();
        $this->data['output'] = $this->alter->generate();
    }

    public function index()
    {
        $this->load->view($this->data['user_view'], $this->data);
    }
}
