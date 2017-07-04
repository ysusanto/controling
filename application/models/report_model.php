<?php

/*
* To change this license header, choose License Headers in Project Properties.
* To change this template file, choose Tools | Templates
* and open the template in the editor.
*/

/**
* Description of report_model
*
* @author ASUS
*/
class report_model extends CI_Model {

  //put your code here

  public function __construct() {
    parent::__construct();
    $this->load->database();
  }
  function getdatareportproject(){
    $sql = "select userid,project_id,name,client,pm,plan,actual,(2000000*plan) as costplan,(2000000*actual) as costactual,operationalcost,(2000000*plan)-((2000000*actual)+COALESCE(operationalcost,0)) as profit  from (
select p.project_id,p.name,p.client,sum(i.hour)as plan,p.userid,
    case when sum(ts.hours) is null then 0 else sum(ts.hours) end as  actual,u.nama as pm,oc.price as 'operationalcost' from item i
    join `group` g on i.group_id=g.group_id
    join feature f on g.feature_id=f.feature_id
    join project_platform pp on f.proplat_id=pp.proplat_id
    join project p on pp.project_id=p.project_id
    join tuser u on p.userid=u.userid
    left join (SELECT item_id,sum(main_hour)as hours FROM `timesheet` group by item_id)ts on ts.item_id=i.item_id
	left join (select project_id,sum(COALESCE(nominal,0))as 'price' from operationalcost where is_sign=2 group by project_id)oc on p.project_id=oc.project_id
    group by project_id
    )a group by project_id";
    $query = $this->db->query($sql);
    return $query;
  }
  function platformbyproject($project_id){
	  $sql2 = "select a.proplat_id,a.project_id,a.platform_id,b.name from project_platform a left join platform b on a.platform_id=b.platform_id where a.project_id='" . $project_id . "'";
        $query2 = $this->db->query($sql2);
		return $query2;
  }
  function getreportproject() {
    $userid = $this->session->userdata('user_id');
    $roleid = $this->session->userdata('roleid');
    $getdata=$this->getdatareportproject();
    if ($getdata->num_rows() > 0) {
      $x = 1;
      foreach ($getdata->result_array() as $row) {
        if(in_array(2,$roleid)&& $row['userid']!=$userid){
          continue;
        }
        $sql2 = "select a.proplat_id,a.project_id,a.platform_id,b.name from project_platform a left join platform b on a.platform_id=b.platform_id where a.project_id='" . $row['project_id'] . "'";
        $query2 = $this->db->query($sql2);
        $htmlplatform = '<ul>';
        foreach ($query2->result_array() as $value) {
          $detail="<a href='#' onclick=\"detailreport('".$row['project_id']."','".$value['platform_id']."')\" >".$value['name']."</a>";
          $htmlplatform .='<li>' . $detail . '</li>';
          //                    array_push($platform,$row['name']);
        }
        $htmlplatform .='</ul>';
        // $detail="<a href='#' onclick=\"detailreport('".$row['project_id']."')\" >".$row['name']."</a>";
        if($row['plan']>=$row['actual']){
          $json['aaData'][] = array(
						$x,
						$row['name'],
						$row['client'],
						$row['pm'],
						$htmlplatform,
						$row['plan'],
						$row['actual'],
						($row['costplan'] == '' || $row['costplan'] == null ? 0: number_format($row['costplan']) ),
						number_format($row['costactual']),
						($row['operationalcost'] == '' || $row['operationalcost'] == null ? 0: number_format($row['operationalcost'] )),
						number_format($row['profit'])
						);
        }else{
          $json['aaData'][] = array(
            "<div class='warning' style='color:  red;font-weight: bold'>".$x."<div>",
            "<div class='warning' style='color:  red;font-weight: bold'>".$row['name']."<div>",
            "<div class='warning' style='color:  red;font-weight: bold'>".$row['client']."<div>",
            "<div class='warning' style='color:  red;font-weight: bold'>".$row['pm']."<div>",
            //  "<div class='warning'>".$row['client']."<div>",
            "<div class='warning' style='color:  red;font-weight: bold'>".$htmlplatform."<div>",
            "<div class='warning' style='color:  red;font-weight: bold'>".$row['plan']."<div>",
            "<div class='warning'style='color:  red;font-weight: bold'>".$row['actual']."<div>",
			"<div class='warning' style='color:  red;font-weight: bold'>".number_format($row['costplan'])."<div>",
            "<div class='warning' style='color:  red;font-weight: bold'>".number_format($row['costactual'])."<div>",
			"<div class='warning' style='color:  red;font-weight: bold'>".number_format($row['operationalcost'])."<div>",
            "<div class='warning' style='color:  red;font-weight: bold'>".number_format($row['profit'])."<div>"
          );
        }
        $x++;
      }
    } else {
      $json['aaData'] = array();
    }
    return $json;
  }

  function getdetailreportdata($project_id,$platform_id) {
    $hasil=array();
    $sql = "select i.item_id,
    i.group_id,
    i.name,g.name as 'group',
    f.name as 'feature',
    i.hour as 'plan',
    sum(t.main_hour) as 'actual',
    pp.platform_id,
    pl.name as 'platform',
    count(*) as 'count'
    from item i
    left join `group` g on i.group_id=g.group_id
    left join feature f on g.feature_id=f.feature_id
    left join timesheet t on i.item_id=t.item_id
    left join project_platform pp on pp.proplat_id=f.proplat_id
    left join platform pl on pl.platform_id=pp.platform_id
    where pp.project_id='".$project_id."' and pp.platform_id='".$platform_id."' and i.name is not null";
    $query = $this->db->query($sql);
    if ($query->num_rows() > 0) {
      foreach ($query->result_array() as $row) {
        array_push($hasil,$row);
      }
    }
    return $hasil;
  }

  function getuseroperationalcost(){
    $roleid=$this->session->userdata('roleid');
if(in_array(2,$roleid)){
}
    $sql="select oc.userid,u.nama from operationalcost oc left join tuser u on oc.userid=u.userid where oc.userid is not null  group by oc.userid,u.nama order by 1";

    $qry=$this->db->query($sql);
    return $qry->result_array();
  }
  function getdatacostreport($data){
    // print_r($data);die(0);
    $roleid=$this->session->userdata('roleid');
    if(in_array(2,$roleid)||in_array(0,$roleid)||in_array(1,$roleid)){
      $sql="select oc.id_oc,oc.desc,oc.nominal,oc.path,oc.sign_by,oc.userid,u.nama,oc.project_id,p.project_id,oc.is_sign,p.name,oc.modified_date,tu.nama as'review' from operationalcost oc left join tuser u on oc.userid=u.userid left join project p on p.project_id=oc.project_id left join tuser tu on tu.userid=oc.sign_by where oc.is_sign=2";

    }else if(in_array(99,$roleid)||in_array(0,$roleid)){
      $sql="select oc.id_oc,oc.desc,oc.nominal,oc.path,oc.sign_by,oc.userid,u.nama,oc.project_id,oc.is_sign,p.project_id,p.name,oc.modified_date,tu.nama as'review' from operationalcost oc left join tuser u on oc.userid=u.userid left join project p on p.project_id=oc.project_id left join tuser tu on tu.userid=oc.sign_by where oc.is_sign in (1,2) ";

    }else{
      $sql="select oc.id_oc,oc.desc,oc.nominal,oc.path,oc.sign_by,oc.userid,u.nama,oc.project_id,oc.is_sign,p.project_id,p.name,oc.modified_date,tu.nama as'review' from operationalcost oc left join tuser u on oc.userid=u.userid left join project p on p.project_id=oc.project_id left join tuser tu on tu.userid=oc.sign_by where oc.is_sign=2 ";

    }
    $iswhere=1;
    if(isset($data['user_id']) && $data['user_id']!=''){
      $where=($iswhere==0 ? ' where' : ' and');
      $sql .=$where ." oc.userid=".$this->db->escape($data['user_id']);
      $iswhere=1;
    }
    if(isset($data['project_id']) && $data['project_id']!=''){
      $where=($iswhere==0 ? ' where' : ' and');
      $sql .=$where ." oc.project_id=".$this->db->escape($data['project_id']);
      $iswhere=1;
    }
    // print_r($sql);die(0);
    // $sql="select oc.id_oc,desc,oc.nominal,oc.path,oc.sign_by,oc.userid,u.nama,oc.project_id,p.project_id,p.name from operationalcost oc left join tuser u on oc.userid=u.userid left join project p on p.project_id=oc.project_id where oc.userid is not null group by oc.userid,u.nama order by 1";
    $qry=$this->db->query($sql);
    return $qry;
  }

}
