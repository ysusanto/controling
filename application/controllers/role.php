<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class role extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('db_load');
        $this->load->model('role_model');
    }

    function getroletable(){
      $getdatarole=$this->role_model->getrole();
      if($getdatarole->num_rows()>0){
        foreach($getdatarole->result_array() as $row){
          $edit='<button class="btn btn-info btn-sm" onclick="editrole('.$row['role_id'].')"><span class="glyphicon glyphicon-pencil"></span></button>';
          $delete='<button class="btn btn-danger btn-sm" onclick="deleterole('.$row['role_id'].')"><span class="glyphicon glyphicon-trash"></span></button>';
          $action=$edit.$delete;
          $json['aaData'][]=array($row['role_id'],$row['name'],$action);
        }
      }else{
        $json['aaData']=array();
      }
      echo json_encode($json);
    }

    function saverole(){
      $data=array();
      foreach ($_POST as $key => $value) {
        $data[$key]=$value;
        # code...
      }

       $chekrole=$this->role_model->getrole($data);
       if($chekrole->num_rows()>0){
         $updaterole=$this->role_model->updaterole($data);
         $return=$insertrole;
       }else{
         $insertrole=$this->role_model->insertrole($data);
         $return=$insertrole;
       }
       if($return){
         $balikan=array('status'=>1,'msg'=>'Role data have been saved');
       }else{
         $balikan=array('status'=>0,'msg'=>'Role data failed save');
       }
       echo json_encode($balikan);
    }
    function getonerole($id){
      $getrole=$this->role_model->getrole(array('role_id'=>$id));
      if($getrole->num_rows()>0){
        $balikan =array('status'=>1,'data'=>$getrole->row_array());
      }else{
        $balikan=array('status'=>0,'msg'=>'Role data empty');
      }
      echo json_encode($balikan);
    }
    function deleterole($id){
      $deleterole=$this->role_model->getrole(array('role_id'=>$id));
      if($deleterole){
        $balikan =array('status'=>1,'msg'=>'Role has been deleted');
      }else{
        $balikan=array('status'=>0,'msg'=>'Role data failed delete');
      }
      echo json_encode($balikan);
    }
  }
