<?php $this->load->view('admin/'.$this->session->userdata('theme').'/v_header');?>
<?php $this->load->view('admin/'.$this->session->userdata('theme').'/v_sidebar');?>


    <!--  page-wrapper -->
    <div id="page-wrapper">
        <?php $this->load->view('admin/page/'.$user_page);?>
    </div>
    <!-- end page-wrapper -->

</div>
<!-- end wrapper -->
<?php $this->load->view('admin/'.$this->session->userdata('theme').'/v_footer');?>

