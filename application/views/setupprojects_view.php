<style>
    #addModal .modal-dialog  {width:50%;}
</style>
<script>
    $(document).ready(function () {
      $('.select2').select2();
      $('#datetimeline').daterangepicker();
        $('#divdatafeature,#divdatagroup,#divdataitem').hide();
        viewproject();

    })
    function viewproject() {
        var table = $('#tabelproject').dataTable({
            "sPaginationType": "full_numbers",
//            "bJQueryUI": true,
            "iDisplayLength": 30,
            "bDestroy": true,
            "bFilter": true,
            "bLengthChange": false,
            "responsive": true,
            "aaSorting": [],
            "bAutoWidth": true,
            "bSortable": false,
            "bSortClasses": true,
            "sAjaxSource": '<?php echo base_url(); ?>projects/projecttabel',
//            "fnServerParams": function (aoData) {
//                aoData.push({'name': 'tug_id', 'value': tug},
//                {'name': 'datefrom', 'value': datefrom},
//                {'name': 'dateto', 'value': dateto},
//                {'name': 'owner_id', 'value': owner},
//                {'name': 'searchkey', 'value': search}
//                );
//            }
            dom: 'Bfrtip',
            buttons: [
                {
                    text: 'Add Project',
                    action: function (e, dt, node, config) {
<?php
$role = $this->session->userdata('roleid');
if (in_array(0,$role)||in_array(1,$role)) {
    ?>
                            $('#addModal').modal('show');
<?php } else { ?>
                            alert("Sorry You don't have Auth, Please call administrator");
<?php } ?>
                    }
                }
            ]
        });
    }
    function getaddMemberModal(groupid) {
        $.ajax({
            type: 'post',
            url: '<?php echo base_url(); ?>projects/getaddMemberModal',
            data: 'groupid=' + groupid,
            success: function (msg) {
                $('#addMemberModal').html(msg);
            }
        });
    }
    function linkback(jenis, id) {
        $.ajax({
            type: 'post',
            url: '<?php echo base_url(); ?>projects/linkback',
            data: 'type=' + jenis + '&id=' + id,
            success: function (msg) {
//                var data = JSON.parse(msg);

                if (jenis == 'item') {
                    $('#linkbackitem').html(msg);
                } else if (jenis == 'group') {
                    $('#linkbackgroup').html(msg);
                } else {
                    $('#linkbackfeature').html(msg);
                }

//                $('#addModal').modal('show');
            }
        });
    }
    function detailproject(id) {
        $.ajax({
            type: 'post',
            url: '<?php echo base_url(); ?>projects/detailproject/' + id,
            success: function (msg) {
                var data = JSON.parse(msg);
                $('#proplat').html(data.platform);
                $('#projectname').text(data.name);
                $('#divdatafeature').show();
                $('#divdataproject,#divdatagroup,#divdataitem').hide();
                linkback('feature', id);
                $('#proplatitemid').val(id);
                viewfeature(id);
//                $('#addModal').modal('show');
            }
        });
    }
    function viewfeature(id) {
        var proplat = $('#proplat').val();
        $('#proplatid').val(proplat);
        var table = $('#tabelfeature').dataTable({
            "sPaginationType": "full_numbers",
//            "bJQueryUI": true,
            "iDisplayLength": 30,
            "bDestroy": true,
            "bFilter": true,
            "bLengthChange": false,
            "aaSorting": [],

            "responsive": true,
            "bAutoWidth": true,
            "bSortable": false,
            "bSortClasses": true,
            "sAjaxSource": '<?php echo base_url(); ?>projects/tabelfeature',
            "fnServerParams": function (aoData) {
                aoData.push({'name': 'project_id', 'value': id},
                        {'name': 'proplat_id', 'value': proplat}

                );
            },
            dom: 'Bfrtip',
            buttons: [
                {
                    text: 'Add Feature',
                    action: function (e, dt, node, config) {
                      <?php
                      $role = $this->session->userdata('roleid');

                      if (in_array(0,$role)||in_array(2,$role)) {
                          ?>
                                                  $('#addModalfeature').modal('show');
                      <?php } else { ?>
                                                  alert("Sorry You don't have Auth, Please call administrator");
                      <?php } ?>

                    }
                }
            ]
        });
    }

    function detailfeature(id) {
        $.ajax({
            type: 'post',
            url: '<?php echo base_url(); ?>projects/detailfeature/' + id,
            success: function (msg) {
                var data = JSON.parse(msg);
                $('#featureid').val(id);
                $('#featurename').text(data.name);
                $('#divdatafeature').hide();
                $('#divdataproject,#divdataitem').hide();
                $('#divdatagroup').show();
                linkback('group', id)
                viewgroup(id);
//                $('#addModal').modal('show');
            }
        });
    }
    function viewgroup(id) {

        var table = $('#tabelgroup').dataTable({
            "sPaginationType": "full_numbers",
//            "bJQueryUI": true,
            "iDisplayLength": 30,
            "bDestroy": true,
            "bFilter": true,
            "bLengthChange": false,
            "aaSorting": [],
            "bAutoWidth": true,
            "bSortable": false,
            "responsive": true,
            "bSortClasses": true,
            "sAjaxSource": '<?php echo base_url(); ?>projects/tabelgroup',
            "fnServerParams": function (aoData) {
                aoData.push({'name': 'feature_id', 'value': id}
//                {'name': 'proplat_id', 'value': proplat}

                );
            },
            dom: 'Bfrtip',
            buttons: [
                {
                    text: 'Add group',
                    action: function (e, dt, node, config) {
                      <?php
                      $role = $this->session->userdata('roleid');

                      if (in_array(0,$role)||in_array(2,$role)) {
                          ?>
                                                  $('#addModalgroup').modal('show');
                      <?php } else { ?>
                                                  alert("Sorry You don't have Auth, Please call administrator");
                      <?php } ?>



                    }
                }
            ]
        });
    }
    function detailgroup(id) {
        getaddMemberModal(id);
        $.ajax({
            type: 'post',
            url: '<?php echo base_url(); ?>projects/detailgroup/' + id,
            success: function (msg) {
                var data = JSON.parse(msg);
                $('#group_id_item').val(id);
                $('#groupname').text(data.name);
                $('#divdatafeature').hide();
                $('#divdataproject').hide();
                $('#divdatagroup').hide();
                $('#divdataitem').show();
                linkback('item', id)
                viewitem(id);
//                $('#addModal').modal('show');
            }
        });
    }
    function viewitem(id) {

        var table = $('#tabelitem').dataTable({
            "sPaginationType": "full_numbers",
//            "bJQueryUI": true,
            "iDisplayLength": 30,
            "bDestroy": true,
            "bFilter": true,
            "bLengthChange": false,
            "aaSorting": [],
            "bAutoWidth": true,
            "bSortable": false,
            "bSortClasses": true,
            "responsive": true,
            "sAjaxSource": '<?php echo base_url(); ?>projects/tabelitem',
            "fnServerParams": function (aoData) {
                aoData.push({'name': 'group_id', 'value': id}
//                {'name': 'proplat_id', 'value': proplat}

                );
            },
            dom: 'Bfrtip',
            buttons: [
                {
                    text: 'Add Item',
                    action: function (e, dt, node, config) {
                      getmember();
                      <?php
                      $role = $this->session->userdata('roleid');

                      if (in_array(0,$role)||in_array(2,$role)) {
                          ?>
                                                    $('#addModalitem').modal('show');
                      <?php } else { ?>
                                                  alert("Sorry You don't have Auth, Please call administrator");
                      <?php } ?>


                    }
                }
            ]
        });
    }
function getmember(){
  var proplat=$('#proplatitemid').val();
  $.ajax({
      type: 'post',
      url: '<?php echo base_url(); ?>projects/getmemberproject/',
      data: 'proplat_id=' + proplat,
      success: function (msg) {
          $('#setdoing').html(msg);
      }
  });
}
    function viewAddMemberModal(group_id, itemid) {
        $('#viewAddMemberModalHidden').val(group_id);
        $('#viewAddMemberModalItemidHidden').val(itemid);
        $('#addMemberModal').modal('show');
    }

    function changeStatusClosed(groupid, itemid) {
        $.ajax({
            type: 'post',
            url: '<?php echo base_url(); ?>projects/changeitemstatus/',
            data: 'groupid=' + groupid + '&itemid=' + itemid,
            success: function (msg) {
                alert(msg);
                viewitem(groupid);
            }
        });
    }
	function editproject(projectid){
	 $.ajax({
            type: 'post',
            url: '<?php echo base_url(); ?>projects/getoneproject/'+projectid,

            success: function (msg) {
				 var data = JSON.parse(msg);

				$('#project_id').val(data.project_id);
				$('#inputprojectname').val(data.name);
				$('#client').val(data.client);
        $('#pm_id').val(data.userid);
        $.each($("#member"), function(){
            $(this).select2('val', data.member);
    });
        // $('#member').select2('val',data.member);
				$('.platform').find('[value=' + data.platform.join('], [value=') + ']').prop("checked", true);
				$('#enddateproject').val(data.enddate);


               $('#addModal').modal('show');
            }
        });
	}

	function editfeature(featureid){
	 $.ajax({
            type: 'post',
            url: '<?php echo base_url(); ?>projects/getonefeature/'+featureid,
            data: 'feature_id=' + featureid ,
            success: function (msg) {
				 var data = JSON.parse(msg);

               $('#feature_id').val(data.feature_id);
			   $('#namafeature').val(data.name);
			   $('#addModalfeature').modal('show');
            }
        });
	}
  function editgroup(grup_id){
    $.ajax({
             type: 'post',
             url: '<?php echo base_url(); ?>projects/getonegroup/'+grup_id,
             data: 'feature_id=' + featureid ,
             success: function (msg) {
          var data = JSON.parse(msg);

                $('#featureid').val(data.feature_id);
                  $('#group_id').val(grup_id);
          $('#namagroup').val(data.name);
          $('#addModalgroup').modal('show');
             }
         });
  }

	function edititem(item_id){
	 $.ajax({
            type: 'post',
            url: '<?php echo base_url(); ?>projects/getoneitem/'+item_id,
            data: 'item_id=' + item_id,
            success: function (msg) {
				 var data = JSON.parse(msg);
getmember();
               $('#item_id').val(data.item_id);
			   $('#itemnama').val(data.name);
			    $('#itemhour').val(data.hour);
          $("#datetimeline").data('daterangepicker').setStartDate(data.start_date);
$("#datetimeline").data('daterangepicker').setEndDate(data.end_date);
$("#doingby").val(data.userid);
			   $('#addModalitem').modal('show');
            }
        });
}

    function remove_assignment(user_id, item_id, group_id) {
        $.ajax({
            type: 'post',
            url: '<?php echo base_url(); ?>projects/remove_assignment/',
            data: 'user_id=' + user_id + '&item_id=' + item_id,
            success: function (msg) {
                alert(msg);
                viewitem(group_id);
            }
        });
    }

    function deleteproject(id) {
        $.ajax({
            type: 'post',
            url: '<?php echo base_url(); ?>projects/deleteproject/' + id,
            success: function (msg) {
                var data = JSON.parse(msg);

                if (data.status == 1) {
                    alert(data.msg);
                    viewproject();
                } else {
                    alert(data.msg);
                }
//                $('#addModal').modal('show');
            }
        });
    }
    function deleteitem(id,grupid) {
        $.ajax({
            type: 'post',
            url: '<?php echo base_url(); ?>projects/deleteitem/' + id,
            success: function (msg) {
                var data = JSON.parse(msg);

                if (data.status == 1) {
                    alert(data.msg);
                    viewitem(grupid);
                } else {
                    alert(data.msg);
                }
  //                $('#addModal').modal('show');
            }
        });
    }
    function deletegroup(id,featureid) {
        $.ajax({
            type: 'post',
            url: '<?php echo base_url(); ?>projects/deletegroup/' + id,
            success: function (msg) {
                var data = JSON.parse(msg);

                if (data.status == 1) {
                    alert(data.msg);
                    viewgroup(featureid);
                } else {
                    alert(data.msg);
                }
  //                $('#addModal').modal('show');
            }
        });
    }
    function deletefeature(id,projectid) {
        $.ajax({
            type: 'post',
            url: '<?php echo base_url(); ?>projects/deletefeature/' + id,
            success: function (msg) {
                var data = JSON.parse(msg);

                if (data.status == 1) {
                    alert(data.msg);
                    viewfeature(projectid);
                } else {
                    alert(data.msg);
                }
  //                $('#addModal').modal('show');
            }
        });
    }
    //This variable is to increment cloned div below
    var divCloneThisIndex = 1;
    function addmore() {
        var divClonedVar = $('#divCloneThis' + divCloneThisIndex).clone();
        var divMemberLabelVar = divClonedVar.find('#divMemberLabel' + ((divCloneThisIndex == 1) ? '' : divCloneThisIndex));
        divMemberLabelVar.css('visibility', 'hidden');
        divMemberLabelVar.attr('id', 'divMemberLabel' + (divCloneThisIndex + 1));
        divClonedVar.appendTo('#divContainerMember').attr('id', 'divCloneThis' + (divCloneThisIndex + 1));
        divCloneThisIndex++;
    }

	function detailmeeting(projectid){

location.replace('<?php echo base_url(); ?>meeting/index/' + projectid);
    	}

	function detaildokumentasi(projectid){
    location.replace('<?php echo base_url(); ?>dokumentasi/index/' + projectid);


	}
  function changepm(){
    var pm=$('#pm_id').val();
    $("#member option[value='"+pm+"']").remove();
  }
</script>

<style>
    .col-md-8 {
        margin-top: 10px;
        margin-bottom: 10px;
    }
</style>

    <div class="row">
        <div class=".col-xs-12 col-lg-12" id="divdataproject">
            <h4>Project</h4>
            <div id="divtug">

                <table id="tabelproject" class="table table-striped table-bordered" cellspacing="0" >
                    <thead>
                    <th>No.</th>
                    <th>Project Name</th>
                    <th>Client</th>
                    <th>Platform</th>
					          <th>Meeting</th>
					          <th>Documentation</th>
                    <th></th>

                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="col-xs-12 col-lg-12" id="divdatafeature">
            <div id="linkbackfeature"></div>
            <h4>Feature</h4>
            <div class="row">
                <div class="col-md-4 col-xs-4">
                    <dl class="dl-horizontal">
                        <dt >Project</dt>
                        <dd id="projectname">...</dd>
                        <dt >Platform</dt>
                        <dd ><select name="proplat" id="proplat" class="form-control" onchange="viewfeature(id)">


                            </select></dd>
                    </dl>
                </div>
                <div class="col-md-8 col-xs-8"></div>
            </div>
            <div id="divtug" style="width: 100%">


                <table id="tabelfeature" class="table table-striped table-bordered" cellspacing="0" >
                    <thead>
                    <th>No.</th>
                    <th>Feature</th>
                    <th></th>

                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="col-lg-12 col-xs-12" id="divdatagroup">
            <div id="linkbackgroup"></div>
            <h4>Group</h4>
            <div class="row">
                <div class="col-md-4 col-xs-4">
                    <dl class="dl-horizontal">
                        <dt >Feature</dt>
                        <dd id="featurename">...</dd>

                    </dl>
                </div>
                <div class="col-md-8 col-xs-8"></div>
            </div>
            <div id="divtug" style="width: 100%">


                <table id="tabelgroup" class="table table-striped table-bordered" cellspacing="0" >
                    <thead>
                    <th>No.</th>
                    <th>Group</th>



                    <th></th>

                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="col-lg-12 col-xs-12" id="divdataitem">
            <div id="linkbackitem"></div>
            <h4>Item</h4>
            <div class="row">
                <div class="col-md-4 col-xs-4">
                    <dl class="dl-horizontal">
                        <dt >Group</dt>
                        <dd id="groupname">...</dd>

                    </dl>
                </div>
                <div class="col-md-8 col-xs-8"></div>
            </div>
            <div id="divtug" style="width: 100%">
                <table id="tabelitem" class="table table-striped table-bordered" cellspacing="0" >
                    <thead>
                    <th>No.</th>
                    <th>Item</th>
                    <th>Hour</th>
                    <th>Doing by</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th></th>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

<!-- /container -->
<!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
<!--<script src="assets/js/ie10-viewport-bug-workaround.js"></script>-->

<div class="modal fade bs-example-modal-sm" id="addModal" tabindex="-1" role="dialog" aria-labelledby="memberuserModal" aria-hidden="true">
    <div class="modal-dialog" >
        <div class="modal-content" >
            <div class="modal-header" style="background-color: #48b5e9;" >
                <button type="button" class="close" style="color:#fff" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" style="font-color:#fff">&times;</span></button>
                <h4 class="modal-title"  style="color:#fff">Project</h4>
            </div>

            <div class="modal-body">
                <!--<div id='alert' class="alert alert-danger alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button><div id='alertdt'></div>...</div>-->
                <form class="form-inline" action='<?php echo base_url(); ?>projects/saveproject' id='formsaveproject' method="POST">
                    <div class="row" style="margin-bottom: 5px;margin-top: 5px;">
                        <div class="col-md-4"><label for="exampleInputName2">Project Name<span class="mandatori">*</span></label></div>
                        <div class="col-md-8"><input type="hidden" id="project_id" name='project_id' value="">
                            <input type="text" class="form-control" id="inputprojectname" name='nama'placeholder="Project Name"  required></div>

                    </div>

                    <div class="row" style="margin-bottom: 5px;margin-top: 5px;">
                        <div class="col-md-4"><label for="exampleInputName2">Client Name<span class="mandatori">*</span></label></div>
                        <div class="col-md-8">
                            <input type="text" class="form-control" id="client" name='client'placeholder="Client Name" ></div>

                    </div>
                    <div class="row" style="margin-bottom: 5px;margin-top: 5px;">
                        <div class="col-md-4"><label for="exampleInputName2">Project Manager<span class="mandatori">*</span></label></div>
                        <div class="col-md-8">
                            <select name="pm_id" id="pm_id" class="form-control" onchange="changepm()">
                                <?php
                                if (isset($manager)) {
                                    foreach ($manager as $value) {
                                        ?>
                                        <option value="<?php echo $value['userid']; ?>"><?php echo $value['salutation'] . " " . $value["firstname"] . " " . $value['lastname']; ?></option>
                                        <?php
                                    }
                                }
                                ?>

                            </select>
                        </div>

                    </div>

                    <div id="divContainerMember" class="row" style="margin-bottom: 5px;margin-top: 5px;">
                        <!--This id is used for cloning-->
                        <div id="divCloneThis1">
                            <div id="divMemberLabel" class="col-md-4"><label for="exampleInputName2">Member<span class="mandatori">*</span></label></div>
                            <div class="col-md-8">

                                <select name="member[]" id="member" style="width:100%"class="form-control select2" multiple="" data-placeholder="Select a State" tabindex="-1" aria-hidden="true" required>
                                    <?php
                                        foreach ($manager as $value) {
                                          if (isset($manager)) {
                                            ?>
                                            <option value="<?php echo $value['userid']; ?>"><?php echo $value['salutation'] . " " . $value["firstname"] . " " . $value['lastname']; ?></option>
                                            <?php
                                        }
                                    }
                                    ?>

                                </select>


                            </div>
                        </div>
                    </div>


					<div id="uploadexcel" class="row" style="margin-bottom: 5px;margin-top: 5px;">
                        <div class="col-md-4"><label for="exampleInputName2">End Date</label></div>
                        <div class="col-md-8">
                            <input type="text" class="form-control date"id="enddateporject" name="enddateproject" >
                        </div>

                    </div>
                    <div id="checkboxplatform" class="row" style="margin-bottom: 5px;margin-top: 5px;">
                        <div class="col-md-4"><label for="exampleInputName2">Platform</label></div>
                        <div class="col-md-8">
                        <div id="divplatform">
                            <?php
                            if (isset($platform)) {
                                foreach ($platform as $value) {
                                    ?>
                                    <div class="checkbox platform" style="margin-left: 5px" >
                                        <label>
                                            <input type="checkbox" name="platform[]" value="<?php echo $value['platform_id']; ?>"><?php echo $value['name']; ?>
                                        </label>
                                    </div> <br>
                                    <?php
                                }
                            }
                            ?>
                            </div>
                        </div>

                    </div>
                    <div id="uploadexcel" class="row" style="margin-bottom: 5px;margin-top: 5px;">
                        <div class="col-md-4"><label for="exampleInputName2">End Date<span class="mandatori">*</span></label></div>
                        <div class="col-md-8">
                            <input type="text" class="form-control date"id="enddateproject" name="enddateproject" required>
                        </div>

                    </div>
<!--                    <input type="text" id="to" class="date hasDatepicker" name="dateTo" placeholder="To" title="input">-->

            </div>
            <div class = "modal-footer">
                <button class="btn btn-primary"type = "button" onclick = "closemodal()">Cancel</button>
                <button class="btn btn-primary"type = "submit" >Save</button>
                </form>
            </div>

        </div><!--/.modal-content -->
    </div><!--/.modal-dialog -->
</div>

<div class="modal fade bs-example-modal-sm" id="addModalfeature" tabindex="-1" role="dialog" aria-labelledby="memberuserModal" aria-hidden="true">
    <div class="modal-dialog" >
        <div class="modal-content" >
            <div class="modal-header" style="background-color: #48b5e9;" >
                <button type="button" class="close" style="color:#fff" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" style="font-color:#fff">&times;</span></button>
                <h4 class="modal-title"  style="color:#fff">Feature</h4>
            </div>

            <div class="modal-body">
                <!--<div id='alert' class="alert alert-danger alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button><div id='alertdt'></div>...</div>-->
                <form class="form-inline" action='<?php echo base_url(); ?>projects/savefeature' id='formsavefeature' method="POST">
                    <div class="row" style="margin-bottom: 5px;margin-top: 5px;">
                        <div class="col-md-4"><label for="exampleInputName2">Feature Name</label></div>
                        <div class="col-md-8"><input type="hidden" id="feature_id" name='feature_id' value="">
                            <input type="hidden" id="proplatid" name='proplatid' value="">
                            <input type="text" class="form-control" id="namafeature" name='nama'placeholder="Feature Name"  required></div>
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

<div class="modal fade bs-example-modal-sm" id="addModalgroup" tabindex="-1" role="dialog" aria-labelledby="memberuserModal" aria-hidden="true">
    <div class="modal-dialog" >
        <div class="modal-content" >
            <div class="modal-header" style="background-color: #48b5e9;" >
                <button type="button" class="close" style="color:#fff" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" style="font-color:#fff">&times;</span></button>
                <h4 class="modal-title"  style="color:#fff">Group</h4>
            </div>

            <div class="modal-body">
                <!--<div id='alert' class="alert alert-danger alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button><div id='alertdt'></div>...</div>-->
                <form class="form-inline" action='<?php echo base_url(); ?>projects/savegroup' id='formsavegroup' method="POST">
                    <div class="row" style="margin-bottom: 5px;margin-top: 5px;">
                        <div class="col-md-4"><label for="exampleInputName2">Group Name</label></div>
                        <div class="col-md-8"><input type="hidden" id="group_id" name='group_id' value="">
                            <input type="hidden" id="featureid" name='featureid' value="">
                            <input type="text" class="form-control" id="namagroup" name='nama'placeholder="Group Name"  required></div>
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
<div class="modal fade bs-example-modal-sm" id="addModalitem" tabindex="-1" role="dialog" aria-labelledby="memberuserModal" aria-hidden="true">
    <div class="modal-dialog" >
        <div class="modal-content" >
            <div class="modal-header" style="background-color: #48b5e9;" >
                <button type="button" class="close" style="color:#fff" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" style="font-color:#fff">&times;</span></button>
                <h4 class="modal-title"  style="color:#fff">Item</h4>
            </div>

            <div class="modal-body">
                <!--<div id='alert' class="alert alert-danger alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button><div id='alertdt'></div>...</div>-->
                <form class="form-inline" action='<?php echo base_url(); ?>projects/saveitem' id='formsaveitem' method="POST" enctype="multipart/form-data">
                    <div class="row" style="margin-bottom: 5px;margin-top: 5px;">
                        <div class="col-md-4"><label for="exampleInputName2">Item Name</label></div>
                        <div class="col-md-8"><input type="hidden" id="group_id_item" name='group_id' value="">
                            <input type="hidden" id="item_id" name='item_id' value="">
                            <input type="hidden" id="proplatitemid" name='proplatid' value="">
                            <input type="text" class="form-control" id="itemnama" name='nama'placeholder="Item Name"  required></div>
                    </div>
                    <div class="row" style="margin-bottom: 5px;margin-top: 5px;">
                        <div class="col-md-4"><label for="exampleInputName2">Hour</label></div>
                        <div class="col-md-8">
                            <input type="number" class="form-control" id="itemhour" name='hour'placeholder="Hours"  required style='width: 30%'></div>
                    </div>
					<div class="row" style="margin-bottom: 5px;margin-top: 5px;">
                        <div class="col-md-4"><label for="exampleInputName2">Date Range</label></div>
                        <div class="col-md-8">
                            <input type="text" class="form-control" id="datetimeline" name='daterange'placeholder="Date Range"  style='width: 60%'required ></div>
                    </div>

            <div class="row" style="margin-bottom: 5px;margin-top: 5px;">
                          <div class="col-md-4"><label for="exampleInputName2">Doing by</label></div>
                          <div class="col-md-8" id="setdoing">
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
<div class="modal fade bs-example-modal-sm" id="addMemberModal" tabindex="-1" role="dialog" aria-labelledby="memberuserModal" aria-hidden="true"></div>
<script>
$('#daterange-btn').daterangepicker(
            {
              ranges: {
                'Today': [moment(), moment()],
                'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
              },
              locale: {
            format: 'DD/MMM/YYYY'
        },
              startDate: moment().subtract(29, 'days'),
              endDate: moment()
            },
        function (start, end) {
          $('#reportrange span').html(start.format('dd/mm/yy') + '-' + end.format('dd/mm/yy'));
        }
        );
    // Attach a submit handler to the form

    var options = {
        beforeSubmit: showRequest,
        success: showResponse,
        dataType: 'json'
    };
    $('#formsaveproject,#formsavefeature,#formsavegroup,#formsaveitem').ajaxForm(options);

    function showRequest(formData, jqForm, options) {
        return true;
    }
    function closemodal() {
        $('addMemberModal').modal('hide');
        $('#addModal').modal('hide');
        $('#addModal').modal('hide');
        $('#addModalfeature,#addModalgroup,#addModalitem,#addMemberModal').modal('hide');
        $('#formsaveproject,#formsavefeature,#formsavegroup,#formsaveitem,#formsavemember').resetForm();
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
            $('#formsaveproject,#formsavefeature,#formsavegroup,#formsaveitem,#formsavemember').resetForm();
            $('#addModal').modal('hide');
            $('#addModalfeature,#addModalgroup,#addModalitem,#addMemberModal').modal('hide');
            alert(data.msg);
            if (data.type == 'project') {
                viewproject();
                location.reload();
            }
            if (data.type == 'feature') {
                viewfeature(0);
            }
            if (data.type == 'group') {
                viewgroup(data.id)
            }
            if (data.type == 'item') {
                viewitem(data.id)
            }
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
