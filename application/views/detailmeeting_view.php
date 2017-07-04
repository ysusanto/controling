

  <div class="box-body">
    <div class='row' style="margin-bottom:10px">
      <div class="col-md-2">
        <img src="<?php echo base_url();?>assets/img/logo_seatech.png">
      </div>
      <div class="col-md-5">
        <h2>MOM <small>Minutes Of the Meeting</small></h2>
      </div>

      <div class="col-md-5">
        <div class="row" style="margin-top:20px">
          <div class="col-md-3">
            <label>Date</label>
          </div>
          <div class="col-md-9">
            <div id="setdate"><?php echo $do_date; ?></div>
          </div>
        </div>

      </div>
    </div>
    <div class='row'>
      <div class="col-md-2">
        <label>Project Name</label>
      </div>
      <div class="col-md-5">
        <div id="setprojectname"><?php echo $project_id; ?></div>
      </div>

      <div class="col-md-5">
        <div class="row">
          <div class="col-md-6">
            <label>Participant Internal</label>
          </div>
          <div class="col-md-6">
            <div id="setmemberinternal"><?php echo $member_internal; ?></div>
          </div>
        </div>

      </div>
    </div>
      <div class='row'>
        <div class="col-md-2">
          <label>Title Meeting</label>
        </div>
        <div class="col-md-5">
          <input type="hidden" name="id_meeting" id="idmeetingdetail" value="<?php echo $id_meeting ?>">
          <div id="setlocation"><?php echo $title; ?></div>
        </div>

        <div class="col-md-5">
          <div class="row">
            <div class="col-md-6">
              <label>Participants External</label>
            </div>
            <div class="col-md-6">
              <div id="setmemberexternal"><?php echo $member_external; ?></div>
            </div>
          </div>

        </div>
    </div>
    <div class='row'>
      <div class="col-md-2">
        <label>Place</label>
      </div>
      <div class="col-md-5">
        <div id="setlocation"><?php echo $location; ?></div>
      </div>

      <div class="col-md-5">
        <div class="row">
          <div class="col-md-6">

          </div>
          <div class="col-md-6">

          </div>
        </div>

      </div>
  </div>
    <div class='row'>
      <div class="col-md-11">
        <div id="divdetailmeeting">

            <table id="tabeldetailmeeting" class="table table-striped table-bordered" cellspacing="0" >
                <thead>
                <th>No.</th>
                <th>Remarks</th>
                <!--<th>Platform</th>-->

                <th>PIC</th>
                 <th>End Date</th>



                <!--<th>Project Manager</th>-->

                </thead>
                <tbody>
                </tbody>
            </table>
        </div>

      </div>
    </div>

  </div><!-- /.box-body -->
  <div class="box-footer">

    <button  class="btn btn-primary pull-center" id="idno" onclick="printmeeting()">print</button>

  </div><!-- /.box-footer -->
