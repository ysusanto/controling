<style>
.bootstrap-timepicker-widget{
  z-index: 2000;
}
</style>

<script>
    $(document).ready(function () {
        $(".timepicker").timepicker({
            showInputs: false,
            use24hours: true,
            showSeconds: true,
            showMeridian: false,
            minuteStep: 1
        });
  $('.btnform').hide();
        datetoday();
//        $("#date").datepicker();
        timesheet(<?php echo $projectid; ?>);
    });


    function timesheet(id,type='') {
        var proplat = $('#proplat').val();
        if(type==''){
          $('#divbtn').show();
          $('.btnform').hide();
        }

        var table = $('#tabeltimesheet').dataTable({
            // "sPaginationType": "full_numbers",
//            "bJQueryUI": true,
            "iDisplayLength": 30,
            "bDestroy": true,
            "bFilter": false,
            "bLengthChange": false,
            "aaSorting": [],
            "bPaginate": false,
            "bAutoWidth": true,
            "bSortable": false,
            "bSortClasses": true,
            "sAjaxSource": '<?php echo base_url(); ?>timesheet/timesheettable/'+type,
            "fnServerParams": function (aoData) {
                aoData.push({'name': 'project_id', 'value': id},
                {'name': 'proplat_id', 'value': proplat}

                );
            }});

    }
    function savetimesheet(id) {
        var hour = $('#' + id).val();
        $.ajax({
            type: 'post',
            url: '<?php echo base_url(); ?>timesheet/savetimesheet',
            data: 'item_id=' + id + '&hour=' + hour,
            success: function (msg) {

                viewitem(id);
//                $('#addModal').modal('show');
            }
        });
    }
    function addmainhours(id) {
        $.ajax({
            type: 'post',
            url: '<?php echo base_url(); ?>timesheet/itemname/' + id,
//            data: 'item_id=' + id + '&hour=' + hour,
            success: function (msg) {
//                var data = JSON.parse(msg);
                $('#itemnameinput').html(msg);
                $('#item_id').val(id);
                $('#timesheetdetailModal').modal('show');
//                $('#addModal').modal('show');
            }
        });


    }
    function detailmainhours(id) {
        $.ajax({
            type: 'post',
            url: '<?php echo base_url(); ?>timesheet/detailmainhours/' + id,
//            data: 'item_id=' + id + '&hour=' + hour,
            success: function (msg) {
                var data = JSON.parse(msg);
                $('#itemnametable').text(data.name);
                $('#datamainhours').html(data.table);
                $('#mainhoursmodal').modal('show');
//                $('#addModal').modal('show');
            }
        });
    }

    function signcost(){
      $('#divbtn').hide();
      $('.btnform').show();
        timesheet(<?php echo $projectid; ?>,'edit');
    }
    function cancelcostform(){
      location.reload();
    }
    function edittimesheet(id){
      $.ajax({
          type: 'post',
          url: '<?php echo base_url(); ?>timesheet/getonetimesheet/'+id,
        //  data: 'type=' + jenis + '&id=' + id,
          success: function (msg) {
               var data = JSON.parse(msg);
$('#item_id').val(data.item_id);
$('#timesheet_id').val(data.timesheet_id);
$('#hours').val(data.hours);
$('#datetimesheet').val(data.date);
$('#isue').val(data.isue);
if(data.remark!=''){
    $("#divoperational").show();
    $('#remark').val(data.remark);
    $('#price').val(data.nominal);
    $("#setimagebukti").attr("src", data.image);

}
$('#timesheetdetailModal').modal('show');
//                $('#addModal').modal('show');
          }
      });
    }
    function datetoday() {
        var today = new Date();
        var dd = today.getDate();
        var mm = today.getMonth() + 1; //January is 0!
        var yyyy = today.getFullYear();

        if (dd < 10) {
            dd = '0' + dd
        }

        if (mm < 10) {
            mm = '0' + mm
        }

        today = yyyy + '-' + mm + '-' + dd;
        $('#date').val(today);
        $('#datetoday').val(today);
    }
    function chekdate() {
        var today = $('#datetoday').val();
        var date = $('#date').val();

        var stDate = new Date(date);
        var enDate = new Date(today);
        var compDate = enDate - stDate;

        if (compDate >= 0)
            return true;
        else
        {
            alert("Please Enter the correct date ");
            return false;
        }
    }
    function uploadfile(){
      $("#filebukti").click();
    }
    function detailcost(projectid,timesheetid){
      if(timesheetid==''){
        alert('Main Hours not inserted');
      }else{
        $.ajax({
            type: 'post',
            url: '<?php echo base_url(); ?>timesheet/detailcost/'+timesheetid,
           data: 'timesheet_id=' + timesheetid + '&project_id=' + projectid,
            success: function (msg) {
              if(msg!=0){
                $('#setcostmodal').html(msg);
                $('#costmodal').modal('show');
              }else{
                alert("This Item not have cost");
              }
            }
          }
        );
      }

    }
</script>
<style>
.warning{
  color: red;
}
</style>
    <div class="row">
        <div class="col-lg-12">
            <h4>Project <?php
                if (isset($project)) {
                    echo $project;
                }
                ?></h4>
            <form class="form-horizontal">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Platform</label>
                    <div class="col-sm-10">
                        <select name="proplat" id="proplat" class="form-control" style="width:15%" onchange="timesheet(<?php echo $projectid; ?>)">
                            <?php
                            if (isset($platform)) {
//     foreach ($platform as $p){
                                echo $platform;
//     }
                            }
                            ?>

                        </select>
                    </div>
                </div>
            </form>
            <div class="row">

              <div class="col-sm-8">

              </div>
              <div class="col-sm-4">
                <div id="divbtn">
                <?php if((in_array(2,$roleid) && $chekpm>0)|| in_array(0,$roleid)){ ?>
                <button class="btn btn-info" onclick="signcost()" id="btnedit">Edit</button>
                <?php } ?>

              </div>
              </div>
            </div>
            <div id="divtug">
            <form action="<?php echo base_url()?>timesheet/savesigncosttimesheet" id="formsigncost" enctype="multipart/form-data" method="post">
                <table id="tabeltimesheet" class="table table-striped table-bordered" cellspacing="0" >
                    <thead>
                    <th>No.</th>
                    <th>Feature</th>
                    <th>Group</th>
                    <th>Item</th>
                    <th>Hours</th>
                      <th>Isue</th>
                      <th>Cost</th>
                    <!--<th>Assign</th>-->
                    <th></th>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
                <center>
                  <div class="btnform">
                    <button type = "button" onclick="return timesheet(<?php echo $projectid; ?>);" class="btn btn-default">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="btnsave">Save</button>
                  </div>
                </center>
              </form>
            </div>
        </div>
    </div>


<div class="modal fade bs-example-modal-sm" id="timesheetdetailModal" tabindex="-1" role="dialog" aria-labelledby="timesheetdetailModal" aria-hidden="true">
    <div class="modal-dialog" >
        <div class="modal-content" >
            <div class="modal-header" style="background-color: #48b5e9;" >
                <button type="button" class="close" style="color:#fff" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" style="font-color:#fff">&times;</span></button>
                <h4 class="modal-title"  style="color:#fff">Add Hours <span id="itemnameinput"></span></h4>
            </div>

            <div class="modal-body">
                <!--<div id='alert' class="alert alert-danger alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button><div id='alertdt'></div>...</div>-->
                <form class="form-horizontal" action='<?php echo base_url(); ?>timesheet/savetimesheet' id='formsavetimesheet' method="POST">
                    <div class="row" style="margin-bottom: 5px;margin-top: 5px;">
                        <div class="col-md-4"><!--<label for="exampleInputName2">Date</label>--></div>
                        <div class="col-md-8"><input type="hidden" id="item_id" name='item_id' value="">
                            <input type="hidden" id="datetoday" name='datetoday' value="">
                            <input type="hidden" id="timesheet_id" name='timesheet_id' value="">
                            <!--<input type="date" class="form-control datetimesheet" id="date" name='date' placeholder="Date" required>--></div>

                    </div>





                    <div class="row" style="margin-bottom: 5px;margin-top: 5px;">
                        <div class="col-md-4"><label for="exampleInputName2">Main Hours</label></div>
                        <div class="col-md-8">
                            <input type="number" class="form-control" name='hours' id='hours' placeholder="Hours"  required>
                        </div>

                    </div>
                    <div class="row" style="margin-bottom: 5px;margin-top: 5px;">
                        <div class="col-md-4"><label for="exampleInputName2">Isue</label></div>
                        <div class="col-md-8">
                            <input type="text" class="form-control" name='isue' id='isue' placeholder="Isue"  required>
                        </div>

                    </div>

<!--                    <input type="text" id="to" class="date hasDatepicker" name="dateTo" placeholder="To" title="input">-->

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


            </div>
            <div class = "modal-footer">
                <button class="btn btn-primary"type = "button" onclick = "closemodal()">Cancel</button>
                <button class="btn btn-primary"type = "submit"  >Save</button>
                </form>
            </div>

        </div><!--/.modal-content -->
    </div><!--/.modal-dialog -->
</div>
<div id="setcostmodal"></div>
<div class="modal fade bs-example-modal-sm" id="mainhoursmodal" tabindex="-1" role="dialog" aria-labelledby="mainhoursmodal" aria-hidden="true">
    <div class="modal-dialog" >
        <div class="modal-content" >
            <div class="modal-header" style="background-color: #48b5e9;" >
                <button type="button" class="close" style="color:#fff" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" style="font-color:#fff">&times;</span></button>
                <h4 class="modal-title"  style="color:#fff">Detail Main Hours <span id="itemnametable"></span></h4>
            </div>

            <div class="modal-body" id='datamainhours'>

            </div>
            <div class = "modal-footer">
                <!--                <button class="btn btn-primary"type = "button" onclick = "return cancelcostform()">Cancel</button>
                                <button class="btn btn-primary"type = "submit"  >Save</button>-->
                </form>
            </div>

        </div><!--/.modal-content -->
    </div><!--/.modal-dialog -->
</div>
<script>
    // Attach a submit handler to the form

    var options = {
        beforeSubmit: showRequest,
        success: showResponse,
        dataType: 'json'
    };
    $('#formsavetimesheet,#formsigncost').ajaxForm(options);

    function showRequest(formData, jqForm, options) {




        return true;
    }
    function closemodal() {
        $('#addModal').modal('hide');
        $('#formsavetimesheet').resetForm();

    }
    function showResponse(data) {
//        alert(data);
//                        if (data.status == 1) {

//        var msg = JSON.parse(data);
//                        document.write(data.type);

//alert(data.status);
        if (data.status == 1 || data.status == '1') {
            $('#formsavetimesheet').resetForm();
//        $('#imagepreview').attr('src', '');
//        $('#addModal').modal('hide');
            $('#timesheetdetailModal').modal('hide');
            alert(data.msg);
            timesheet(<?php echo $projectid; ?>);

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
