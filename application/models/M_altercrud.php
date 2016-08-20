<?php
/**
 * Created by PhpStorm.
 * User: febrian
 * Date: 7/6/16
 * Time: 4:28 PM
 */

defined('BASEPATH') or exit('No direct script access allowed');

class M_altercrud extends CI_Model
{
    public function insert($table, $data)
    {
        if ($this->db->insert($table, $data)) {
            return true;
        } else {
            $this->session->set_userdata('alter_err', $this->db->_error_message());
            return false;
        }
    }

    public function getSingle($table, $data)
    {
        return $this->db->get_where($table, $data)->row();
    }

    public function getAll($table)
    {
        return $this->db->get($table);
    }

    public function delete($table, $data)
    {
        $this->db->where($data);
        $this->db->delete($table);
    }

    public function update($table, $where, $data)
    {
        $this->db->where($where);
        $this->db->update($table, $data);
        return true;
    }

    public function setField($table, $order=array())
    {
        $tbl_field  = $this->db->field_data($table);
        $tmp_nama_field_ori = array();
        //get nama field asli table
        foreach ($tbl_field as $c) {
            array_push($tmp_nama_field_ori, $c->name);
        }
        //kalau tidak ada di array, tampilkan pesan
        if (count($order)) {
            $new_fields = array();
            foreach ($order as $a =>$b) {
                if (!in_array($b, $tmp_nama_field_ori)) {
                    echo "Maaf, Pengurutan Field Salah, field <strong><i>$b</i></strong> Tidak Ditemukan";
                    die();
                } else {
                    foreach ($tbl_field as $c) {
                        if ($b === $c->name) {
                            array_push($new_fields, $c);
                        }
                    }
                }
            }
            return $new_fields;
        } else {
            return $tbl_field;
        }
    }

    public function getSingleData($tabel, $aliastabel, $rules)
    {
        $this->db->select($rules['select'], false);

        foreach ($rules['join'] as $a => $b):
            $this->db->join(
                $b["join_real_table"].' as '.$b["join_table_alias"],
                $b["right_on"].' = '.$b["left_on"]
            );
        endforeach;

        $this->db->where($rules['where']);

        return $this->db->get($tabel.' as '.$aliastabel)->row();
    }

    public function getAllData($tabel, $aliastabel, $rules)
    {
        $this->db->select($rules['select'], false);

        if (isset($rules['join'])) {
            foreach ($rules['join'] as $a => $b):
                $this->db->join(
                    $b["join_real_table"] . ' as ' . $b["join_table_alias"],
                    $b["right_on"] . ' = ' . $b["left_on"]
                );
            endforeach;
        }

        if (isset($rules['where'])) {
            $this->db->where($rules['where']);
        }

        return $this->db->get($tabel.' as '.$aliastabel);
    }

    public function getLastId()
    {
        return $this->db->insert_id();
    }

    public function getWhere($tabel, $where)
    {
        $this->db->where($where);
        return $this->db->get($tabel);
    }
}
