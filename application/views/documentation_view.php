<script>
    $(document).ready(function () {

        viewdokumentasi();
    })
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
    function viewdokumentasi() {
      var projectid='<?php echo $project_id;?>';
    linkback('feature',projectid);

      $.ajax({
          type: 'post',
          url: '<?php echo base_url(); ?>home/checksession/',
          // data: 'user_id=' + user_id + '&item_id=' + item_id,
          success: function (msg) {
              var data=JSON.parse(msg);
              var idtabel='#tabeldokumentasi';
              var link='<?php echo base_url(); ?>dokumentasi/getdokumentasi/';
              var textbutton='Add Documentation';
              var idmodal='#addmodaldokumetasi';
              var role='';
              var parameter={'name':'project_id','value':projectid};
              if(jQuery.inArray( "3", data.roleid )>=0|| jQuery.inArray( "0", data.roleid )>=0){

                tablewithbutton(idtabel,link,parameter,textbutton,idmodal,role);
              }else{
                table(idtabel,link,parameter);
              }


          }
      });

    }
    function deletedoc(iddoc){
      $.ajax({
          type: 'post',
          url: '<?php echo base_url(); ?>dokumentasi/deletedoc/'+iddoc,
          // data: 'user_id=' + user_id + '&item_id=' + item_id,
          success: function (msg) {
                        var data =JSON.parse(msg);
                        if(data.status==1){
                            alert(data.msg);
                          location.reload();
                        }else{
                          alert(data.msg);
                        }
          }
        });

    }
</script>

  <div id="linkbackfeature"></div>
    <div class="row">
        <div class="col-lg-12" id="divdataproject">
            <div id="divdokumentasi">

                <table id="tabeldokumentasi" class="table table-striped table-bordered" cellspacing="0" >
                    <thead>
                    <th>No.</th>
                    <th>Name</th>
                    <!--<th>Platform</th>-->

                    <th>Hours</th>

                    <th></th>


                    <!--<th>Project Manager</th>-->

                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<div id="divdetailmeeting"></div>
