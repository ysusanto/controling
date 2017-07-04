<?php

if (!defined('BASEPATH'))
exit('No direct script access allowed');

class Home extends CI_Controller {

  public function __construct() {
    parent::__construct();
    $this->load->model('db_load');
    $this->load->model('role_model');
    $this->load->model('project_model');
      $this->load->model('report_model');
  }

  public function index() {
    $content['user_id'] = $this->session->userdata('user_id');
    $header['menu']=$this->db_load->getmenu($content['user_id']);
    // print_r($header['menu']);die(0);
    $header['title']='SIC|Welcome';
    $header['username']=$this->session->userdata('nama');

    $content['user'] = $this->user_model->get_user($content['user_id']);
    $content['content']['content']='';
    $content['content']['title']='Welcome in SIC';
    $user_id = $this->session->userdata('user_id');
    $user = $this->user_model->get_user($user_id);
    // print_r($header);die(0);
    if ($user) {
      $this->load->view('master/header',$header);
      $this->load->view('master/content',$content);
    } else {
      $this->load->view('signin');
    }
  }
  public function signin() {
    if ($this->session->userdata('user_id')) {
      redirect();
    } else {
      $email = $this->input->post('email');
      $password = md5($this->input->post('password'));

      $user = $this->user_model->validate($email, $password);
      // print_r(strlen($user['nama']));die(0);
      if ($user!=0) {
        $user['nama']=(strlen($user['nama'])<=1? $user['username'] :$user['nama']);
        $getrole=$this->user_model->getrolebyuser($user['userid']);
        $user['nama']=($user['gender']=='m'? 'Mr. '.$user['nama']: 'Ms. '.$user['nama']);
        $this->session->set_userdata(array('nama'=>$user['nama'],'user_id' => $user['userid'], 'roleid' => $getrole, 'username' => $user['username']));
        redirect();
      } else {
        //this->load->view('header');

        $data = array(
          'error' => 'Your email or password is incorrect, please try again.',
          'email' => $email);
          $this->load->view('signin', $data);
        }
      }
    }

    public function signout() {
      $this->session->unset_userdata(array('user_id' => ''));
      redirect();
    }

    protected function error_register($name, $email, $error_msg = 'General Error!') {
      $this->load->view('header');

      $data = array('error' => $error_msg,
      'name' => $name,
      'email' => $email);
      $this->load->view('register', $data);
    }

    public function projectMaster() {
      $content['user_id'] = $this->session->userdata('user_id');

      $content['user'] = $this->user_model->get_user($content['user_id']);
      $header['menu']=$this->db_load->getmenu($content['user_id']);

      $header['title']='SIC|Project Setup';
      $header['username']=$this->session->userdata('nama');
      $user = $this->user_model->get_user($content['user_id']);
      $content['platform'] = $this->db_load->getplatform();
      $content['manager'] = $this->db_load->getmember();
      $content['peran'] = $this->db_load->getrolewithouthob();

      $content['content']['content']=$this->load->view('setupprojects_view', $content,TRUE);
      $content['content']['title']='Project Setup';

      if ($user == false) {
        $this->load->view('signin');
      } else {
        $this->load->view('master/header',$header);
        $this->load->view('master/content',$content);
      }
    }

    public function timesheet() {
      $content['user_id'] = $this->session->userdata('user_id');
      $content['chekpm'] = $this->db_load->chekpm($content['user_id']);
      $content['user'] = $this->user_model->get_user($content['user_id']);
      $header['menu']=$this->db_load->getmenu($content['user_id']);

      $header['title']='SIC|Timesheet';
      $header['username']=$this->session->userdata('nama');
      $user = $this->user_model->get_user($content['user_id']);
      $content['platform'] = $this->db_load->getplatform();
      $content['manager'] = $this->db_load->getmember();
      $content['peran'] = $this->db_load->getrolewithouthob();
      //        echo json_encode($content['role']);die(0);
      $content['content']['content']=$this->load->view('timesheet_view', $content,TRUE);
      $content['content']['title']='Time Sheet';

      if ($user == false) {
        $this->load->view('signin');
      } else {
        $this->load->view('master/header',$header);
        $this->load->view('master/content',$content);
      }
    }

    public function timesheetdetail() {

      $content['user_id'] = $this->session->userdata('user_id');

      $content['user'] = $this->user_model->get_user($content['user_id']);
      $this->load->view('header', $content);


      $user = $this->user_model->get_user($content['user_id']);
      $content['platform'] = $this->db_load->getplatform();
      $content['manager'] = $this->db_load->getmember();

      if ($content['user'] == false) {
        $this->load->view('signin');
      } else {
        $this->load->view('timesheetdetail_view', $content);
      }
    }

    public function reportplanactual() {
      $content['user_id'] = $this->session->userdata('user_id');

      $content['user'] = $this->user_model->get_user($content['user_id']);
      $header['menu']=$this->db_load->getmenu($content['user_id']);

      $header['title']='SIC|Report Plan And Actual';
      $header['username']=$this->session->userdata('nama');
      $user = $this->user_model->get_user($content['user_id']);
      $content['platform'] = $this->db_load->getplatform();
      $content['manager'] = $this->db_load->getmember();
      $content['peran'] = $this->db_load->getrolewithouthob();
      //        echo json_encode($content['role']);die(0);
      $content['content']['content']=$this->load->view('report_view', $content,TRUE);
      $content['content']['title']='Report Plan And Actual';

      if ($user == false) {
        $this->load->view('signin');
      } else {
        $this->load->view('master/header',$header);
        $this->load->view('master/content',$content);
      }

    }

    public function viewreportdetail() {

      $content['project_id']=$this->uri->segment(3); // 1stsegment
      $content['platform_id']=$this->uri->segment(4); // 2ndsegment
      $content['roleid'] = $this->session->userdata('roleid');
      $getproject=$this->project_model->getdataproject(array('project_id'=>$content['project_id']))->row_array();
      $content['project_name']=$getproject['name'];
      $getplatform=$this->db_load->getplatformbyid(array('platform_id'=>$content['platform_id']))->row_array();
      $content['platform_name']=$getplatform['name'];
      $content['user_id'] = $this->session->userdata('user_id');

      $content['user'] = $this->user_model->get_user($content['user_id']);
      $header['menu']=$this->db_load->getmenu($content['user_id']);

      $header['title']='SIC|Report Plan And Actual';
      $header['username']=$this->session->userdata('nama');
      $user = $this->user_model->get_user($content['user_id']);
      $content['platform'] = $this->db_load->getplatform();
      $content['manager'] = $this->db_load->getmember();
      $content['peran'] = $this->db_load->getrolewithouthob();
      //        echo json_encode($content['role']);die(0);
      $content['content']['content']=$this->load->view('reportdetail_view', $content,TRUE);
      $content['content']['title']='Report Plan And Actual Detail';

      if ($user == false) {
        $this->load->view('signin');
      } else {
        $this->load->view('master/header',$header);
        $this->load->view('master/content',$content);
      }

    }

    function reportcost(){

      $content['user_id'] = $this->session->userdata('user_id');
      $content['roleid'] = $this->session->userdata('roleid');
      // print_r($content['roleid']);die(0);
      $content['user'] = $this->user_model->get_user($content['user_id']);
      $header['menu']=$this->db_load->getmenu($content['user_id']);

      $header['title']='SIC|Report Operational Cost';
      $header['username']=$this->session->userdata('nama');
      $user = $this->user_model->get_user($content['user_id']);
            //  echo json_encode($content['role']);die(0);
      $content['member']=$this->report_model->getuseroperationalcost();
      // echo json_encode($content['member']);die(0);
      $content['content']['content']=$this->load->view('reportcost_view', $content,TRUE);
      $content['content']['title']='Report Operational Cost';

      if ($user == false) {
        $this->load->view('signin');
      } else {
        $this->load->view('master/header',$header);
        $this->load->view('master/content',$content);
      }
    }
    public function setupuser(){
      $content['user_id'] = $this->session->userdata('user_id');
      $header['menu']=$this->db_load->getmenu($content['user_id']);

      $header['title']='SIC|User Management';
      $header['username']=$this->session->userdata('nama');
      $user = $this->user_model->get_user($content['user_id']);
      $content['user'] = $user;

      $content['platform'] = $this->db_load->getplatform();
      $content['manager'] = $this->db_load->getmember();
      $content['peran'] = $this->db_load->getrolewithouthob();
      //        echo json_encode($content['role']);die(0);
      $content['position'] = $this->db_load->getrole();

      $content['content']['content']=  $this->load->view('setupuser', $content,TRUE);//$this->load->view('timesheet_view', $content,TRUE);
      $content['content']['title']='User Management';

      $form=$this->role_model->formrole();

      $modal['id']='rolemodal';
      $modal['title']='Role';
      $modal['content']=$this->load->view('master/form',$form,TRUE);
      $content['content']['content']=$content['content']['content'].$this->load->view('master/modals',$modal,TRUE);

      if ($user == false) {
        $this->load->view('signin');
      } else {
        $this->load->view('master/header',$header);
        $this->load->view('master/content',$content);
      }

    }

    public function sendReminder(){
      $this->user_model->send_reminder();
    }

    public function checksession(){
      $session['roleid'] = $this->session->userdata('roleid');
      echo json_encode($session);
    }


  }
