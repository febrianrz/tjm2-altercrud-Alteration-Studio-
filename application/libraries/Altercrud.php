<?php

class Altercrud
{
    private $CI;
    public $table;
    public $tambah_skip = array();
    public $table_skip = array();
    public $detail_skip = array();
    public $delete_skip = array();
    public $money_format = array();
    public $dir_upload  = '/assets/upload/';
    public $edit = true;
    public $detail = true;
    public $delete = true;
    public $redirect_after_insert = true;
    public $redirect_after_update = true;

    private $current_id;
    private $relation_1_n = array();
    private $relation_m_n_single = array();
    private $relation_m_n_multifield = array();
    private $image_upload = array();
    private $file_upload = array();
    private $relation_link = array();
    private $sources;
    private $relation_new_name = array();
    private $base_url;
    private $state;
    private $fields;
    private $model;
    private $fields_alias;
    private $field_table_key;
    private $field_table_key_alias;
    private $table_alias;
    private $display_as = array();
    private $file_upload_multiple = array();
    private $date_type = array('timestamp','date','time','datetime');

    public function __construct()
    {
        $this->CI =& get_instance();
        $this->CI->load->model('M_altercrud');
        $this->model = new M_altercrud();
        $this->base_url = base_url(uri_string());
        $this->setState();
        $this->sources = new Source($this->state, $this->base_url, $this->dir_upload);
    }

    /**
     * @void setting field dari tabel yang di crud
     */
    private function setField()
    {
        $this->fields = $this->model->setField($this->table);
        $this->generateAliasField();
    }

    /**
     * @void
     * Setting primary key untuk tabel ini
     */

    private function setPrimaryField()
    {
        $not_have_primary = true;
        foreach ($this->fields as $field) {
            if ($field->primary_key == 1) {
                $this->field_table_key = $field->name;
                $this->field_table_key_alias = 'table_id_'.$this->getRandomString();
                $not_have_primary = false;
            }
        }
        if ($not_have_primary) {
            echo "Table Doesn't have primary key";
            die();
        }
    }

    /**
     * @param $table = nama tabel
     * setting tabel database
     */
    public function setTable($table)
    {
        $this->table = $table;
        $this->setField();
        $this->setPrimaryField();
        $this->setTableAlias();
    }

    /**
    * Set relation_link
    **/
    public function setRelationLink($field, $link, $other_site = false)
    {
        $this->relation_link[$field] = array($link,$other_site);
    }

    /**
     * @return string
     * Melakukan generate tabel index list data
     */

    private function generateTable()
    {
        $datas = $this->getAllData()->result();
        $url = $this->generateUrl(base_url(uri_string()), 0);

        $html = '<table class="table table-striped table-bordered" id="alterTable">';
        /** Bagian Headernya */
        $html .= '<thead>';
        $html .= '<tr>';
        foreach ($this->fields as $field):
            if (in_array($field->name, $this->table_skip)) {
                continue;
            }

            //nama baru cek
            $newnamafield = $field->name;
        if (array_key_exists($field->name, $this->display_as)) {
            $newnamafield = $this->display_as[$field->name];
        }

        $html .= '<th>'.ucfirst(str_replace('_', ' ', $newnamafield)).'</th>';
        endforeach;
        $html .= '<th style="width: 70px">Action</th>';
        $html .= '</tr>';
        $html .= '</thead>';
        /** Akhir Bagian Header */

        $html .= '<tbody>';
        foreach ($datas as $data):
            $html .= '<tr>';
        foreach ($this->fields as $field):
                if (in_array($field->name, $this->table_skip)) {
                    continue;
                }

                //cek relasi dengan nama baru
                if (array_key_exists($field->name, $this->relation_new_name)) {
                    $x = $this->relation_new_name[$field->name];
                    $html .= '<td>'.$data->$x.'</td>';
                    continue;
                }
        $x = $this->fields_alias[$field->name];

        if (in_array($field->name, $this->image_upload)) {
            $html .= "<td><a class='img_file_user' href='".base_url($this->dir_upload.'/'.$data->$x)."'><img src='".base_url($this->dir_upload.'/'.$data->$x)."' width='100'/></a></td>";
            continue;
        }
        if (in_array($field->name, $this->money_format)) {
            $html .= "<td>Rp".number_format($data->$x)."</td>";
            continue;
        }
        $html .= '<td>'.$data->$x.'</td>';

        endforeach;
        $id_alias = $this->field_table_key_alias;
        $html .= '<td>'.$this->generateAction($url, $data->$id_alias).'</td>';
        $html .= '</tr>';
        endforeach;
        $html .= '</tbody>';
        $html .= '</table>';
        $html .= $this->sources->getSource();
        return $html;
    }

    /**
     * @void
     * melakukan generate alias name dari field tabel yang di crud
     */
    private function generateAliasField()
    {
        foreach ($this->fields as $field):
            $newname = '4lt3r_'.$field->name.'_'.$this->getRandomString();
        $this->fields_alias[$field->name] = $newname;
        endforeach;
    }

    /**
    * Setting nama baru
    **/
    public function display_as($last_field, $new_field)
    {
        $this->display_as[$last_field] = $new_field;
    }

    public function generate()
    {
        if ($this->state == 'create') {
            return $this->generateAdd();
        } elseif ($this->state == 'read') {
            return $this->generateTable();
        } elseif ($this->state == 'edit') {
            $exp = explode('/', str_replace('http://', '', $this->base_url));
            $this->current_id = $exp[count($exp)-1];

            if (!is_numeric($this->current_id)) {
                redirect(base_url());
            }

            return $this->generateEdit();
        } elseif ($this->state == 'delete') {
            if (isset($_POST['deleteAlter'])) {
                if (in_array($this->CI->input->post('id'), $this->delete_skip)) {
                    redirect(base_url());
                }
                return $this->generateDelete();
            } else {
                redirect(base_url());
            }
        } elseif ($this->state == 'detail') {
            $exp = explode('/', str_replace('http://', '', $this->base_url));
            $exp = explode('/', str_replace('http://', '', $this->base_url));
            $this->current_id = $exp[count($exp)-1];
            return $this->generateDetail();
        }
    }

    public function saveData()
    {
        //generate insert semua data
        $form_data_original_table = $_POST;
        $form_data_relation_single = array();
        $form_data_relation_multiple = array();
        $remove_element = array();

        //hapus elemen jika memiliki relasi m_n ke tabel lain
        if (count($this->relation_m_n_multifield) > 0) {
            foreach ($this->relation_m_n_multifield as $key => $value) {
                array_push($remove_element, strtolower(str_replace(' ', '_', $key)));
                foreach ($value['additional_field'] as $a => $b) {
                    array_push($remove_element, strtolower(str_replace(' ', '_', $a)));
                }
            }
        }
        //hapus elemen jika memiliki relasi m_n single
        if (count($this->relation_m_n_single > 0)) {
            foreach ($this->relation_m_n_single as $key => $value) {
                array_push($remove_element, strtolower(str_replace(' ', '_', $key)));
            }
        }
        //hapus multifile
        if (count($this->file_upload_multiple) > 0) {
            array_push($remove_element, 'multifile');
        }
        $form_data_original_table = $this->unsetAdditionalElement($form_data_original_table, $remove_element);
        // insert ke tabel utama dahulu
        if ($this->model->insert($this->table, $form_data_original_table)) {
            //get id yang baru dimasukkan
            $id_main_table = $this->model->getLastId();
            //insert ke tabel relasi 1_n single
            if (count($this->relation_m_n_single) > 0) {
                foreach ($this->relation_m_n_single as $key => $value) {
                    $tmp = strtolower(str_replace(' ', '_', $key));
                    if (isset($_POST[$tmp])) {
                        foreach ($_POST[$tmp] as $a) {
                            $form_data_relation_single = array();
                            $form_data_relation_single[$value['id_relasi_tabel_left']] = $id_main_table;
                            $form_data_relation_single[$value['id_relasi_tabel_right']] = $a;
                            //masukkan ke tabel relasinya
                            $this->model->insert($value['tabel_tengah'], $form_data_relation_single);
                        }
                    }
                }
            }

            //insert ke tabel relasi 1_m_n multifield
            if (count($this->relation_m_n_multifield) > 0) {
                foreach ($this->relation_m_n_multifield as $key => $value) {
                    $tmp = strtolower(str_replace(' ', '_', $key));
                    foreach ($_POST[$tmp] as $a => $b) {
                        $form_data_relation_multiple = array();
                        $form_data_relation_multiple[$value['id_relasi_tabel_left']] = $id_main_table;
                        $form_data_relation_multiple[$value['id_relasi_tabel_right']] = $b;
                        foreach ($value['additional_field'] as $c => $d) {
                            //                            print_r($_POST[$c][$a]);
                            $form_data_relation_multiple[$c] = $_POST[$c][$a];
                        }
                        //masukkan ke tabel relasinya
                        $this->model->insert($value['tabel_tengah'], $form_data_relation_multiple);
                    }
                }
            }

            //insert ke tabel gambar banyak
            if (count($this->file_upload_multiple) > 0) {
                if (isset($_POST['multifile'])) {
                    foreach ($this->file_upload_multiple as $key => $value) {
                        foreach ($_POST['multifile'] as $a => $b) {
                            $this->model->insert($value['nama_tabel'], array(
                                $value['id_left_table'] => $id_main_table,
                                $value['nama_field'] => $b
                            ));
                        }
                    }
                }
            }
        }
    }

    public function editData()
    {
        if (!$_POST || $this->current_id == "") {
            redirect(base_url());
        }
        //generate update semua data
        $form_data_original_table = $_POST;
        $form_data_relation_single = array();
        $form_data_relation_multiple = array();
        $remove_element = array();

        //hapus elemen jika memiliki relasi m_n ke tabel lain
        if (count($this->relation_m_n_multifield) > 0) {
            foreach ($this->relation_m_n_multifield as $key => $value) {
                array_push($remove_element, strtolower(str_replace(' ', '_', $key)));
                foreach ($value['additional_field'] as $a => $b) {
                    array_push($remove_element, strtolower(str_replace(' ', '_', $a)));
                }
            }
        }
        //hapus elemen jika memiliki relasi m_n single
        if (count($this->relation_m_n_single > 0)) {
            foreach ($this->relation_m_n_single as $key => $value) {
                array_push($remove_element, strtolower(str_replace(' ', '_', $key)));
            }
        }
        //hapus multifile
        if (count($this->file_upload_multiple) > 0) {
            array_push($remove_element, 'multifile');
        }
        $form_data_original_table = $this->unsetAdditionalElement($form_data_original_table, $remove_element);
        // update ke tabel utama dahulu
        $update_where = $this->table.'.'.$this->field_table_key.'='.$this->current_id;
        if ($this->model->update($this->table, $update_where, $form_data_original_table)) {
            //get id yang baru dimasukkan
            $id_main_table = $this->current_id;

            //hapus dahulu baru di insert ulang
            //insert ke tabel relasi 1_n single
            if (count($this->relation_m_n_single) > 0) {
                foreach ($this->relation_m_n_single as $key => $value) {
                    $tmp = strtolower(str_replace(' ', '_', $key));
                    if (isset($_POST[$tmp])) {
                        $delete_where = $value['id_relasi_tabel_left'].'='.$this->current_id;
                        $this->model->delete($value['tabel_tengah'], $delete_where);
                        foreach ($_POST[$tmp] as $a) {
                            $form_data_relation_single = array();
                            $form_data_relation_single[$value['id_relasi_tabel_left']] = $this->current_id;
                            $form_data_relation_single[$value['id_relasi_tabel_right']] = $a;
                            //masukkan ke tabel relasinya
                            $this->model->insert($value['tabel_tengah'], $form_data_relation_single);
                        }
                    }
                }
            }

            //hapus dahulu lalu insert
            //insert ke tabel relasi 1_m_n multifield
            if (count($this->relation_m_n_multifield) > 0) {
                foreach ($this->relation_m_n_multifield as $key => $value) {
                    $tmp = strtolower(str_replace(' ', '_', $key));
                    $delete_where = $value['id_relasi_tabel_left'].'='.$id_main_table;
                    $this->model->delete($value['tabel_tengah'], $delete_where);
                    foreach ($_POST[$tmp] as $a => $b) {
                        $form_data_relation_multiple = array();
                        $form_data_relation_multiple[$value['id_relasi_tabel_left']] = $id_main_table;
                        $form_data_relation_multiple[$value['id_relasi_tabel_right']] = $b;
                        foreach ($value['additional_field'] as $c => $d) {
                            $form_data_relation_multiple[$c] = $_POST[$c][$a];
                        }
                        //masukkan ke tabel relasinya
                        $this->model->insert($value['tabel_tengah'], $form_data_relation_multiple);
                    }
                }
            }

            //insert ke tabel gambar banyak
            if (count($this->file_upload_multiple) > 0) {
                if (isset($_POST['multifile'])) {
                    foreach ($this->file_upload_multiple as $key => $value) {
                        $delete_where = $value['id_left_table'].'='.$id_main_table;
                        $this->model->delete($value['nama_tabel'], $delete_where);
                        foreach ($_POST['multifile'] as $a => $b) {
                            $this->model->insert($value['nama_tabel'], array(
                                $value['id_left_table'] => $id_main_table,
                                $value['nama_field'] => $b
                            ));
                        }
                    }
                }
            }
        }
        redirect($this->generateUrl($this->base_url, 2));
    }

    private function generateAdd()
    {
        $html = $this->openForm();
        foreach ($this->fields as $field):
            $tmp_type = $field->type;
            //kalau di skip tambah
            if (in_array($field->name, $this->tambah_skip)) {
                continue;
            }
            //kalau ada di relasi 1_n
            if (array_key_exists($field->name, $this->relation_1_n)) {
                $tmp_type = 'relasi_1_n';
            }
            //cek image upload
            if (in_array($field->name, $this->image_upload)) {
                $tmp_type = 'image';
            }
            //cek single file upload
            if (in_array($field->name, $this->file_upload)) {
                $tmp_type = 'file';
            }

        $html .= $this->generateElement($tmp_type, $field->name, $field->default);
        endforeach;

        //relasi 1 ke banyak tanpa field
        foreach ($this->relation_m_n_single as $key => $value) {
            $html .= $this->generateElement('relasi_m_n_single', $key);
        }

        //relasi 1 ke banyak dengan field tabel ketiga
        foreach ($this->relation_m_n_multifield as $key => $value) {
            $html .= $this->generateElement('relasi_m_n_multifield', $key);
        }


        //element dropzoneupload banyak
        if (count($this->file_upload_multiple) == 0) {
            $html .= '<div class="dropzone" style="display: none"></div>';
        } else {
            foreach ($this->file_upload_multiple as $key => $value) {
                $html .= $this->generateElement('multiple', $key);
            }
        }

        $html .= $this->additionalElement();
        $html .= $this->closeForm();
        $html .= $this->sources->getSource();
        return $html;
    }

    private function generateElement($typefield, $name, $value=null, $skiplabel=false, $multiname=false)
    {
        $newnamafield = $name;
        if (array_key_exists($newnamafield, $this->display_as)) {
            $newnamafield = $this->display_as[$newnamafield];
        }

        $html = "";
        if (!$skiplabel) {
            $html .= '<div class="form-group">';
            $html .= '<label>' . ucfirst(str_replace('_', ' ', $newnamafield)) . '</label>';
        }

        if ($typefield == 'int' || $typefield == 'mediumint' || $typefield == 'smallint') {
            $html .= '<input type="'.(in_array($name, $this->money_format)?'text':'number').'" name="' . $name .($multiname?'[]':''). '" class="form-control '.(in_array($name, $this->money_format)?'format_uang':'').'"  placeholder="'. ucfirst($newnamafield)
                .'" value="'.(in_array($name, $this->money_format)?number_format($value):$value).'" id="'.$name.'">';
        } elseif ($typefield == 'varchar') {
            $html .= '<input type="text" name="'. $name .($multiname?'[]':'').'" class="form-control" placeholder="'.ucfirst($newnamafield).'"
                value="'.$value.'" id="'.$name.'">';
        } elseif ($typefield == 'enum') {
            $html .= '<select name="'.$name.($multiname?'[]':'').'" class="form-control select2-single" id="'.$name.'">';
            $tmp = $this->get_enum_values($name);
            foreach ($tmp as $a) {
                if ($a == $value) {
                    $html .= '<option name="' . $a . '" selected>' . $a . '</option>';
                } else {
                    $html .= '<option name="' . $a . '">' . $a . '</option>';
                }
            }
            $html .= '</select>';
        } elseif ($typefield== 'text') {
            $html .= '<textarea class="tinymce" name="'.$name.($multiname?'[]':'').'" id="'.$name.'">'.$value.'</textarea>';
        } elseif ($typefield == 'datetime') {
            $html .= '<input type="text" name="'. $name .($multiname?'[]':'').'" class="form-control form_datetime" placeholder="'.ucfirst($newnamafield)
                .'" value="'.$value.'" id="'.$name.'">';
        } elseif ($typefield == 'decimal' || $typefield == 'tinyint') {
            $html .= '<input type="text" name="' . $name .($multiname?'[]':''). '" class="form-control format_decimal"  placeholder="'. ucfirst($newnamafield)
                .'" value="'.(in_array($name, $this->money_format)?number_format($value):$value).'" id="'.$name.'">';
        } elseif ($typefield == 'date') {
            $html .= '<input type="text" name="'. $name .($multiname?'[]':'').'" class="form-control form_date" placeholder="'.ucfirst($newnamafield)
                .'" value="'.$value.'" id="'.$name.'">';
        } elseif ($typefield == 'image') {
            if ($value == "") {
                $img = base_url($this->dir_upload.'default.png');
                $html .= '<br /><img src="'.$img.'" style="border:1px solid;padding:5px;width:100px;height:100px" class="pre-upload '.$name.'" data-field="'.$name.'" data-type="image"/>';
                $html .= '&nbsp;&nbsp;<a href="javascript:;" class="upload-remove" style="display: none"><i class="glyphicon glyphicon-remove"></i> Hapus</a>';
                $html .= '<input type="hidden" name="'.$name.'" id="'.$name.'" value="default.png" class="temp_upload_filename">';
            } else {
                $img = base_url($this->dir_upload.$value);
                $html .= '<br /><img src="'.$img.'" style="border:1px solid;padding:5px;width:100px;height:100px" class="pre-upload '.$name.'" data-field="'.$name.'" data-type="image"/>';
                $html .= '&nbsp;&nbsp;<a href="javascript:;" class="upload-remove" style=""><i class="glyphicon glyphicon-remove"></i> Hapus</a>';
                $html .= '<input type="hidden" name="'.$name.'" id="'.$name.'" value="'.$value.'" class="temp_upload_filename">';
            }
        } elseif ($typefield == 'file') {
            if ($value == "") {
                $html .= '<br />&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:;" class="pre-upload" data-field="'.$name.'" data-type="file"><i class="fa fa-cloud-upload fa-2x"></i></a>';
                $html .= '<span class="'.$name.' tmp_file_name_result"></span>';
                $html .= '&nbsp;&nbsp;<a href="javascript:;" class="upload-file-remove" style="display: none"><i class="glyphicon glyphicon-remove"></i> Hapus</a>';
                $html .= '<input type="hidden" name="'.$name.'" id="'.$name.'" value="" class="temp_upload_filename">';
            } else {
                $html .= '<br />&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:;" class="pre-upload" data-field="'.$name.'" data-type="file" style="display:none"><i class="fa fa-cloud-upload fa-2x"></i></a>';
                $html .= '<span class="'.$name.' tmp_file_name_result">';
                $html .= '&nbsp;&nbsp;&nbsp;<a href="'.base_url('assets/upload').'/'.$value.'" target="_blank"><i>'.$value.'</a></i>&nbsp;&nbsp;&nbsp;';
                $html .= '</span>';
                $html .= '&nbsp;&nbsp;<a href="javascript:;" class="upload-file-remove"><i class="glyphicon glyphicon-remove"></i> Hapus</a>';
                $html .= '<input type="hidden" name="'.$name.'" id="'.$name.'" value="'.$value.'" class="temp_upload_filename">';
            }
        } elseif ($typefield == 'relasi_1_n') {
            $tmp = $this->getRelation($this->relation_1_n[$name][0]);
            $html .= '<select name="'.$name.'" class="form-control select2-single" id="'.$name.'">';
            foreach ($tmp->result() as $a) {
                $b = $this->relation_1_n[$name][1];
                $c = $this->relation_1_n[$name][2];
                if ($a->$c == $value) {
                    $html .= '<option value="'.$a->$c.'" selected>'.$a->$b.'</option>';
                } else {
                    $html .= '<option value="'.$a->$c.'">'.$a->$b.'</option>';
                }
            }
            $html .= '</select>';
        } elseif ($typefield == 'multiple') {
            if (count($value) == 0) {
                $html .= '<div class="dropzone">';
                $html .= '<input type="file" name="" id="' . str_replace(' ', '_', $name) . '" style="display:none" class="multiple-upload" multiple>';
                $html .= '<input type="hidden" name="" value="" class="namemultifile" data-filename="">';
                $html .= '</div>';
            } else {
                $json= array();
                $tmp_field = $this->file_upload_multiple[$name]['nama_field'];
                foreach ($value as $a) {
                    $json[] = array(
                        'name'=>$a->$tmp_field,
                        'size'=>filesize('./'.$this->dir_upload.$a->$tmp_field),
                    );
                }
                $html .= '<script>var mocks = '.json_encode($json).';</script>';
                $html .= '<div class="dropzone">';
                $html .= '<input type="file" name="" id="' . str_replace(' ', '_', $name) . '" style="display:none" class="multiple-upload" multiple>';
                foreach ($value as $a) {
                    $html .= '<input type="hidden" name="multifile[]" value="'.$a->$tmp_field.'" class="namemultifile" data-filename="'.$a->$tmp_field.'">';
                }

                $html .= '</div>';
            }
        } elseif ($typefield == 'relasi_m_n_single') {
            if (count($value) == 0) {
                $tmp_name = str_replace(' ', '_', $name);
                $tbl = $this->relation_m_n_single[$name];
                $tmp_id = $tbl['id_tabel_right'];
                $tmp_name_field = $tbl['nama_tampil_tabel_right'];
                $relasi = $this->getRelation($tbl['tabel_right'])->result();
                $html .= '<select name="' . strtolower($tmp_name) . '[]" class="form-control select2-single" id="' . strtolower($tmp_name) . '" multiple="multiple">';

                foreach ($relasi as $key) {
                    if ($key->$tmp_id == $value) {
                        $html .= '<option value="' . $key->$tmp_id . '" selected>' . $key->$tmp_name_field . '</option>';
                    } else {
                        $html .= '<option value="' . $key->$tmp_id . '">' . $key->$tmp_name_field . '</option>';
                    }
                }
                $html .= '</select>';
            } else {
                $tmp_name = str_replace(' ', '_', $name);
                $tbl = $this->relation_m_n_single[$name];
                $tmp_id = $tbl['id_tabel_right'];
                $tmp_name_field = $tbl['nama_tampil_tabel_right'];
                $relasi = $this->getRelation($tbl['tabel_right'])->result();
                $html .= '<select name="' . strtolower($tmp_name) . '[]" class="form-control select2-single" id="' . strtolower($tmp_name) . '" multiple="multiple">';

                foreach ($relasi as $key) {
                    if (in_array($key->$tmp_id, $value)) {
                        $html .= '<option value="' . $key->$tmp_id . '" selected>' . $key->$tmp_name_field . '</option>';
                    } else {
                        $html .= '<option value="' . $key->$tmp_id . '">' . $key->$tmp_name_field . '</option>';
                    }
                }
                $html .= '</select>';
            }
        } elseif ($typefield == 'relasi_m_n_multifield') {
            if (count($value) == 0) {
                $tmp_name = str_replace(' ', '_', $name);
                $tbl = $this->relation_m_n_multifield[$name];
                $tmp_id = $tbl['id_tabel_right'];
                $tmp_name_field = $tbl['nama_tampil_tabel_right'];
                $relasi = $this->getRelation($tbl['tabel_right'])->result();
                $html .= '<table class="table table-bordered" id="table_'.$tmp_name.'">';
                $html .= '<thead>';
                $html .= '<tr>';
                $html .= '<th>&nbsp;</th>';
                $html .= '<td>&nbsp;</td>';
                foreach ($tbl['additional_field'] as $key => $value) {
                    $html .= '<th style="text-align: center">'.$value[0].'</th>';
                }
                $html .= '</tr>';
                $html .= '</thead>';
                $html .= '<tbody>';
                $html .= '<tr>';
                $html .= '<td width="5" style="vertical-align: middle"><a href="javascript:;" style="color: #ff0000;display: none" class="remove-row-table"><i class="glyphicon glyphicon-remove"></i> </a></td>';
                $html .= '<td>';
                $html .= '<select name="'.strtolower($tmp_name).'[]" class="form-control select2-single" style="width:100%">';
                foreach ($relasi as $key) {
                    $html .= '<option value="'.$key->$tmp_id.'">'.$key->$tmp_name_field.'</option>';
                }
                $html .= '</select>';
                $html .= '</td>';

                $colspan_footer = 2;
                foreach ($tbl['additional_field'] as $key => $value) {
                    $html .= '<td>';
                    $html .= $this->generateElement($value[1], $key, $value[2], true, true);
                    $html .= '</td>';
                    $colspan_footer++;
                }
                $html .= '</tr>';
                $html .= '</tbody>';
                $html .= '<tfoot>';
                $html .= '<tr>';
                $html .= '<td colspan="'.$colspan_footer.'" style="text-align:center">';
                $html .= '<a href="javascript:;" class="btn btn-primary btn-xs add_tabel_relation"><i class="glyphicon glyphicon-plus"></i> Tambah</a>';
                $html .= '</td>';
                $html .= '</tr>';


                $html .= '</tfoot>';
                $html .= '</table>';
            } else {
                //untuk edit
                $tmp_name = str_replace(' ', '_', $name);
                $tbl = $this->relation_m_n_multifield[$name];
                $tmp_id = $tbl['id_tabel_right'];
                $tmp_name_field = $tbl['nama_tampil_tabel_right'];
                $relasi = $this->getRelation($tbl['tabel_right'])->result();
                $html .= '<table class="table table-bordered" id="table_'.$tmp_name.'">';
                $html .= '<thead>';
                $html .= '<tr>';
                $html .= '<th>&nbsp;</th>';
                $html .= '<td>&nbsp;</td>';
                foreach ($tbl['additional_field'] as $key => $a) {
                    $html .= '<th style="text-align: center">'.$a[0].'</th>';
                }
                $html .= '</tr>';
                $html .= '</thead>';
                $html .= '<tbody>';
                foreach ($value as $a) {
                    $html .= '<tr>';
                    $html .= '<td width="5" style="vertical-align: middle"><a href="javascript:;" style="color: #ff0000;display: none" class="remove-row-table"><i class="glyphicon glyphicon-remove"></i> </a></td>';
                    $html .= '<td>';
                    $html .= '<select name="' . strtolower($tmp_name) . '[]" class="form-control select2-single" style="width:100%">';
                    $tmp_rs = $this->relation_m_n_multifield[$name]['id_relasi_tabel_right'];
                    foreach ($relasi as $key) {
                        if ($key->$tmp_id == $a->$tmp_rs) {
                            $html .= '<option value="' . $key->$tmp_id . '" selected>' . $key->$tmp_name_field . '</option>';
                        } else {
                            $html .= '<option value="' . $key->$tmp_id . '">' . $key->$tmp_name_field . '</option>';
                        }
                    }
                    $html .= '</select>';
                    $html .= '</td>';

                    $colspan_footer = 2;
                    foreach ($tbl['additional_field'] as $key => $values) {
                        $html .= '<td>';
                        $html .= $this->generateElement($values[1], $key, $a->$key, true, true);
                        $html .= '</td>';
                        $colspan_footer++;
                    }
                    $html .= '</tr>';
                }
                $html .= '</tbody>';
                $html .= '<tfoot>';
                $html .= '<tr>';
                $html .= '<td colspan="'.$colspan_footer.'" style="text-align:center">';
                $html .= '<a href="javascript:;" class="btn btn-primary btn-xs add_tabel_relation"><i class="glyphicon glyphicon-plus"></i> Tambah</a>';
                $html .= '</td>';
                $html .= '</tr>';


                $html .= '</tfoot>';
                $html .= '</table>';
            }
        } elseif ($typefield == 'time') {
            $html .= '<input type="text" name="'. $name .($multiname?'[]':'').'" class="form-control form_time" placeholder="'.ucfirst($newnamafield)
                .'" value="'.$value.'" id="'.$name.'">';
        }
        if (!$skiplabel) {
            $html .= "</div>";
        }

        return $html;
    }

    private function additionalElement()
    {
        $html = "";
        $html .= '<input type="file" name="userfile_1" style="display:none" class="file-upload browse">';
        $html .= '<input type="hidden" name="temp_upload" value="" id="temp_upload">';
        $html .= '<input type="hidden" name="temp_type" value="" id="temp_type">';
        $html .= '<input type="hidden" name="path_to_upload" value="'.$this->dir_upload.'" id="">';
        if (count($this->file_upload_multiple) == 0) {
            $html .= '<script>var mocks =null;</script>';
        }
        return $html;
    }

    private function closeForm()
    {
        $html = "";
        $html .= '<input type="submit" value="Simpan" class="btn btn-primary">';
        $html .= '</form>';
        return $html;
    }

    private function openForm()
    {
        return '<form role="form" method="post" action="" enctype="multipart/form-data" id="alterCRUD">';
    }

    private function unsetAdditionalElement($post=array(), $other=array())
    {
        $list_unset = array('temp_upload','temp_type','path_to_upload');
        $arr = array_merge($list_unset, $other);
        foreach ($arr as $key) {
            unset($post[$key]);
        }
        $tmp = array();
        foreach ($post as $a => $b) {
            if (in_array($a, $this->money_format)) {
                $tmp[$a]=$this->toInt($b);
            } else {
                $tmp[$a]=$b;
            }
        }
        return $tmp;
    }

    private function toInt($val, $sign=',')
    {
        return str_replace($sign, '', $val);
    }

    private function generateEdit()
    {
        $data = $this->getSingleData();

        $html = $this->openForm();
        foreach ($this->fields as $field):
            $tmp_type = $field->type;
        $tmp_value = $this->fields_alias[$field->name];
            //kalau di skip tambah
            if (in_array($field->name, $this->tambah_skip)) {
                continue;
            }

            //kalau ada di relasi 1_n
            if (array_key_exists($field->name, $this->relation_1_n)) {
                $tmp_type = 'relasi_1_n';
            }
            //cek image upload
            if (in_array($field->name, $this->image_upload)) {
                $tmp_type = 'image';
            }
            //cek single file upload
            if (in_array($field->name, $this->file_upload)) {
                $tmp_type = 'file';
            }


        $html .= $this->generateElement($tmp_type, $field->name, $data->$tmp_value);
        endforeach;

        //relasi 1 ke banyak tanpa field
        if (count($this->relation_m_n_single) > 0) {
            foreach ($this->relation_m_n_single as $key => $value) {
                $where = $value['tabel_tengah'] . '.' . $value['id_relasi_tabel_left'] . '=' . $this->current_id;
                $tmps = $this->model->getWhere($value['tabel_tengah'], $where);
                $tmp_value = array();
                foreach ($tmps->result() as $a) {
                    array_push($tmp_value, $a->id_kategori);
                }
                $html .= $this->generateElement('relasi_m_n_single', $key, $tmp_value);
            }
        }

        //relasi 1 ke banyak dengan field tabel ketiga
        if (count($this->relation_m_n_multifield) > 0) {
            foreach ($this->relation_m_n_multifield as $key => $value) {
                //ambil data dari tabel yang bersangkutan
                $where = $value['tabel_tengah'] . '.' . $value['id_relasi_tabel_left'] . '=' . $this->current_id;
                $tmps = $this->model->getWhere($value['tabel_tengah'], $where);
                $html .= $this->generateElement('relasi_m_n_multifield', $key, $tmps->result());
            }
        }


        //element dropzoneupload banyak
        if (count($this->file_upload_multiple) == 0) {
            $html .= '<div class="dropzone" style="display: none"></div>';
        } else {
            foreach ($this->file_upload_multiple as $key => $value) {
                $where = $value['nama_tabel'].'.'.$value['id_left_table'].'='.$this->current_id;
                $tmps = $this->model->getWhere($value['nama_tabel'], $where);
                $html .= $this->generateElement('multiple', $key, $tmps->result());
            }
        }

        $html .= $this->additionalElement();
        $html .= $this->closeForm();
        $html .= $this->sources->getSource();
        return $html;
    }

    private function generateDelete()
    {
        $data = array($this->field_table_key=>$this->CI->input->post('id'));
        $this->CI->M_altercrud->delete($this->table, $data);
        redirect($this->generateUrl($this->base_url, 2));
    }

    private function generateDetail()
    {
        $data = $this->getSingleData();

        $html = '<table class="table table-striped table-bordered">';
        foreach ($this->fields as $field):
            if (in_array($field->name, $this->detail_skip)) {
                continue;
            }

            //nama baru cek
            $newnamafield = $field->name;
        if (array_key_exists($field->name, $this->display_as)) {
            $newnamafield = $this->display_as[$field->name];
        }

        if (array_key_exists($field->name, $this->relation_new_name)) {
            $html .= '<tr>';
            $html .= '<th>'.ucfirst(str_replace('_', ' ', $newnamafield)).'</th>';
            $x = $this->relation_new_name[$field->name];
            if (array_key_exists($field->name, $this->relation_link)) {
                $z = $this->fields_alias[$field->name];
                $html .= '<td><a href="'.($this->relation_link[$field->name][1]?$this->relation_link[$field->name][0]:base_url($this->relation_link[$field->name][0].'/'.$data->$z))
                .'" target="_blank">'.$data->$x.'</a></td>';
            } else {
                $html .= '<td>'.$data->$x.'</td>';
            }
            $html .= '</tr>';
            continue;
        }

            //cek jika gambar
            if (in_array($field->name, $this->image_upload)) {
                $html .= '<tr>';
                $html .= '<th>'.ucfirst(str_replace('_', ' ', $newnamafield)).'</th>';
                $x = $this->fields_alias[$field->name];
                $img = base_url($this->dir_upload.$data->$x);
                $html .= '<td><a href="'.$img.'" target="_blank"><img src="'.$img.'" width="100"></a></td>';
                $html .= '</tr>';
                continue;
            }
            //cek jika file
            if (in_array($field->name, $this->file_upload)) {
                $html .= '<tr>';
                $html .= '<th>'.ucfirst(str_replace('_', ' ', $newnamafield)).'</th>';
                $x = $this->fields_alias[$field->name];
                $file = base_url($this->dir_upload.$data->$x);
                $html .= '<td><a href="'.$file.'" download>'.$data->$x.'</a></td>';
                $html .= '</tr>';
                continue;
            }

        $html .= '<tr>';
        $html .= '<th>'.ucfirst(str_replace('_', ' ', $newnamafield)).'</th>';
        $x = $this->fields_alias[$field->name];
        if (in_array($field->name, $this->money_format)) {
            $html .= '<td>Rp'.number_format($data->$x).'</td>';
            $html .= '</tr>';
            continue;
        }
        if (in_array($field->type, $this->date_type)) {
            $html .= '<td>'.date('d F Y - H:i', strtotime($data->$x)).' WIB</td>';
            $html .= '</tr>';
            continue;
        }
        $html .= '<td>'.$data->$x.'</td>';
        $html .= '</tr>';
        endforeach;

        //cek relasi 1_n single
        if (count($this->relation_m_n_single) > 0) {
            foreach ($this->relation_m_n_single as $a => $b) {
                $html .= '<tr>';
                $html .= '<th>'.$a.'</th>';
                $tmp_alias = $b['tabel_tengah'].'_'.$this->getRandomString();
                $tmp_alias_2 = $b['tabel_right'].'_'.$this->getRandomString();

                $rules['select'] = $tmp_alias_2.'.'.$b['nama_tampil_tabel_right'];
                $rules['join'] = array(array(
                    'join_real_table'=>$b['tabel_tengah'],
                    'join_table_alias'=>$tmp_alias,
                    'right_on'=>$tmp_alias.'.'.$b['id_relasi_tabel_left'],
                    'left_on'=>$this->table_alias.'.'.$b['id_tabel_left']
                ),array(
                    'join_real_table'=>$b['tabel_right'],
                    'join_table_alias'=>$tmp_alias_2,
                    'right_on'=>$tmp_alias_2.'.'.$b['id_tabel_right'],
                    'left_on'=>$tmp_alias.'.'.$b['id_relasi_tabel_right']
                ));
                $rules['where'] = $this->table_alias.'.'.$this->field_table_key.'='.$this->current_id;
                $tmp_data = $this->model->getAllData($this->table, $this->table_alias, $rules);
                $html .= '<td><ul>';
                foreach ($tmp_data->result() as $c) {
                    $e = $b['nama_tampil_tabel_right'];
                    $html .= '<li>'.$c->$e.'</li>';
                }
                $html .= '</ul></td>';
                $html .= '</tr>';
            }
        }
        if (count($this->relation_m_n_multifield) > 0) {
            foreach ($this->relation_m_n_multifield as $a => $b) {
                $html .= '<tr>';
                $html .= '<th>'.$a.'</th>';
                $tmp_alias = $b['tabel_tengah'].'_'.$this->getRandomString();
                $tmp_alias_2 = $b['tabel_right'].'_'.$this->getRandomString();

                $rules['select'] = $tmp_alias_2.'.'.$b['nama_tampil_tabel_right'];
                foreach ($b['additional_field'] as $c => $d) {
                    $rules['select'] .= ','.$tmp_alias.'.'.$c;
                }
                $rules['join'] = array(array(
                    'join_real_table'=>$b['tabel_tengah'],
                    'join_table_alias'=>$tmp_alias,
                    'right_on'=>$tmp_alias.'.'.$b['id_relasi_tabel_left'],
                    'left_on'=>$this->table_alias.'.'.$b['id_tabel_left']
                ),array(
                    'join_real_table'=>$b['tabel_right'],
                    'join_table_alias'=>$tmp_alias_2,
                    'right_on'=>$tmp_alias_2.'.'.$b['id_tabel_right'],
                    'left_on'=>$tmp_alias.'.'.$b['id_relasi_tabel_right']
                ));
                $rules['where'] = $this->table_alias.'.'.$this->field_table_key.'='.$this->current_id;
                $tmp_data = $this->model->getAllData($this->table, $this->table_alias, $rules);
                $html .= '<td><table class="table table-bordered">';
                $html .= '<tr>';
                $html .= '<th>'.$a.'</th>';
                foreach ($b['additional_field'] as $c) {
                    $html .= '<th>'.$c[0].'</th>';
                }
                $html .= '</tr>';
                foreach ($tmp_data->result() as $c) {
                    $html .= '<tr>';
                    $e = $b['nama_tampil_tabel_right'];
                    $html .= '<td>'.$c->$e.'</td>';
                    foreach ($b['additional_field'] as $g => $h) {
                        $html .= '<td>'.$c->$g.'</td>';
                    }
                    $html .= '</tr>';
                }
                $html .= '</table></td>';
                $html .= '</tr>';
            }
        }
        if (count($this->file_upload_multiple) > 0) {
            //            print_r($this->file_upload_multiple);
            foreach ($this->file_upload_multiple as $x => $y) {
                $html .= '<tr>';
                $html .= '<th>'.$x.'</th>';
                $html .= '<td>';
                $tmp_alias = $y['nama_tabel'].'_'.$this->getRandomString();
                $ruless['select'] = $tmp_alias.'.'.$y['nama_field'];
                $ruless['join'] = array(array(
                    'join_real_table'=>$y['nama_tabel'],
                    'join_table_alias'=>$tmp_alias,
                    'right_on'=>$tmp_alias.'.'.$y['id_left_table'],
                    'left_on'=>$this->table_alias.'.'.$this->field_table_key
                ));
//                print_r($y);
                $ruless['where'] = $this->table_alias.'.'.$this->field_table_key."=".$this->current_id;
                $tmp_data = $this->model->getAllData($this->table, $this->table_alias, $ruless);
//                print_r($tmp_data->result());
                foreach ($tmp_data->result() as $m) {
                    $x = $y['nama_field'];

                    $html .= '<a href="'.base_url($y['path']).'/'.$m->$x.'" target="_blank"><img src="'.base_url($y['path']).'/'.$m->$x.'" style="width:100px;max-height:100px"></a>';
                }
                $html .= '</td>';
                $html .= '</tr>';
            }
        }
        $html .= "</table>";
        $html .= '<br><a href="'.$this->generateUrl($this->base_url, 2).'"><button class="btn btn-default">Back</button></a>';
        return $html;
    }

    private function setState()
    {
        /**
         * Jika segment akhir = create, maka status create,
         * jika segment akhir = edit, maka status edit,
         * jika segment akhir = delete, maka status delete
         */
        $state = array('create','edit','delete','detail');
        $exp = explode('/', str_replace('http://', '', $this->base_url));
        if (in_array($exp[count($exp)-1], $state)) {
            $this->state = $exp[count($exp)-1];
        } else {
            if (in_array($exp[count($exp)-2], $state)) {
                $this->state = $exp[count($exp)-2];
            } else {
                $this->state = 'read';
            }
        }
    }

    private function get_enum_values($field)
    {
        $type = $this->CI->db->query("SHOW COLUMNS FROM {$this->table} WHERE Field = '{$field}'")->row(0)->Type;
        preg_match("/^enum\(\'(.*)\'\)$/", $type, $matches);
        $enum = explode("','", $matches[1]);
        return $enum;
    }

    public function set_relation($field, $table_relation, $field_table_relation, $id)
    {
        $this->relation_1_n[$field] = array($table_relation,$field_table_relation,$id);
    }

    /**
     * @param $name_tampil
     * @param $id_tabel_left
     * @param $third_table
     * @param $id_relasi_tabel_left
     * @param $id_relasi_tabel_right
     * @param $id_tabel_right
     * @param $tabel_right
     * @param $nama_tampil_tabel_right
     * @param array $field_isi_tabel_tengah
     */
    public function set_relation_m_n($name_tampil, $id_tabel_left, $third_table, $id_relasi_tabel_left, $id_relasi_tabel_right, $id_tabel_right, $tabel_right, $nama_tampil_tabel_right, $field_isi_tabel_tengah=array())
    {
        if (count($field_isi_tabel_tengah) == 0) {
            //simple relasi mn
            $this->relation_m_n_single[$name_tampil] = array(
                'id_tabel_left' =>$id_tabel_left,
                'tabel_tengah'=>$third_table,
                'id_relasi_tabel_left'=>$id_relasi_tabel_left,
                'id_relasi_tabel_right'=>$id_relasi_tabel_right,
                'id_tabel_right'=>$id_tabel_right,
                'tabel_right'=>$tabel_right,
                'nama_tampil_tabel_right'=>$nama_tampil_tabel_right,
            );
        } else {
            $this->relation_m_n_multifield[$name_tampil] = array(
                'id_tabel_left' =>$id_tabel_left,
                'tabel_tengah'=>$third_table,
                'id_relasi_tabel_left'=>$id_relasi_tabel_left,
                'id_relasi_tabel_right'=>$id_relasi_tabel_right,
                'id_tabel_right'=>$id_tabel_right,
                'tabel_right'=>$tabel_right,
                'nama_tampil_tabel_right'=>$nama_tampil_tabel_right,
                'additional_field' => $field_isi_tabel_tengah
            );
        }
    }

    public function set_field_upload($field, $type="image")
    {
        if ($type == "image") {
            array_push($this->image_upload, $field);
        } else {
            array_push($this->file_upload, $field);
        }
    }

    public function set_multi_upload($nama_tampil, $nama_tabel, $key_master, $nama_field, $path)
    {
        if (count($this->file_upload_multiple) > 0) {
            echo "Multi Upload Hanya boleh 1 saja";
            die();
        } else {
            $this->file_upload_multiple[$nama_tampil] = array(
                'id_left_table'=>$key_master,
                'nama_tabel'=>$nama_tabel,
                'nama_field'=>$nama_field,
                'path'=>$path
            );
        }
    }

    private function getRelation($table)
    {
        return $this->CI->M_altercrud->getAll($table);
    }

    /**
     * generate nama alias untuk tabel saat ini
     */
    private function setTableAlias()
    {
        $this->table_alias = 'table_'.$this->table.'_'.$this->getRandomString(8);
    }

    private function generateAction($url, $id)
    {
        $btn = array(
            'Edit'=>'btn btn-primary btn-xs',
            'Detail'=>'btn btn-warning btn-xs',
            'Delete'=>'btn btn-danger btn-xs'
        );

        if ($this->edit) {
            $tmp = '<a href="'.$url.'/edit/'.$id.'"><button class="'.$btn['Edit'].'" title="Edit"><i class="glyphicon glyphicon-pencil"></i> </button></a>';
        }

        if ($this->detail) {
            $tmp .= '&nbsp;&nbsp;<a href="'.$url.'/detail/'.$id.'"><button class="'.$btn['Detail'].'" title="Detail"><i class="glyphicon glyphicon-search"></i> </button></a>';
        }

        if ($this->delete) {
            $tmp .= '&nbsp;<form method="post" action="'.$url.'/delete/'.$id.'" class="form-inline alterDelete" style="float:right"><input type="hidden" name="id" value="'.$id.'"><input type="hidden" name="deleteAlter" value="delete"><button type="submit" value="x" class="'.$btn['Delete'].'" title="Delete"><i class="glyphicon glyphicon-remove"></i></button></form>';
        }

        return $tmp;
    }

    private function generateUrl($url, $removeLastBySlash=0)
    {
        $tmp = explode('/', str_replace('http://', '', $url));
        $newurl = "http://";
        $limit = count($tmp) - $removeLastBySlash;
        for ($i=0;$i<count($tmp);$i++) {
            if ($i < $limit) {
                $newurl .= '/'.$tmp[$i];
            }
        }
        return $newurl;
    }


    public function getSingleData()
    {
        $db['select'] = $this->generateSelect();

        if (count($this->relation_1_n) > 0) {
            foreach ($this->relation_1_n as $key => $value) {
                //jika relasi == tabel saat ini,maka
                if ($this->table == $value[0]) {
                    $alias_table = "new_join_" . $value[1] . '_' . $this->getRandomString();
                    $field_new_name = "field_new_" . $this->getRandomString();
                    $this->relation_new_name[$key] = $field_new_name;
                    $db['select'] .= ',' . '(SELECT ' . $alias_table . '.' . $value[1] . ' as '
                        . $field_new_name . ' FROM ' . $value[0] . ' as ' . $alias_table . ' WHERE '
                        . $alias_table . '.' . $this->field_table_key . ' = ' . $this->table_alias . '.' . $key . ') as ' . $field_new_name;
                } else {
                    //jika berelasi dengan tabel lain
                    $alias_table = "new_join_" . $value[1] . '_' . $this->getRandomString();
                    $field_new_name = "field_new_" . $this->getRandomString();
                    $this->relation_new_name[$key] = $field_new_name;
                    $db['select'] .= ',' . $alias_table . '.' . $value[1] . ' as    ' . $field_new_name;

                    $db['join'][$key] = array(
                        "join_real_table" => $value[0],
                        "join_table_alias" => $alias_table,
                        "left_on" => $this->table_alias . '.' . $key, //kunci dari master tabelnya
                        "right_on" => $alias_table . '.' . $value[2]
                    );
                }
            }
        } else {
            $db['join'] = array();
        }

        $db['where'] = array($this->table_alias.'.'.$this->field_table_key=>$this->current_id);
        return $this->model->getSingleData($this->table, $this->table_alias, $db);
    }

    public function getAllData()
    {
        $db['select'] = $this->generateSelect();
        $db['join'] = array();
        foreach ($this->relation_1_n as $key => $value) {
            //jika relasi == tabel saat ini,maka
            if ($this->table == $value[0]) {
                $alias_table = "new_join_" . $value[0] . '_' . $this->getRandomString();
                $field_new_name = "field_new_" . $this->getRandomString();
                $this->relation_new_name[$key] = $field_new_name;
                $db['select'] .= ',' . '(SELECT '.$alias_table.'.'.$value[1].' as '
                    .$field_new_name.' FROM '.$value[0].' as '.$alias_table.' WHERE '
                    .$alias_table.'.'.$this->field_table_key.' = '.$this->table_alias.'.'.$key.') as '.$field_new_name;
            } else {
                //jika berelasi dengan tabel lain
                $alias_table = "new_join_" . $value[0] . '_' . $this->getRandomString();
                $field_new_name = "field_new_" . $this->getRandomString();
                $this->relation_new_name[$key] = $field_new_name;
                $db['select'] .= ',' . $alias_table . '.' . $value[1] . ' as    ' . $field_new_name;
                $db['join'][$key] = array(
                    "join_real_table" => $value[0],
                    "join_table_alias" => $alias_table,
                    "left_on" => $this->table_alias . '.' . $key, //kunci dari master tabelnya
                    "right_on" => $alias_table . '.' . $value[2]
                );
            }
        }
        $db['where'] = array();
        // print_r($db['join']);
        return $this->model->getAllData($this->table, $this->table_alias, $db);
    }

    /**
     * @param $table = nama tabel
     * @param $field = field
     */
    private function generateSelect($table=null, $field=null)
    {
        $select = "";
        //jika tabelnya null, maka gunakan tabel saat ini
        if ($table == null) {
            $i = 0;
            foreach ($this->fields_alias as $a => $b):
                if ($i != 0) {
                    $select .= ', ';
                }
            if ($a == $this->field_table_key) {
                $select .= $this->table_alias . '.' . $a . ' as ' . $this->field_table_key_alias;
            } else {
                $select .= $this->table_alias . '.' . $a . ' as ' . $b;
            }
            $i++;
            endforeach;
        }

        return $select;
    }

    /**
     * @param int $length
     * @return string
     * Acak string untuk alias
     */
    public static function getRandomString($length = 8)
    {
        $string = "qwertyuioplkjhgfdsazxcvbnm1234567890QWERTYUIOPASDFGHJKLZXCVBNM!@#$%^&*(";
        $tmp = "";
        for ($i=0;$i<=$length;$i++) {
            $tmp .= $string[rand(1, 61)];
        }
        return $tmp;
    }
}

class Source
{
    private $state;
    private $source;
    private $base_url;
    private $dir_upload = '/assets/upload/';

    public function __construct($state, $base_url, $dir_upload)
    {
        $this->state = $state;
        $this->base_url = $base_url;
        $this->dir_upload = $dir_upload;
        $this->setCss();
        $this->setJs();
        $this->setCustomJs();
    }

    private function setCss()
    {
        $tmp = array();
        if ($this->state == 'create' || $this->state == 'edit') {
            $tmp = array(
                'assets/plugin/select2/dist/css/select2.min.css',
                'assets/plugin/datetimepicker/css/bootstrap-datetimepicker.min.css',
                'assets/plugin/datepicker/css/datepicker.css',
                'assets/plugin/dropzone/min/dropzone.min.css',
                'assets/plugin/jquery-ui/jquery-ui.min.css',
                'assets/plugin/jquery-ui/jquery-ui-timepicker.css',
            );
        } elseif ($this->state == 'read') {
            $tmp = array(
                'assets/plugin/datatables/datatables.min.css',
                'assets/plugin/fancybox/jquery.fancybox.css',
            );
        }

        foreach ($tmp as $s) {
            $this->source .= '<link href="'.base_url($s).'" rel="stylesheet">';
        }
    }

    private function setJs()
    {
        $tmp = array();
        if ($this->state == 'create' || $this->state == 'edit') {
            $tmp = array(
                'assets/plugin/select2/dist/js/select2.min.js',
                'assets/plugin/tinymce/js/tinymce/jquery.tinymce.min.js',
                'assets/plugin/tinymce/js/tinymce/tinymce.min.js',
                'assets/plugin/datetimepicker/js/bootstrap-datetimepicker.min.js',
                'assets/plugin/datepicker/js/bootstrap-datepicker.js',
                'assets/plugin/dropzone/dropzone.js',
                'assets/plugin/jquery-ui/jquery-ui.min.js',
                'assets/plugin/jquery-ui/jquery-ui-timepicker.js',
                'assets/plugin/jquery-mask/jquery.mask.min.js',

            );
        } elseif ($this->state == 'read') {
            $tmp = array(
                'assets/plugin/datatables/datatables.min.js',
                'assets/plugin/fancybox/jquery.fancybox.js',
            );
        }

        foreach ($tmp as $s) {
            $this->source .= '<script src="'.base_url($s).'"></script>';
        }
    }

    private function setCustomJs()
    {
        if ($this->state == 'read') {
            $deleteMsg = "Apakah Anda Yakin Ingin Menghapus ?";
            $this->source .= '<script>';
            $this->source .= '$(document).ready(function(){var table = $(\'#alterTable\').DataTable( {buttons: [{text: \'<i class="glyphicon glyphicon-plus"></i> Add\',action: function ( e, dt, node, config ) {window.location.href = "'.$this->base_url.'/create'.'";}},\'copy\', \'excel\', \'pdf\']} ); table.buttons().container().appendTo( $(\'.col-sm-6:eq(0)\', table.table().container())); $("form.alterDelete").submit(function() {if (confirm("'.$deleteMsg.'")) {return true;} else {return false;}}); });';
            $this->source .= '</script>';
            $this->source .= '<script>';
            $this->source .= '$(document).ready(function(){$("a.img_file_user").fancybox();})';
            $this->source .= '</script>';
        } elseif ($this->state == 'create') {
            $this->source .= '<script>';
            $this->source .= '$(".select2-single").select2();tinymce.init({selector:\'textarea.tinymce\'});';
            $this->source .= 'Dropzone.autoDiscover = false;var dropzone = new Dropzone(\'.dropzone\',{addRemoveLinks:true,autoDiscover:false,dictDefaultMessage: "Drag Drop File Kesini atau klik kotak ini.",url:"'.base_url('altercrud/upload').'",success:function(file,response){arr = JSON.parse(response);if(!arr.status){alert(arr.msg);dropzone.removeFile(file);if(confirm("Apakah Anda Ingin Membuat Direktori ?")){$.post("'.base_url('altercrud/create_dir').'",{path:"'.$this->dir_upload.'"},function(data){if(!data.status){alert(data.msg);} else {alert(data.msg);}},\'json\');}} else {var div = $(\'.namemultifile:first\');var klon = div.clone().prop(\'value\',arr.msg).prop(\'name\',\'multifile[]\');klon.attr(\'data-filename\',file.name);$(\'.dropzone\').append(klon);}},sending: function(file, xhr, formData){formData.append(\'upload_path\', "'.$this->dir_upload.'");},init: function() {this.on("removedfile",function(file){$(".dropzone").find("[data-filename=\'" + file.name + "\']").remove();})} });';
            $this->source .= '</script>';
            $this->source .= '<script>';
            $this->source .= '$(document).ready(function(){$( ".form_date" ).datepicker({showButtonPanel: true,dateFormat:"dd/mm/yy",changeMonth: true,changeYear: true});})';
            $this->source .= '</script>';
            $this->source .= '<script>';
            $this->source .= '$(document).ready(function(){$( ".form_datetime" ).datetimepicker({showButtonPanel: true,dateFormat:"dd/mm/yy",timeFormat:"hh:mm",changeMonth: true,changeYear: true});})';
            $this->source .= '</script>';
            $this->source .= '<script>';
            $this->source .= '$(document).ready(function(){$( ".form_time" ).timepicker();$(".format_uang").mask("000,000,000,000,000",{reverse:true});$(".format_decimal").mask("0000000000.00",{reverse:true});})';
            $this->source .= '</script>';
        } elseif ($this->state == 'edit') {
            $this->source .= '<script>';
            $this->source .= '$(".select2-single").select2();tinymce.init({selector:\'textarea.tinymce\'});';
            $this->source .= 'var newmocks = mocks;Dropzone.autoDiscover = false;var dropzone = new Dropzone(\'.dropzone\',{addRemoveLinks:true,autoDiscover:false,dictDefaultMessage: "Drag Drop File Kesini atau klik kotak ini.",url:"'.base_url('altercrud/upload').'",success:function(file,response){arr = JSON.parse(response);if(!arr.status){alert(arr.msg);dropzone.removeFile(file);if(confirm("Apakah Anda Ingin Membuat Direktori ?")){$.post("'.base_url('altercrud/create_dir').'",{path:"'.$this->dir_upload.'"},function(data){if(!data.status){alert(data.msg);} else {alert(data.msg);}},\'json\');}} else {var div = $(\'.namemultifile:first\');var klon = div.clone().prop(\'value\',arr.msg).prop(\'name\',\'multifile[]\');klon.attr(\'data-filename\',file.name);$(\'.dropzone\').append(klon);}},sending: function(file, xhr, formData){formData.append(\'upload_path\', "'.$this->dir_upload.'");},init: function() {thisDropzone = this; this.on("removedfile",function(file){$(".dropzone").find("[data-filename=\'" + file.name + "\']").remove();});  if(newmocks != null){$.each(newmocks, function(key,value){var mockFile = { name: value.name, size: value.size };thisDropzone.options.addedfile.call(thisDropzone, mockFile);thisDropzone.options.thumbnail.call(thisDropzone, mockFile, "'.base_url($this->dir_upload).'/"+value.name);});} }});';
            $this->source .= '</script>';
            $this->source .= '<script>';
            $this->source .= '$(document).ready(function(){$( ".form_date" ).datepicker({showButtonPanel: true,dateFormat:"dd/mm/yy",changeMonth: true,changeYear: true});})';
            $this->source .= '</script>';
            $this->source .= '<script>';
            $this->source .= '$(document).ready(function(){$( ".form_datetime" ).datetimepicker({showButtonPanel: true,dateFormat:"dd/mm/yy",timeFormat:"hh:mm",changeMonth: true,changeYear: true});})';
            $this->source .= '</script>';
            $this->source .= '<script>';
            $this->source .= '$(document).ready(function(){$( ".form_time" ).timepicker();$(".format_uang").mask("000,000,000,000,000",{reverse:true});$(".format_decimal").mask("0000000000.00",{reverse:true});})';
            $this->source .= '</script>';
        }
    }

    public function getSource()
    {
        return $this->source;
    }
}
