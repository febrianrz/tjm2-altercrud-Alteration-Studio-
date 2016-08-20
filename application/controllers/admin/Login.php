<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller {

  private  $model;

  public function __construct()
  {
    parent::__construct();
    $this->checkDefaultTableAlter();
    $this->load->model('M_loginadmin');
    $this->model = new M_loginadmin();
  }

  public function index()
  {
    if($this->session->userdata('4lt3r_login_admin'))
      redirect('admin/dashboard');

    if($_POST){
      //cek login
      if($this->model->login($_POST)){
        redirect('admin/dashboard');
      } else {
        redirect('admin/login');
      }
    } else {
      $this->load->view('login/flat.php');
    }

  }

  public function logout(){
    if(!$this->session->userdata('4lt3r_login_admin'))
      redirect('admin/login');

    $this->model->logout();
    redirect('admin/login');
  }

  public function forgot(){

  }

  private function checkDefaultTableAlter(){
    $tmp = $this->db->list_tables();
    $default_table = array('t_kategori_user','admin','t_dashboard','t_menu','t_options');
    foreach($default_table as $key){
      if(!in_array($key,$tmp)){
        $sqlname = $key.'.sql';
        $this->import_dump($sqlname);
      }
    }
  }

  private function import_dump($file_name) {
    $path = APPPATH.'.altercrud/'; // Codeigniter application /assets
    $file_restore = $this->load->file($path . '/' . $file_name, true);
    $file_array = explode(';', $file_restore);
    foreach ($file_array as $query)
    {
      if($query == "" || empty($query) || $query == null)
        continue;
      $this->db->query("SET FOREIGN_KEY_CHECKS = 0");
      $this->db->query("$query");
      $this->db->query("SET FOREIGN_KEY_CHECKS = 1");
    }
  }
}
