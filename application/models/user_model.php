<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class User_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    function validate($email, $password) {
      $db=$this->load->database('default',TRUE);
        $data = array(
            'email' => $email,
            'password' => $password);
        $sql="select * from tuser where BINARY email='".$email. "' and BINARY password='".$password. "'";

        $query = $this->db->query($sql);

        if ($query->num_rows() > 0) {
            return $query->row_array();
        }else{
          return 0;
        }
    }

    function get_user($user_id) {
        $data = array(
            'userid' => $user_id);

        $query = $this->db->get_where('tuser', $data)->row_array();
        $cond = array(
            'user_id' => $user_id);
        $querygetperson = $this->db->get_where('tperson', $cond)->row_array();
        if (sizeof($querygetperson) > 0)
            $query['personname'] = $querygetperson['salutation'] . " " . $querygetperson['firstname'] . " " . $querygetperson['lastname'];
        if (sizeof($query) > 0) {
            return $query;
        }
    }
    function getuserdata($data){
      $sql="select * from tuser ";
      $iswhere=0;
      if(isset($data['userid']) && $data['userid']!=''){
        $where=($iswhere==0 ? ' where' : ' and');
        $sql .=$where ." userid=".$this->db->escape($data['userid']);
        $iswhere=1;
      }

  $query = $this->db->query($sql);

return $query;
    }


    function get_menu($access_level) {
        $sql = "select * from tmenu where access_level >= " . $access_level . " order by  menu_id";
        $query = $this->db->query($sql)->result_array();
//        echo json_encode($query);die(0);
        if (sizeof($query) > 0) {
            return $query;
        }
    }

    function getalluser() {
      $sql = "SELECT tuser.userid,tuser.nama,tuser.username,tuser.email,tperson.salutation,tperson.firstname,tperson.lastname "
              . "FROM tuser LEFT JOIN tperson on tuser.userid = tperson.user_id ";
              // print_r($sql);die();
        $result = $this->db->query($sql)->result_array();
        if (sizeof($result) > 0) {
            return $result;
        }
    }

    function checkuser($email) {
        $sql = "SELECT * from tuser where email ='" . $email . "'";
        $result = $this->db->query($sql)->result_array();
        if (sizeof($result) > 0) {
            return $result;
        } else {
            return array();
        }
    }

    public function add($email, $password, $name = "", $is_admin = false) {
        $data = array(
            'email' => $email,
            'password' => md5($password),
            'name' => $name,
            'is_admin' => $is_admin
        );
        $affected_rows = $this->db->insert('tuser', $data);
        if ($affected_rows > 0) {
            return $this->db->insert_id();
        }
        return 'error';
    }

    public function addnewuser($data, $tablename) {
//        echo json_encode($data);die(0);
        $affected_rows = $this->db->insert($tablename, $data);
        if ($affected_rows > 0) {
            return $this->db->insert_id();
        }
        return 'error';
    }

    public function is_email_available($email) {
        $query = $this->db->get_where('tb_user', array('email' => $email));
        if ($query->num_rows() > 0) {
            return false;
        }
        return true;
    }
function getrolebyuser($userid){
  $role=array();
  $sql = "SELECT * from gruprole where user_id ='" . $userid . "' order by role_id";
  $result = $this->db->query($sql)->result_array();
  if (sizeof($result) > 0) {
    foreach ($result as  $value) {
      # code...
      array_push($role,$value['role_id']);
    }
      return $role;
  } else {
      return array();
  }
}
    public function send_reminder() {
        $sqlgetuser = "select userid,username,email,last_login from tuser";
        $querygetuser = $this->db->query($sqlgetuser)->result_array();
        foreach ($querygetuser as $value) {
            $sql = 'select pro.name as proname,pro.client, i.item_id,i.name,x.user_id,t.hours,g.name as grup,f.name as fitur '
                    . 'from item i left join t_task x on x.item_id = i.item_id'
                    . ' left join `group` g on g.group_id=i.group_id  left '
                    . 'join feature f on g.feature_id=f.feature_id left join project_platform p on f.proplat_id=p.proplat_id '
                    . 'left join project_platform pp on f.proplat_id = pp.proplat_id
                   left join project pro on pp.project_id = pro.project_id '
                    . 'left join (select item_id,sum(main_hour) as "hours" from timesheet group by item_id)t on i.item_id=t.item_id where  x.user_id = "' . $value['userid'] . '" and i.isclosed = "0" order by proname';

            $query = $this->db->query($sql)->result_array();
            if (sizeof($query) > 0) {
                $to = $value['email'];
                $subject = "Hallo, Reminder Harian Seatech Timesheet";

                $txt = "Hello " . $value['username'] . ",\r\n"
                        . "Silahkan login di http://api.seatechmobile.com/STS/ " . "sistem kami mencatat waktu login terakhir kamu ada pada tanggal " . $value['last_login'] . "\r\n" . "berikut adalah pendingan kamu : " . "\r\n";
                foreach ($query as $value2) {
                    $txt = $txt . "-" . $value2['proname'] . " : " . $value2['name'] . "\r\n";
                }
                $headers = "From: dailyreminder@sts.com";
//                echo $txt;die(0);
                mail($to, $subject, $txt, $headers);
            }
        }
    }

    public function initialemail() {
        $sqlgetuser = "select userid,username,email,last_login from tuser";
        $querygetuser = $this->db->query($sqlgetuser)->result_array();
        foreach ($querygetuser as $value) {
            $sql = 'select * from tperson where user_id = "' . $value['userid'] . '"';

            $query = $this->db->query($sql)->row_array();
//            if (sizeof($query) > 0) {
            $to = $value['email'];
            $subject = "Hallo, Terhitung Hari Ini Seatech Time Sheet Aktif yah";

            $txt = "Hello Mr Henry Roberto,\r\n"
                    . "Silahkan login sts di http://api.seatechmobile.com/STS/ " .
                    "\r\n" . "Untuk usernamenya silahkan gunakan email kantor masing masing" . "\r\n" . "Passwordnya seatechmobile" . "\r\n"
                    . "Silahka masukan Project Raksa Phase 2 di STS apabila proses Initialization sudah dimulai"
                    . "regards," . "\r\n" . "Seatech Time Sheet";

            $headers = "From: mail@sts.com";
//                echo $txt;die(0);
            mail($to, $subject, $txt, $headers);
//            }
        }
    }

    function register_sample($data) {
        $balikan = array("status" => "0", "message" => "Register Gagal");
        $affected_rows = $this->db->insert("t_numpang", $data);
        if ($affected_rows > 0) {
            $balikan['status'] = "1";
            $balikan['message'] = "Register Sukses";
        }
//        return 'error';
        return json_encode($balikan);
    }

    function login_sample($data) {
        $balikan = array("status" => "0", "message" => "Login Gagal", "data" => array());
        $sql = 'select * from t_numpang where email = "' . $data['email'] . '" and password = "' . $data['password'] . '"';
        $query = $this->db->query($sql)->row_array();
        if (sizeof($query) > 0) {
            $balikan['status'] = "1";
            $balikan['message'] = "Login Sukses";
            $balikan['data'] = $query;
        }

        echo json_encode($balikan);
    }

}
