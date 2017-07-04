
<script>

$(document).ready(function () {
  loaduser();
  loadrole();
});


function loaduser() {
  $('#dtuser').dataTable({
    "sPaginationType": "full_numbers",
    "iDisplayLength": 100,
    "bDestroy": true,
    "bLengthChange": false,
    "aaSorting": [],
    "bAutoWidth": true,
    "bSortable": false,
    "bSortClasses": true,
    "responsive": true,
    "sAjaxSource": '<?php echo site_url(); ?>stssetup/getuserlist'
  });

}
function loadrole(){
  var idtabel='#dtrole';
  var link='<?php echo site_url(); ?>role/getroletable';
  var parameter={'name': 'role_id', 'value':''};
  var textbutton='Add Role';
  var idmodal='#rolemodal';
  var role=1;
  tablewithbutton(idtabel,link,parameter,textbutton,idmodal,role);
}

function addnew() {
  $("#addModal").modal("show");
}

var options = {
  beforeSubmit: showRequest,
  success: showResponse,
  dataType: 'json'
};
$('#formsaveproject').ajaxForm(options);

function showResponse(data) {
  //        alert(data.toString());
  //        console.log(data);
  var obj = JSON.parse(JSON.stringify(data));
  if (obj.status == "0"){
    alert(obj.message);
  }
  else{
    alert(obj.message);
    console.log("yeee");
    window.location.replace("<?php base_url() +"home/setupuser"?>");
  }
}
function showRequest(formData, jqForm, options) {
  dovalidation();
}


function dovalidation() {
  var firstname = $("#firstname").val();
  var lastname = $("#lastname").val();
  var email = $("#email").val();
  var resultvalidat = "no";
  if (firstname == "") {
    alert( "Nama Pertama Kosong");
    return;
  }
  if (lastname == "")
  {
    return "Nama Belakang Kosong";
  }
  if (email == "")
  {
    return "Email Kosong";
  }
  return 1;
}
$('#formrole').ajaxForm(pilihan);

var pilihan ={
  beforeSubmit: validasirole,
  success: balikan,
  dataType: 'json'
};
function validasirole(formData, jqForm, options){
  var roleid = $("#role_id").val();
  var name = $("#name").val();
  if(name==''){
    $("#name").attr('style','border-color : red');
  }
  if(roleid){
    $("#role_id").attr('style','border-color : red');
  }
  function balikan(data){
    var obj = JSON.parse(data);
    if(obj.status==1){
      $('#formrole').resetForm();
      $('#rolemodal').modal('hide');
      loadrole();
    }else{
      alert(obj.msg);
    }
    function editrole(id){
      $.ajax({
          type: 'post',
          url: '<?php echo base_url(); ?>role/getonerole/'+id,
          // data: 'groupid=' + groupid,
          success: function (msg) {
            var data = JSON.parse(msg);
            if(data.status==1){
              $('#role_id').val(data.role_id);
              $('#name').val(data.name);
              $('#role_id').attr('readonly', true);
              $('#rolemodal').modal('show');
            }else{
              alert(data.msg);
            }

          }
      });
    }
    function deleterole(id){
      $.ajax({
          type: 'post',
          url: '<?php echo base_url(); ?>role/deleterole/'+id,
          // data: 'groupid=' + groupid,
          success: function (msg) {
            var data = JSON.parse(msg);
            if(data.status==1){
              $('#formrole').resetForm();
              $('#rolemodal').modal('hide');
              loadrole();
            }else{
              alert(data.msg);
            }

          }
      });
    }

  }
}
</script>
<div>

  <!-- Nav tabs -->
  <ul class="nav nav-tabs" role="tablist" id='mytabs'>
    <li role="presentation" class="active"><a href="#user" aria-controls="user" role="tab" data-toggle="tab">User Setup</a></li>
    <li role="presentation"><a href="#grupakses" aria-controls="grupakses" role="tab" data-toggle="tab">Group Access</a></li>
    <li role="presentation"><a href="#role" aria-controls="role" role="tab" data-toggle="tab">Role</a></li>
    <li role="presentation"><a href="#settings" aria-controls="settings" role="tab" data-toggle="tab">Settings</a></li>
  </ul>

  <!-- Tab panes -->
  <div class="tab-content">
    <div role="tabpanel" class="tab-pane active" id="user">
      <div class="row">
        <div class="col-lg-12" id="divdataproject">
          <h4>user List</h4>
          <button onclick="addnew()">Add New User</button><br>
          <div id="divuser">

            <table class="display" cellspacing="0"  id="dtuser">
              <thead>
                <tr>
                  <!--<th width="15%">Unit</th>-->
                  <th>User Id</th>
                  <th>Salutation</th>
                  <th>First Name</th>
                  <th>Last Name</th>
                  <th>Email</th>
                  <th>Username</th>
                  <th>Position</th>
                </tr>
              </thead>
              <tbody>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
    <div role="tabpanel" class="tab-pane" id="grupakses">ashdkjashdk</div>
    <div role="tabpanel" class="tab-pane" id="role"><div class="row">
      <div class="col-lg-12" id="divdataproject">
        <h4>Role Setup</h4>

        <div id="divuser">

          <table class="display" cellspacing="0"  id="dtrole">
            <thead>
              <tr>
                <!--<th width="15%">Unit</th>-->
                <th>Role ID</th>
                <th>Role Name</th>
                <th>Action</th>

              </tr>
            </thead>
            <tbody>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

</div>

</div>






<div class="modal fade bs-example-modal-sm" id="addModal" tabindex="-1" role="dialog" aria-labelledby="memberuserModal" aria-hidden="true">
  <div class="modal-dialog" >
    <div class="modal-content" >
      <div class="modal-header" style="background-color: #48b5e9;" >
        <button type="button" class="close" style="color:#fff" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" style="font-color:#fff">&times;</span></button>
        <h4 class="modal-title"  style="color:#fff">Add New User</h4>
      </div>

      <div class="modal-body">
        <!--<div id='alert' class="alert alert-danger alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button><div id='alertdt'></div>...</div>-->
        <form class="form-inline" action='<?php echo base_url(); ?>stssetup/saveuser' id='formsaveproject' method="POST">
          <div class="row" style="margin-bottom: 5px;margin-top: 5px;">
            <div class="col-md-4"><label for="exampleInputName2">Salutation</label></div>
            <div class="col-md-8"><input type="hidden" id="project_id" name='project_id' value="">

              <select class="form-control" id="salutation" name="salutation">
                <option value="Mr">Mr</option>
                <option value="Mrs">Mrs</option>
                <option value="Ms">Ms</option>
              </select></div>

            </div>




            <div class="row" style="margin-bottom: 5px;margin-top: 5px;">
              <div class="col-md-4"><label for="exampleInputName2">First Name</label></div>
              <div class="col-md-8">
                <input type="text" class="form-control" id="firstname" name='firstname'placeholder="First Name" ></div>

              </div>

              <div class="row" style="margin-bottom: 5px;margin-top: 5px;">
                <div class="col-md-4"><label for="exampleInputName2">Last Name</label></div>
                <div class="col-md-8">
                  <input type="text" class="form-control" id="lastname" name='lastname'placeholder="Last Name" ></div>

                </div>

                <div class="row" style="margin-bottom: 5px;margin-top: 5px;">
                  <div class="col-md-4"><label for="exampleInputName2">Email</label></div>
                  <div class="col-md-8">
                    <input type="text" class="form-control" id="email" name='email'placeholder="example@seatech.com" ></div>

                  </div>

                  <div class="row" style="margin-bottom: 5px;margin-top: 5px;">
                    <div class="col-md-4"><label for="exampleInputName2">Position</label></div>
                    <select class="form-control" id="position" name="position">

                      <?php
                      foreach ($position as $value) {
                        echo "<option value=" . $value['role_id'] . ">" . $value['name'] . "</option>";
                      }
                      ?>

                    </select></div>


                  </div>

                  <div class = "modal-footer">
                    <button class="btn btn-primary"type = "button" onclick = "closemodal()">Cancel</button>
                    <button class="btn btn-primary"type = "submit" >Save</button>
                  </form>

                </div>


              </div>


            </div><!--/.modal-content -->
          </div><!--/.modal-dialog -->
