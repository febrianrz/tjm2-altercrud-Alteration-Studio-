<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class CheckAdmin extends CI_Controller {

  public function index()
  {
    if($this->session->userdata('adminlogin'))
      redirect(base_url('admin/dashboard'));
    else
      redirect(base_url('admin/login'));

  }

}
