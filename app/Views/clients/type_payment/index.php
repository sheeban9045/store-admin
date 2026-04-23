  

<!-- <div>

   <div class="form-group">
        <div class="row">
            <label for="gender" class="abc"><?php echo app_lang('type_payment'); ?></label>
            <div class="abc">
                <?php
                echo form_radio(array(
                    "id" => "gender_male",
                    "name" => "gender",
                    "class" => "form-check-input"
                        ));                ?>
                <label for="gender_male" class="mr15 p0"><?php echo app_lang('monthly_payment'); ?></label> <?php
                echo form_radio(array(
                    "id" => "gender_female",
                    "name" => "gender",
                    "class" => "form-check-input"
                       ));
                ?>
                <label for="gender_female" class="p0"><?php echo app_lang('weekly_payment'); ?></label>
            </div>
        </div>
    </div>

                        </div>
 -->




                          <div class="form-group">
                        <div class="row">
                            <label for="type_payment" class=" col-md-2"><?php echo app_lang('type_payment'); ?></label>
                            <div class="col-md-10">
                                <?php
                                echo form_dropdown(
                                        "type_payment", array(
                                    "monthly_payment" => app_lang("monthly_payment"),
                                    "weekly_payment" => app_lang("weekly_payment")
                                        ), get_setting('type_payment'), "class='select2 mini'"
                                );
                                ?>
                            </div>
                        </div>
                    </div>



