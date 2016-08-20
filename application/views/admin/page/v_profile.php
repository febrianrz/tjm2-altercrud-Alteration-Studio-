<!-- Page Heading -->
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">
            <?php echo $judul;?>
        </h1>
    </div>
</div>
<!-- /.row -->

<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <?php echo $judul_2;?>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-lg-12">
                        <form role="form" method="post" action="<?php echo base_url('admin/profile/simpan');?>">

                            <div class="form-group">
                                <label>Username</label>
                                <input class="form-control" value="<?php echo $this->session->userdata('username');?>" readonly="readonly">
                            </div>
                            <div class="form-group">
                                <label>Nama</label>
                                <input name="nama" class="form-control" value="<?php echo $this->session->userdata('nama');?>">
                            </div>
                            <div class="form-group">
                                <label>Theme</label>
                                <select name="theme" class="form-control">
                                    <option value="sbadmin1" <?php echo ($this->session->userdata('theme')=='sbadmin1'?'selected':'');?>>SB Admin 1</option>
                                    <option value="sbadmin2" <?php echo ($this->session->userdata('theme')=='sbadmin2'?'selected':'');?>>SB Admin 2</option>
                                    <option value="siminta" <?php echo ($this->session->userdata('theme')=='siminta'?'selected':'');?>>SIMINTA</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Password</label>
                                <input name="password" class="form-control" value="" placeholder="Password">
                            </div>
                            <button type="submit" class="btn btn-default">Simpan</button>
                            <div style="margin-bottom: 70px"></div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

