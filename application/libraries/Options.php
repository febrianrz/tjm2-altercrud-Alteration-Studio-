<?php

class Options{

    private $CI;
    private $table;

    public function __construct(){
        $this->CI =& get_instance();
        $this->table = 't_options';
    }

    public function getOption($id){
        return $this->CI->db->get_where($this->table,array('id'=>$id))->row();
    }
}