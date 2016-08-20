<?php
defined('BASEPATH') OR exit('No direct script access allowed');
include "Super.php";
class texport extends Super{

    protected $alter;

    public function __construct(){
        parent::__construct();
        $this->data['menu_id'] = 30 ;
        $this->generateTitle();
        /**
         * Perintah generate CRUD di html
         */
    }

    public function index(){
        $this->load->dbutil();
        $filename = $this->options->getOption(1)->keterangan.time().'.zip';
        $prefs = array(
            'format'        => 'zip',                       // gzip, zip, txt
            'filename'      => $filename,              // File name - NEEDED ONLY WITH ZIP FILES
            'add_drop'      => TRUE,                        // Whether to add DROP TABLE statements to backup file
            'add_insert'    => TRUE,                        // Whether to add INSERT data to backup file
            'newline'       => "\n"                         // Newline character used in backup file
        );

        $backup = $this->dbutil->backup($prefs);
        $this->load->helper('download');
        force_download($filename, $backup);
    }

}