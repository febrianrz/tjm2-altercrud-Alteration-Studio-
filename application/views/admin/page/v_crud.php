
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

                         <?php  echo $output?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    // Prevent Dropzone from attaching twice.

    $(document).ready(function(){
        var img = false;
        $("form#alterCRUD").submit(function(){
            if(img){
                $.ajax({url: "<?php echo base_url('altercrud/upload_single');?>",
                    type: "POST",
                    data: new FormData(this),
                    contentType: false,
                    cache: false,
                    processData:false,
                    dataType:"json",
                    success: function(data){
                        if(data.status){
                            $("#"+data.field).val(data.msg);
                            if(data.type == 'file'){
                                $("#" + data.field).parent().find('.upload-file-remove').slideDown();
                                $("#" + data.field).parent().find('.pre-upload').fadeOut();
                                $("."+data.field).html('&nbsp;&nbsp;&nbsp;<a href="<?php echo base_url('assets/upload');?>/'+data.msg+'" target="_blank"><i>'+data.msg+'</a></i>&nbsp;&nbsp;&nbsp;');
                            } else {
                                $("."+data.field).attr("src","<?php echo base_url('assets/upload');?>/"+data.msg);
                                $("#" + data.field).parent().find('.upload-remove').slideDown();
                            }
                        } else {
                            alert(data.msg);
                        }
                        img = false;
                        $("#temp_upload").val("");
                        $("#temp_type").val("");
                    },error: function(err){console.log(err);}});img = false;return false;} else {if(confirm("Apakah Anda Yakin ?")){return true;} else {return false;}}});$(".browse").on('change',function(){img = true;$("form#alterCRUD").trigger('submit');});$(".pre-upload").on('click',function(){var field = $(this).data("field");var type = $(this).data("type");$("#temp_upload").val(field);$("#temp_type").val(type);$(".browse").trigger('click');});$(".upload-remove").on("click",function(){$(this).parent().find('.pre-upload').attr("src","<?php echo base_url('assets/upload/default.png');?>");$(this).parent().find('.temp_upload_filename').val("default.png");$(this).slideUp();});$(".upload-file-remove").on("click",function(){$(this).parent().find('.pre-upload').slideDown();$(this).parent().find('.temp_upload_filename').val("");$(this).parent().find('.tmp_file_name_result').html("");$(this).slideUp();})
        $(".add_tabel_relation").on('click',function(){
            var clone = $(this).parents().eq(3);
            var table_id = clone.attr('id');
            $('#'+table_id).find('select').select2('destroy').end();
            var row = $('#'+table_id+' tbody>tr:last').clone(true);
            $('#'+table_id).find('select').select2();
            row.find('select').select2();
            row.find('input').val("");
            row.find('.remove-row-table').css('display','block');
            row.insertAfter('#'+table_id+' tbody>tr:last');

            $(".remove-row-table").on('click',function(){

                    $(this).parents().eq(1).remove()

            });
        });

    });


</script>
