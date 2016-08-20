<?php
defined('BASEPATH') OR exit('No direct script access allowed');
include "Super.php";
class Tmenu extends Super{

    protected $alter;

    public function __construct(){
        parent::__construct();
        $this->data['menu_id'] = 15;
        $this->generateTitle();
        $this->alter = new Altercrud();
        $this->alter->setTable('t_menu');
        $this->alter->detail_skip = array(1);
        $this->alter->tambah_skip = array('id','gambar');
        $this->alter->table_skip = array('id','tes','gambar');
        $this->alter->detail_skip = array('id','tes','gambar');
        $this->alter->display_as('id_tkategori_user','Kategori');
        $this->alter->set_relation('parent_id','t_menu','nama','id');
        $this->alter->set_relation('id_tkategori_user','t_kategori_user','nama','id');
//        $this->alter->set_relation_m_n('Kategori User','id','admin_kategori','id_menu','id_kategori','id','t_kategori_user','nama',
//            array()
//        );
//        $this->alter->set_relation_m_n('Kategori Users_2','id','admin_kategori','id_menu','id_kategori','id','t_kategori_user','nama',
//            array('tes_1'=>array('alias','varchar',''))
//        );
//        $this->alter->set_field_upload('gambar');
//        $this->alter->set_field_upload('urutan','file');
        // $this->alter->set_multi_upload('Nama Tampil','gambar_banyak','id_menu','nama_gambar','/assets/upload/');

        /**
         * Perintah generate CRUD di html
         */
        $this->data['output'] = $this->alter->generate();
    }

    public function index(){
        $this->load->view($this->data['user_view'],$this->data);
    }

    public function create(){
        if($_POST){
            $this->alter->saveData();
            //copy file
            if($_POST['url'] != "" || empty($_POST['url'])) {
                error_reporting(-1);
                ini_set('display_errors', 'On');
                $file_path = APPPATH . '.altercrud/.ori_master';
                $file_ori = fopen($file_path, 'r') or die("File Tidak Ada");
                $new_file = fread($file_ori, filesize($file_path));
                $tmp = explode('/', $_POST['url']);
                $filename = $tmp[count($tmp) - 1];
                //ambil data baru
                $this->db->where('nama', $_POST['nama']);
                $this->db->where('url', $_POST['url']);
                $this->db->where('tabel', $_POST['tabel']);
                $this->db->where('urutan', $_POST['urutan']);
                $tmp_id = $this->db->get('t_menu')->row();
                $id_menu = $tmp_id->id;
                $new_file_ready = str_replace('||nama_file||', ucfirst($filename), $new_file);
                $new_file_ready = str_replace('||menu_id||', $id_menu, $new_file_ready);
                $new_file_ready = str_replace('||nama_table||', '"' . $_POST['tabel'] . '"', $new_file_ready);
                //buat file baru
                $new_controller_file_name = APPPATH . 'controllers/admin/' . ucfirst($filename) . '.php';
                $generateNewFile = fopen($new_controller_file_name, "w");
                fwrite($generateNewFile, $new_file_ready);
                fclose($generateNewFile);
                chmod($new_controller_file_name, 0777);
            }
            redirect($this->generateUrl(current_url(),1));
        } else {
            $this->load->view($this->data['user_view'], $this->data);
        }
    }

}
