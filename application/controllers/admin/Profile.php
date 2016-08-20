<?php
defined('BASEPATH') OR exit('No direct script access allowed');
include "Super.php";
class Profile extends Super{

    public function __construct(){
        parent::__construct();
    }

    public function index(){
        $data = $this->data;
        $data['user_page'] = 'v_profile';
        $data['judul'] = 'Setting Profile';
        $data['judul_2'] = 'Pengaturan Admin';
        $this->load->view($this->data['user_view'],$data);
    }

    public function simpan(){
        if($_POST){
            $tmp = $_POST;
            if($this->input->post('password') == ""){
                unset($tmp['password']);
            }
            $this->db->where('id',$this->session->userdata('id'));
            $this->db->update('admin',$tmp);
            $this->session->set_userdata($tmp);
            redirect('admin/profile');
        } else {
            redirect('admin/profile');
        }
    }
}