<!-- /container -->
<!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
<!--<script src="assets/js/ie10-viewport-bug-workaround.js"></script>-->

<script>


function uploadfile(){
  $("#filebukti").click();
}

    // Attach a submit handler to the form

    var options = {
        beforeSubmit: showRequest,
        success: showResponse,
        dataType: 'json'
    };
    $('#formmeeting').ajaxForm(options);

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
       alert(data['status'])
//                        if (data.status == 1) {

      //  var msg = JSON.parse(data);

        // alert(m);
//                        document.write(data.type);

//alert(data.status);
        if (data['status'] == 1 || data['status'] == '1') {
            $('#formmeeting').resetForm();
            $('#addmodalmeeting').modal('hide');

alert(data['msg']);

                location.reload();

        } else {
            alert(data['msg']);
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
    function isNumeric(n) {
      return !isNaN(parseFloat(n)) && isFinite(n);
    }
</script>

<form class="form-horizontal" action="<?php echo base_url();?>meeting/savemeeting" id="formmeeting" method="POST" enctype="multipart/form-data">
  <div class="box-body" style="padding-left:20px;padding-right:20px;overflow: auto;
    overflow-x:hidden; max-height:400px">
    <div class='row'>
      <div class="col-md-6">

        <div class="form-group">
          <label for="" class=" control-label">Title<span class="mandatori" style="color:red">*</span></label>
          <input type="hidden" name="id_meeting" id="id_meeting"   value="">
          <input type="hidden" name="project_id" id="project_idmeeting"   value="<?php echo $project_id; ?>">
          <input type="text" class="form-control " name="title" id="title"  placeholder="Title" style="width: 80%;" required>

        </div>

        <div class="form-group">
          <label for="" class=" control-label">Location<span class="mandatori" style="color:red">*</span></label>

          <textarea class="form-control " name="lokasi" id="lokasi"  placeholder="" style="width: 80%;" required></textarea>

        </div>
        <div class="form-group">
          <label for="" class=" control-label">Date<span class="mandatori" style="color:red">*</span></label>
          <div class="row">
            <div class="col-sm-4">
              <input type="text" class="form-control date" name="do_date" id="do_date"  placeholder="" required>
            </div>
            <div class="col-sm-3">
              <!-- <input type="text" class="form-control timepicker" name="starttime" id="starttime"  placeholder=""> -->
            </div>
            <div class="col-sm-2" style="width:1px;vertical-align: middle; margin:0">
              -
            </div>
            <div class="col-sm-3">
              <!-- <input type="text" class="form-control timepicker" name="endtime" id="endtime"  placeholder=""> -->
            </div>


          </div>

        </div>
      </div>
      <div class="col-md-6">
        <div class="form-group">
          <label>Member Internal</label>

          <select class="form-control select2" multiple='multiple' name="member_internal[]" id="member" style="width: 80%;">';
            <?php $x='';

            foreach ($member as $o) {
              # code...

              echo '<option value="'.$o['userid'].'">'.$o['salutation']." ".$o['nama'].'</option>';


            }
            ?>
          </select>
        </div>
        <div class="form-group">
          <label for="" class=" control-label">Member External</label>

          <textarea class="form-control " name="member_external" id="member_external"  placeholder="" style="width: 80%;"></textarea>

        </div>
      </div>
    </div>

    <div class="row divrowdetailmeeting">
      <div class="col-md-11">
        <div class="row divdetailmeeting">
          <div class="col-md-6">
            <div class="form-group">
              <label for="exampleInputEmail1">Description</label>
              <input class="form-control" name = "desc[]" id="descdetailmeeting" placeholder="" style="width: 90%">
            </div>
          </div>
          <div class="col-md-3">
            <div class="form-group">
              <label for="exampleInputEmail1">PIC</label>
              <select class="form-control " name="pic[]" id="pic" style="width: 90%;">';
                <option value="">None</option>
                <?php $x='';

                foreach ($member as $o) {
                  # code...

                  echo '<option value="'.$o['userid'].'">'.$o['salutation']." ".$o['nama'].'</option>';


                }
                ?>
              </select>
            </div>
          </div>
          <div class="col-md-3">

            <div class="form-group">
              <label for="exampleInputEmail1">End Date</label>
              <div class="row">
                  <input type="text" class="form-control date" name="enddate[]" id="enddatemeeting"  placeholder="" >
              </div>




            </div>

          </div>
        </div>
      </div>
      <div class="col-md-1">
        <div class="form-group" style="margin-top:25px">
<label for="exampleInputEmail1">&nbsp</label>
        <a href="#" class='btn btn-info' style="float: right;" id="buttonadddesc" onclick="adddesc()"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span></a>
</div>
      </div>
    </div>
    <div class="row">
      <div class="col-md-11">
        <div id="divstoreeditdetail">

        </div>
      </div>
      <div class="col-md-1">
      </div>
    </div>
    <div class="row">
      <div class="col-md-11">
        <div id="divstoreadddetail">

        </div>
      </div>
      <div class="col-md-1">
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
            <input type="text" class="form-control" name="remark" id="remark" placeholder="Remark">
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
  var newDiv=$(".divdetailmeeting").clone(true).appendTo("#divstoreadddetail");
  // newDiv.find('.input.enddatemeeting:').removeClass('hasDatepicker').datepicker();
  newDiv.find('.date').each(function() {
            $(this).removeAttr('id').removeClass('hasDatepicker'); // added the removeClass part.
            $('.date').datepicker();
        });

}

</script>
