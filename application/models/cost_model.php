<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class cost_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

function insertcost($data){
  $db=$this->load->database('default',TRUE);
  date_default_timezone_set('Asia/Jakarta');
  $tanggal = date('Y-m-d H:i:s');
  $username=$this->session->userdata('username');
  $userid=$this->session->userdata('user_id');
            $datainsert=array(
              'project_id'=>$data['project_id'],
              'desc'=>$data['remark'],
              'nominal'=>$data['price'],
              'path'=>$data['path'],
              'userid'=>$userid,
              'type'=>$data['type'],
              'id'=>$data['id'],
              'created_date'=>$tanggal,
              'created_by'=>$username,
              'modified_date'=>$tanggal,
              'modified_by'=>$username
            );
            $query = $db->insert('operationalcost', $datainsert);
            $insert_id = $db->insert_id();
            return $insert_id;



}

function updatesigncost($data,$array_update){
  $username=$this->session->userdata('username');
      $roleid = $this->session->userdata('roleid');
  $db=$this->load->database('default',TRUE);
  date_default_timezone_set('Asia/Jakarta');
  $tanggal = date('Y-m-d H:i:s');
  // print_r($data);die();
  $userid=$this->session->userdata('user_id');
  $data['username']=$username=$this->session->userdata('username');
  $cupdate = array(

    'modified_date' => $tanggal,
    'modified_by' => $username,
  );
  $costupdate= array_merge($array_update,$cupdate);
  if(in_array(99,$roleid)){
    $db->where('id_oc', $data['id_oc']);

  }else{
    $db->where('type', $data['type']);
     $db->where('id', $data['id']);
  }


  $query = $db->update('operationalcost', $costupdate);
  if($query){
    return 1;
  }else{
    return 0;
  }
}
function editcost($data){
  $username=$this->session->userdata('username');
  $db=$this->load->database('default',TRUE);
  date_default_timezone_set('Asia/Jakarta');
  $tanggal = date('Y-m-d H:i:s');
  // print_r($data);die();
  $userid=$this->session->userdata('user_id');
  $data['username']=$username=$this->session->userdata('username');
  $costupdate = array(
    'project_id'=>$data['project_id'],
    'desc'=>$data['remark'],
    'nominal'=>$data['price'],
    'path'=>$data['path'],
    'userid'=>$userid,
    'modified_date' => $tanggal,
    'modified_by' => $username,
  );
   $db->where('type', $data['type']);
    $db->where('id', $data['id']);

  $query = $db->update('operationalcost', $costupdate);
  if($query){
    return 1;
  }else{
    return 0;
  }
}
function getdatacost($data=''){
  $sql="select * from operationalcost ";
  $iswhere=0;
  if(isset($data['project_id']) && $data['project_id']!=''){
    $where=($iswhere==0 ? ' where' : ' and');
    $sql .=$where ." project_id=".$this->db->escape($data['project_id']);
    $iswhere=1;
  }
  if(isset($data['userid']) && $data['userid']!=''){
    $where=($iswhere==0 ? ' where' : ' and');
    $sql .=$where ." userid=".$this->db->escape($data['userid']);
    $iswhere=1;
  }
  if(isset($data['type']) && $data['type']!=''){
    $where=($iswhere==0 ? ' where' : ' and');
    $sql .=$where ." type=".$this->db->escape($data['type']);
    $iswhere=1;
  }
  if(isset($data['id']) && $data['id']!=''){
    $where=($iswhere==0 ? ' where' : ' and');
    $sql .=$where ." id=".$this->db->escape($data['id']);
    $iswhere=1;
  }
  // print_r($sql);die();
$query = $this->db->query($sql);

return $query;
}
function deletecost($data){
  $getdoc=$this->getdatacost(array('type'=>$data['type'],'id'=>$data['id']))->row_array();
  if(isset($getdoc['path']) && $getdoc['path']!=''){
    $this->load->helper("url");
    unlink($getdoc['path']);
  }

  $sql2="delete from operationalcost where type=? and id=?";
  $qry=$this->db->query($sql2,array($data['type'],$data['id']));
  if($qry){
    $balikan =1;
  }else{
    $balikan=0;
  }
  return $balikan;
}
}
