<?php

if (!defined('BASEPATH'))
exit('No direct script access allowed');

class Projects extends CI_Controller {

  public function __construct() {
    parent::__construct();
    $this->load->model('project_model');
    $this->load->model('timesheet_model');
    $this->load->model('db_load');
  }

  public function index() {
    $user_id = $this->session->userdata('user_id');
    $user = $this->user_model->get_user($user_id);

    if ($user_id > 0) {
      $content['user_id'] = $this->session->userdata('user_id');
      $content['chekpm'] = $this->db_load->chekpm($content['user_id']);
      $content['user'] = $this->user_model->get_user($content['user_id']);
      //        print_r($content);die(0);
      $this->load->view('header', $content);
      //            $this->load->view('header');
      //            $projects = $this->project_model->get_projects();
      //
      //            foreach ($projects as $project) {
      //                $feature_count = $this->project_model->feature_count($project->project_id);
      //                $project->feature_count = $feature_count;
      //            }
      $projects = '';
      $data = array(
        'projects' => $projects,
        'is_admin' => true);

        $this->load->view('projects', $data);
      } else {
        show_404();
      }
    }

    public function index2() {
      $user_id = $this->session->userdata('user_id');
      $user = $this->user_model->get_user($user_id);

      if ($user_id > 0) {
        $this->load->view('header');

        $projects = $this->project_model->get_projects();

        foreach ($projects as $project) {
          $feature_count = $this->project_model->feature_count($project->project_id);
          $project->feature_count = $feature_count;
        }

        $data = array(
          'projects' => $projects,
          'is_admin' => true);

          $this->load->view('projects', $data);
        } else {
          show_404();
        }
      }

      public function project($project_id) {
        $user_id = $this->session->userdata('user_id');
        $user = $this->user_model->get_user($user_id);

        if ($user_id > 0 && $user->is_admin == 1) {
          $this->load->view('header');

          $project = $this->project_model->get($project_id);

          $data = array(
            'project' => $project,
            'project_name' => $project->name,
            'project_alias' => $project->alias,
            'features' => $this->get_feature_grid($project_id),
            'tasks' => $this->project_model->get_tasks($project_id, true));

            $this->load->view('project_details', $data);
          } else {
            show_404();
          }
        }

        private function get_feature_grid($project_id) {
          $features_parent = $this->project_model->get_features($project_id);

          $result = '<tr>'
          . '<th>No.</th>'
          . '<th colspan="3">Feature</th>'
          . '<th>Estimated</th>'
          . '<th>Actual</th>'
          . '</tr>';

          $index = 0;
          foreach ($features_parent as $feature_parent) {

            $features_group = $this->project_model->get_features($project_id, $feature_parent->feature_id);

            if (sizeof($features_group) > 0) {

              foreach ($features_group as $feature_group) {

                $features_function = $this->project_model->get_features($project_id, $feature_group->feature_id);

                if (sizeof($features_function) > 0) {

                  foreach ($features_function as $feature_function) {

                    $index++;
                    $result .= '<tr>'
                    . '<td  style="text-align: center;">' . $index . '</td>'
                    . '<td>' . $feature_parent->name . '</td>'
                    . '<td>' . $feature_group->name . '</td>'
                    . '<td>' . $feature_function->name . '</td>'
                    . $this->handle_feature($feature_function)
                    . '</tr>';
                    // Add onClick
                  }
                } else {

                  $index++;
                  $result .= '<tr>'
                  . '<td  style="text-align: center;">' . $index . '</td>'
                  . '<td>' . $feature_parent->name . '</td>'
                  . '<td colspan="2">' . $feature_group->name . '</td>'
                  . $this->handle_feature($feature_group)
                  . '</tr>';
                }
              }
            } else {
              $index++;
              $result .= '<tr>'
              . '<td  style="text-align: center;">' . $index . '</td>'
              . '<td colspan="3">' . $feature_parent->name . '</td>'
              . $this->handle_feature($feature_parent)
              . '</tr>';
            }
          }

          return $result;
        }



        public function get_platforms($project_id) {

        }

        function projecttabel() {
          $getproject = $this->project_model->get_projecttabel();
          echo json_encode($getproject);
        }

        function saveproject() {
          $data = array();
          foreach ($_POST as $key => $value) {
            $data[$key] = $value;
          }

          // print_r($data);die(0);
          // echo json_encode($data);
          // echo json_encode(
          //     array(
          //         'status' => 0,
          //         'type' => 'project',
          //         'msg' => json_encode($data)
          //     )
          // );
          // return;
          if($data['nama']==''){
            $hasil=array('status'=>0,'msg'=>'Project Must be Required');
          }else if($data['client']==''){
            $hasil=array('status'=>0,'msg'=>'Client Must be Required');
          }else{
            date_default_timezone_set('Asia/Jakarta');
            $data['enddateproject']=strtotime($data['enddateproject']);
            $data['enddateproject']=date('Y-m-d',$data['enddateproject']);
            if ($data['project_id'] == '') {
              if (!empty($_FILES['filename']['name'])) {
                $dataupload = array(
                  'path' => 'assets/uploadfile/',
                  'nama' => $data['nama']
                );
                $uploadfile = $this->db_load->uploadfile($dataupload);

                if ($uploadfile['status'] == 0) {
                  echo json_encode($uploadfile);
                  die(0);
                }
                $data['path'] = $uploadfile['path'];
              } else {
                $data['path'] = '';
              }

              /* echo json_encode(array(
              'status' => 0,
              'msg' => json_encode($data)
            ));
            die(); */
            $hasil = $this->project_model->addproject($data);
          } else {
            $hasil = $this->project_model->updateproject($data);
          }

        }

        echo json_encode($hasil);
      }

      function detailproject($id) {
        $getdetailproject = $this->project_model->get_detailproject($id);
        echo json_encode($getdetailproject);
      }

      function tabelfeature() {
        $data = array();
        foreach ($_GET as $key => $value) {
          $data[$key] = $value;
        }
        $getfeature = $this->project_model->get_featuretabel($data);
        echo json_encode($getfeature);
      }

      function savefeature() {
        $data = array();
        foreach ($_POST as $key => $value) {
          $data[$key] = $value;
        }

        if ($data['feature_id'] == '') {
          $saveproject = $this->project_model->addfeature($data);
          $hasil = $saveproject;
        } else {
          $updateproject = $this->project_model->updatefeature($data);
          $hasil = $updateproject;
        }
        echo json_encode($hasil);
      }
      function getonefeature($id){
        $getdata=$this->project_model->getdatafeature(array('feature_id'=>$id))->row_array();
        echo json_encode($getdata);
      }
      function detailfeature($id) {
        $getdetailfeature = $this->project_model->get_detailfeature($id);
        //        $getdetailfeature['url']=''
        echo json_encode($getdetailfeature);
      }

      function tabelgroup() {
        $data = array();
        foreach ($_GET as $key => $value) {
          $data[$key] = $value;
        }
        $gettabel = $this->project_model->get_grouptabel($data);
        echo json_encode($gettabel);
      }
      function getonegroup($id){
        $getdata=$this->project_model->getdatagrup(array('group_id'=>$id))->row_array();
        echo json_encode($getdata);
      }
      function savegroup() {
        $data = array();
        foreach ($_POST as $key => $value) {
          $data[$key] = $value;
        }

        if ($data['group_id'] == '') {
          $save = $this->project_model->addgroup($data);
          $hasil = $save;
        } else {
          $update = $this->project_model->updategroup($data);
          $hasil = $update;
        }
        echo json_encode($hasil);
      }

      function tabelitem() {
        $data = array();
        foreach ($_GET as $key => $value) {
          $data[$key] = $value;
        }
        $gettabel = $this->project_model->get_Item($data);
        echo json_encode($gettabel);
      }

      function changeitemstatus() {
        $data = array();
        foreach ($_POST as $key => $value) {
          $data[$key] = $value;
        }
        //        echo json_encode($data);
        $update = $this->project_model->updatestatusclosed($data);
        echo json_encode($update);
      }

      function saveitem() {
        $data = array();
        foreach ($_POST as $key => $value) {
          $data[$key] = $value;
        }

        if ($data['item_id'] == '') {
          $save = $this->project_model->addItem($data);
          $hasil = $save;
        } else {
          $update = $this->project_model->updateitem($data);
          $hasil = $update;
        }
        echo json_encode($hasil);
      }
      function getoneitem($id){
        $getdata=$this->project_model->getdataitem(array('item_id'=>$id))->row_array();
        $getdata['start_date']=date('m/d/Y',strtotime($getdata['start_date']));
        $getdata['end_date']=date('m/d/Y',strtotime($getdata['end_date']));
        echo json_encode($getdata);
      }
      function detailgroup($id) {
        $getdetailgroup = $this->project_model->get_detailgroup($id);
        echo json_encode($getdetailgroup);
      }

      function linkback() {
        $data = array();
        foreach ($_POST as $key => $value) {
          $data[$key] = $value;
        }
        $chek = $this->project_model->chekdataforlink($data);

        if ($data['type'] == 'item') {
          $html = "<button type=\"button\"  class=\"btn btn-link\"><<<a href=\"" . base_url() . "home/projectMaster\" style=\"margin:none;\">Project</a></button><button type=\"button\" onclick=\"detailproject('" . $chek['proplat_id'] . "')\" class=\"btn btn-link\" style=\"margin:none;\"><< Feature</button><button type=\"button\" onclick=\"detailfeature('" . $chek['feature_id'] . "')\" class=\"btn btn-link\" style=\"margin:none;\"><< Group</button>";
        } else if ($data['type'] == 'group') {
          $html = "<button type=\"button\"  class=\"btn btn-link\"><<<a href=\"" . base_url() . "home/projectMaster\" style=\"margin:none;\">Project</a></button><button type=\"button\" onclick=\"detailproject('" . $chek['proplat_id'] . "')\" class=\"btn btn-link\" style=\"margin:none;\"><< Feature</button>";
        } else {
          $html = "<button type=\"button\"  class=\"btn btn-link\"><<<a href=\"" . base_url() . "home/projectMaster\" style=\"margin:none;\">Project</a></button>";
        }
        echo $html;
      }

      function get_projectid_bygroupid($groupid) {
        return $this->project_model->get_projectid_bygroupid($groupid);
      }

      function getaddMemberModal() {
        $data = array();
        $groupid = $_POST['groupid'];
        $project_id = $this->get_projectid_bygroupid($groupid);
        echo $project_id;
        $data['member'] = $this->project_model->get_project_member($project_id);
        echo json_encode($data);
        echo $this->load->view('item_addmember_modal', $data, FALSE);
      }

      function saveMemberAssignment() {
        $data = array();
        foreach ($_POST as $key => $value) {
          $data[$key] = $value;
        }
        //$data = {"member":["4","5"]}
        //        $addassignment_result = $this->project_model->add_assignment($data);

        $addassignment_result = $this->project_model->addtabletask($data);
        echo json_encode($addassignment_result);
      }

      function remove_assignment() {
        $data = array();
        foreach ($_POST as $key => $value) {
          $data[$key] = $value;
        }
        //data is user_id, item_id
        $result = $this->project_model->remove_assignment($data);
        echo json_encode($result);
      }

      function deleteproject($project_id) {
        $delete = $this->project_model->harddeleteproject($project_id);
        echo json_encode($delete);
      }
      function deletefeature($id) {
        $delete = $this->project_model->harddeletefeature($id);
        echo json_encode($delete);
      }
      function deleteitem($id) {
        $delete = $this->project_model->harddeleteitem($id);
        echo json_encode($delete);
      }
      function deletegroup($id) {
        $delete = $this->project_model->harddeletegroup($id);
        echo json_encode($delete);
      }

      function getdashboard(){
        $content = array();
        $content['task'] = $this->timesheet_model->gettaskfordashboard();
        echo $this->load->view("dashboardview",$content);
      }

      function updatemanhours(){
        $data = array();
        foreach ($_POST as $key => $value) {
          $data[$key] = $value;
        }
        //        echo "cde";die(0);
        $balikan = $this->timesheet_model->updatetimesheet($data);
      }


      function getoneproject($projectid){
        $html=$html2='';
        $m=$p=array();
        $getproject=$this->project_model->getdataproject(array('project_id'=>$projectid))->row_array();
        $hasil=$getproject;
        // $hasil['enddate']=date('m/d/Y',strtotime($hasil['enddate']));

        $getmember=$this->project_model->getprojectMember(array('project_id'=>$projectid))->result_array();
        if(sizeof($getmember)>0){
          foreach ($getmember as $value) {
            # code...
            array_push($m,$value['member_id']);
          }
        }
        $hasil['member']=$m;
        $getplatform=$this->project_model->getprojectplatform(array('project_id'=>$projectid))->result_array();
        if(sizeof($getplatform)>0){
          foreach ($getplatform as $value) {
            # code...
            array_push($p,$value['platform_id']);
          }
        }
        $hasil['platform']=$p;

        echo json_encode($hasil);
      }
      function getmemberproject(){
        $data=array();
        foreach ($_POST as $key => $value) {
          # code...
          $data[$key]=$value;
        }
        $getmember=$this->project_model->getmemberbyproplat($data['proplat_id']);
        if($getmember->num_rows()>0){
          $html='<select class="form-control " name="doingby" id="doingby" style="width: 90%;">';


          foreach ($getmember->result_array() as $o) {
            # code...
            if($o['gender']=='m'){
              $html .= '<option value="'.$o['member_id'].'" >Mr. '.$o['nama'].'</option>';
            }else{
              $html .= '<option value="'.$o['member_id'].'">Ms.'.$o['nama'].'</option>';
            }



          }

          $html.='</select>';

        }else{
          $html='<select class="form-control " name="doingby" id="doingby" style="width: 90%;">';


          $html .= '<option value="" >-</option>';




          $html.='</select>';
        }

        echo $html;

      }

    }
