<?php

if (!defined('BASEPATH'))
exit('No direct script access allowed');

class role_model extends CI_Model {

  public function __construct() {
    parent::__construct();
    $this->load->database();
  }
  function getrole($data=''){
    $db=$this->load->database('default',TRUE);
    $sql='select role_id,name from role ';
    $iswhere=0;
    if(isset($data['role_id']) && $data['role_id']!=''){
      $where =($iswhere==0? ' where':'and');
      $sql .=$where.' role_id='.$data['role_id'];
    }
    $sql .= ' order by 1 asc';
    $qry=$db->query($sql);
    return $qry;
  }

  function formrole(){
    $keyword=array();
    $getrole=$this->getrole();
    $value=$getrole->row_array();
    # code...
    foreach ($value as $key =>$v ) {

      $a['type']=($key=='role_id'?'number':'text');
      $a['name']=$key;
      $a['label']=($key=='role_id'?'Role ID':ucwords($key));
      $a['id']=$key;
      array_push($keyword,$a);
    }


    // echo json_encode($keyword);die(0);s
    $data=  array(
      'action'=>base_url().'role/saverole',
      'id_form'=>'formrole',
      'inputform'=>$keyword,
      'button'=>array('idno'=>'cancel','labelno'=>'Cancel','idyes'=>'save','labelyes'=>'Save')


    );
    return $data;
  }
  function insertrole($data){
    $db=$this->load->database('default',TRUE);
    date_default_timezone_set('Asia/Jakarta');
    $tanggal = date('Y-m-d H:i:s');
    $insert = array(
        'role_id' => $data['role_id'],
        'name' => $data['name'],
        'created_date' => $tanggal,
        'created_by'=> $this->session->userdata('username')

    );
    $query = $db->insert('role', $insert);
    $insert_id = $db->insert_id();
    return $query;
  }

  function updaterole($data){
    $db=$this->load->database('default',TRUE);
    $sql=" update role set name='".$data['name']."' where role_id='".$data['role_id']."'";
    $query=$db->query($sql);
    return $query;
  }

  function deleterole($data){
    $db=$this->load->database('default',TRUE);
    $sql=" delete from role  where role_id='".$data['role_id']."'";
    $query=$db->query($sql);
    return $query;
  }
  function getgruprole($data){
    $db=$this->load->database('default',TRUE);
    $sql='select  gr.user_id,r.role_id,r.name from role r right  join gruprole gr on r.role_id=gr.role_id ';
    $iswhere=0;
    if(isset($data['user_id']) && $data['user_id']!=''){
      $where =($iswhere==0? ' where':'and');
      $sql .=$where.' gr.user_id='.$data['user_id'];
    }
    $sql .= ' group by gr.user_id,r.role_id,r.name order by 1 asc';
    $qry=$db->query($sql);
    return $qry;
  }
}
