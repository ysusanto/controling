<script>
    $('#formsavemember').ajaxForm(options);
</script>

<div class="modal-dialog" >
    <div class="modal-content" >
        <div class="modal-header" style="background-color: #48b5e9;" >
            <button type="button" class="close" style="color:#fff" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" style="font-color:#fff">&times;</span></button>
            <h4 class="modal-title"  style="color:#fff">Project</h4>
        </div>
        <form class="form-inline" action='<?php echo base_url(); ?>projects/saveMemberAssignment' id='formsavemember' method="POST">
            <div class="modal-body">
                <!--<div id='alert' class="alert alert-danger alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button><div id='alertdt'></div>...</div>-->
                <div id="checkboxplatform" class="row" style="margin-bottom: 5px;margin-top: 5px;">
                    <div class="col-md-4"><label for="exampleInputName2">Member</label></div>
                    <div class="col-md-8">
                        <?php
//                        echo sizeof($member);
                        if (isset($member)) {
                            echo '<input type="hidden" value="" id="viewAddMemberModalHidden" name="addmember_group_id" />';
                            echo '<input type="hidden" value="" id="viewAddMemberModalItemidHidden" name="addmember_itemid" />';
                            foreach ($member as $m) {
                                echo '<div class="checkbox" style="margin-left: 5px">
                                        <label>
                                            <input type="checkbox" name="member[]" value="' . $m['user_id'] . '">' .
                                            $m['salutation'] . ' ' . $m['firstname'] . '
                                        </label>
                                    </div> <br>';
                            }
                        }
                        ?>
                    </div>
                </div>
            </div>
            <!-- <input type="hidden" name="groupid" /> -->
            <div class = "modal-footer">
                <button class="btn btn-primary" type="button" onclick="closemodal()">Cancel</button>
                <button class="btn btn-primary" type="submit" >Save</button>
            </div>
        </form>

    </div><!--/.modal-content -->
</div><!--/.modal-dialog -->
