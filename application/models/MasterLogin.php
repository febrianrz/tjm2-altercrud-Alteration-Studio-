<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once (FCPATH."application/models/Login_inf.php");

class MasterLogin extends CI_Model implements Login_inf{

    protected $table;

    public function login($data)
    {
        $tmp = $data;
        unset($tmp['remember']);
        $cek = $this->db->get_where($this->table, $tmp);
        if($cek->num_rows() > 0){
            //session long
            if(isset($data['remember'])){
                $this->session->sess_expiration = 72000;
                $this->session->sess_expire_on_close = FALSE;
            }
            $row = $cek->row();
            $this->session->set_userdata((array)$row);
            $this->session->set_userdata('4lt3r_login_'.$this->table,true);
            return true;
        } else {
            $this->session->set_userdata('err',true);
            $this->session->mark_as_flash('err');
            return false;
        }
    }

    public function logout()
    {
        $this->session->sess_destroy();
    }
}
