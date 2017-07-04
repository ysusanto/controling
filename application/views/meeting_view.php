<script>
    $(document).ready(function () {

        viewmeeting();
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
    function viewmeeting() {
      var projectid='<?php echo $project_id;?>';
    linkback('feature',projectid);

      $.ajax({
          type: 'post',
          url: '<?php echo base_url(); ?>home/checksession/',
          // data: 'user_id=' + user_id + '&item_id=' + item_id,
          success: function (msg) {
              var data=JSON.parse(msg);
              var idtabel='#tabelmeeting';
              var link='<?php echo base_url(); ?>meeting/getmeeting/';
              var textbutton='Add Meeting';
              var idmodal='#addmodalmeeting';
              var role='';
              var parameter={'name':'project_id','value':projectid};
              // alert(jQuery.inArray( "3", data.roleid ));
              if(jQuery.inArray( "3", data.roleid )>=0|| jQuery.inArray( "0", data.roleid )>=0){

                tablewithbutton(idtabel,link,parameter,textbutton,idmodal,role);
              }else{
                table(idtabel,link,parameter);
              }


          }
      });

    }
    function editmeeting(meetingid){
      $.ajax({
          type: 'post',
          url: '<?php echo base_url(); ?>meeting/getonemeeting/'+meetingid,
          // data: 'user_id=' + user_id + '&item_id=' + item_id,
          success: function (msg) {
            var data =JSON.parse(msg);
            $('#id_meeting').val(data.id_meeting);
            $('#project_idmeeting').val(data.project_id);
              $('#title').val(data.title);
            $('#lokasi').val(data.location);
            $('#do_date').datepicker('setDate',data.do_date);
              $('#starttime').val(data.starttime);
                $('#endtime').val(data.endtime);
                $('#remark').val(data.remark);
                $('#price').val(data.price);
                $('#setimagebukti').attr('src',data.imgbukti)
            $('#member_external').val(data.member_external);
            $.each($("#member"), function(){
                $(this).select2('val', data.member_internal);
        });
            $('#divstoreeditdetail').html(data.detailmeeting);
            $('.date').datepicker({
              dateFormat: 'mm/dd/yy'

            }
            );
            $(".divrowdetailmeeting").hide();
            $('#addmodalmeeting').modal('show');
          if(data.remark!=''){
            $("#divoperational").show();
          }
          }
        });
    }
    function detailmeeting(idmeeting){
      $.ajax({
          type: 'post',
          url: '<?php echo base_url(); ?>meeting/viewdetailmeeting/'+idmeeting,
          // data: 'user_id=' + user_id + '&item_id=' + item_id,
          success: function (msg) {
            // alert(msg);
            var data =JSON.parse(msg);
            $('#divdetailmeeting').html(data.html);
            var idtabel="#tabeldetailmeeting";
            var link="<?php echo base_url()?>meeting/getdetailmeetingtabel";
            var parameter={name:'id_meeting',value:idmeeting};
            var table = $(idtabel).dataTable({
              "bPaginate": false,
                // "sPaginationType": "full_numbers",
            //            "bJQueryUI": true,
                // "iDisplayLength": 30,
                "bDestroy": true,
                "bFilter": false,
                "bLengthChange": false,
                "responsive": true,
                "aaSorting": [],
                "bAutoWidth": true,
                "bSortable": false,
                "bSortClasses": true,
                "sAjaxSource": link,
                "fnServerParams": function (aoData) {
                           aoData.push(parameter);
                       }

            });
            $('#detailmodalmeeting').modal('show');

          }
        });
    }
    function deletemeeting(idmeeting){
      $.ajax({
          type: 'post',
          url: '<?php echo base_url(); ?>meeting/deletemeeting/'+idmeeting,
          // data: 'user_id=' + user_id + '&item_id=' + item_id,
          success: function (msg) {
                        var data =JSON.parse(msg);
                        if(data.status==1){
                          location.reload();
                        }else{
                          alert(data.msg);
                        }
          }
        });

    }

    function printmeeting(){
      // alert('a');
      var idmeeting= $('#idmeetingdetail').val();
    window.open("<?php echo base_url();?>meeting/printmeeting/"+idmeeting,'_blank');

    }
</script>

  <div id="linkbackfeature"></div>
    <div class="row">
        <div class="col-lg-12" id="divdataproject">
            <div id="divmeeting">

                <table id="tabelmeeting" class="table table-striped table-bordered" cellspacing="0" >
                    <thead>
                    <th>No.</th>
                    <th>Title</th>
                    <!--<th>Platform</th>-->

                    <th>Date</th>
                     <th>Location</th>
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
