<script>
  $(function () {
    //Initialize Select2 Elements
    $(".select2").select2();
      $(".textarea").wysihtml5();
});
</script>

<form class="form-horizontal" action="<?php echo $action; ?>" id="<?php echo $id_form; ?>" method="POST" enctype="multipart/form-data">
  <div class="box-body">
    <div class='row'>
      <div class="col-md-12">
    <?php

    if(isset($inputform)){
      $x='';
      foreach ($inputform as $input) {

        if(isset($input['class'])){
          $class=  $input['class'];
        }else{
          $class='';
        }
        if($input['type']=='hidden'){
        $x.=  '<input type="'.$input['type'].'"
          class="form-control '.$class.'"
          name="'.$input['name'].'"
          id="'.$input['id'].'"
          value=""
          placeholder="'.$input['label'].'">';
        }else{
          $x.=   '<div class="form-group">
              <label for="input'.$input['name'].'" class="col-sm-2 control-label">'.$input['label'].'</label>
              <div class="col-sm-10">
                <input type="'.$input['type'].'"
                class="form-control '.$class.'"
                name="'.$input['name'].'"
                id="'.$input['id'].'"
                placeholder="'.$input['label'].'">
              </div>
            </div>';
        }

        if($input['type']=='dropdown'){
             if(isset($input['multiple'])){
               $multiple= 'multiple="multiple"';
             }else{
               $multiple='';
             }
          $x.= '<div class="form-group">
                      <label>'.$input['label'].'</label>
                      <div class="col-sm-10">
                        <select class="form-control select2" '.$multiple.' name="'.$input['name'].'" id="'.$input['id'].'" style="width: 100%;">';
                         $x=0;
                          foreach ($input['option'] as $o) {
                            # code...
                              if($x==0){
                                  $x.= '<option value="'.$o['value'].'" selected="selected">'.$o['name'].'</option>';
                              }else{
                                    $x.=   '<option value="'.$o['value'].'">'.$o['name'].'Alaska</option>';
                              }
                            $x++;
                          }

                          $x.= '</select>
                      </div>
                    </div>';
                }
              if($input['type']=='textarea'){
          $x.= '<div class="form-group">
            <label for="exampleInputFile">'.$input['label'].'</label>';
            if(isset($input['options'])&&$input['options']='e'){
        $x.=    '<textarea class="textarea" name = "'.$input['name'].'" id="'.$input['id'].'" placeholder="..." style="width: 100%; height: 200px; font-size: 14px; line-height: 18px; border: 1px solid #dddddd; padding: 10px;"></textarea>';
            }else {
        $x.=    '<textarea class="form-control" name = "'.$input['name'].'" id="'.$input['id'].'" placeholder="..." ></textarea>';
            }

      $x.= '</div>';
}
       }
echo $x;
    }
echo '</div></div>';
    if(isset($form2)){
      echo $form2;
    }
    ?>



  </div><!-- /.box-body -->
  <div class="box-footer">
    <button type="submit" class="btn btn-primary pull-right" id="<?php echo $button['idyes'];?>"style="margin-left:5px"><?php echo $button['labelyes']; ?></button>
    <button  class="btn btn-primary pull-right" id="<?php echo $button['idno'];?>"><?php echo $button['labelno']; ?></button>

  </div><!-- /.box-footer -->
</form>
