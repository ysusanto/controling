<?php

/*
* To change this license header, choose License Headers in Project Properties.
* To change this template file, choose Tools | Templates
* and open the template in the editor.
*/

/**
* Description of timesheet
*
* @author ASUS
*/
class timesheet extends CI_Controller {

  //put your code here

  public function __construct() {
    parent::__construct();
    $this->load->model('timesheet_model');
    $this->load->model('db_load');
  }

  function viewproject() {
    $getproject = $this->timesheet_model->get_projecttabel();
    echo json_encode($getproject);
  }
function dokumentasi($Project_id){

}
  function detailproject($id) {


    $getdetailproject = $this->timesheet_model->get_detailproject($id);


    //        print_r($content);die(0);


    $content['user_id'] = $this->session->userdata('user_id');
    $header['menu']=$this->db_load->getmenu($content['user_id']);
  $data['chekpm'] = $this->timesheet_model->checkpmtimesheet();
    $header['title']='SIC|Timesheet Setup';
    $header['username']=$this->session->userdata('nama');
    $user = $this->user_model->get_user($content['user_id']);
    $content['user'] = $user;
    $data['projectid']=$id;
    $data['platform']=$getdetailproject['platform'];
    $data['project'] = $getdetailproject['name'];
    $data['roleid'] = $this->session->userdata('roleid');
    // print_r($content);die(0);
    $meeting=$this->load->view('timesheetdetail_view',$data,TRUE);

    $content['content']['title']='Timesheet Setup';

    $content['content']['content']=$meeting;
    if($content['user_id']){
      $this->load->view('master/header',$header);
      $this->load->view('master/content',$content);
    }else{
      $this->load->view('signin');
    }


  }

  function timesheettable($type='') {
    date_default_timezone_set('Asia/Jakarta');
    $data = array();
    foreach ($_GET as $key => $value) {
      $data[$key] = $value;
    }
    $data['type']=$type;
    $gettimesheet = $this->timesheet_model->gettimesheettableall($data);
    echo json_encode($gettimesheet);
  }
  function getonetimesheet($id){
    $userid=$this->session->userdata('user_id');
    $getdata=$this->timesheet_model->getonetimesheet(array('timesheet_id'=>$id))->row_array();
    $getcost=$this->cost_model->getdatacost(array('type'=>2,'id'=>$id,'userid'=>$userid))->row_array();
    if(sizeof($getcost)>0){
      $getdata['remark']=$getcost['desc'];
      $getdata['nominal']=$getcost['nominal'];
      $getdata['image']=($getcost['path']!='' ?base_url().$getcost['path']: base_url().'assets/img/default-no-image.png');
    }else{
      $getdata['remark']='';
      $getdata['nominal']='';
      $getdata['image']=base_url().'assets/img/default-no-image.png';
    }

    echo json_encode($getdata);
  }
  function savetimesheetbaru() {
    date_default_timezone_set('Asia/Jakarta');
    $data = array();
    foreach ($_POST as $key => $value) {
      $data[$key] = $value;
    }

    //        $implodeitemid=explode('_',$data['item_id']);
    $data['start_date'] = $data['date'] . " " . $data['start_time'];
    $data['end_date'] = $data['date'] . " " . $data['end_time'];
    $datetime1 = strtotime($data['start_date']);
    $datetime2 = strtotime($data['end_date']);
    $interval = abs($datetime2 - $datetime1);
    $data['hours'] = $interval / 60 / 60;
    //        echo json_encode($data);die(0);

    $savedata = $this->timesheet_model->savetimesheet($data);
    Redirect(base_url()+"STS/projects", false);
    die(0);
    exit();
  }

  function savetimesheet() {
    $username=$this->session->userdata('username');
    date_default_timezone_set('Asia/Jakarta');
    $data = array();
    foreach ($_POST as $key => $value) {
      $data[$key] = $value;
    }

    if(!empty($_FILES['imgbukti']['name'])){
      $ext = end(explode(".", $_FILES['imgbukti']['name']));
      if (!file_exists('assets/cost/'.$username)) {
        mkdir('assets/cost/'.$username, 0777, true);
      }
      $config['upload_path'] = 'assets/cost/'.$username;
      $config['allowed_types'] = 'gif|jpg|png|doc|txt';
      $config['max_size'] = 1024 * 8;
      // $config['encrypt_name'] = TRUE;
      $config['file_name'] = md5($data['item_id'])."_".date('ymdHis').".".$ext;
      // print_r($config);die();

      $this->upload->initialize($config);
      if (!$this->upload->do_upload('imgbukti'))
      {
        $status = 0;
        $msg = $this->upload->display_errors('', '');
        echo json_encode(array('status'=>0,'msg'=>$msg));
        die();
      }
      $dataimg = $this->upload->data();
      $path='assets/cost/'.$username."/".$dataimg['file_name'];
      unlink($path);
    }

    $chektimeesheet=$this->timesheet_model->getonetimesheet(array('item_id'=>$data['item_id']))->row_array();
    $data['project_id']=$chektimeesheet['project_id'];
    //        print_r($data);die(0);
    //        $implodeitemid=explode('_',$data['item_id']);
    //$data['start_date'] = $data['date'] . " " . $data['start_time'];
    // $data['end_date'] = $data['date'] . " " . $data['end_time'];
    // $datetime1 = strtotime($data['start_date']);
    // $datetime2 = strtotime($data['end_date']);
    // $interval = abs($datetime2 - $datetime1);
    //$data['hours'] = round($interval / 60 / 60);
    if($data['timesheet_id']==''){
      $savedata = $this->timesheet_model->savetimesheet($data);
      $data['id']=$savedata['id'];
      $hasil=$savedata;
    }else{
      $update=$this->timesheet_model->updatetimesheets($data);
      $savedata['id']=$data['timesheet_id'];
      $hasil=$update;
    }

    if(strlen($data['remark'])>1){
      $username=$this->session->userdata('username');
      if(!empty($_FILES['imgbukti']['name'])){
        $ext = end(explode(".", $_FILES['imgbukti']['name']));
        if (!file_exists('assets/cost/'.$username)) {
          mkdir('assets/cost/'.$username, 0777, true);
        }
        $config['upload_path'] = 'assets/cost/'.$username;
        $config['allowed_types'] = 'gif|jpg|png|doc|txt';
        $config['max_size'] = 1024 * 8;
        // $config['encrypt_name'] = TRUE;
        $config['file_name'] = md5($data['item_id'])."_".date('ymdHis').$ext;
        // print_r($config);die();

        $this->upload->initialize($config);
        if (!$this->upload->do_upload('imgbukti'))
        {
          $status = 0;
          $msg = $this->upload->display_errors('', '');
          echo json_encode(array('status'=>0,'msg'=>$msg));
          die(0);
        }
        else
        {
          $dataimg = $this->upload->data();
          $data['path']='assets/cost/'.$username."/".$dataimg['file_name'];
        }
      }else{
        $data['type']=2;
        $data['id']=$savedata['id'];
        $getcost=$this->cost_model->getdatacost($data);
        if($getcost->num_rows()>0){
          $row=$getcost->row_array();
          $data['path']=$row['path'];
        }else{

          $data['path']='';
        }
      }
      $data['type']=2;
      $data['id']=$savedata['id'];
      $getcost=$this->cost_model->getdatacost($data);
      if($getcost->num_rows()>0){
        $data['type']=2;
        $data['id']=$savedata['id'];
        $insertcost=$this->cost_model->editcost($data);
        $hsl=$insertcost;
      }else{
        $data['type']=2;
        $data['id']=$savedata['id'];
        $insertcost=$this->cost_model->insertcost($data);
        $hsl=$insertcost;
      }
    }
    echo json_encode($hasil);
  }
  function detailcost($timesheet_id){
    $data=array();
    foreach ($_POST as $key => $value) {
      # code...
      $data[$key]=$value;
    }
    $userid = $this->session->userdata('user_id');
    $roleid=$this->session->userdata('roleid');
    $condcost=array(
      'id'=>$data['timesheet_id'],
      'type'=>2,
      'userid'=>$userid
    );
    if(in_array(2,$roleid)){
      $condcost=array(
        'id'=>$data['timesheet_id'],
        'type'=>2,
        'project_id'=>$data['project_id']
      );
    }
    $getcost=$this->cost_model->getdatacost($condcost);
    if($getcost->num_rows()>0){
      $row=$getcost->row_array();
      $cost['image']=($row['path']!=''? base_url().$row['path']: base_url().'assets/img/default-no-image.png');
      $cost['remark']=$row['desc'];
      $cost['price']=$row['nominal'];
      $viewcost=$this->load->view('cost_detail_view',$cost,TRUE);
      $modal['id']='costmodal';
      $modal['title']='Operational Cost';
      $modal['content']=$viewcost;
      $hasil=$this->load->view('master/modals',$modal,TRUE);
    }else{

      $hasil=0;
    }
    echo $hasil;
  }
  function detailmainhours($item_id) {
    date_default_timezone_set('Asia/Jakarta');
    $detailmainhours = $this->timesheet_model->detailmainhours($item_id);
    $detailitemname = $this->timesheet_model->getitemname($item_id);

    $itemname = $detailitemname['name'];

    $table = ' <div class="box-body table-responsive no-padding">
    <table class="table table-hover">
    <tr>
    <th>No.</th>
    <th>Main Hours</th>
    <th>Range Date</th>
    <th>Assign</th>

    </tr>';
    $x = 1;
    if (sizeof($detailmainhours) > 0) {
      foreach ($detailmainhours as $row) {
        $row['start_date'] = strtotime($row['start_date']);
        $row['start_date'] = date('d M Y H:i:s', $row['start_date']);
        $row['end_date'] = strtotime($row['end_date']);
        $row['end_date'] = date('d M Y H:i:s', $row['end_date']);

        $range = $row['start_date'] . " - " . $row['end_date'];

        $table .='
        <tr>
        <td>' . $x . '</td>
        <td>' . $row['main_hour'] . '</td>
        <td>' . $range . '</td>
        <td>' . $row['username'] . '</td>

        </tr>';
        $x++;
      }
    }
    $table .='</table>
    </div>';

    echo json_encode(array('table' => $table, 'name' => $itemname));
  }

  function itemname($id) {
    $detailitemname = $this->timesheet_model->getitemname($id);
    $itemname = $detailitemname['name'];
    echo $itemname;
  }
  function savesigncosttimesheet(){
    $data=array();
    foreach ($_POST as $key => $value) {
      # code...
      $data[$key]=$value;
    }
    $userid=$this->session->userdata('user_id');
    $hasil=array();
    if(sizeof($data['sighpm'])>0){
      $x=0;
      foreach ($data['sighpm'] as  $tmd) {
        # code...
        $array_update=array(
          'is_sign'=>1,
          'sign_by'=>$userid
        );
        $cond=array(
          'id'=>$tmd,
          'type'=>2
        );
        $updatesigncost=$this->cost_model->updatesigncost($cond,$array_update);
        array_push($hasil,$updatesigncost);
      }


    }
    if(in_array(0,$hasil)){
      $balikan=array(
        'status'=>0,
        'msg'=>'Sign Cost Failed Update'
      );
    }else{
      $balikan=array(
        'status'=>1,
        'msg'=>'Sign Cost Updated'
      );
    }
    echo json_encode($balikan);
  }
}
