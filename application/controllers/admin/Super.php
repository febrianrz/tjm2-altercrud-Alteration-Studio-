<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Super extends CI_Controller{

    protected $data;

    public function __construct(){
        parent::__construct();

        if(!$this->session->userdata('4lt3r_login_admin'))
            redirect('admin/login');

        $this->data['user_theme'] = $this->session->userdata('theme');
        $this->data['user_page'] = 'v_crud';
        $this->data['user_view']  = 'admin/'.$this->session->userdata('theme').'/main';

    }

    protected function generateTitle(){
        $this->is_authorize();
        $tmp = $this->M_altercrud->getWhere('t_menu',array('id'=>$this->data['menu_id']))->row();
        $this->data['judul'] = $tmp->nama;
        $this->data['judul_2'] = 'Master Data '.$tmp->nama;
    }

    protected function is_authorize(){
        $tmp = $this->M_altercrud->getWhere('t_menu',array(
            'id'=>$this->data['menu_id'],
            'id_tkategori_user'=>$this->session->userdata('id_kategori_user')));
        if($tmp->num_rows() == 0){
            show_404();
        }

    }

    public function create(){
        if($_POST){
            $this->alter->saveData();
            redirect($this->generateUrl(current_url(),1));
        } else {
            $this->load->view($this->data['user_view'], $this->data);
        }
    }

    public function edit($id=1){
        if($_POST){
            $this->alter->editData();
            redirect($this->generateUrl(current_url(),2));
        } else {
            $this->load->view($this->data['user_view'], $this->data);
        }
    }

    public function detail($id=1){
        $this->load->view($this->data['user_view'],$this->data);
    }

    public function delete($id=1){
        $this->load->view($this->data['user_view'],$this->data);
    }

    protected function generateUrl($url,$removeLastBySlash=0){
        $tmp = explode('/',str_replace('http://','',$url));
        $newurl = "http://";
        $limit = count($tmp) - $removeLastBySlash;
        for($i=0;$i<count($tmp);$i++){
            if($i < $limit)
                $newurl .= '/'.$tmp[$i];
        }
        return $newurl;
    }
}
