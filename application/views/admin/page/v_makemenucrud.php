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
                    <form role="form" method="post" action="<?php echo base_url('admin/profile/simpan');?>">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>Nama Menu</label>
                                <input class="form-control" placeholder="Nama Menu Yang Tampil Disamping Kiri">
                            </div>
                            <div class="form-group">
                                <label>Parent</label>
                                <select name="parent_id" id="" class="form-control">
                                    <?php foreach($menus->result() as $key):?>
                                        <option value="<?php echo $key->id;?>"><?php echo $key->nama;?></option>
                                    <?php endforeach;?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>URL</label>
                                <input name="url" class="form-control" placeholder="URL">
                            </div>
                            <div class="form-group">
                                <label>Urutan</label>
                                <input name="urutan" class="form-control" placeholder="Urutan 1-N">
                            </div>
                            <div class="form-group">
                                <label>Icon</label>
                                <input name="icon" class="form-control" placeholder="Icon Font Awesome">
                            </div>
                            <div class="form-group">
                                <label>Status</label>
                                <select name="status" id="" class="form-control">
                                    <option value="Aktif">Aktif</option>
                                    <option value="Tidak Aktif">Tidak Aktif</option>
                                </select>
                            </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label>Nama Tabel</label>
                            <select name="nama_tabel" id="" class="form-control">
                                <?php foreach($this->db->list_tables() as $key):?>
                                    <option value="<?php echo $key;?>"><?php echo $key;?></option>
                                <?php endforeach;?>
                            </select>
                        </div>
                    </div>
                        <div class="col-lg-12">
                            <input type="submit" value="Simpan dan Generate" class="btn btn-success" style="width: 100%">
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>

