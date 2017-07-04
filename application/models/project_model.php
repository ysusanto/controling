<?php

if (!defined('BASEPATH'))
exit('No direct script access allowed');

class Project_model extends CI_Model {

  public function __construct() {
    parent::__construct();
    $this->load->database();
    require_once 'assets/PHPExcel.php';
    require_once 'assets/PHPExcel/IOFactory.php';
    //        error_reporting(E_ALL);
    //        set_error_handler(array($this, "errorHandler"), E_ALL);
    date_default_timezone_set('Asia/Jakarta');
    ini_set('memory_limit', '500M');
    ini_set('max_execution_time', '300000');
  }

  public function get($project_id) {
    $query = $this->db->get_where('tb_project', array('project_id' => $project_id));
    if ($query->num_rows() > 0) {
      return $query->row();
    }
  }

  public function get_projects() {
    $query = $this->db->get('tb_project');
    return $query->result();
  }

  public function feature_count($project_id) {
    $index = 0;
    $features_parent = $this->get_features($project_id);
    foreach ($features_parent as $feature_parent) {
      $features_group = $this->get_features($project_id, $feature_parent->feature_id);
      if (sizeof($features_group) > 0) {
        foreach ($features_group as $feature_group) {
          $features_function = $this->get_features($project_id, $feature_group->feature_id);
          if (sizeof($features_function) > 0) {
            $index += sizeof($features_function);
          } else {
            $index++;
          }
        }
      } else {
        $index++;
      }
    }
    return $index;
  }

  public function get_features($project_id, $parent_id = NULL) {
    $query = $this->db->get_where('tb_feature', array(
      'project_id' => $project_id,
      'parent_id' => $parent_id));

      return $query->result();
    }



    function getdataproject($data=''){
      $roleid = $this->session->userdata('roleid');
      $sql = "select a.project_id,a.name,a.client,date_format(a.stardate,'%d/%m/%Y')as 'stardate',date_format(a.enddate,'%m/%d/%Y')as 'enddate',b.username,a.userid from project a left join tuser b on a.userid=b.userid ";
      $iswhere=0;
      if(isset($data['project_id']) && $data['project_id']!=''){
        $where=($iswhere==0 ? ' where' : ' and');
        $sql .=$where ." a.project_id='".$data['project_id']."'";
        $iswhere=1;
      }
      $sql .=' order by a.created_date desc';
      // print_r($sql);die();
      $query = $this->db->query($sql);
      return $query;

    }

    function getprojectplatform($data=''){
      $sql2 = "select a.proplat_id,a.project_id,a.platform_id,b.name from project_platform a left join platform b on a.platform_id=b.platform_id ";
      $iswhere=0;
      if(isset($data['project_id']) && $data['project_id']!=''){
        $where=($iswhere==0 ? ' where' : ' and');
        $sql2 .=$where ." a.project_id='".$data['project_id']."'";
        $iswhere=1;
      }
      $query2 = $this->db->query($sql2);
      return $query2;
    }

    function getprojectMember($data=''){
      $sql2 = "select * from t_project_member ";
      $iswhere=0;
      if(isset($data['project_id']) && $data['project_id']!=''){
        $where=($iswhere==0 ? ' where' : ' and');
        $sql2 .=$where ." project_id='".$data['project_id']."'";
        $iswhere=1;
      }

      // print_r($sql2);die(0);
      $query2 = $this->db->query($sql2);
      return $query2;
    }
    function deleteprojectmember($data){
      $sql="delete from t_project_member where project_id=?";
      $query=$this->db->query($sql,array($data['project_id']));
      return $query;
    }
    function deletegruprole($data){
      $sql="delete from gruprole where user_id=? and role_id=?";
      $query=$this->db->query($sql,array($data['user_id'],$data['role_id']));
      return $query;
    }
    function get_projecttabel() {
      $userid = $this->session->userdata('user_id');
      $roleid = $this->session->userdata('roleid');
      $query = $this->getdataproject();
      $platform = array();
      $x = 1;
      $userid=$this->session->userdata('user_id');
      if ($query->num_rows() > 0) {
        $htmlplatform = '';

        foreach ($query->result_array() as $value) {
          if(in_array(2,$roleid)){
          if($value['userid']!=$userid){
            continue;
          }
        }
          $sql2 = "select a.proplat_id,a.project_id,a.platform_id,b.name from project_platform a left join platform b on a.platform_id=b.platform_id where a.project_id='" . $value['project_id'] . "'";
          $query2 = $this->db->query($sql2);
          $htmlplatform = '<ul>';
          foreach ($query2->result_array() as $row) {
            $htmlplatform .='<li>' . $row['name'] . '</li>';
            //                    array_push($platform,$row['name']);
          }
          $htmlplatform .='</ul>';
          $edit=$delete='';
          if (in_array(0,$roleid) || in_array(1,$roleid)) {
            $edit = " <button type='button' onclick=\"editproject('" . $value['project_id'] . "')\" class='btn btn-info btn-xs'><span class='glyphicon glyphicon-pencil' aria-hidden='true'></span></button>";
            $delete = " <button type='button' onclick=\"deleteproject('" . $value['project_id'] . "')\" class='btn btn-danger btn-xs'><span class='glyphicon glyphicon-remove' aria-hidden='true'></span></button>";
          } else {
            $delete = '';

          }
          $meeting="<a href='#' onclick=\"detailmeeting('".$value['project_id']."')\">Detail...</a>";
          $dokumentasi="<a href='#' onclick=\"detaildokumentasi('".$value['project_id']."')\">Detail...</a>";;
          $namedetail = '<a href="#" onclick="detailproject(' . $value['project_id'] . ')">' . $value['name'] . '</a>';
          $json['aaData'][] = array($x, $namedetail, $value['client'], $htmlplatform,$meeting,$dokumentasi,$edit." ". $delete);
          $x++;
        }
      } else {
        $json['aaData'] = array();
      }
      return $json;
    }


    function addproject($data) {
      $username = $this->session->userdata('username');
      $data['username'] = $username;
      date_default_timezone_set('Asia/Jakarta');
      $tanggal = date('Y-m-d H:i:s');
      $projectinsert = array(
        'name' => $data['nama'],
        'client' => $data['client'],
        'userid' => $data['pm_id'],
        'enddate'=>$data['enddateproject'],
        'created_date' => $tanggal,
        'created_by' => $username,
        'modified_date' => $tanggal,
        'modified_by' => $username,
      );
      $query = $this->db->insert('project', $projectinsert);
      $insert_id = $this->db->insert_id();


      if (isset($data['platform']) && sizeof($data['platform']) > 0) {
        $chekprojectplatform=$this->getprojectplatform(array('project_id'=>$insert_id));
        if($chekprojectplatform->num_rows()>0){
          $deleteprojectplatform=$this->deleteprojectplatform(array('project_id'=>$insert_id));
        }
        foreach ($data['platform'] as $value) {
          $prolatinsert = array(
            'project_id' => $insert_id,
            'platform_id' => $value,
            'created_date' => $tanggal,
            'created_by' => $username,
          );
          $query2 = $this->db->insert('project_platform', $prolatinsert);
        }
      }
      // print_r($data);die(0);
      $datarolepm=array('user_id'=>$data['pm_id'],
      'role_id'=>2,
      'username'=>$username);
      $chekgruprole=$this->getgruprole($datarolepm);
      if($chekgruprole->num_rows()>0){
        $deletegruprole=$this->deletegruprole($datarolepm);
      }
      $this->insertgruprole($datarolepm);

      $chekmemberproject=$this->getprojectMember(array('project_id'=>$insert_id));
      if($chekmemberproject->num_rows()>0){
        $deletememberproject=$this->deleteprojectmember(array('project_id'=>$insert_id));
      }
      $insertprojectmember=$this->insertProjectMemberTable($data, $insert_id, $username);


      if ($query) {
        $balikan = array(
          'status' => 1,
          'type' => 'project',
          'msg' => 'Project added'
        );
      } else if($insertprojectmember['status']==0){
        $balikan =array(
          'status'=>0,
          'msg'=>$insertprojectmember['msg']
        );
      }else{
        $balikan = array(
          'status' => 0,
          'msg' => 'Project failed to save'
        );
      }
      //return $readxlx;
      return $balikan;
    }
    function getgruprole($data){
      $bind=array();
      $sql2 = "select * from gruprole ";

      $iswhere=0;
      if(isset($data['user_id']) && $data['user_id']!=''){
        $where=($iswhere==0 ? ' where' : ' and');
        $sql2 .=$where ." user_id=?";
        $iswhere=1;
        array_push($bind,$data['user_id']);
      }
      if(isset($data['role_id']) && $data['role_id']!=''){
        $where=($iswhere==0 ? ' where' : ' and');
        $sql2 .=$where ." role_id=?";
        $iswhere=1;
        array_push($bind,$data['role_id']);
      }
      // print_r($sql2);die(0);
      $query2 = $this->db->query($sql2,$bind);
      return $query2;
    }
    function insertProjectMemberTable($data, $insert_id, $username) {
      date_default_timezone_set('Asia/Jakarta');
      $tanggal = date('Y-m-d H:i:s');
      // print_r($data['member']);die();
      //Insert into project_member
      if (isset($data['member']) ) {
        for ($m = 0; $m < sizeof($data['member']); $m++) {
          $peran = 2;
          if (isset($data['peran'][$m])) {
            $peran = $data['peran'][$m];
          }
          if($data['pm_id']==$data['member'][$m]){
            die();
            return array('status'=>0,'msg'=>'Project manager can not be project member in same project');
          }
          $project_member_data = array(
            'member_id' => $data['member'][$m],
            'project_id' => $insert_id,
            //'role_id' => $peran,
            'created_date'=>$tanggal,
            'created_by' => $username
          );
          $queryMember = $this->db->insert('t_project_member', $project_member_data);
          $datarolepm=array('user_id'=>$data['member'][$m],
          'role_id'=>4,
          'username'=>$username);

          $chekgruprole=$this->getgruprole($datarolepm);
          if($chekgruprole->num_rows()>0){
            $deletegruprole=$this->deletegruprole($datarolepm);
          }
          $this->insertgruprole($datarolepm);
        }
      }
      return array('status'=>1);
    }
    function getdatafeature($data){
      $roleid=$this->session->userdata('roleid');
      $sql = "select feature_id,proplat_id,name from feature ";
      $iswhere=0;
      if(isset($data['proplat_id']) && $data['proplat_id']!=''){
        $where=($iswhere==0 ? ' where' : ' and');
        $sql .=$where ." proplat_id='".$data['proplat_id']."'";
        $iswhere=1;
      }
      if(isset($data['feature_id']) && $data['feature_id']!=''){
        $where=($iswhere==0 ? ' where' : ' and');
        $sql .=$where ." feature_id='".$data['feature_id']."'";
        $iswhere=1;
      }

      $query = $this->db->query($sql);
      return $query;
    }
    function updatefeature($data){
      date_default_timezone_set('Asia/Jakarta');
      $tanggal = date('Y-m-d H:i:s');
      $data['username']=$username=$this->session->userdata('username');
      $featureupdate = array(
        'name' => $data['nama'],

        'modified_date' => $tanggal,
        'modified_by' => $data['username'],
      );
      $this->db->where('feature_id', $data['feature_id']);
      $query = $this->db->update('feature', $featureupdate);
      if($query){
        return  array(
          'status' => 1,
          'proplat_id' => $data['proplatid'],
          'type' => 'feature',
          'msg' => 'Feature updated'
        );
      }else{
        return array(
          'status' => 0,
          'msg' => 'Feature failed to save'
        );
      }
    }
    function get_featuretabel($data) {
      $roleid=$this->session->userdata('roleid');

      $query = $this->getdatafeature($data);
      $platform = array();
      $x = 1;
      if ($query->num_rows() > 0) {
        $htmlplatform = '';

        foreach ($query->result_array() as $value) {

          $edit = "<button type='button' onclick=\"editfeature('" . $value['feature_id'] . "')\" class='btn btn-info btn-xs'><span class='glyphicon glyphicon-pencil' aria-hidden='true'></span></button>";
          $delete = " <button type='button' onclick=\"deletefeature('" . $value['feature_id'] . "','".$value['proplat_id']."')\" class='btn btn-danger btn-xs'><span class='glyphicon glyphicon-remove' aria-hidden='true'></span></button>";
          if(in_array(0,$roleid)||in_array(2,$roleid)){
            $action=$edit." ".$delete;
          }else{
            $action='';
          }
          $namedetail = '<a href="#" onclick="detailfeature(' . $value['feature_id'] . ')">' . $value['name'] . '</a>';
          $json['aaData'][] = array($x, $namedetail, $action);
          $x++;
        }
      } else {
        $json['aaData'] = array();
      }
      return $json;
    }

    function get_detailproject($id) {
      $sql = 'select a.project_id,a.name,a.client from project a where project_id=?';
      $query = $this->db->query($sql, array($id));
      $platform = $hasil = array();
      $x = 1;
      if ($query->num_rows() > 0) {
        $htmlplatform = '';
        $value = $query->row_array();
        $sql2 = "select a.proplat_id,a.project_id,a.platform_id,b.name from project_platform a left join platform b on a.platform_id=b.platform_id where a.project_id='" . $value['project_id'] . "'";
        $query2 = $this->db->query($sql2);
        $html = '';
        foreach ($query2->result_array() as $row) {
          $html .='<option value="' . $row['proplat_id'] . '">' . $row['name'] . '</option>';
        }
        $value['platform'] = $html;

        $hasil = $value;
      }
      return $hasil;
    }

    function addfeature($data) {
      $username = $this->session->userdata('username');
      date_default_timezone_set('Asia/Jakarta');
      $tanggal = date('Y-m-d H:i:s');
      $projectinsert = array(
        'proplat_id' => $data['proplatid'],
        'name' => $data['nama'],
        'created_date' => $tanggal,
        'created_by' => $username,
        'modified_date' => $tanggal,
        'modified_by' => $username,
      );
      $query = $this->db->insert('feature', $projectinsert);
      $insert_id = $this->db->insert_id();


      if ($query) {
        $balikan = array(
          'status' => 1,
          'proplat_id' => $data['proplatid'],
          'type' => 'feature',
          'msg' => 'Feature added'
        );
      } else {
        $balikan = array(
          'status' => 0,
          'msg' => 'Feature failed to save'
        );
      }
      return $balikan;
    }

    function get_detailfeature($id) {
      $sql = 'select a.feature_id,a.name from feature a where feature_id=?';
      $query = $this->db->query($sql, array($id));
      $platform = $hasil = array();
      $x = 1;
      if ($query->num_rows() > 0) {
        $value = $query->row_array();
        $hasil = $value;
      }
      return $hasil;
    }

    function getdatagrup($data){
      $roleid=$this->session->userdata('roleid');
      $sql = "SELECT group_id,feature_id,name FROM `group` ";
      $iswhere=0;
      if(isset($data['group_id']) && $data['group_id']!=''){
        $where=($iswhere==0 ? ' where' : ' and');
        $sql .=$where ." group_id='".$data['group_id']."'";
        $iswhere=1;
      }
      if(isset($data['feature_id']) && $data['feature_id']!=''){
        $where=($iswhere==0 ? ' where' : ' and');
        $sql .=$where ." feature_id='".$data['feature_id']."'";
        $iswhere=1;
      }

      $query = $this->db->query($sql);
      return $query;
    }

    function get_grouptabel($data) {
      $roleid=$this->session->userdata('roleid');
      $sql = "SELECT group_id,feature_id,name FROM `group` where feature_id='" . $data['feature_id'] . "'";
      $query = $this->db->query($sql);
      $platform = array();
      $x = 1;
      if ($query->num_rows() > 0) {
        $htmlplatform = '';

        foreach ($query->result_array() as $value) {

          $edit =" <button type='button' onclick=\"editgroup('" . $value['group_id'] . "')\" class='btn btn-info btn-xs'><span class='glyphicon glyphicon-pencil' aria-hidden='true'></span></button>";
          $delete = " <button type='button' onclick=\"deletegroup('" . $value['group_id'] . "','".$value['feature_id']."')\" class='btn btn-danger btn-xs'><span class='glyphicon glyphicon-remove' aria-hidden='true'></span></button>";
          if(in_array(0,$roleid)||in_array(2,$roleid)){
            $action=$edit." ".$delete;
          }else{
            $action='';
          }
          $namedetail = '<a href="#" onclick="detailgroup(' . $value['group_id'] . ')">' . $value['name'] . '</a>';
          $json['aaData'][] = array($x, $namedetail,$action);
          $x++;
        }
      } else {
        $json['aaData'] = array();
      }
      return $json;
    }

    function updatestatusclosed($data){

      $updatedvalue = "0";
      //        echo "a";die(0);
      $checkcurrent = "select isclosed from item where item_id ='".$data['itemid']."'";
      $doquery = $this->db->query($checkcurrent)->row_array();
      //        echo "a";die(0);

      if (sizeof($doquery)>0){
        if(strcasecmp($doquery["isclosed"], "0") == 0)
        {
          $updatedvalue = "1";
        }

        $updatequery = "update item set isclosed = '".$updatedvalue."' where item_id ='".$data['itemid']."'";
        //            echo $updatequery;die(0);
        $doupdate = $this->db->query($updatequery);

        if($doupdate){
          return "Update Sukses";
        } else{
          return "Update Gagal";

        }
      }
    }

    function addgroup($data) {
      $username = $this->session->userdata('username');
      date_default_timezone_set('Asia/Jakarta');
      $tanggal = date('Y-m-d H:i:s');
      $projectinsert = array(
        'feature_id' => $data['featureid'],
        'name' => $data['nama'],
        'created_date' => $tanggal,
        'create_by' => $username,
        'modified_date' => $tanggal,
        'modified_by' => $username,
      );
      $query = $this->db->insert('group', $projectinsert);
      $insert_id = $this->db->insert_id();


      if ($query) {
        $balikan = array(
          'status' => 1,
          'id' => $data['featureid'],
          'type' => 'group',
          'msg' => 'Group added'
        );
      } else {
        $balikan = array(
          'status' => 0,
          'msg' => 'Group failed to save'
        );
      }
      return $balikan;
    }
    function updategroup($data){
      date_default_timezone_set('Asia/Jakarta');
      $tanggal = date('Y-m-d H:i:s');
      $data['username']=$username=$this->session->userdata('username');
      $update = array(
        'name' => $data['nama'],

        'modified_date' => $tanggal,
        'modified_by' => $data['username'],
      );
      $this->db->where('group_id', $data['group_id']);
      $query = $this->db->update('group', $update);
      if ($query) {
        $balikan = array(
          'status' => 1,
          'id' => $data['featureid'],
          'type' => 'group',
          'msg' => 'Group updated'
        );
      } else {
        $balikan = array(
          'status' => 0,
          'msg' => 'Group failed to update'
        );
      }
      return $balikan;
    }
    function getdataitem($data){
      $roleid=$this->session->userdata('roleid');
      $sql = "SELECT i.item_id,i.group_id,i.name,i.hour,i.start_date,i.end_date,u.nama as doingby,i.userid FROM `item` i left join tuser u on i.userid=u.userid ";
      $iswhere=0;
      if(isset($data['group_id']) && $data['group_id']!=''){
        $where=($iswhere==0 ? ' where' : ' and');
        $sql .=$where ." i.group_id='".$data['group_id']."'";
        $iswhere=1;
      }
      if(isset($data['item_id']) && $data['item_id']!=''){
        $where=($iswhere==0 ? ' where' : ' and');
        $sql .=$where ." i.item_id='".$data['item_id']."'";
        $iswhere=1;
      }

      $query = $this->db->query($sql);
      return $query;
    }

    function get_Item($data) {
      $roleid=$this->session->userdata('roleid');
      $sql = "SELECT i.item_id,i.group_id,i.name,i.hour,i.start_date,i.end_date,u.nama as doingby,i.userid FROM `item` i left join tuser u on i.userid=u.userid where group_id='" . $data['group_id'] . "'";
      $query = $this->db->query($sql);
      $platform = array();
      $x = 1;
      if ($query->num_rows() > 0) {
        $query = $query->result_array();
        foreach ($query as $value) {
          $edit = " <button type='button' onclick=\"edititem('" . $value['item_id'] . "')\" class='btn btn-info btn-xs'><span class='glyphicon glyphicon-pencil' aria-hidden='true'></span></button>";
          $delete = " <button type='button' onclick=\"deleteitem('" . $value['item_id'] . "','".$value['group_id']."')\" class='btn btn-danger btn-xs'><span class='glyphicon glyphicon-remove' aria-hidden='true'></span></button>";
          if(in_array(0,$roleid)||in_array(2,$roleid)){
            $action=$edit." ".$delete;
          }else{
            $action='';
          }
          $json['aaData'][] = array($x,
          $value['name'],
          $value['hour'],
          $value['doingby'],
          date('m/d/Y',strtotime($value['start_date'])),
          date('m/d/Y',strtotime($value['end_date'])),
          $action
        );
        $x++;
      }
    } else {
      $json['aaData'] = array();
    }
    return $json;
  }

  //Add members to assignment table
  //Add member to item
  function add_assignment($data) {
    $members = '';
    $datenow = date('Y-m-d H:i:s');
    // echo json_encode($data);return;
    // return array(
    //     'status' => 0,
    //     'msg' => json_encode($data)
    // );
    if (isset($data['member']) && isset($data['addmember_itemid'])) {
      foreach ($data['member'] as $m_id) {
        $assignment_table_data = array(
          'user_id' => $m_id,
          'item_id' => $data['addmember_itemid'],
          'startdate' => '',
          'efd' => '',
          'efh' => '',
          'efm' => '',
          'created_by' => '',
          'created_date' => $datenow,
          'modified_by' => '',
          'modified_date' => $datenow
        );
        $insertresult = $this->db->insert('assignment', $assignment_table_data);
        if ($insertresult) {
          return array(
            'status' => 1,
            'type' => 'item',
            'id' => $data['addmember_group_id'],
            'msg' => 'Success'
          );
        } else {
          return array(
            'status' => 0,
            'type' => 'item',
            'id' => $data['addmember_group_id'],
            'msg' => ($this->db->_error_number() == 1062 ? 'Cant put the same personnel again' : $this->db->_error_message())
          );
        }
      }
    }

    return array(
      'status' => 0,
      'msg' => 'Cant find member or item id'
    );
  }

  function remove_assignment($data) {
    //data is user_id, item_id
    $query = "delete from assignment where 	user_id=? and item_id=?";
    $query_result = $this->db->query($query, array($data['user_id'], $data['item_id']));
    if ($query_result) {
      return 'Remove personel success';
    } else {
      return 'Failed to remove personnel';
    }
  }

  //Get list of user ids in assignment
  function get_assignment($itemid) {
    $user_ids = '';
    //previous code
    //        $query = "select * from assignment where item_id=?";
    $query = "select * from t_task where item_id=?";
    $assignment_result = $this->db->query($query, array($itemid));
    if ($assignment_result->num_rows() > 0) {
      $assignment_result = $assignment_result->result_array();
      foreach ($assignment_result as $r) {
        if ($user_ids != '') {
          $user_ids .= ',';
        }
        $user_ids .= "'" . $r['user_id'] . "'";
      }
    }
    return $user_ids;
  }

  //Get member name using a string list of user_id
  function get_member_name($user_ids) {
    if ($user_ids == "") {
      return array();
    }
    $query = "select * from tperson where user_id in (" . $user_ids . ") group by user_id";
    return $this->db->query($query)->result_array();
  }

  // Get all involved member in a project
  function get_project_member($projectid) {
    $query = "select * from t_project_member where project_id=?";
    $members = $this->db->query($query, array($projectid));
    if ($members->num_rows() > 0) {
      $memberarray = '';
      foreach ($members->result_array() as $member) {
        if ($memberarray != '') {
          $memberarray .= ',';
        }
        $memberarray .= "'" . $member['member_id'] . "'";
      }
      $query_member_name = "select * from tperson where person_id in(" . $memberarray . ")";
      $queryperson = $this->db->query($query_member_name);
      if ($queryperson->num_rows() > 0) {
        return $queryperson->result_array();
      }
    }
  }

  // Get project id from item id
  function get_projectid($itemid) {
    $query = "select `project_platform`.project_id from `item`
    left join `group` on `item`.group_id=`group`.group_id
    left join `feature` on `feature`.feature_id=`group`.feature_id
    left join `project_platform` on `feature`.proplat_id=`project_platform`.proplat_id
    where `item`.item_id=?";
    $query_result = $this->db->query($query, array($itemid));
    if ($query_result->num_rows() > 0) {
      $query_array = $query_result->row_array();
      return $query_array['project_id'];
    } else {
      return 0;
    }
  }

  // Get project id from group id
  function get_projectid_bygroupid($groupid) {
    $query = "select `project_platform`.project_id
    from `group`
    left join `feature` on `feature`.feature_id=`group`.feature_id
    left join `project_platform` on `feature`.proplat_id=`project_platform`.proplat_id
    where `group`.group_id=?";
    $query_result = $this->db->query($query, array($groupid));
    if ($query_result->num_rows() > 0) {
      $query_array = $query_result->row_array();
      return $query_array['project_id'];
    } else {
      return 0;
    }
  }

  function addItem($data) {
    $daterange=explode('-',$data['daterange']);
    $username = $this->session->userdata('username');
    $tanggal = date('Y-m-d H:i:s');
    $projectinsert = array(
      'group_id' => $data['group_id'],
      'name' => $data['nama'],
      'hour' => $data['hour'],
      'userid'=>$data['doingby'],
      'start_date'=>date('Y-m-d',strtotime(trim($daterange[0]))),
      'end_date'=>date('Y-m-d',strtotime(trim($daterange[1]))),
      'created_date' => $tanggal,
      'created_by' => $username,
      'modified_date' => $tanggal,
      'modified_by' => $username,
    );
    $query = $this->db->insert('item', $projectinsert);
    $insert_id = $this->db->insert_id();

    if ($query) {
      $balikan = array(
        'status' => 1,
        'id' => $data['group_id'],
        'type' => 'item',
        'msg' => 'Item added'
      );
    } else {
      $balikan = array(
        'status' => 0,
        'msg' => 'Item failed to save'
      );
    }
    return $balikan;
  }
  function updateitem($data){
      $daterange=explode('-',$data['daterange']);
    date_default_timezone_set('Asia/Jakarta');
    $tanggal = date('Y-m-d H:i:s');
    $data['username']=$username=$this->session->userdata('username');
    $update = array(
      'group_id' => $data['group_id'],
      'name' => $data['nama'],
      'hour' => $data['hour'],
      'userid'=>$data['doingby'],
      'start_date'=>date('Y-m-d',strtotime(trim($daterange[0]))),
      'end_date'=>date('Y-m-d',strtotime(trim($daterange[1]))),

      'modified_date' => $tanggal,
      'modified_by' => $data['username'],
    );
    $this->db->where('item_id', $data['item_id']);
    $query = $this->db->update('item', $update);
    if ($query) {
      $balikan = array(
        'status' => 1,
        'id' => $data['group_id'],
        'type' => 'item',
        'msg' => 'Item updated'
      );
    } else {
      $balikan = array(
        'status' => 0,
        'msg' => 'Item failed to update'
      );
    }
    return $balikan;
  }
  function addtabletask($data) {
    $username = $this->session->userdata('username');
    $tanggal = date('Y-m-d H:i:s');
    $failuretoinsert = array();
    if (isset($data['member']) && isset($data['addmember_itemid'])) {
      foreach ($data['member'] as $m_id) {
        $assignment_table_data = array(
          'user_id' => $m_id,
          'item_id' => $data['addmember_itemid'],
          'hour_spent' => '0',
          'is_done' => '0',
          'created_by' => $username,
          'created_date' => $tanggal,
          'modified_by' => '',
          'modified_date' => $tanggal
        );
        $query = "select task_id from t_task where user_id = '" . $m_id . "' and item_id = '" . $data['addmember_itemid'] . "'";
        $sqlcheck = $this->db->query($query)->row_array();
        //                echo sizeof($sqlcheck);die(0);
        if (sizeof($sqlcheck) == 0) {
          $insertresult = $this->db->insert('t_task', $assignment_table_data);
        } else {
          $query = "select salutation,firstname,lastname from tperson where user_id ='" . $m_id . "'";
          $query_result = $this->db->query($query)->row_array();
          array_push($failuretoinsert, $query_result['salutation'] . " " . $query_result['firstname'] . " " . $query_result['lastname']);
        }
      }
      if (sizeof($failuretoinsert) > 0) {
        $failurelist = "";
        foreach ($failuretoinsert as $value) {
          $failurelist = $failurelist . $value . " ";
        }
        $balikan = array(
          'status' => 0,
          'type' => 'item',
          'id' => $data['addmember_group_id'],
          'msg' => $failurelist . " already Registered For This Item"
        );
      } else {
        $balikan = array(
          'status' => 1,
          'type' => 'item',
          'id' => $data['addmember_group_id'],
          'msg' => 'Personel Berhasil Didaftarkan'
        );
      }
    }

    return $balikan;
  }

  function get_detailgroup($id) {
    $sql = 'select a.group_id,a.name from `group` a where group_id=?';
    $query = $this->db->query($sql, array($id));
    $platform = $hasil = array();
    $x = 1;
    if ($query->num_rows() > 0) {
      $value = $query->row_array();
      $hasil = $value;
    }
    return $hasil;
  }

  function chekdataforlink($data) {
    if ($data['type'] == 'item') {
      $sql = 'select a.group_id,a.name,a.feature_id,b.proplat_id from  `group` a left join feature b on a.feature_id=b.feature_id where a.group_id="' . $data['id'] . '"';
    } else if ($data['type'] == 'group') {
      $sql = 'select feature_id,proplat_id from feature where feature_id="' . $data['id'] . '"';
    } else {
      $sql = 'select feature_id,proplat_id from feature where proplat_id="' . $data['id'] . '"';
    }

    $query = $this->db->query($sql);
    $row = $query->row_array();
    return $row;
  }

  function harddeleteproject($id) {
    $sql = "select i.item_id,i.group_id,f.feature_id,i.name,i.hour,g.name as 'group' , f.name as 'feature' from item i  join `group` g on i.group_id=g.group_id  join feature f on g.feature_id=f.feature_id  join project_platform pp on f.proplat_id=pp.proplat_id where pp.project_id=?";
    $query = $this->db->query($sql, array($id));
    if ($query->num_rows() > 0) {
      foreach ($query->result_array() as $row) {
        $deleteitem = $this->db->query('delete from item where item_id="' . $row['item_id'] . '"');
        $deletegroup = $this->db->query('delete from `group` where group_id="' . $row['group_id'] . '"');
        $deletefitur = $this->db->query('delete from feature where feature_id="' . $row['feature_id'] . '"');

      }
    }
    // No need to unlink file no more
    // $qry = $this->db->query("select * from project where project_id=?", array($id));
    // if ($qry->num_rows() > 0) {
    // $r = $qry->row_array();
    // unlink($r['file_path']);
    // }
    $deletemapping = $this->db->query('delete from project_platform where project_id="' . $id . '"');
    $deleteproject = $this->db->query('delete from project where project_id="' . $id . '"');
    if ($deleteproject) {
      return array('status' => 1, 'msg' => 'Project has been deleted');
    } else {
      return array('status' => 0, 'msg' => 'Delete Project Failed');
    }
  }
  function harddeletefeature($idfeature){
    $sql = "select i.item_id,i.group_id,f.feature_id,i.name,i.hour,g.name as 'group' , f.name as 'feature' from item i  join `group` g on i.group_id=g.group_id  join feature f on g.feature_id=f.feature_id  join project_platform pp on f.proplat_id=pp.proplat_id where f.feature_id=?";
    $query = $this->db->query($sql, array($idfeature));
    if ($query->num_rows() > 0) {
      foreach ($query->result_array() as $row) {
        $deleteitem = $this->db->query('delete from item where item_id="' . $row['item_id'] . '"');
        $deletegroup = $this->db->query('delete from `group` where group_id="' . $row['group_id'] . '"');


      }
    }
    $deletefitur = $this->db->query('delete from feature where feature_id="' . $idfeature . '"');

    if ($deletefitur) {
      return array('status' => 1, 'msg' => 'Feature has been deleted');
    } else {
      return array('status' => 0, 'msg' => 'Delete Feature Failed');
    }
  }
  function harddeletegroup($idgroup){
    $sql = "select i.item_id,i.group_id,f.feature_id,i.name,i.hour,g.name as 'group' , f.name as 'feature' from item i  join `group` g on i.group_id=g.group_id  join feature f on g.feature_id=f.feature_id  join project_platform pp on f.proplat_id=pp.proplat_id where i.group_id=?";
    $query = $this->db->query($sql, array($idgroup));
    if ($query->num_rows() > 0) {
      foreach ($query->result_array() as $row) {
        $deleteitem = $this->db->query('delete from item where item_id="' . $row['item_id'] . '"');



      }
    }

    $deletegroup = $this->db->query('delete from `group` where group_id="' . $idgroup . '"');

    if ($deletegroup) {
      return array('status' => 1, 'msg' => 'Group has been deleted');
    } else {
      return array('status' => 0, 'msg' => 'Delete Group Failed');
    }
  }
  function harddeleteitem($iditem){
    $deleteitem = $this->db->query('delete from item where item_id="' . $iditem. '"');
    if ($deleteitem) {
      return array('status' => 1, 'msg' => 'Item has been deleted');
    } else {
      return array('status' => 0, 'msg' => 'Delete Item Failed');
    }
  }

  function readexcellmaindays($data) {
    $cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_memcache;
    $cacheSettings = array('memcacheServer' => 'localhost',
    'memcachePort' => 11211,
    'cacheTime' => 600,
    'memoryCacheSize' => '32MB'
  );
  PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);

  $fileType = PHPExcel_IOFactory::identify($data['path']);
  if ($fileType == 'CSV') {
    $uploadid = $this->session->userdata('uploadid');
    if ($uploadid != null && trim($uploadid) != '') {
      $cekdel = $this->checkdeletefile($uploadid, true);
      $this->deletefile($uploadid, $cekdel);
      $this->session->unset_userdata('uploadid');
    }
    echo "<script>alert('Unrecognized file format " .
    "\\nMake sure Your file is a proper Excel file\\n Resave the file if possible\\nYour File type is: $mimeType" .
    "'); </script>";
    die();
  }
  $objReader = PHPExcel_IOFactory::createReader($fileType);
  $sheetNames = $objReader->listWorksheetNames($data['path']);

  $ExcelObj = $objReader->load($data['path']);
  $indexnumber = 0;
  $sheet['web'] = "";
  $sheet['ws'] = "";
  $sheet['android'] = "";
  $sheet['ios'] = "";
  $sheet['inisialisasi'] = "";
  $dataExcelBySheet = "";

  foreach ($sheetNames as $sheetname) {
    if ((trim(strtolower($sheetname)) == "web")) {
      $sheet['web'] = $ExcelObj->getSheet($indexnumber)->toArray(null, true, true, true);
    } else if ((trim(strtolower($sheetname)) == "ws")) {
      $sheet['ws'] = $ExcelObj->getSheet($indexnumber)->toArray(null, true, true, true);
    } else if ((trim(strtolower($sheetname)) == "android")) {
      $sheet['android'] = $ExcelObj->getSheet($indexnumber)->toArray(null, true, true, true);
    } else if ((trim(strtolower($sheetname)) == "ios")) {
      $sheet['ios'] = $ExcelObj->getSheet($indexnumber)->toArray(null, true, true, true);
    } else if ((trim(strtolower($sheetname)) == "inisialisasi")) {
      $sheet['inisialisasi'] = $ExcelObj->getSheet($indexnumber)->toArray(null, true, true, true);
    }
    $indexnumber++;
  }

  if ($sheet['web'] != '') {
    $condata = array(
      'project_id' => $data['project_id'],
      'platform_id' => 1,
      'username' => $data['username']
    );
    $chekproject = $this->chekprojectplatform($condata);
    if (sizeof($chekproject) <= 0) {
      $chekproject['proplat_id'] = $this->addprojectplatform($condata);
    }
    $insert = $this->inserttemptimesheet($condata, $chekproject['proplat_id'], $sheet['web']);
  }
  if ($sheet['ws'] != '') {
    $condata = array(
      'project_id' => $data['project_id'],
      'platform_id' => 4,
      'username' => $data['username']
    );
    $chekproject = $this->chekprojectplatform($condata);
    if (sizeof($chekproject) <= 0) {
      $chekproject['proplat_id'] = $this->addprojectplatform($condata);
    }
    $insert = $this->inserttemptimesheet($condata, $chekproject['proplat_id'], $sheet['ws']);
  }
  if ($sheet['android'] != '') {
    $condata = array(
      'project_id' => $data['project_id'],
      'platform_id' => 3,
      'username' => $data['username']
    );
    $chekproject = $this->chekprojectplatform($condata);
    if (sizeof($chekproject) <= 0) {
      $chekproject['proplat_id'] = $this->addprojectplatform($condata);
    }
    $insert = $this->inserttemptimesheet($condata, $chekproject['proplat_id'], $sheet['android']);
  }

  if ($sheet['ios'] != '') {
    $condata = array(
      'project_id' => $data['project_id'],
      'platform_id' => 2,
      'username' => $data['username']
    );
    $chekproject = $this->chekprojectplatform($condata);
    if (sizeof($chekproject) <= 0) {
      $chekproject['proplat_id'] = $this->addprojectplatform($condata);
    }
    $insert = $this->inserttemptimesheet($condata, $chekproject['proplat_id'], $sheet['ios']);
  }
  if ($sheet['inisialisasi'] != '') {
    $condata = array(
      'project_id' => $data['project_id'],
      'platform_id' => 5,
      'username' => $data['username']
    );
    $chekproject = $this->chekprojectplatform($condata);
    if (sizeof($chekproject) <= 0) {
      $chekproject['proplat_id'] = $this->addprojectplatform($condata);
    }
    $insert = $this->inserttemptimesheet($condata, $chekproject['proplat_id'], $sheet['inisialisasi']);
  }
}

function chekprojectplatform($data) {
  $hasil = array();
  $sql = "select * from project_platform where project_id='" . $data['project_id'] . "' and platform_id='" . $data['platform_id'] . "' ";
  $qry = $this->db->query($sql);
  if ($qry->num_rows() > 0) {
    $hasil = $qry->row_array();
  }
  return $hasil;
}

//Insert to table project platform
function addprojectplatform($data) {
  $prolatinsert = array(
    'project_id' => $data['project_id'],
    'platform_id' => $data['platform_id'],
    'created_date' => date('Y-m-d H:i:s'),
    'created_by' => $data['username']
  );
  $query2 = $this->db->insert('project_platform', $prolatinsert);
  if ($query2) {
    //proplat_id
    return $this->db->insert_id();
  }
}

//No longer temp table, just the function name
function inserttemptimesheet($data, $proplat_id, $sheet) {
  $username = $this->session->userdata('username');
  date_default_timezone_set('Asia/Jakarta');
  $tanggal = date('Y-m-d H:i:s');
  $projectinsert = array();
  foreach ($sheet as $sheetdata) {
    $sheetdata['A'] = (strtolower($sheetdata['A']) != 'Task Assigment' || $sheetdata['A'] != null ? $sheetdata['A'] : '');
    $sheetdata['B'] = (strtolower($sheetdata['B']) != 'Task Assigment' || $sheetdata['B'] != null ? $sheetdata['B'] : '');
    $sheetdata['C'] = (strtolower($sheetdata['C']) != 'Task Assigment' || $sheetdata['C'] != null ? $sheetdata['C'] : '');

    $sheetdata['D'] = ($sheetdata['D'] != 'hour' || $sheetdata['D'] != null ? $sheetdata['D'] : 0);
    if ($sheetdata['D'] != null && $sheetdata['C'] != null && $sheetdata['B'] != null && ($sheetdata['A'] != null || strtolower($sheetdata['C']) != 'Task Assigment')) {
      $projectinsert[] = array(
        'proplat_id' => $proplat_id,
        'feature' => $sheetdata['A'],
        'group' => $sheetdata['B'],
        'item' => $sheetdata['C'],
        'hour' => $sheetdata['D'],
        'created_date' => $tanggal
      );
      //$query = $this->db->insert('temp_uploadtimesheet', $projectinsert);
    }
  }
  $this->db->insert_batch('temp_uploadtimesheet', $projectinsert);
  $this->insertfeatureExcel($data, $projectinsert);
  $this->insertgroupExcell($data, $projectinsert);
}

/* function insertfeatureExcel($data) {
$username = $this->session->userdata('username');
date_default_timezone_set('Asia/Jakarta');
$tanggal = date('Y-m-d H:i:s');
$sql = "insert into feature(proplat_id,name,created_date,created_by,modified_date,modified_by) SELECT tu.proplat_id,tu.feature,'" . $tanggal . "' as 'created_date','" . $username . "' as 'created_by','" . $tanggal . "' as 'modified_date','" . $username . "' as 'modified_by' FROM `temp_uploadtimesheet` tu left join project_platform pp on tu.proplat_id=pp.proplat_id where pp.project_id='" . $data['project_id'] . "' and pp.platform_id='" . $data['platform_id'] . "' group by tu.proplat_id,tu.feature order by 1 ";
$qryfeature = $this->db->query($sql);
} */

function insertfeatureExcel($data, $projectdata) {
  date_default_timezone_set('Asia/Jakarta');
  $tanggal = date('Y-m-d H:i:s');
  $sql = "insert into feature(proplat_id,name,created_date,created_by,modified_date,modified_by) values";
  if (sizeof($projectdata) > 0) {
    foreach ($projectdata as $key => $p) {
      if ($key > 0) {
        $sql .= ",";
      }
      $sql .= "('" . $p['proplat_id'] . "', '" . $p['feature'] . "', '$tanggal', '" . $data['username'] . "', '$tanggal', '" . $data['username'] . "')";
    }
  }
  $qryfeature = $this->db->query($sql);
}

/* function insertgroupExcell($data){
$username = $this->session->userdata('username');
date_default_timezone_set('Asia/Jakarta');
$tanggal = date('Y-m-d H:i:s');
$sql = "insert into group(proplat_id,name,created_date,created_by,modified_date,modified_by) SELECT tu.proplat_id,tu.feature,'" . $tanggal . "' as 'created_date','" . $username . "' as 'created_by','" . $tanggal . "' as 'modified_date','" . $username . "' as 'modified_by' FROM `temp_uploadtimesheet` tu left join project_platform pp on tu.proplat_id=pp.proplat_id where pp.project_id='" . $data['project_id'] . "' and pp.platform_id='" . $data['platform_id'] . "' group by tu.proplat_id,tu.feature order by 1 ";
$qryfeature = $this->db->query($sql);
} */

function insertgroupExcell($data, $projectdata) {
  $projectid = $data['project_id'];
  date_default_timezone_set('Asia/Jakarta');
  $username = $data['username'];
  $tanggal = date('Y-m-d H:i:s');

  //$query_select = "select * from project_platform where project_id = '" . $projectid . "'";
  /* if (sizeof($projectdata) > 0) {
  foreach ($projectdata as $p) [

}
} */

//$sql = "insert into group(proplat_id,feature_id,name,created_date,create_by,modified_date,modified_by) values('" . $projectdata['proplat_id'] . "')";
//$qryfeature = $this->db->query($sql);
}
function insertgruprole($data){
  date_default_timezone_set('Asia/Jakarta');
  $tanggal = date('Y-m-d H:i:s');
  $projectinsert = array(
    'user_id' => $data['user_id'],
    'role_id' => $data['role_id'],

    'created_date' => $tanggal,
    'created_by' => $data['username'],
    'modified_date' => $tanggal,
    'modified_by' => $data['username'],
  );
  $query = $this->db->insert('gruprole', $projectinsert);
  $insert_id = $this->db->insert_id();
  return $insert_id;
}
function updateproject($data){
  // print_r($data);die(0);
  date_default_timezone_set('Asia/Jakarta');
  $tanggal = date('Y-m-d H:i:s');
  $data['username']=$username=$this->session->userdata('username');
  $projectupdate = array(
    'name' => $data['nama'],
    'client' => $data['client'],
    'userid' => $data['pm_id'],
    'enddate'=>$data['enddateproject'],
    'modified_date' => $tanggal,
    'modified_by' => $data['username'],
  );
  $this->db->where('project_id', $data['project_id']);
  $query = $this->db->update('project', $projectupdate);


  if (isset($data['platform']) && sizeof($data['platform']) > 0) {
    $chekprojectplatform=$this->getprojectplatform(array('project_id'=>$data['project_id']));
    if($chekprojectplatform->num_rows()>0){
      $deleteprojectplatform=$this->deleteprojectplatform(array('project_id'=>$data['project_id']));
    }
    foreach ($data['platform'] as $value) {
      $prolatinsert = array(
        'project_id' => $data['project_id'],
        'platform_id' => $value,
        'created_date' => $tanggal,
        'created_by' => $username,
      );
      $query2 = $this->db->insert('project_platform', $prolatinsert);
    }
  }
  // print_r($data);die(0);
  $datarolepm=array('user_id'=>$data['pm_id'],
  'role_id'=>2,
  'username'=>$username);
  $chekgruprole=$this->getgruprole($datarolepm);
  if($chekgruprole->num_rows()>0){
    $deletegruprole=$this->deletegruprole($datarolepm);
  }
  $this->insertgruprole($datarolepm);


  $chekmemberproject=$this->getprojectMember(array('project_id'=> $data['project_id']));
  if($chekmemberproject->num_rows()>0){
    $deletememberproject=$this->deleteprojectmember(array('project_id'=> $data['project_id']));
  }
$insertprojectmember=  $this->insertProjectMemberTable($data, $data['project_id'], $username);
// print_r($insertprojectmember);die(0);
  if($query){
    $balikan=array('status'=>1,'type' => 'project','msg'=>'Project data has been update');
  }else if($insertprojectmember['status']==0){
    $balikan =array(
      'status'=>0,
      'msg'=>$insertprojectmember['msg']
    );
  }else{
    $balikan=array('status'=>0,'msg'=>'Project data failed upda');
  }
  return  $balikan;
}

function deleteprojectplatform($data){
  $sql="delete from project_platform where project_id=".$this->db->escape($data['project_id']);
  $query=$this->db->query($sql);
  return $query;
}
function deletememberproject($data){
  $sql="delete from t_project_member where project_id='".$this->db->escape($data['project_id'])."'";
  $query=$this->db->query($sql);
  return $query;
}
function getmemberbyproplat($idproplat){
  $sql="select m.member_id,u.nama,u.gender from t_project_member m
  left join tuser u on u.userid=m.member_id

  where m.project_id='".$idproplat."'";
  // print_r($sql);die();
  $query=$this->db->query($sql);
  return $query;
}
}
