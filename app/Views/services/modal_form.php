<?php echo form_open(get_uri("services/save"), array("id" => "service-form", "class" => "general-form bg-white", "role" => "form")); ?>
<div class="modal-body clearfix">
    <div class="container-fluid">
        <input type="hidden" name="id" value="<?php echo $model_info->id; ?>" />
        <div class="form-group">
            <div class="row">
                <label for="title" class=" col-md-3"><?php echo app_lang('service_name'); ?></label>
                <div class=" col-md-9">
                    <?php
                    echo form_input(array(
                        "id" => "title",
                        "name" => "title",
                        "value" => $model_info->title,
                        "class" => "form-control",
                        "placeholder" => app_lang('service_name'),
                        "maxlength" => 255,
                        "autofocus" => true,
                        "data-rule-required" => true,
                        "data-msg-required" => app_lang("field_required"),
                    ));
                    ?>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="row">
                <label for="slug" class=" col-md-3"><?php echo app_lang("slug"); ?></label>
                <div class=" col-md-9">
                    <?php
                    echo form_input(array(
                        "id" => "slug",
                        "name" => "slug",
                        "value" => $model_info->slug,
                        "class" => "form-control",
                        "placeholder" => app_lang("service_slug_placeholder"),
                        "maxlength" => 255,
                    ));
                    ?>
                    <span class="text-off font-12"><?php echo app_lang("service_slug_help"); ?></span>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="row">
                <label for="short_description" class=" col-md-3"><?php echo app_lang('short_description'); ?></label>
                <div class=" col-md-9">
                    <?php
                    echo form_textarea(array(
                        "id" => "short_description",
                        "name" => "short_description",
                        "value" => $model_info->short_description ? $model_info->short_description : "",
                        "class" => "form-control",
                        "rows" => 3,
                        "placeholder" => app_lang('short_description'),
                    ));
                    ?>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="row">
                <label for="service_description" class=" col-md-3"><?php echo app_lang('full_description'); ?></label>
                <div class=" col-md-9">
                    <?php
                    echo form_textarea(array(
                        "id" => "service_description",
                        "name" => "description",
                        "value" => $model_info->description ? $model_info->description : "",
                        "class" => "form-control"
                    ));
                    ?>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="row">
                <label for="price_type" class=" col-md-3"><?php echo app_lang('price_type'); ?></label>
                <div class="col-md-9">
                    <?php
                    echo form_dropdown("price_type", $price_types_dropdown, $model_info->price_type ? $model_info->price_type : "fixed", "class='select2' id='price_type'");
                    ?>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="row">
                <label for="price" class=" col-md-3"><?php echo app_lang('price'); ?></label>
                <div class=" col-md-9">
                    <div class="input-group">
                    <span class="input-group-text"><?php echo get_setting("currency_symbol") ? get_setting("currency_symbol") : "$"; ?></span>
                    <?php
                    echo form_input(array(
                        "id" => "price",
                        "name" => "price",
                        "value" => ($model_info->price === "" || is_null($model_info->price)) ? "" : to_decimal_format($model_info->price),
                        "class" => "form-control",
                        "placeholder" => app_lang('price'),
                        "data-rule-required" => true,
                        "data-msg-required" => app_lang("field_required"),
                    ));
                    ?>
                    </div>
                    <span id="price-custom-quote-help" class="text-off font-12 hide"><?php echo app_lang("service_custom_quote_help"); ?></span>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="row">
                <label for="icon" class=" col-md-3"><?php echo app_lang('icon'); ?></label>
                <div class=" col-md-9">
                    <?php
                    echo form_input(array(
                        "id" => "icon",
                        "name" => "icon",
                        "value" => $model_info->icon,
                        "class" => "form-control",
                        "placeholder" => app_lang('icon'),
                        "maxlength" => 255,
                    ));
                    ?>
                    <span class="text-off font-12"><?php echo app_lang("service_icon_help"); ?></span>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="row">
                <label for="sort_order" class=" col-md-3"><?php echo app_lang('sort_order'); ?></label>
                <div class=" col-md-9">
                    <?php
                    echo form_input(array(
                        "id" => "sort_order",
                        "name" => "sort_order",
                        "type" => "number",
                        "step" => 1,
                        "value" => $model_info->sort_order ? $model_info->sort_order : 0,
                        "class" => "form-control",
                        "placeholder" => app_lang('sort_order'),
                        "data-rule-digits" => true,
                        "data-msg-digits" => app_lang("service_sort_order_invalid"),
                    ));
                    ?>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="row">
                <label for="status" class=" col-md-3"><?php echo app_lang('status'); ?></label>
                <div class="col-md-9">
                    <?php
                    $status_dropdown = array("active" => app_lang("active"), "inactive" => app_lang("inactive"));
                    echo form_dropdown("status", $status_dropdown, $model_info->status ? $model_info->status : "active", "class='select2' id='status'");
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-default" data-bs-dismiss="modal"><span data-feather="x" class="icon-16"></span> <?php echo app_lang('close'); ?></button>
    <button type="submit" class="btn btn-primary"><span data-feather="check-circle" class="icon-16"></span> <?php echo app_lang('save'); ?></button>
</div>
<?php echo form_close(); ?>

<script type="text/javascript">
    $(document).ready(function () {
        $("#service-form").appForm({
            beforeAjaxSubmit: function (data) {
                $.each(data, function (index, obj) {
                    if (obj.name === "description") {
                        data[index]["value"] = encodeAjaxPostData(getWYSIWYGEditorHTML("#service_description"));
                    }
                });
            },
            onSuccess: function (result) {
                $("#services-table").appTable({newData: result.data, dataId: result.id});
            }
        });

        initWYSIWYGEditor("#service_description", {height: 250});
        setTimeout(function () {
            $("#title").focus();
        }, 200);
        $("#service-form .select2").select2();

        var makeSlug = function (text) {
            return $.trim(text).toLowerCase()
                    .replace(/[^a-z0-9\s-]/g, "")
                    .replace(/[\s-]+/g, "-")
                    .replace(/^-+|-+$/g, "");
        };

        //auto generate slug from the service name until the slug is edited manually
        var slugEdited = !!$.trim($("#slug").val());
        $("#slug").on("input", function () {
            slugEdited = !!$.trim($(this).val());
        }).on("blur", function () {
            $(this).val(makeSlug($(this).val()));
        });
        $("#title").on("input", function () {
            if (!slugEdited) {
                $("#slug").val(makeSlug($(this).val()));
            }
        });

        //price isn't applicable for custom quote
        var togglePriceField = function () {
            var isCustomQuote = $("#price_type").val() === "custom_quote";
            $("#price").prop("disabled", isCustomQuote);
            $("#price-custom-quote-help").toggleClass("hide", !isCustomQuote);
            if (isCustomQuote) {
                $("#price").val("").removeClass("error").closest(".form-group").find("label.error").remove();
            }
        };
        $("#price_type").on("change", togglePriceField);
        togglePriceField();
    });
</script>
