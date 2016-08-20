<?php

class Menu{

    private $CI;
    private $table;
    private $first_li;
    private $top_tag_open;
    private $top_tag_close;
    private $item_tag_open;
    private $item_tag_close;
    private $anchor_has_child_open;
    private $anchor_has_child_close;
    private $second_tag_open;
    private $second_tag_close;
    private $item_second_tag_open;
    private $item_second_tag_close;
    private $html;

    public function __construct(){
        $this->CI =& get_instance();
        $this->table = 't_menu';
        $this->generateTag();
    }

    public function getMenu($kategori=1){

        $this->html .= $this->top_tag_open;
        $this->html .= $this->first_li;
//        ambil induknya dulu
        $this->CI->db->where('id != ',1);
        $this->CI->db->where('status = "Aktif"');
        $this->CI->db->order_by('urutan','asc');
        $this->CI->db->order_by('nama','asc');
        $parent = $this->CI->db->get_where($this->table,array('id_tkategori_user'=>$kategori,'parent_id'=>1));
        foreach($parent->result() as $pr){
            $this->html .= str_replace('>',' class="li_'.$pr->id.'"',$this->item_tag_open).'>';
            //cek anak
            $this->CI->db->where('status = "Aktif"');
            $child = $this->CI->db->get_where($this->table,array('parent_id'=>$pr->id));
            if($child->num_rows()==0){
                $this->html .= '<a href="'.base_url($pr->url).'">'.'<i class="'.$pr->icon.'"></i>&nbsp; '.$pr->nama.'</a>';
            } else {
                $this->html .= str_replace('#demo','#menu'.$pr->id,$this->anchor_has_child_open);
                $this->html .= '<i class="'.$pr->icon.'"></i>&nbsp; '.$pr->nama.' &nbsp;&nbsp;&nbsp;<i class="caret"></i>';
                $this->html .= $this->anchor_has_child_close;
                $this->html .= str_replace('demo','menu'.$pr->id,$this->second_tag_open);
                foreach($child->result() as $ch){
                    $this->html .= str_replace('>',' class="li_'.$pr->id.'"',$this->item_second_tag_open).'>';
                    $this->html .= '<a href="'.base_url($ch->url).'">'.'<i class="'.$ch->icon.'"></i>&nbsp; '.$ch->nama.'</a>';
                    $this->html .= $this->item_second_tag_close;
                }
                $this->html .= $this->second_tag_close;

            }
            $this->html .= $this->item_tag_close;
        }

        $this->html .= $this->top_tag_close;
        return $this->html;
    }

    private function generateTag(){
        $this->firstLi();
        if($this->CI->session->userdata('theme') == 'sbadmin1'){
            $this->top_tag_open = '<ul class="nav navbar-nav side-nav">';
            $this->top_tag_close = '</ul>';
            $this->item_tag_open = ' <li>';
            $this->item_tag_close = ' </li>';
            $this->anchor_has_child_open = '<a href="javascript:;" data-toggle="collapse" data-target="#demo">';
            $this->anchor_has_child_close = '</a>';
            $this->second_tag_open = '<ul id="demo" class="collapse">';
            $this->second_tag_close = '</ul>';
            $this->item_second_tag_open = "<li>";
            $this->item_second_tag_close = "</li>";
        } else if($this->CI->session->userdata('theme') == 'sbadmin2'){
            $this->top_tag_open = '<ul class="nav" id="side-menu">';
            $this->top_tag_close = '</ul>';
            $this->item_tag_open = ' <li>';
            $this->item_tag_close = ' </li>';
            $this->anchor_has_child_open = '<a href="#">';
            $this->anchor_has_child_close = '</a>';
            $this->second_tag_open = '<ul class="nav nav-second-level">';
            $this->second_tag_close = '</ul>';
            $this->item_second_tag_open = "<li>";
            $this->item_second_tag_close = "</li>";
        } else if($this->CI->session->userdata('theme') == 'siminta'){
            $this->top_tag_open = '<ul class="nav" id="side-menu">';
            $this->top_tag_close = '</ul>';
            $this->item_tag_open = ' <li>';
            $this->item_tag_close = ' </li>';
            $this->anchor_has_child_open = '<a href="#">';
            $this->anchor_has_child_close = '</a>';
            $this->second_tag_open = '<ul class="nav nav-second-level">';
            $this->second_tag_close = '</ul>';
            $this->item_second_tag_open = "<li>";
            $this->item_second_tag_close = "</li>";
        }
    }

    private function firstLi(){
        if($this->CI->session->userdata('theme') == 'sbadmin1'){
            $this->first_li = '';
        } else if($this->CI->session->userdata('theme') == 'sbadmin2'){
            $this->first_li = '<li class="sidebar-search">
                            <div class="input-group custom-search-form">
                                <input type="text" class="form-control" placeholder="Search...">
                                <span class="input-group-btn">
                                <button class="btn btn-default" type="button">
                                    <i class="fa fa-search"></i>
                                </button>
                            </span>
                            </div>
                            <!-- /input-group -->
                        </li>';
        } else if($this->CI->session->userdata('theme') == 'siminta'){
            $this->first_li = '<li>
                        <!-- user image section-->
                        <div class="user-section">
                            <div class="user-section-inner">
                                <img src="assets/img/user.jpg" alt="">
                            </div>
                            <div class="user-info">
                                <div>Jonny <strong>Deen</strong></div>
                                <div class="user-text-online">
                                    <span class="user-circle-online btn btn-success btn-circle "></span>&nbsp;Online
                                </div>
                            </div>
                        </div>
                        <!--end user image section-->
                    </li>
                    <li class="sidebar-search">
                        <!-- search section-->
                        <div class="input-group custom-search-form">
                            <input type="text" class="form-control" placeholder="Search...">
                            <span class="input-group-btn">
                                <button class="btn btn-default" type="button">
                                    <i class="fa fa-search"></i>
                                </button>
                            </span>
                        </div>
                        <!--end search section-->
                    </li>';
        }
    }

}