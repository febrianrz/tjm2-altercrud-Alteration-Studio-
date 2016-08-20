<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Altercrud extends CI_Controller {

	private $permission_path = 0777;
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * handle dropzone upload
	 */
	public function upload(){
		if($this->input->post() == null)
			show_404();

		if (!empty($_FILES)) {
			$tempFile = $_FILES['file']['tmp_name'];
			$path_parts = pathinfo($_FILES["file"]["name"]);
			$ext = $path_parts['extension'];
			$fileName = date('YmdHis').rand(1,999999).'.'.$ext;
			$targetPath = getcwd() . $this->input->post('upload_path');
			$targetFile = $targetPath . $fileName ;
			try {
				if(!is_dir(getcwd().$this->input->post('upload_path'))){
					echo json_encode(array('status'=>false,'msg'=>'tidak ada direktori'));
					die();
				} else {
					try{
						if(!move_uploaded_file($tempFile, $targetFile)) {
							echo json_encode(array('status' => false, 'msg' => 'Tidak Dapat Mengupload File.'));
							die();
						}
						else
							echo json_encode(array('status'=>true,'msg'=>$fileName));
					} catch(Exception $e){
						echo json_encode(array('status'=>false,'msg'=>$e->getMessage()));
						die();
					}
				}
			}catch(Exception $e){
				echo json_encode(array('status'=>false,'msg'=>$e->getMessage()));
				die();
			}
		} else {
			show_404();
		}
	}

	public function create_dir(){
		if($this->input->post() == null)
			show_404();

		$path = $this->input->post('path');
		if(is_dir(getcwd().$path)){
			echo json_encode(array('status'=>false,'msg'=>'Direktori Sudah Ada'));
		} else {
			if (!mkdir(getcwd() . $path, $this->permission_path, true)) {
				echo json_encode(array('status'=>false,'msg'=>'Gagal Membuat Direktori'));
			} else {
				echo json_encode(array('status'=>true,'msg'=>'Berhasil membuat direktori, silahkan melakukan upload ulang.'));
			}
		}
	}

	/** handle single upload */
	public function upload_single(){
		if($this->input->post() == null)
			show_404();

		$upload_path = '.'.$this->input->post('path_to_upload');
		$allowed_type = "";

		if($this->input->post("temp_type") == 'file'){
			$allowed_type = "pdf|mp3|mp4|avi|doc|docx|odt|odp|xls|xlsx";
		} else {
			$allowed_type = "gif|jpg|png|jpeg";
		}

		$config = array(
			'upload_path' => $upload_path,
			'allowed_types' => $allowed_type,
			'overwrite' => TRUE,
			'max_size' => "2048000", // Can be set to particular file size , here it is 2 MB(2048 Kb)
		);
		$this->load->library('upload', $config);
		if($this->upload->do_upload('userfile_1'))
		{
			$data = array('status'=>true,'msg'=>$this->upload->data('file_name'),
				'field'=>$this->input->post("temp_upload"),
				'type' =>$this->input->post("temp_type")
			);
		}
		else
		{
			$data = array('status'=>false,'msg'=>$this->upload->display_errors());
		}

		echo json_encode($data);
	}
}
