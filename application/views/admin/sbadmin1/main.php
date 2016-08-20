<?php $this->load->view('admin/'.$this->session->userdata('theme').'/v_header');?>
<?php $this->load->view('admin/'.$this->session->userdata('theme').'/v_sidebar');?>

    <div id="page-wrapper">

        <div class="container-fluid">

            <?php $this->load->view('admin/page/'.$user_page);?>

        </div>
        <!-- /.container-fluid -->

    </div>
    <!-- /#page-wrapper -->

</div>
<!-- /#wrapper -->
<?php $this->load->view('admin/'.$this->session->userdata('theme').'/v_footer');?>


