 <!-- /container -->
<!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
<!--<script src="assets/js/ie10-viewport-bug-workaround.js"></script>-->
<html >
    <head>
        <meta charset="UTF-8">
        <title>SIC | Log in</title>
        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
        <!-- bootstrap 3.0.2 -->
        <link href="<?php echo base_url() ?>assets/bootstrap-3.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
        <!-- font Awesome -->
        <!-- <link href="<?php echo base_url(); ?>assets/css/font-awesome.min.css" rel="stylesheet" type="text/css" /> -->
        <!-- Theme style -->
        <link href="<?php echo base_url(); ?>assets/css/AdminLTE.min.css" rel="stylesheet" type="text/css" />
        <link rel="stylesheet" href="<?php echo base_url() ?>assets/css/sts.css">

        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
          <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
          <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
        <![endif]-->
    </head>
    <body class="bg-grey">

        <div class="form-box" id="login-box">
          <div class="container text-center">
              <div class="box">
                <center><h1>SIC|Seatech</h1></center>
                  <form class="form-signin" method="POST" action="<?php echo site_url() ?>home/signin">
                      <?php
                      if (isset($error)) {
                          echo '<div class="alert alert-danger" role="alert">' . $error . '</div>';
                      }
                      ?>
                      <input type="email" class="form-control" name="email" placeholder="Email" required autofocus value="<?php if (isset($email)) echo $email ?>">
                      <input type="password" class="form-control" name="password" placeholder="Password" style="margin-top:10px" required>
                      <input type="submit" class="btn btn-lg btn-primary btn-block" value="Sign in">
                  </form>

          <!--        <div class="text-center">
                      <h6>Don't have an account? Do create one!</h6>
                      <a href="<?php echo site_url() ?>home/register" class="btn btn-default" role="button">Register</a>
                  </div>-->
              </div>
          </div>

        </div>


        <!-- jQuery 2.0.2 -->
        <script src="<?php echo base_url() ?>assets/js/jquery-1.11.3.min.js"></script>
        <script src="<?php echo base_url() ?>assets/jquery-ui-1.11.4.custom/jquery-ui.min.js"></script>
        <script src="<?php echo base_url() ?>assets/bootstrap-3.3.5/dist/js/bootstrap.min.js" ></script>
        <script src="<?php echo base_url(); ?>assets/js/jquery.form.js" type="text/javascript"></script>
        <script type="text/javascript">
            $('#formlogin').ajaxForm(options);
var options={beforeSubmit:  showRequest,  // pre-submit callback
        success:       showResponse
};
    function showRequest(formData, jqForm, options) {
        var tugname = $('#nama').val();
        var tugtype = $('#bargetype').val();
        var owner = $('#owner').val();
        var status = $('#status').val();
        var from = $('#from').val();
        var to = $('#to').val();

        if (tugname == '') {
            alert('Barge name required');
            $('#nama').focus();
            return false;
        } else
        if (tugtype == '-') {
            alert('Barge type required');
            $('#bargetype').focus();
            return false;
        } else
        if (owner == '-') {
            alert('Owner required');
            $('#owner').focus();
            return false;

        } else if (status == '-')
        {
            alert('Status required');
            $('#status').focus();
            return false;
        } else if (from == '')
        {
            alert('Start Date Contract required');
            $('#from').focus();
            return false;
        }
        else if (to == '')
        {
            alert('End  Date contract required');
            $('#to').focus();
            return false;
        }
//chekbarge();

        $('.loader').show();
        return true;
    }

    function showResponse(data) {


        $('.loader').hide();
        if (data.status == 1) {
             $('#formsavebarge').resetForm();
        $('#imagepreview').attr('src', '');
            $('#addModal').modal('hide');
            alert(data.msg);
            location.reload();
        } else {
            alert(data.msg);
        }

    }
            </script>

    </body>
</html>
