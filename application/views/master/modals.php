<div class="modal fade <?php $a=(isset($class)?  $class: '' ); echo $a;?>" id="<?php echo $id ?>" tabindex="-1" role="dialog" aria-labelledby="addItemModal" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #48b5e9;">
                <button type="button" class="close" style="color:#fff" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" >&times;</span></button>
                <h4 class="modal-title" id="myModalLabel" style="color:#fff" ><?php echo $title;?></h4>
            </div>
            <div class="modal-body" style="">
              <?php echo $content; ?>
            </div>

        </div>
    </div>
</div>
