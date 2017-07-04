<?php

/*
* To change this license header, choose License Headers in Project Properties.
* To change this template file, choose Tools | Templates
* and open the template in the editor.
*/

/**
* Description of report
*
* @author ASUS
*/

class report extends CI_Controller{
  //put your code here

  public function __construct() {
    parent::__construct();
    $this->load->model('report_model');
    $this->load->model('db_load');
	$this->load->library('excel');
  }

  function getprojectreport(){
    $get=$this->report_model->getreportproject();
    echo json_encode($get);
  }
  function exportexcelplanactual(){
		date_default_timezone_set('Asia/Jakarta');
		$tanggal = date('Ymd');
		$get=$this->report_model->getdatareportproject();


		$objPHPExcel = new PHPExcel();
		$objPHPExcel->setActiveSheetIndex(0);
				$styleArray = array(
			  'borders' => array(
				  'allborders' => array(
					  'style' => PHPExcel_Style_Border::BORDER_THIN
				  )
			  )
		  );
		$objPHPExcel->getDefaultStyle()->applyFromArray($styleArray);
		$objPHPExcel->getActiveSheet()->SetCellValue('A1', "Plan And Actual Report" );
		$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setSize(16);
		$rowCount = 4;
		$x=1;
		$platform='';
		foreach($get->result_array() as $row){
			$getplatform=$this->report_model->platformbyproject($row['project_id']);
			foreach($getplatform->result_array() as $gt){
				$platform .=$gt['name']."\n";
			}

			$objPHPExcel->getActiveSheet()->SetCellValue('A3', "No." );
			$objPHPExcel->getActiveSheet()->SetCellValue('B3' , "Project Name");
			$objPHPExcel->getActiveSheet()->SetCellValue('C3' , "Client");
			$objPHPExcel->getActiveSheet()->SetCellValue('D3' , "Project Manager");
			$objPHPExcel->getActiveSheet()->SetCellValue('E3' , "Platform");
			$objPHPExcel->getActiveSheet()->SetCellValue('F3' , "Main Hours Plan");
			$objPHPExcel->getActiveSheet()->SetCellValue('G3' , "Main Hours Actual");
			$objPHPExcel->getActiveSheet()->SetCellValue('H3' , "Cost Plan");
			$objPHPExcel->getActiveSheet()->SetCellValue('I3' , "Cost Actual");
			$objPHPExcel->getActiveSheet()->SetCellValue('J3' , "Operational Cost");
			$objPHPExcel->getActiveSheet()->SetCellValue('K3' , "Profit");

			foreach(range('A','K') as $al){
				$objPHPExcel->getActiveSheet()->getStyle($al.'3')->getFont()->setBold(true);
			}
			foreach(range('A','K') as $al){
			$objPHPExcel->getActiveSheet()->getColumnDimension($al)->setAutoSize(true);
			}
			foreach(range('A','K') as $al){
				 $objPHPExcel->getActiveSheet()->getStyle($al.'3')->applyFromArray(
						array(
							'fill' => array(
								'type' => PHPExcel_Style_Fill::FILL_SOLID,
								'color' => array('rgb' => 'dfdfdf')
							)
						)

				);
			}



			$objPHPExcel->getActiveSheet()->SetCellValue('A'.$rowCount, $x);
			$objPHPExcel->getActiveSheet()->SetCellValue('B'.$rowCount, $row['name']);
			$objPHPExcel->getActiveSheet()->SetCellValue('C'.$rowCount, $row['client']);
			$objPHPExcel->getActiveSheet()->SetCellValue('D'.$rowCount, $row['pm']);
			$objPHPExcel->getActiveSheet()->SetCellValue('E'.$rowCount, $platform);
			$objPHPExcel->getActiveSheet()->getStyle('E'.$rowCount)->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->SetCellValue('F'.$rowCount, $row['plan']);
			$objPHPExcel->getActiveSheet()->SetCellValue('G'.$rowCount, $row['actual']);
			$objPHPExcel->getActiveSheet()->SetCellValue('H'.$rowCount, ($row['costplan'] == '' || $row['costplan'] == null ? 0: $row['costplan'] ));
			$objPHPExcel->getActiveSheet()->SetCellValue('I'.$rowCount, $row['costactual']);
			$objPHPExcel->getActiveSheet()->SetCellValue('J'.$rowCount, ($row['operationalcost'] == '' || $row['operationalcost'] == null ? 0: $row['operationalcost'] ));
			$objPHPExcel->getActiveSheet()->SetCellValue('K'.$rowCount, $row['profit']);
			$rowCount++;
			$x++;
		}

		#echo date('H:i:s') . " Write to Excel2007 format\n";
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		//ob_end_clean();
		// We'll be outputting an excel file
		header('Content-type: application/vnd.ms-excel');
		header('Content-Disposition: attachment; filename="PlanActual_Report_'.$tanggal.'.xlsx"');
		$objWriter->save('php://output');
		//$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);

		//$objWriter->save('PlanActual_Report_'.$tanggal.'.xlsx');
	}

  function getprojectreportdetail(){
    $data=array();
    foreach ($_GET as $key => $value) {
      # code...
      $data[$key]=$value;
    }
    $detail=$this->report_model->getdetailreportdata($data['project_id'],$data['platform_id']);
    $x=1;
    if(sizeof($detail)>1){
      foreach ($detail as  $value) {
        # code...
        if($value['plan']>=$value['actual']){
          $balikan['aaData'][]=array($x,$value['feature'],$value['group'],$value['name'],$value['plan'],$value['actual']);
        }else{
          $balikan['aaData'][]=array(
            "<div class='warning' style='color:  red;font-weight: bold'>".$x."</div>",
            "<div class='warning' style='color:  red;font-weight: bold'>".$value['feature']."</div>",
            "<div class='warning' style='color:  red;font-weight: bold'>".$value['group']."</div>",
            "<div class='warning' style='color:  red;font-weight: bold'>".$value['name']."</div>",
            "<div class='warning' style='color:  red;font-weight: bold'>".$value['plan']."</div>",
            "<div class='warning' style='color:  red;font-weight: bold'>".$value['actual']."</div>"
          );
        }
      }


    }else if(sizeof($detail)==1){
      if($detail[0]['count']==0){
        $balikan['aaData']=array();
      }else{
        foreach ($detail as  $value) {
          # code...
          if($value['plan']>=$value['actual']){
            $balikan['aaData'][]=array($x,$value['feature'],$value['group'],$value['name'],$value['plan'],$value['actual']);
          }else{
            $balikan['aaData'][]=array(
              "<div class='warning' style='color:  red;font-weight: bold'>".$x."</div>",
              "<div class='warning' style='color:  red;font-weight: bold'>".$value['feature']."</div>",
              "<div class='warning' style='color:  red;font-weight: bold'>".$value['group']."</div>",
              "<div class='warning' style='color:  red;font-weight: bold'>".$value['name']."</div>",
              "<div class='warning' style='color:  red;font-weight: bold'>".$value['plan']."</div>",
              "<div class='warning' style='color:  red;font-weight: bold'>".$value['actual']."</div>"
            );
          }
        }
      }

    }else{
      $balikan['aaData']=array();
    }
    echo json_encode($balikan);
  }
  function getdatachartproject(){
    $balikan=array();
    $userid = $this->session->userdata('user_id');
    $roleid = $this->session->userdata('roleid');
    $getdata=$this->report_model->getdatareportproject();
    if($getdata->num_rows()>0){
      foreach ($getdata->result_array() as $value) {
        # code...
        if(in_array(2,$roleid)&& $value['userid']!=$userid){
          continue;
        }
        $balikan['label'][]=$value['name'];
        $balikan['actual'][]=(float)$value['actual'];
        $balikan['plan'][]=(float)$value['plan'];
      }

      echo json_encode($balikan);
    }
  }
  function getdatachartprojectdetail(){
    $balikan=array();
    $getdata=$this->report_model->getdatareportproject();
    if($getdata->num_rows()>0){
      foreach ($getdata->result_array() as $value) {
        # code...
        $balikan['label'][]=$value['name'];
        $balikan['actual'][]=(float)$value['actual'];
        $balikan['plan'][]=(float)$value['plan'];
      }

      echo json_encode($balikan);
    }
  }
  function getreportcosttabel($type=''){
    $data=array();
    foreach ($_GET as $key => $value) {
      # code...
      $data[$key]=$value;
    }
    $getdatacost=$this->report_model->getdatacostreport($data);
    // print_r($getdatacost->num_rows());die();
    if($getdatacost->num_rows()>0){
      $x=1;
      foreach ($getdatacost->result_array() as  $value) {
        # code...
        if($type=='edit'){
          if($value['is_sign']==2){
            $edit='';
          }else{
          $edit="<div class=\"checkbox\">
          <label>
          <input type=\"checkbox\" name=\"signby[]\" value='".$value['id_oc']."'>
          </label>
          </div>";
        }
        }else{
          $edit='';
        }
        $json['aaData'][]=array($x,
        $value['name'],
        $value['desc'],
        $value['nominal'],
        $value['review'],
        date("m/d/Y",strtotime($value['modified_date'])),
        $edit
      );
      $x++;
    }
  }else{
    $json['aaData']=array();
  }
  echo json_encode($json);



}

function exportpdfcost($userid){
  $getcost=$this->report_model->getdatacostreport(array('user_id'=>$userid));
  $user=$this->user_model->getuserdata(array('userid'=>$userid));
  if($user->num_rows()>0){
    $r=$user->row_array();
    $name=$r['nama'];
  }else{
    $name='-';
  }

  $pdf = new Pdf('P', 'mm', 'A4', true, 'UTF-8', false);
  $pdf->SetTitle('Pdf Example');
  $pdf->SetHeaderMargin(30);
  $pdf->SetTopMargin(20);
  $pdf->setFooterMargin(20);
  $pdf->SetAutoPageBreak(true);
  $pdf->SetFont('helvetica', '', 10);
  $html='';
  // add a page
  $pdf->AddPage();
  $html.='
  <h1 class="title" style="

  font-size: 16pt;
  align:center">Operational Cost</i></h1>';
  $html.='
  <table  cellspacing="0" cellpadding="2" border="0">
  <tr >
  <td width="100">Name</td>
  <td width="200">'.$name.'</td>
  <td width="100"></td>
  <td width="100"></td>
  </tr>

  ';


  $html.='<tr><td colspan="4">';
  $html .="</td></tr></table> <br>";

  $html.='
  <table border="1" cellspacing="0" cellpadding="2">
  <tr bgcolor="#2166d3" color="white" align="center">
  <th width="20">No.</th>
  <th width="100">Project</th>
  <th width="150">Description</th>
  <th width="75">Cost (Rp)</th>
  <th width="75">Date</th>
  <th width="100">Sign</th>
  </tr>';
  if($getcost->num_rows()>0){
    $a=1;
    foreach ($getcost->result_array() as $value) {
      if($a%2==0){
        $html.='<tr bgcolor="#dddddd">';
      }else{
        $html.="<tr>";
      }


      $html.="<td>".$a."</td>
      <td>".$value['name']."</td>
      <td>".$value['desc']."</td>
      <td>".$value['nominal']."</td>
      <td>".date('m/d/Y',strtotime($value['modified_date']))."</td>
      <td>".$value['review']."</td>
      </tr>";
      $a++;
    }

  }else{
    $html.='<tr>
    <td colspan="4">Data not available</td>

    </tr>';
  }


  $html.="</table>";
  $html.='
  <table  cellspacing="0" cellpadding="2" border="0">';
  if($getcost->num_rows()>0){
    $a=1;
    $html .='<tr >';
    foreach ($getcost->result_array() as $value) {

      $html .=' <td width="250">'.$a.'<br><img src="'. base_url() .$value['path']. '"  width="250" >
      </td>';
      if($a%2==0){
        $html.='</tr>

        ';
      }
      if($getcost->num_rows()==$a){
        $html.='</tr>

        ';
      }
      $a++;
    }
  }
  $html.="</table>";

  $pdf->writeHTML($html, true, false, true, false, '');
  $pdf->Output('pdfexample.pdf', 'I');


}
 function savesigncost(){
   $data=$hasil=array();
   $userid=$this->session->userdata('user_id');
   foreach ($_POST as $key => $value) {
     # code...
     $data[$key]=$value;
   }
   if(sizeof($data['signby'])>0){
     foreach ($data['signby'] as $val) {
       # code...
       $array_update=array(
         'is_sign'=>2,
         'sign_by'=>$userid
       );
       $cond=array(
         'id_oc'=>$val

       );
       $updatesigncost=$this->cost_model->updatesigncost($cond,$array_update);
  array_push($hasil,$updatesigncost);
     }
   }

   if(in_array(0,$hasil)){
     $balikan=array(
       'status'=>0,
       'msg'=>'Sign Cost Failed Update'
     );
   }else{
     $balikan=array(
       'status'=>1,
       'msg'=>'Sign Cost Updated'
     );
   }
   echo json_encode($balikan);
 }
}
