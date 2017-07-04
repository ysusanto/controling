<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of db_load
 *
 * @author ASUS
 */
class db_load extends CI_Model {

    //put your code here
    public function __construct() {
        parent::__construct();
        $this->load->database();
         date_default_timezone_set('Asia/Jakarta');
        $this->load->library('session');
        $this->load->helper('date');
        $this->load->helper('form');
        $this->load->helper('url');
        $this->load->library('image_lib');
//         $this->load->library('image_lib');
        $this->load->library('upload');
        $this->load->library('email');
    }

public function getmenu($userid){
  $hasil=array();
  $db=$this->load->database('default',TRUE);
  $sql="select  m.menu_id,m.description,m.fungsi from tmenu m
left join grupakses ga on m.menu_id=ga.menu_id

left join gruprole gr on gr.role_id=ga.role_id

where gr.user_id='".$userid."' and parent_id=0 and ga.action in('r','w') group by m.menu_id,m.description,m.fungsi order by 1";
// print_r($sql);die(0);
  $qry=$db->query($sql);
  if($qry->num_rows()>0){
    foreach ($qry->result_array() as $row) {

      $row['submenu']=$this->submenu($userid,$row['menu_id']);
    //   if($row['menu_id']==3){
    //   print_r($row['submenu']);die();
    // }
      array_push($hasil,$row);
    }
  }
  return $hasil;
}
public function submenu($userid,$menuid){
  $hasil=0;
  $db=$this->load->database('default',TRUE);
  $sql="select  m.menu_id,m.description,m.fungsi from tmenu m
left join grupakses ga on m.menu_id=ga.menu_id

left join gruprole gr on gr.role_id=ga.role_id

  where parent_id='".$menuid."' and gr.user_id='".$userid."'  and ga.action in('r','w') group by m.menu_id,m.description,m.fungsi order by 1";
  $qry=$db->query($sql);
  if($qry->num_rows()>0){
    $hasil=$qry->result_array();
  }
  return $hasil;

}
    public function getplatform() {
        $query = $this->db->query('select * from platform');
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
    }

    public function getrole() {
        $query = $this->db->query('select * from role');
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
    }

    public function getrolewithouthob() {
        $query = $this->db->query('select * from role where role_id not in(0,99)');
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
    }



    public function getmember() {
        $query = $this->db->query("SELECT distinct tuser.userid,tuser.nama,tuser.username,tuser.email,tperson.salutation,tperson.firstname,
tperson.lastname FROM tuser LEFT JOIN tperson on tuser.userid = tperson.user_id where userid not in (1,14,6,12,2)");
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
    }

    public function chekpm($user_id) {
        $query = $this->db->query('select userid from project where userid="' . $user_id . '"');
        if ($query->num_rows() > 0) {
            return 1;
        } else {
            return 0;
        }
    }

    function uploadfile($data) {


        $path = $data['path'];

        $file = $_FILES['filename']['name'];
        $type = 'application/octet-stream|xls|xlsx|application/vnd.ms-excel|application/excel|application/vnd.openxmlformats-officedocument.spreadsheetml.sheet|application/vnd.oasis.opendocument.spreadsheet';
        $name = 'filename';
		/*$return = array(
			'status' => 0,
			'message' => 'Upload gagal',
			'msg' => $_FILES['filename']['type']
		);
        return $return;*/

        if (!empty($file)) {
            $pathfile = pathinfo($file);
            $config['upload_path'] = $path; //'assets/img/boat/';
            $config['allowed_types'] = $type;
            $config['file_name'] = $data['nama'] . '_' . date('dmy') . '.' . $pathfile['extension'];
            $config['max_size'] = '2000';
//            $config['max_width'] = '500';
//            $config['max_height'] = '600';
            $config['overwrite'] = TRUE;
//            $config['encrypt_name'] = FALSE;
            $config['remove_spaces'] = TRUE;
            $this->upload->initialize($config);
            if ($this->upload->do_upload($name)) {
                $image = $this->upload->data();
//                print_r($image);
//                die(0);
                $imagepath = $path . $image['file_name'];
                $names = $image['file_name'];
//                $data['thumbimage'] = 'assets/img/'.$image['file_name'];
//                $data['namaimage'] = $image['orig_name'];
////                if($this->image_lib->resize()){
////                    $data['thumbimage'] = 'assets/shoppict/thumb/'.$image['raw_name'].'_thumb'.$image['file_ext'];
////                }
////                $return = $this->webshop_model->updateCover($data);
//                $images = file_get_contents($data['mainimage']);
////                $base64 = 'data:image/' . $pathfile['extension'] . ';base64,' . base64_encode($images);
//                $base64=base64_encode($images);
                $return = array(
                    'status' => 1,
                    'path' => $imagepath,
                    'name' => $names
                );
                return $return;
            } else {
                $return = array(
                    'status' => 0,
                    'message' => 'Upload gagal',
                    'msg' => $this->upload->display_errors()
                );

                return $return;
            }
        }
    }
function getplatformbyid($data){
  $qry=$this->db->query('select * from platform where platform_id='.$data['platform_id']);
  return $qry;
}
}
