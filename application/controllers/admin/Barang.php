<?php
defined('BASEPATH') or exit('No direct script access allowed');
include "Super.php";
class Barang extends Super
{
    protected $alter;

    public function __construct()
    {
        parent::__construct();
        $this->data['menu_id'] = 34 ;
        $this->alter = new Altercrud();
        $this->alter->setTable("barang");
        $this->alter->table_skip = array('id_barang');
        $this->alter->tambah_skip = array('id_barang','tanggal_input');
        $this->alter->table_skip = array('id_barang','tanggal_input');
        $this->alter->detail_skip = array('id_barang');
        $this->alter->money_format = array('harga');
        /**
         * Perintah generate CRUD di html
         */
        $this->alter->set_relation('id_jenis_celana', 'jenis_celana', 'nama_jenis_celana', 'id_jenis_celana');
        $this->alter->set_relation('id_kategori', 'kategori', 'nama_kategori', 'id_kategori');
        $this->alter->set_field_upload('gambar');
        $this->alter->setRelationLink('id_kategori', 'admin/kategori/detail');
        $this->alter->setRelationLink('id_jenis_celana', 'admin/jenis_celana/detail');
        $this->alter->display_as('id_kategori', 'Kategori');
        $this->alter->display_as('id_jenis_celana', 'Jenis Celana');
        $this->generateTitle();
        $this->data['output'] = $this->alter->generate();
    }

    public function index()
    {
        $this->load->view($this->data['user_view'], $this->data);
    }
}
