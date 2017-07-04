<!-- /container -->
<!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
<!--<script src="assets/js/ie10-viewport-bug-workaround.js"></script>-->

<script>


function uploadfile(){
  $("#filebukti").click();
}
function operational(){
  $("#divoperational").show();
}
    // Attach a submit handler to the form

    var options = {
        beforeSubmit: showRequest,
        success: showResponse,
        dataType: 'json'
    };
    $('#formdocumentasi').ajaxForm(options);

    function showRequest(formData, jqForm, options) {
        return true;
    }
    function closemodalmeeting() {
        $('addMemberModal').modal('hide');
        $('#addModal').modal('hide');
        $('#addModal').modal('hide');
        $('#addmodalmeeting').modal('hide');
        $('#formmeeting').resetForm();
        location.reload();
    }
    //Hide uploadExcel by default
    $('#uploadexcel').hide();
    function hideOtherMenu(type) {
        if (type === 0) {
            $('#checkboxplatform').show();
            $('#uploadexcel').hide();
            $('#filename').val('');
        } else if (type === 1) {
            $("input:checkbox").removeAttr('checked');
            $('#checkboxplatform').hide();
            $('#uploadexcel').show();
        }
    }
    function showResponse(data) {
//        alert(data);
//                        if (data.status == 1) {

//        var msg = JSON.parse(data);
//                        document.write(data.type);

//alert(data.status);
        if (data.status == 1 || data.status == '1') {
            $('#formmeeting').resetForm();
            $('#addmodalmeeting').modal('hide');

  alert(data.msg);

                location.reload();

        } else {
            alert(data.msg);
        }


//                            location.reload();
//                        } else if (data.status == 2) {
//                            alert(data);
//                            window.location.replace("<?php echo base_url(''); ?>");
//                        } else {
//                            alert(data.message);
//                            $('#submitAdd').prop('disabled', false);
//                        }
    }

</script>

<form class="form-horizontal" action="<?php echo base_url();?>dokumentasi/savedokumentasi" id="formdocumentasi" method="POST" enctype="multipart/form-data">
  <div class="box-body" style="padding-left:20px;padding-right:20px;overflow: auto;
    overflow-x:hidden; max-height:400px">
    <div class='row'>
      <div class="col-md-12">

        <div class="form-group">
          <label for="" class="col-sm-2 control-label">Name<span class="mandatori">*</span></label>
          <div class="col-sm-10">
          <input type="hidden" name="id_doc" id="id_doc"   value="">
          <input type="hidden" name="project_id" id="project_idmeeting"   value="<?php echo $project_id; ?>">
          <input type="text" class="form-control " name="namedoc" id="namedoc"  placeholder="Name" style="width: 80%;" required="">
        </div>
        </div>

        <div class="form-group">
          <label for="" class="col-sm-2 control-label">Hours<span class="mandatori">*</span></label>
          <div class="col-sm-10">
          <input type="number" class="form-control " name="hoursdoc" id="hoursdoc"  placeholder="Hours" style="width: 30%;" required="">
        </div>
        </div>
        <div class="form-group">
          <label for="inputEmail3" class="col-sm-2 control-label">File<span class="mandatori">*</span></label>
          <div class="col-sm-10">

            <input type="file" id="filedoc"  name ="filedoc" required>
          </div>
        </div>
      </div>
      </div>


    <div class="row">
      <div class="col-md-12">
        <a href="#" class='' style="float: left;" id="linkoperationacost" onclick="operational()">Operational Cost</a>
      </div>
    </div>
    <div class="row" id="divoperational" style="display: none;">
      <div class="col-md-12">
        <div class="form-group">
          <label for="inputEmail3" class="col-sm-2 control-label">Remark</label>
          <div class="col-sm-10">
            <input type="text" class="form-control" name="remark" id="remark" placeholder="Remark" style="width:80%">
          </div>
        </div>
        <div class="form-group">
          <label for="inputEmail3" class="col-sm-2 control-label">Price</label>
          <div class="col-sm-10">
            <input type="number" class="form-control" name="price" id="price" placeholder="Price" style="width:50%">
          </div>
        </div>
        <div class="form-group">
          <label for="inputEmail3" class="col-sm-2 control-label"></label>
          <div class="col-sm-10">
            <img type="image" src="<?php echo base_url(); ?>assets/img/default-no-image.png" id="setimagebukti" onclick="uploadfile()"width="100px"/>
            <input type="file" id="filebukti"  name ="imgbukti"style="display: none;" />
          </div>
        </div>
      </div>
    </div>



  </div><!-- /.box-body -->
  <div class="box-footer">
    <button type="submit" class="btn btn-primary pull-right" id="idyes"style="margin-left:5px">Save</button>
    <button  class="btn btn-primary pull-right" id="idno" onclick="closemodalmeeting()">Cancel</button>

  </div><!-- /.box-footer -->
</form>
<script>
function readURL(input) {

if (input.files && input.files[0]) {
    var reader = new FileReader();

    reader.onload = function (e) {
        $('#setimagebukti').attr('src', e.target.result);
    }

    reader.readAsDataURL(input.files[0]);
}
}

$("#filebukti").change(function(){
readURL(this);
});
function adddesc(){
  var newDiv=$(".divdetailmeeting").clone().appendTo("#divstoreadddetail");
  newDiv.find('input[id=enddatedetail]').datepicker();
}

</script>
