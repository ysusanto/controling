<?php

if (!defined('BASEPATH'))
exit('No direct script access allowed');

class documentation_model extends CI_Model {

  public function __construct() {
    parent::__construct();
    $this->load->database();
  }

  function getdocumentation($data='') {
    $sql="select id_doc,project_id,nama,path,hours from documentation ";
    $iswhere=0;
    if(isset($data['project_id']) && $data['project_id']!=''){
      $where=($iswhere==0 ? ' where' : ' and');
      $sql .=$where ." project_id=".$this->db->escape($data['project_id']);
      $iswhere=1;
    }
    if(isset($data['id_doc']) && $data['id_doc']!=''){
      $where=($iswhere==0 ? ' where' : ' and');
      $sql .=$where ." id_doc=".$this->db->escape($data['id_doc']);
      $iswhere=1;
    }
    $sql .=" order by modified_date desc";
    $query = $this->db->query($sql);

    return $query;
  }

  function insertdokumentasi($data){
    // print_r($data);die();
    $db=$this->load->database('default',TRUE);
    date_default_timezone_set('Asia/Jakarta');
    $tanggal = date('Y-m-d H:i:s');
    $username=$this->session->userdata('username');


    $datainsert=array(
      'project_id'=>$data['project_id'],
      'nama'=>$data['namedoc'],
      'hours'=>$data['hoursdoc'],
      'path'=>$data['pathfile'],
      'created_date'=>$tanggal,
      'created_by'=>$username,
      'modified_date'=>$tanggal,
      'modified_by'=>$username
    );
    $query = $db->insert('documentation', $datainsert);
    $insert_id = $db->insert_id();
    return $insert_id;
  }

  function deletedokumentasi($data){
    $db=$this->load->database('default',TRUE);

    $sql="delete from documentation where id_doc=?";
    $qry=$db->query($sql,array($data['id_doc']));
$deletecost=$this->cost_model->deletecost(array('type'=>3,'id'=>$data['id_doc']));
    if($qry){
      $balikan =1;
    }else{
      $balikan=0;
    }
    return $balikan;
  }



}
