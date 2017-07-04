<?php
if (!defined('BASEPATH'))
exit('No direct script access allowed');

class dokumentasi extends CI_Controller {

  public function __construct() {
    parent::__construct();
    $this->load->library('upload');
    $this->load->model('db_load');
    $this->load->model('documentation_model');
    $this->load->model('user_model');
    $this->load->model('project_model');
    $this->load->model('cost_model');
  }

  function index($projectid){
    $content['user_id'] = $this->session->userdata('user_id');
    $header['menu']=$this->db_load->getmenu($content['user_id']);

    $header['title']='SIC|Documentation Setup';
    $header['username']=$this->session->userdata('nama');
    $user = $this->user_model->get_user($content['user_id']);
    $content['user'] = $user;
    $data['project_id']=$projectid;
    $data['member']=$this->db_load->getmember();

    $modal['id']='addmodaldokumetasi';
    $modal['title']='Documentation';
    $modal['class']= '';
    $modal['content']=$this->load->view('formdocumentation',$data,TRUE);
    $meeting=$this->load->view('documentation_view',$data,TRUE);

    $content['content']['title']='Documentation Setup';

    $content['content']['content']=$meeting.$this->load->view('master/modals',$modal,TRUE);
    if($content['user_id']){
      $this->load->view('master/header',$header);
      $this->load->view('master/content',$content);
    }else{
      $this->load->view('signin');
    }
  }
  function getdokumentasi(){
    $roleid=$this->session->userdata('roleid');
    $data=array();
    foreach ($_GET as $key => $value) {
      # code...
      $data[$key]=$value;
    }
    $getdoc=$this->documentation_model->getdocumentation($data);
    if($getdoc->num_rows()>0){
      $x=1;
      foreach ($getdoc->result_array() as $value) {
        # code...
        $filedokumentasi="<a href='".base_url().$value['path']."' >".$value['nama']."</a>";
        $edit = " <button type='button' onclick=\"editdoc('" . $value['id_doc'] . "')\" class='btn btn-info btn-xs'><span class='glyphicon glyphicon-pencil' aria-hidden='true'></span></button>";
        $edit='';
        $delete = " <button type='button' onclick=\"deletedoc('" . $value['id_doc'] . "')\" class='btn btn-danger btn-xs'><span class='glyphicon glyphicon-remove' aria-hidden='true'></span></button>";
        if(in_array(0,$roleid)||in_array(3,$roleid)){
          $action=$edit." ".$delete;
        }else{
          $action='';
        }

        $json['aaData'][]=array($x,$filedokumentasi,$value['hours'],$action);
        $x++;
      }
    }else{
      $json['aaData']=array();
    }
    echo json_encode($json);
  }
  function savedokumentasi(){
    $data=array();
      $username=$this->session->userdata('username');
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
      $dataimg = $this->upload->data();
      $path='assets/cost/'.$username."/".$dataimg['file_name'];
      unlink($path);
    }



    if($data['id_doc']==''){
      $checkproject=$this->project_model->getdataproject($data)->row_array();
      // print_r($_FILES['imgbukti']['name']);die();

      if(!empty($_FILES['filedoc']['name'])){
        $ext = end(explode(".", $_FILES['filedoc']['name']));
        if (!file_exists('assets/dokumentasi/'.strtolower($checkproject['name']))) {
          mkdir('assets/dokumentasi/'.strtolower($checkproject['name']), 0777, true);
        }
        $config['upload_path'] = 'assets/dokumentasi/'.strtolower($checkproject['name']);
        $config['allowed_types'] = 'doc|docx|pdf|xls|xlsx';
        // $config['max_size'] = 1024 * 8;
        // $config['encrypt_name'] = TRUE;
        $config['file_name'] = $data['namedoc']."_".date('ymdHis').".".$ext;
        // print_r($config);die();

        $this->upload->initialize($config);
        if (!$this->upload->do_upload('filedoc'))
        {
          $status = 0;
          $msg = $this->upload->display_errors('', '');
          echo json_encode(array('status'=>0,'msg'=>$msg));
          die();
        }
        else
        {
          $dataimg = $this->upload->data();
          $data['pathfile']='assets/dokumentasi/'.strtolower($checkproject['name'])."/".$dataimg['file_name'];
        }
      }else{
        $data['pathfile']='';
      }
      $savedokumentasi=$this->documentation_model->insertdokumentasi($data);
      $data['id_doc']=$savedokumentasi;
      //upload image operationalcost
      if(strlen($data['remark'])>1){
        $username=$this->session->userdata('username');
        if(!empty($_FILES['imgbukti']['name'])){
          $ext = end(explode(".", $_FILES['imgbukti']['name']));
          if (!file_exists('assets/cost/'.$username)) {
            mkdir('assets/cost/'.$username, 0777, true);
          }
          $config['upload_path'] = 'assets/cost/'.$username;
          $config['allowed_types'] = 'gif|jpg|png';
          $config['max_size'] = 1024 * 8;
          // $config['encrypt_name'] = TRUE;
          $config['file_name'] = md5($data['project_id'].$username)."_".date('ymdHis').$ext;
          // print_r($config);die();

          $this->upload->initialize($config);
          if (!$this->upload->do_upload('imgbukti'))
          {
            $status = 0;
            $msg = $this->upload->display_errors('', '');
            echo json_encode(array('status'=>0,'msg'=>$msg));
          }
          else
          {
            $dataimg = $this->upload->data();
            $data['path']='assets/cost/'.$username."/".$dataimg['file_name'];
          }
        }else{
          $data['path']='';
        }
        $data['type']=3;
        $data['id']=$data['id_doc'];
        $insertcost=$this->cost_model->insertcost($data);
      }
      $hsl=$savedokumentasi;
    }else{
      $updatemeeting='';
    }
    if($hsl!=0){
      $balikan =array('status'=>1,'msg'=>"Documentation added");
    }else{
      $balikan =array('status'=>0,'msg'=>"Documentation failed added");
    }
    echo json_encode($balikan);
  }



  function deletedoc($iddoc){
    $getdoc=$this->documentation_model->getdocumentation(array('id_doc'=>$iddoc))->row_array();
    if($getdoc['path']!=''){
      $this->load->helper("url");
      unlink($getdoc['path']);
    }

    $deletedoc=$this->documentation_model->deletedokumentasi(array('id_doc'=>$iddoc));
    if($deletedoc==1){
      $balikan=array('status'=>1,'msg'=>'Documentation have been deleted');
    }else{
      $balikan=array('status'=>0,'msg'=>'Documentation failed deleted');
    }
    echo json_encode($balikan);
  }
}
