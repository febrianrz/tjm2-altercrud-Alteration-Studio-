<?php
defined('BASEPATH') or exit('No direct script access allowed');
include "Super.php";
class Jenis_celana extends Super
{
    protected $alter;

    public function __construct()
    {
        parent::__construct();
        $this->data['menu_id'] = 36 ;
        $this->alter = new Altercrud();
        $this->alter->setTable("jenis_celana");
        $this->alter->table_skip = array('id');
        $this->alter->tambah_skip = array('id_jenis_celana');
        $this->alter->table_skip = array('id_jenis_celana');
        $this->alter->detail_skip = array('id_jenis_celana');
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
