<?php $this->load->view('admin/'.$this->session->userdata('theme').'/v_header');?>
<?php $this->load->view('admin/'.$this->session->userdata('theme').'/v_sidebar');?>


    <div id="page-wrapper">
        <?php $this->load->view('admin/page/'.$user_page);?>
    </div>
    <!-- /#page-wrapper -->

</div>
<?php $this->load->view('admin/'.$this->session->userdata('theme').'/v_footer');?>
