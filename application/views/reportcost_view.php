<script>
    $(document).ready(function () {
  $('.btnform').hide();
        viewreport();
            })
    function viewreport(type='') {
var id=$('#member_id').val();
if(type==''){
  $('#divbtn').show();
  $('.btnform').hide();
}
        var table = $('#tabelcost').dataTable({
            "sPaginationType": "full_numbers",
//            "bJQueryUI": true,
            "iDisplayLength": 30,
            "bDestroy": true,
            "bFilter": false,
            "bLengthChange": false,
            "aaSorting": [],
            "bAutoWidth": true,
            "bSortable": false,
            "bSortClasses": true,
            "sAjaxSource": '<?php echo base_url(); ?>report/getreportcosttabel/'+type,
           "fnServerParams": function (aoData) {
               aoData.push({'name': 'user_id', 'value': id}

               );
           }

        });
    }
    function detailreport(projectid,platformid){
      location.replace("<?php echo base_url(); ?>home/viewreportdetail/"+projectid+"/"+platformid);
    }
    function signcost(){
      $('#divbtn').hide();
      $('.btnform').show();
        viewreport('edit');
    }
    function cancelcostform(){
      location.reload();
    }

    function printpdf(){
      var userid=$('#member_id').val();
      window.open('<?php echo base_url();?>report/exportpdfcost/'+userid,'_blank');
    }

    var options = {
        beforeSubmit: showRequest,
        success: showResponse,
        dataType: 'json'
    };
    $('#formsigncost').ajaxForm(options);

    function showRequest(formData, jqForm, options) {




        return true;
    }
    function closemodal() {
        $('#addModal').modal('hide');
        $('#formsavetimesheet').resetForm();

    }
    function showResponse(data) {
        if (data.status == 1 || data.status == '1') {
            $('#formsavetimesheet').resetForm();
//        $('#imagepreview').attr('src', '');
//        $('#addModal').modal('hide');
            $('#timesheetdetailModal').modal('hide');
            alert(data.msg);
           viewreport(type='');

        } else {
            alert(data.msg);
        }
}
</script>


    <div class="row">
        <div class="col-lg-12" id="divdataproject">

            <div id="divproject">
              <div class="row">
                <label for="inputEmail3" class="col-sm-2 control-label">Name</label>
                <div class="col-sm-4">
                  <select class="form-control select2"  name="id" id="member_id" style="width: 80%;">
                    <?php $x='';
                    // print_r($member);die();
                    foreach ($member as $o) {
                      # code...

                      $x.="<option value='".$o['userid']."'>".$o['nama']."</option>";


                    }
                    echo $x;
                    ?>
                  </select>
              </div>
              <div class="col-sm-4">
                <button class="btn btn-primary" onclick="viewreport()">View</button>
              </div>
            </div>
              <div class="row">

                <div class="col-sm-8">

                </div>
                <div class="col-sm-4">
                  <div id="divbtn">
                  <?php if(in_array(2,$roleid) || in_array(0,$roleid)|| in_array(99,$roleid)){ ?>
                  <button class="btn btn-info" onclick="signcost()" id="btnedit">Edit</button>
                  <?php } ?>
                  <button class="btn btn-default" onclick="printpdf()">Print</button>
                </div>
                </div>
              </div>
              <form action="<?php echo base_url()?>report/savesigncost" id="formsigncost" enctype="multipart/form-data" method="post">
                <table id="tabelcost" class="table table-striped table-bordered" cellspacing="0" >
                    <thead>
                    <th>No.</th>
                    <th>Project</th>
                    <!--<th>Platform</th>-->

                    <th>Description</th>
                     <th>Cost(IDR)</th>
                     <th>sign by</th>
                     <th>Date</th>

                    <th></th>


                    <!--<th>Project Manager</th>-->

                    </thead>
                    <tbody>
                    </tbody>
                </table>
                <center>
                  <div class="btnform">
                    <button type="button" onclick="cancelcostform()" class="btn btn-default">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="btnsave">Save</button>
                  </div>
                </center>
              </form>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12" id="divchartproject" style="padding-right:40px;margin-left:20px;">
          <div class="chart">
                    <!-- <canvas id="barChart" style="height:250px"></canvas> -->
            </div>
        </div>
    </div>
