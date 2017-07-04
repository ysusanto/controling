<!DOCTYPE html>
<html lang="en">
    <head>
        <link rel="icon" href="<?php echo base_url() ?>assets/checkbox_checked.png">

        <title><?php echo $title; ?></title>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
        <link rel="stylesheet" href="<?php echo base_url() ?>assets/css/font-awesome.min.css">
        <!-- Bootstrap core CSS -->
        <link href="<?php echo base_url() ?>assets/bootstrap-3.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="<?php echo base_url() ?>assets/bootstrap-3.3.5/dist/css/bootstrap-timepicker.min.css" rel="stylesheet">
        <!-- <link href="<?php echo base_url() ?>assets/plugins/datepicker/datepicker3.css" rel="stylesheet"> -->
        <!-- Custom styles for this template -->

        <!--<link rel="stylesheet" href="<?php echo base_url() ?>assets/jquery-ui-1.11.4.custom/jquery-ui.css">-->
        <link rel="stylesheet" href="<?php echo base_url() ?>assets/datatables/css/jquery.dataTables.min.css">
        <!--<link rel="stylesheet" href="<?php echo base_url() ?>assets/datatables/css/dataTables.bootstrap.min.css">-->
        <link rel="stylesheet" href="<?php echo base_url() ?>assets/datatables/css/buttons.dataTables.min.css">
        <link rel="stylesheet" href="<?php echo base_url() ?>assets/datatables/css/buttons.bootstrap.min.css">
        <link rel="stylesheet" href="<?php echo base_url() ?>assets/jquery-ui-1.11.4.custom/jquery-ui.css">

        <!-- <link rel="stylesheet" href="<?php echo base_url() ?>assets/plugins/datepicker/datepicker3.css"> -->
        <link rel="stylesheet" href="<?php echo base_url(); ?>assets/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css">
        <link rel="stylesheet" href="<?php echo base_url() ?>assets/jquery-ui-1.11.4.custom/jquery-ui.css">
        <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/select2.min.css">
        <link rel="stylesheet" href="<?php echo base_url() ?>assets/plugins/daterangepicker/daterangepicker-bs3.css">
        <link rel="stylesheet" href="<?php echo base_url() ?>assets/css/sts.css">

        <link rel="stylesheet" href="<?php echo base_url() ?>assets/css/AdminLTE.min.css">
        <link rel="stylesheet" href="<?php echo base_url() ?>assets/css/skins/_all-skins.min.css">
                <script src="<?php echo base_url() ?>assets/js/jquery-1.11.3.min.js"></script>

        <script src="<?php echo base_url() ?>assets/bootstrap-3.3.5/dist/js/bootstrap.min.js" ></script>
        <script src="<?php echo base_url() ?>assets/bootstrap-3.3.5/dist/js/bootstrap-timepicker.min.js"></script>
        <script src="<?php echo base_url() ?>assets/datatables/js/jquery.dataTables.min.js"></script>
        <script src="<?php echo base_url() ?>assets/datatables/js/dataTables.responsive.min.js"></script>
          <script src="<?php echo base_url() ?>assets/jquery-ui-1.11.4.custom/jquery-ui.min.js"></script>
        <!--<script src="<?php echo base_url() ?>assets/datatables/js/dataTables.bootstrap.min.js"></script>-->
        <script src="<?php echo base_url() ?>assets/datatables/js/dataTables.buttons.min.js"></script>
        <script src="<?php echo base_url() ?>assets/datatables/js/buttons.bootstrap.min.js"></script>
        <script src="<?php echo base_url() ?>assets/js/jquery.form.js"></script>
        <script src="<?php echo base_url() ?>assets/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js"></script>
          <script src="<?php echo base_url() ?>assets/plugins/daterangepicker/moment.min.js"></script>
        <script src="<?php echo base_url() ?>assets/plugins/daterangepicker/daterangepicker.js"></script>
        <script src="<?php echo base_url() ?>assets/plugins/chartjs/Chart.min.js"></script>

        <script src="<?php echo base_url() ?>assets/plugins/select2/select2.full.min.js"></script>
        <!-- <script src="<?php echo base_url() ?>assets/plugins/datepicker/bootstrap-datepicker.js"></script> -->
        <script src="<?php echo base_url() ?>assets/js/app.min.js"></script>
        <script src="<?php echo base_url() ?>assets/js/master.js"></script>
        <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
        <!--        [if lt IE 9]>
                  <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
                  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
                <![endif]-->
                <script>
                $(document).ready(function () {
                  $('.date').datepicker({

                      Format: 'dd/mm/yy'


                  }
                  );
                  $(".timepicker").timepicker({
                    minuteStep: 1,
              template: 'modal',
              appendWidgetTo: 'body',
              showSeconds: true,
              showMeridian: false,
              defaultTime: false,

                    });
                    $(".select2").select2();
                      $(".textarea").wysihtml5();
                })
                function operational(){
                  $("#divoperational").show();
                }
                </script>
                <style>
                .timepicker{
                  z-index:9999 !important;
                }

                </style>
    </head>
    <body class="hold-transition skin-blue sidebar-mini">
        <div class="wrapper">

          <header class="main-header">
            <!-- Logo -->
            <a href="<?php echo base_url();?>" class="logo">
              <!-- mini logo for sidebar mini 50x50 pixels -->
              <span class="logo-mini"><b>SIC</b></span>
              <!-- logo for regular state and mobile devices -->
              <span class="logo-lg"><b>Seatechmobile</b></span>
            </a>
            <!-- Header Navbar: style can be found in header.less -->
            <nav class="navbar navbar-static-top" role="navigation">
              <!-- Sidebar toggle button-->
              <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
                <span class="sr-only">Toggle navigation</span>
              </a>
              <div class="navbar-custom-menu">
                <ul class="nav navbar-nav">
                  <!-- Messages: style can be found in dropdown.less-->

                  <!-- User Account: style can be found in dropdown.less -->
                  <li class="dropdown user user-menu">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                      <!-- <img src="dist/img/user2-160x160.jpg" class="user-image" alt="User Image"> -->
                      <span class="hidden-xs">Welcome <?php echo $username; ?></span>
                    </a>
                    <ul class="dropdown-menu">
                      <!-- User image -->
                                            <!-- Menu Footer-->
                      <li class="user-footer">
                        <!-- <div class="pull-left">
                          <a href="#" class="btn btn-default btn-flat">Profile</a>
                        </div> -->
                        <!-- <div class=""> -->
                        <center>  <a href="<?php echo base_url();?>home/signout" class="">Sign out</a></center>
                        <!-- </div> -->
                      </li>
                    </ul>
                  </li>
                  <!-- Control Sidebar Toggle Button -->

                </ul>
              </div>
            </nav>
          </header>
          <!-- Left side column. contains the logo and sidebar -->
          <aside class="main-sidebar">
            <!-- sidebar: style can be found in sidebar.less -->
            <section class="sidebar">
              <!-- Sidebar user panel -->
              <!-- <div class="user-panel">
                <div class="pull-left image">
                  <img src="dist/img/user2-160x160.jpg" class="img-circle" alt="User Image">
                </div>
                <div class="pull-left info">
                  <p>Alexander Pierce</p>
                  <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
                </div>
              </div> -->

              <!-- /.search form -->
              <!-- sidebar menu: : style can be found in sidebar.less -->
              <ul class="sidebar-menu">
                <li class="header">MAIN NAVIGATION</li>

<?php if(isset($menu)){
  foreach ($menu as $m) { ?>

    <li class="active treeview">
      <a href="<?php echo base_url().'home/'.$m['fungsi'] ;?>">
         <span><?php echo $m['description'] ?></span> <?php if($m['submenu']!=0){ echo '<i class="fa fa-angle-left pull-right"></i>';} ?>
      </a>
      <?php if($m['submenu']!=0){
  echo  '<ul class="treeview-menu" style="display:none">';
        foreach ($m['submenu'] as $sm) {

            echo'<li><a href="'.base_url().'home/'.$sm['fungsi'].'"><i class="fa fa-circle-o"></i>'.$sm['description'].'</a></li>';

                 }
                 echo '</ul>';
      } ?>

    </li>
<?php  }
} ?>


              </ul>
            </section>
            <!-- /.sidebar -->
          </aside>
