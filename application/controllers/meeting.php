<?php
if (!defined('BASEPATH'))
exit('No direct script access allowed');

class meeting extends CI_Controller {

  public function __construct() {
    parent::__construct();
    $this->load->library('upload');
    $this->load->model('db_load');
    $this->load->model('meeting_model');
    $this->load->model('user_model');
    $this->load->model('project_model');
    $this->load->model('cost_model');
  }

  function index($projectid){
    $content['user_id'] = $this->session->userdata('user_id');
    $header['menu']=$this->db_load->getmenu($content['user_id']);

    $header['title']='SIC|Meeting Setup';
    $header['username']=$this->session->userdata('nama');
    $user = $this->user_model->get_user($content['user_id']);
    $content['user'] = $user;
    $data['project_id']=$projectid;
    $data['member']=$this->db_load->getmember();

    $modal['id']='addmodalmeeting';
    $modal['title']='Meeting';
    $modal['class']= 'bs-example-modal-lg';
    $modal['content']=$this->load->view('formdetailmeeting',$data,TRUE);
    $meeting=$this->load->view('meeting_view',$data,TRUE);

    $content['content']['title']='Meeting Setup';

    $content['content']['content']=$meeting.$this->load->view('master/modals',$modal,TRUE);
    if($content['user_id']){
      $this->load->view('master/header',$header);
      $this->load->view('master/content',$content);
    }else{
      $this->load->view('signin');
    }
  }
  function getmeeting(){
    $roleid=$this->session->userdata('roleid');
    $data=array();
    foreach ($_GET as $key => $value) {
      # code...
      $data[$key]=$value;
    }
    $getmeeting=$this->meeting_model->getmeeting($data);
    if($getmeeting->num_rows()>0){
      $x=1;
      foreach ($getmeeting->result_array() as $value) {
        # code...
        $detailmeeting="<button class='btn btn-link' onclick=\"detailmeeting('".$value['id_meeting']."')\">".$value['title']."</button>";
        $edit = " <button type='button' onclick=\"editmeeting('" . $value['id_meeting'] . "')\" class='btn btn-info btn-xs'><span class='glyphicon glyphicon-pencil' aria-hidden='true'></span></button>";
        $delete = " <button type='button' onclick=\"deletemeeting('" . $value['id_meeting'] . "')\" class='btn btn-danger btn-xs'><span class='glyphicon glyphicon-remove' aria-hidden='true'></span></button>";
        if(in_array(0,$roleid)||in_array(3,$roleid)){
          $action=$edit." ".$delete;
        }else{
          $action='';
        }
        $date=date('d/m/Y',strtotime($value['do_date']));
        $json['aaData'][]=array($x,$detailmeeting,$date,$value['location'],$action);
        $x++;
      }
    }else{
      $json['aaData']=array();
    }
    echo json_encode($json);
  }
  function savemeeting(){
      $username=$this->session->userdata('username');
    $data=array();
    foreach ($_POST as $key => $value) {
      # code...
      $data[$key]=$value;
    }
    if(!empty($_FILES['imgbukti']['name'])){
      $ext = end(explode(".", $_FILES['imgbukti']['name']));
      if (!file_exists('assets/cost/'.$username)) {
        mkdir('assets/cost/'.$username, 0777, true);
      }
      $config['upload_path'] = 'assets/cost/'.$username;
      $config['allowed_types'] = 'gif|jpg|png';
      $config['max_size'] = 1024 * 8;
      // $config['encrypt_name'] = TRUE;
      $config['file_name'] = md5($data['project_id'])."_".date('ymdHis').".".$ext;
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



    if(!isset($data['member_internal'])){
      $data['member_internal']=array();
    }
    date_default_timezone_set('Asia/Jakarta');
    $data['do_date']=date('Y-m-d',strtotime($data['do_date']));
    if($data['id_meeting']==''){
      // print_r($_FILES['imgbukti']['name']);die();
      $savemeeting=$this->meeting_model->insertmeeting($data);
      if(sizeof($data['desc'])>0 && sizeof($data['pic'])>0 ){
        $data['id_meeting']=($savemeeting!=0?$savemeeting:'');

        for ($i=0; $i <sizeof($data['desc']) ; $i++) {
          # code...
          if($data['desc'][$i]==''){
            continue;
          }
          $dataMD=array(
            'id_meeting'=>$data['id_meeting'],
            'deskripsi'=>$data['desc'][$i],
            'pic'=>$data['pic'][$i],
            'enddate'=>$data['enddate'][$i]

          );
          $savemeetingdetail=$this->meeting_model->insertMeetingDetail($dataMD);
        }


      }
      if(strlen($data['remark'])>1){

        if(!empty($_FILES['imgbukti']['name'])){
          $ext = end(explode(".", $_FILES['imgbukti']['name']));
          if (!file_exists('assets/cost/'.$username)) {
            mkdir('assets/cost/'.$username, 0777, true);
          }
          $config['upload_path'] = 'assets/cost/'.$username;
          $config['allowed_types'] = 'gif|jpg|png|doc|txt';
          $config['max_size'] = 1024 * 8;
          // $config['encrypt_name'] = TRUE;
          $config['file_name'] = md5($data['project_id'])."_".date('ymdHis').".".$ext;
          // print_r($config);die();

          $this->upload->initialize($config);
          if (!$this->upload->do_upload('imgbukti'))
          {
            $status = 0;
            $msg = $this->upload->display_errors('', '');
            echo json_encode(array('status'=>0,'msg'=>$msg));
            die();
          }
          else
          {
            $dataimg = $this->upload->data();
            $data['path']='assets/cost/'.$username."/".$dataimg['file_name'];
          }
        }else{
          $path['path']='';
        }
        $data['type']=1;
        $data['id']=$data['id_meeting'];
        $insertcost=$this->cost_model->insertcost($data);
        $hsl=$insertcost;
      }
      if($savemeeting!=0){
        $balikan =array('status'=>1,'msg'=>"Meeting added");
      }else{
        $balikan =array('status'=>0,'msg'=>"Meeting failed added");
      }
    }else{
      $updatemeeting=$this->meeting_model->editmeeting($data);
      if($updatemeeting==1){
        $deletemeetingdetail=$this->meeting_model->deletemeetingdetail($data);

        if(sizeof($data['desc'])>0 && sizeof($data['pic'])>0 ){

          for ($i=0; $i <sizeof($data['desc']) ; $i++) {
            # code...
            $dataMD=array(
              'id_meeting'=>$data['id_meeting'],
              'deskripsi'=>$data['desc'][$i],
              'pic'=>$data['pic'][$i],
              'enddate'=>$data['enddate'][$i]
            );
            $savemeetingdetail=$this->meeting_model->insertMeetingDetail($dataMD);
          }


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
            $config['file_name'] = md5($data['project_id'])."_".date('ymdHis').$ext;
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
            $data['type']=1;
            $data['id']=$data['id_meeting'];
            $getcost=$this->cost_model->getdatacost($data);
            if($getcost->num_rows()>0){
              $row=$getcost->row_array();
              $data['path']=$row['path'];
            }else{

              $data['path']='';
            }
          }
          $data['type']=1;
          $data['id']=$data['id_meeting'];
          $insertcost=$this->cost_model->editcost($data);
          $hsl=$insertcost;
        }
        $balikan =array('status'=>1,'msg'=>"Meeting  Updated");
      }else{
        $balikan =array('status'=>0,'msg'=>"Meeting failed Update");
      }
    }

    echo json_encode($balikan);
  }

  function viewdetailmeeting($idmeeting=''){
    $getmeeting=$this->meeting_model->getmeeting(array('id_meeting'=>$idmeeting));
    if($getmeeting->num_rows()>0){
      $value=$getmeeting->row_array();
      $memberinternal='';
      if($value['member_internal']!=null||$value['member_internal']!=''){
        $memberinternal="<ul style='padding-left:0'>";
        foreach(explode(',',$value['member_internal']) as $userid){
          $user=$this->user_model->getuserdata(array('userid'=>$userid))->row_array();
          $memberinternal .="<li>".($user['gender']=='m'? "Mr.".$user['nama']:"Ms.".$user['nama'])."</li>";
        }
        $memberinternal.="</ul>";
      }
      $value['member_internal']=$memberinternal;
      $project=$this->project_model->getdataproject(array('project_id'=>$value['project_id']))->row_array();
      $value['project_id']=(sizeof($project)>0?$project['name']:'');
      $value['do_date']=date('d M Y',strtotime($value['do_date']));
      // print_r($value);die(0);
      $view=$this->load->view('detailmeeting_view',$value,TRUE);

      $modal['id']='detailmodalmeeting';
      $modal['title']='Detail Meeting';
      $modal['class']= 'bs-example-modal-lg';
      $modal['content']=$view;
      $balikan=$this->load->view('master/modals',$modal,TRUE);
      // print_r($balikan);die(0);
      echo json_encode(array('html'=>$balikan,'id'=>$idmeeting));

    }


  }
  function getdetailmeetingtabel(){
    $roleid=$this->session->userdata('roleid');
    $data=array();

    foreach ($_GET as $key => $value) {
      $data[$key]=$value;
    }
    $getmeeting=$this->meeting_model->getmeetingdetail($data);

    if($getmeeting->num_rows()>0){
      $x=1;
      foreach ($getmeeting->result_array() as $value) {
        # code...
        if(strlen($value['deskripsi'])<=1){
          continue;
        }
        $edit = " <button type='button' onclick=\"editmeetingdetail('" . $value['id_dm'] . "')\" class='btn btn-info btn-xs'><span class='glyphicon glyphicon-pencil' aria-hidden='true'></span></button>";
        $delete = " <button type='button' onclick=\"deletemeetingdetail('" . $value['id_dm'] . "')\" class='btn btn-danger btn-xs'><span class='glyphicon glyphicon-remove' aria-hidden='true'></span></button>";
        if($roleid==0||$roleid==3){
          $action=$edit." ".$delete;
        }else{
          $action='';
        }
        $user=$this->user_model->getuserdata(array('userid'=>$value['pic']))->row_array();
        $date="<div id='desc".$value['id_dm']."' >".$value['enddate']."</div>";
        $json['aaData'][]=array($x,
        "<div id='desc".$value['id_dm']."' >".$value['deskripsi']."</div>",
        "<div id='pic".$value['id_dm']."' >".($user['gender']=='m'?'Mr. '.$user['nama']: 'Ms. '.$user['nama'])."</div>",
        $date
      );
      $x++;
    }
  }else{
    $json['aaData']=array();
  }
  echo json_encode($json);
}

function deletemeeting($idmeeting){
  $deletemeeting=$this->meeting_model->deletemeeting(array('id_meeting'=>$idmeeting));
  if($deletemeeting==1){
    $balikan=array('status'=>1,'msg'=>'Meeting have been deleted');
  }else{
    $balikan=array('status'=>0,'msg'=>'Meeting failed deleted');
  }
  echo json_encode($balikan);
}
function getonemeeting($idmeetng){
  $getmeeting=$this->meeting_model->getmeeting(array('id_meeting'=>$idmeetng));
  $row=$getmeeting->row_array();
  $row['member_internal']=explode(',',$row['member_internal']);
  date_default_timezone_set('Asia/Jakarta');
  $row['do_date']=date('m/d/Y',strtotime($row['do_date']));
  $datacost=array('id'=>$idmeetng,
  'type'=>1);
  $getdatacost=$this->cost_model->getdatacost($datacost);
  if($getdatacost->num_rows()>0){
    $v=$getdatacost->row_array();
    $row['remark']=$v['desc'];
    $row['price']=$v['nominal'];
    $row['imgbukti']=($v['path']!=null &&$v['path']!=''?base_url().$v['path']:base_url()."assets/img/default-no-image.png");
  }else{
    $row['remark']='';
    $row['price']='';
    $row['imgbukti']=base_url()."assets/img/default-no-image.png";
  }
  $member=$this->db_load->getmember();
  $getmeetingdetail=$this->meeting_model->getmeetingdetail(array('id_meeting'=>$idmeetng));
  $html='';
  if($getmeetingdetail->num_rows()>0){
    foreach ($getmeetingdetail->result_array() as  $value) {
      $enddate=explode("-",$value['enddate']);
      $html='<div class="row divdetailmeeting">
      <div class="col-md-6">
      <div class="form-group">
      <label for="exampleInputEmail1">Description</label>
      <input class="form-control" name = "desc[]" id="descdetailmeeting" placeholder="..." style="width: 90%" value="'.$value['deskripsi'].'">
      </div>
      </div>
      <div class="col-md-3">
      <div class="form-group">
      <label for="exampleInputEmail1">PIC</label>
      <select class="form-control " name="pic[]" id="pic" style="width: 90%;">';


      foreach ($member as $o) {
        # code...
        if($value['pic']==$o['userid']){
          $html .= '<option value="'.$o['userid'].'" seleted>'.$o['salutation']." ".$o['nama'].'</option>';
        }else{
          $html .= '<option value="'.$o['userid'].'">'.$o['salutation']." ".$o['nama'].'</option>';
        }



      }

      $html.='</select>
      </div>
      </div>
      <div class="col-md-3">

      <div class="form-group">
      <label for="exampleInputEmail1">End Date(dd-mm-yyyy)</label>

      <div class="row">

      <input type="text" class="form-control date" name="enddate" id="enddate'.$value['id_dm'].'" placeholder="" >
      <script>
      $(\'#enddate'.$value['id_dm'].'\').datepicker(\'setDate\', new Date("'.date('m/d/Y',strtotime($value['enddate'])).'"));
      </script>
      </div>
      </div>

      </div>
      </div>';
    }
  }
  $row['detailmeeting']=$html;
  echo json_encode($row);

}

function printmeeting($idmeeting){
  $html='';
  $getmeeting=$this->meeting_model->getmeeting(array('id_meeting'=>$idmeeting));
  $row=$getmeeting->row_array();
  $getmeetingdetail=$this->meeting_model->getmeetingdetail(array('id_meeting'=>$idmeeting));
  $pdf = new Pdf('P', 'mm', 'A4', true, 'UTF-8', false);
$pdf->SetTitle('Pdf Example');
$pdf->SetHeaderMargin(30);
$pdf->SetTopMargin(20);
$pdf->setFooterMargin(20);
$pdf->SetAutoPageBreak(true);
$pdf->SetFont('helvetica', '', 10);

// add a page
$pdf->AddPage();
$html.='
<h1 class="title" style="

      font-size: 16pt;
      align:center">Minutes Of the Meeting</i></h1>';
$html.='
<table  cellspacing="0" cellpadding="2" border="0">
  <tr >
    <td width="100">Project Name</td>
    <td width="200">'.$row['name'].'</td>
    <td width="100">Date</td>
    <td width="100">'.date('m/d/Y',strtotime($row['do_date'])).'</td>
  </tr>
  <tr >
    <td width="100">Title Meeting</td>
    <td width="200">'.$row['title'].'</td>
    <td width="100">Internal Member</td>
    <td width="100">';
    $row['member_internal']=explode(',',$row['member_internal']);
    if(sizeof($row['member_internal']) >0){
      $html .='<ul>';
    foreach ($row['member_internal'] as  $value) {
      # code...
      $user=$this->user_model->getuserdata(array('userid'=>$value));
      if($user->num_rows()>0){
        $r=$user->row_array();
        $html .= '<li>'.$r['nama'].'</li>';
      }else{
        continue;
      }

    }
    $html .= '</ul>';
  }
    $html .='</td>
  </tr>
  <tr >
    <td width="100">Place</td>
    <td width="200">'.$row['location'].'</td>
    <td width="100">External Member</td>
    <td width="100">'.$row['member_external'].'</td>
  </tr>';


$html.='<tr><td colspan="4">';
$html .="</td></tr></table> <br>";

$html.='
<table border="1" cellspacing="0" cellpadding="2">
  <tr bgcolor="#2166d3" color="white" align="center">
    <th width="20">No.</th>
    <th width="300">Remark</th>
    <th width="120">PIC</th>
    <th width="100">End Date</th>
  </tr>';
  if($getmeetingdetail->num_rows()>0){
    $a=1;
    foreach ($getmeetingdetail->result_array() as $value) {
      if($a%2==0){
        $html.='<tr bgcolor="#dddddd">';
      }else{
      $html.="<tr>";
      }


        $html.="<td>".$a."</td>
        <td>".$value['deskripsi']."</td>
        <td>".$value['nama']."</td>
        <td>".date('m/d/Y',strtotime($value['enddate']))."</td>
      </tr>";
      $a++;
    }

  }else{
    $html.='<tr>
      <td colspan="4" algn="center">Data not available</td>

    </tr>';
  }


$html.="</table>";


$pdf->writeHTML($html, true, false, true, false, '');
$pdf->Output('pdfexample.pdf', 'I');
}

}
