<?php

if (!defined('BASEPATH'))
exit('No direct script access allowed');

class meeting_model extends CI_Model {

  public function __construct() {
    parent::__construct();
    $this->load->database();
  }

  function getmeeting($data='') {
    $sql="select id_meeting,m.project_id,judul as 'title',lokasi as location,p.name,do_date,starttime,endtime,member_internal,member_external from meeting m left join project p on m.project_id=p.project_id";
    $iswhere=0;
    if(isset($data['project_id']) && $data['project_id']!=''){
      $where=($iswhere==0 ? ' where' : ' and');
      $sql .=$where ." m.project_id=".$this->db->escape($data['project_id']);
      $iswhere=1;
    }
    if(isset($data['id_meeting']) && $data['id_meeting']!=''){
      $where=($iswhere==0 ? ' where' : ' and');
      $sql .=$where ." id_meeting=".$this->db->escape($data['id_meeting']);
      $iswhere=1;
    }
    $sql .=" order by m.modified_date desc";
    $query = $this->db->query($sql);

    return $query;
  }
  function getmeetingdetail($data=''){
    $sql="select id_dm,id_meeting,deskripsi,pic,u.nama,enddate from detail_meeting dm left join tuser u on u.userid=dm.pic where CHAR_LENGTH(deskripsi) >=1";
    $iswhere=1;
    if(isset($data['id_meeting']) && $data['id_meeting']!=''){
      $where=($iswhere==0 ? ' where' : ' and');
      $sql .=$where ." id_meeting=".$this->db->escape($data['id_meeting']);
      $iswhere=1;
    }
    if(isset($data['id_dm']) && $data['id_dm']!=''){
      $where=($iswhere==0 ? ' where' : ' and');
      $sql .=$where ." id_dm=".$this->db->escape($data['id_dm']);
      $iswhere=1;
    }
    $query = $this->db->query($sql);
    return $query;
  }
  function getformdatameeting() {
    $keyword=array();
    $getmeeting=$this->getmeeting();
    if($getmeeting->num_rows()>0){

      foreach ($getmeeting->row_array() as $key => $value) {
        # code...

        if($key=='id_meeting'){
          $a['type']='hidden';
        }elseif ($key=='member_internal') {
          $a['type']='dropdown';
          $option=array();
          $getmember=$this->db_load->getmember();
          if(sizeof($getmember)>0){
            foreach ($getmember as $value) {
              # code...
              array_push($option,array('value'=>$value['userid'],'name'=>$value['salutation']." ".$value['nama']));
            }
          }
          $a['option']=$option;
          $a['multiple']=1;
          # code...
        }elseif ($key=='member_external') {
          $a['type']='textarea';
          # code...
        }elseif($key=='do_date'){
          $a['type']='text';
          $a['class']='date';
        }elseif($key=='starttime'||$key=='endtime'){
          $a['type']='text';
          $a['class']='timepicker';
        }else{
          $a['type']='text';
        }

        $a['name']=$key;
        $a['label']=($key=='do_date'?'Due Date':ucwords($key));
        $a['id']=$key;
        array_push($keyword,$a);
      }
    }
    $data=  array(
      'action'=>base_url().'meeting/savemeeting',
      'id_form'=>'formmeeting',
      'inputform'=>$keyword,
      'button'=>array('idno'=>'cancel','labelno'=>'Cancel','idyes'=>'save','labelyes'=>'Save')


    );
    return $data;
  }
  function insertmeeting($data){
    // print_r($data);die();
    $db=$this->load->database('default',TRUE);
    date_default_timezone_set('Asia/Jakarta');
    $tanggal = date('Y-m-d H:i:s');
    $username=$this->session->userdata('username');

  date_default_timezone_set('Asia/Jakarta');
    $datainsert=array(
      'project_id'=>$data['project_id'],
      'judul'=>$data['title'],
      'lokasi'=>$data['lokasi'],
      'do_date'=>$data['do_date'],
      // 'starttime'=>$data['starttime'],
      // 'endtime'=>$data['endtime'],
      'member_internal'=>implode(',',$data['member_internal']),
      'member_external'=>$data['member_external'],
      'created_date'=>$tanggal,
      'created_by'=>$username,
      'modified_date'=>$tanggal,
      'modified_by'=>$username
    );
    $query = $db->insert('meeting', $datainsert);
    $insert_id = $db->insert_id();
    return $insert_id;
  }

  function insertMeetingDetail($data){
    $db=$this->load->database('default',TRUE);
    date_default_timezone_set('Asia/Jakarta');
    $tanggal = date('Y-m-d H:i:s');
    $username=$this->session->userdata('username');


    $datainsert=array(
      'created_date'=>$tanggal,
      'created_by'=>$username,
      'modified_date'=>$tanggal,
      'modified_by'=>$username
    );
    $insert=array_merge($data,$datainsert);
    $query = $db->insert('detail_meeting', $insert);
    $insert_id = $db->insert_id();
    return $insert_id;


  }
  function deletemeeting($data){
    $db=$this->load->database('default',TRUE);

    $sql="delete from meeting where id_meeting=?";
    $qry=$db->query($sql,array($data['id_meeting']));
    $sql2="delete from detail_meeting where id_meeting=?";
    $qry=$db->query($sql2,array($data['id_meeting']));
    $deletecost=$this->cost_model->deletecost(array('type'=>1,'id'=>$data['id_meeting']));
    if($qry){
      $balikan =1;
    }else{
      $balikan=0;
    }
    return $balikan;
  }

  function editmeeting($data){
    $username=$this->session->userdata('username');
    $db=$this->load->database('default',TRUE);
    date_default_timezone_set('Asia/Jakarta');
    $tanggal = date('Y-m-d H:i:s');
    // print_r($data);die();
    $data['username']=$username=$this->session->userdata('username');
    $meetingupdate = array(

      'judul'=>$data['title'],
      'lokasi'=>$data['lokasi'],
      'do_date'=>date('Y-m-d H:i:s',strtotime($data['do_date'])),
      // 'starttime'=>$data['starttime'],
      // 'endtime'=>$data['endtime'],
      'member_internal'=>implode(',',$data['member_internal']),
      'member_external'=>$data['member_external'],
      'modified_date' => $tanggal,
      'modified_by' => $username,
    );
     $db->where('id_meeting', $data['id_meeting']);
    $query = $db->update('meeting', $meetingupdate);
    if($query){
      return 1;
    }else{
      return 0;
    }

  }
  function deletemeetingdetail($data){
    $db=$this->load->database('default',TRUE);
    $sql2="delete from detail_meeting where id_meeting=?";
    $qry=$db->query($sql2,array($data['id_meeting']));
    if($qry){
      $balikan =1;
    }else{
      $balikan=0;
    }

  }

}
