<?php $rand = date("is"); ?>
<script src="{{ URL::to('js/adminformbuilder/jquery.min.js') }}"></script>
<script src="{{ URL::to('js/adminformbuilder/jquery-ui.min.js') }}"></script>
<script src="{{ URL::to('js/adminformbuilder/form-builder-cf.min.js') }}?rand={{$rand}}"></script>
<script src="{{ URL::to('js/jquery.tagsinput-admin.js') }}?rand={{$rand}}"></script>
<link rel="stylesheet" href="{{ URL::to('css/jquery.tagsinput-admin.css') }} " />

<script>
    var json_html = <?php echo $data['html']; ?>

    function setTagField(id) {
        $("#" + id + " .field-options ol .option-value").tagsInput();

    }


    function setFieldChange(id) {
        // $("#" + id + " .field-options ol .option-value").attr('readonly', true);
        $("#" + id + " .field-options ol .option-image").attr('readonly', true);

    }


    $('body').on('change', ".ui-sortable-handle .option-logo", function(event) {

        var target = event.target;
        console.log(target);
        var nextInput = target.nextElementSibling;
        console.log(nextInput);
        var elmId = $(this).attr("id");
        var file_data = $("#" + elmId).prop('files')[0];
        var form_data = new FormData();
        form_data.append('file', file_data);
        // var statusElement = $("#" + elmId).closest(".field-option");

        // AJAX request
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            url: '{{Url::to("/logo")}}', // Point to the server-side PHP script 
            dataType: 'json',
            cache: false,
            processData: false,
            contentType: false,
            data: form_data,
            type: 'post',

            success: function(output) {

                $('#' + elmId).prop('type', 'text').val(output.name);
                nextInput.value = output.name;
            },

        });
    });
    // $('body').on('click', ".ui-sortable-handle .icon_type", function() {
    // alert('hdjsf');
    // var elmId = $(this).attr("id");
    // // alert(elmId);
    // var i = 1;
    // var file_data = $("#" + elmId).prop('files')[0];
    // var form_data = new FormData();
    // form_data.append('file', file_data);
    // console.log(form_data);
    // $.ajaxSetup({
    //     headers: {
    //         'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    //     }
    // });
    // $.ajax({

    //     url: '{{Url::to("/logo")}}', // point to server-side PHP script 
    //     dataType: 'json', // what to expect back from the PHP script, if anything
    //     cache: false,
    //     processData: false,
    //     contentType: false,
    //     data: form_data,
    //     type: 'post',
    //     success: function(output) {
    //         console.log(elmId);
    //         console.log(output.name);
    //         $('#' + elmId).prop('type', 'text');
    //         $('#' + elmId).val(output.name);
    //     //display response from the PHP script, if any
    //     }
    // });

    // i++;
    // });
    // 




    /* disable name of name field */
    $("#build-wrap").bind("DOMSubtreeModified", function() {
        $('.option-logo').attr('readonly', true);
        $('.option-image').attr('readonly', true);
        $('.fld-name').attr('readonly', true);
        $('.fld-name').css("background", "#efefef");
    });

    $("body").delegate(".field-options ol", "DOMNodeInserted", function() {
        FieldId = $(".field-brandwithmodel-preview").parent('.prev-holder').parent('li').attr('id');
        if ((FieldId != "undefined") || (FieldId != "") || (FieldId != null)) {
            FieldDivId = FieldId + "-holder";
            setTagField(FieldDivId);
            $("#" + FieldDivId + " .field-options ol li").css("cssText", "display: flow-root !important;width: 100% !important;");
        }
    });

    var userAttrs = {};

    var options = {

        formData: json_html,
        dataType: 'json',

        // onAddOption: (optionTemplate, optionIndex) => {

        //     // optionTemplate.logo = optionIndex.value;
        //     if(optionTemplate.logo !== undefined && optionTemplate.logo.length > 0) {

        //         optionTemplate.logo = optionTemplate.logo
        //         return optionTemplate
        //     }else{
        //         optionTemplate.logo = `logo-${optionIndex.index + 1}`
        //         return optionTemplate
        //     }
        //     return optionTemplate
        //           },


        onOpenFieldEdit: function(editPanel) {
            var FieldClassName = $(editPanel).find('.fld-className').val();
            var str1 = FieldClassName;
            var str2 = "inputtags";
            if (str1.indexOf(str2) != -1) {
                FieldId = $(".field-brandwithmodel-preview").parent('.prev-holder').parent('li').attr('id');
                FieldDivId = FieldId + "-holder";
                setTagField(FieldDivId);
                $("#" + FieldDivId + " .field-options ol li").css("cssText", "display: flow-root !important;width: 100% !important;");
                // $("#" + FieldDivId + " .field-options ol .option-value").tagsInput();
            }

            var getid = $(editPanel).attr('id');
            // $("#" + getid + " .field-options ol .option-value").attr('readonly', true);
            $("#" + getid + " .field-options ol .option-logo").prop('type', 'file').addClass("option-logo option-attr");


            var counter = 0;
            $("#" + getid + " .field-options ol .option-logo").each(function() {
                counter++;
                var self = $(this);
                self.attr('id', "icon_" + counter);
            });

            var image_count = 0;
            $("#" + getid + " .field-options ol .option-image").each(function() {
                image_count++;
                var self = $(this);
                self.attr('id', "images_" + image_count);
            });
            setFieldChange(getid);
        },

        onAddOption: (optionTemplate, optionIndex) => {
              console.log('empty',optionTemplate)
            if (optionTemplate.logo) {
            // if (optionTemplate.logo || optionTemplate.logo.length != 0) {

                optionTemplate.logo = optionTemplate.logo;
                //optionTemplate.image = optionTemplate.image;
                if(optionTemplate.logo != `noimage50.png`){
                    optionTemplate.image = optionTemplate.logo;
                }else{
                    optionTemplate.image = optionTemplate.image;
                }
                //   console.log(optionTemplate.image);
            }
            if (!optionTemplate.logo || optionTemplate.logo.length === 0) {
                optionTemplate.logo = `noimage50.png`;
                optionTemplate.image = `noimage50.png`;
            }

            // console.log('final',optionTemplate);
            return optionTemplate;

        },


        // ... All your options here ...
        disabledAttrs: ['multiple', 'access', 'description'],
        inputSets: [{
            label: 'Brands With Models',
            showHeader: false, // optional
            fields: [{
                type: 'select',
                label: 'Brands',
                name: "brandwithmodel",
                className: 'inputtags form-control',
                multiple: false,
                values: [{
                        label: 'Brand Name',
                        value: '',
                        selected: false
                    },
                    {
                        label: 'Brand Name',
                        value: '',
                        selected: false
                    }
                ],
            }, ]
        }, ],
    }

    var formBuilder = $('#build-wrap').formBuilder(options);
    // console.log(formBuilder);

    //Fill old data - begin
    setTimeout(function() {
        formBuilder.actions.setData(json_html);
    }, 1000);
    //Fill old data - end


    document.getElementById('post_json_insert').addEventListener('click', function() {
        var jsondata = formBuilder.actions.getData('json');
        // console.log(jsondata);
        var jsonCount = jQuery.parseJSON(jsondata).length;
        var jsonFinal = (jsonCount == 0) ? '' : jsondata;
        $('#id_cathtml').val(jsonFinal);
        $('#id_catfieldcount').val(jsonCount);
    });
    //cancel begin
    $("#post_cancel").click(function() {
        location.href = document.referrer;
    });
    //cancel end
</script>

<style>
    li.input-set-control::before {
        content: '\e806';
        font-family: "formbuilder-icons";
    }

    .tagsinput {
        margin-left: 29px;
        margin-top: 10px;
    }
    
    .tag-input:focus,
    .tag-input.error {
        outline: none;
        border: unset !important;
    }
</style>