<!-- Page Heading -->
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">
            Dashboard <small></small>
        </h1>
        <ol class="breadcrumb">
            <li class="active">
                <i class="fa fa-dashboard"></i> Dashboard
            </li>
        </ol>
    </div>
</div>
<!-- /.row -->

<!--<div class="row">-->
<!--    <div class="col-lg-12">-->
<!--        <div class="alert alert-info alert-dismissable">-->
<!--            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>-->
<!--            <i class="fa fa-info-circle"></i>  <strong>Like SB Admin?</strong> Try out <a href="http://startbootstrap.com/template-overviews/sb-admin-2" class="alert-link">SB Admin 2</a> for additional features!-->
<!--        </div>-->
<!--    </div>-->
<!--</div>-->
<!-- /.row -->

<div class="row">
    <?php
    foreach($rows_dashboard->result() as $a):?>
        <div class="col-lg-<?php echo $a->panel_width;?> col-md-6">
            <div class="panel <?php echo $a->panel;?>">
                <div class="panel-heading">
                    <div class="row">
                        <div class="col-xs-3">
                            <i class="<?php echo $a->icon;?> fa-5x"></i>
                        </div>
                        <div class="col-xs-9 text-right">
                            <div class="huge">
                                <?php if(strip_tags($a->query) == ""){
                                    $query = "select * from t_menu;";
                                } else {
                                    $query = strip_tags($a->query);
                                }
                                echo $this->db->query(strip_tags($query))->num_rows();?></div>
                            <div><?php echo $a->nama;?></div>
                        </div>
                    </div>
                </div>
                <a href="<?php echo base_url($a->url);?>">
                    <div class="panel-footer">
                        <span class="pull-left">View Details</span>
                        <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                        <div class="clearfix"></div>
                    </div>
                </a>
            </div>
        </div>
    <?php endforeach;?>
</div>
<!-- /.row -->

