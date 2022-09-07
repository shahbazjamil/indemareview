<style>
.text-muted {
    color: #728197 !important;
}
.pre-custom {
    background: #d4dbe3;
    padding: 1rem;
    border: none;
    width: 100% !important;
}
.rounded {
    border-radius: .25rem !important;
}
*{box-sizing:border-box;}
			form{max-width:600px;margin:0 auto;font: 400 13.3333px Arial;padding:0 15px;font-family:Arial;}
			form .form-group{margin:0 -15px;display:flex;align-items:center;}
			form .form-group>*{margin-bottom:10px}form .form-group>label{padding:0 15px;width:100px}
			.form-control {
				display: block;
				width: 100%;
				height: calc(1.5em + .75rem + 2px);
				padding: .375rem .75rem;
				font-size: 1rem;
				font-weight: 400;
				line-height: 1.5;
				color: #495057;
				background-color: #fff;
				background-clip: padding-box;
				border: 1px solid #ced4da;
				border-radius: .25rem;
				transition: border-color .15s ease-in-out,box-shadow .15s ease-in-out;
				}
				.submit{background:#007bff;border:none;height:35px;width:auto;padding:0 30px;margin:0 auto;display:block;line-height:37px;border-radius:5px;color:#FFF}
</style>
<?php 

$form_html ='<style>*{box-sizing:border-box;}
form{max-width:600px;margin:0 auto;font: 400 13.3333px Arial;padding:0 15px;font-family:Arial;}
form .form-group{margin:0 -15px;display:flex;align-items:center;}
form .form-group>*{margin-bottom:10px}form .form-group>label{padding:0 15px;width:100px}
.form-control {display: block;width: 100%;height: calc(1.5em + .75rem + 2px); padding: .375rem .75rem;font-size: 1rem;font-weight: 400;line-height: 1.5;color: #495057;background-color: #fff;background-clip: padding-box;border: 1px solid #ced4da;border-radius: .25rem;transition: border-color .15s ease-in-out,box-shadow .15s ease-in-out;} .submit{background:#007bff;border:none;height:35px;width:auto;padding:0 30px;margin:0 auto;display:block;line-height:37px;border-radius:5px;color:#FFF}</style>';

$form_html .='<form action="#">';
$form_html .='<div class="form-group"><label>Name</label><input type="text" name="lead_name" id="lead_name" value="" class="form-control"></div>';
$form_html .='<div class="form-group"><label>Email</label><input type="text" name="lead_email" id="lead_email" value="" class="form-control"></div>';
$form_html .='<div class="form-group"><label>Phone</label><input type="text" name="lead_phone" id="lead_phone" value="" class="form-control"></div>';
$form_html .='<div class="form-group"><label>Message</label><input type="text" name="lead_message" id="lead_message" value="" class="form-control"></div>';

if($leadForm) {
    foreach ($leadForm as $form) {
        $label_name = ucfirst($form->field_name);
        $label_id = $form->id;
        $form_html .='<div class="form-group"><label>'.$label_name.'</label><input type="text" name="lead_extra[]" id="lead_extra-'.$label_id.'" value="" class="form-control"></div>';
    }
}

$form_html .='</form>';

?>

<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h4 class="modal-title">Copy @lang('modules.lead.leadForm')</h4>
</div>
<div class="modal-body">
    <div class="portlet-body">        
        <div class="form-body">
            <div class="row">
                <div class="col-12">
                        <p class="text-muted">Copy and paste the following Code Snippet on th conact us page of your website.</p>
                        <form action="#">
                        <div class="form-group"><label>Name</label><input type="text" name="lead_name" id="lead_name" value="" class="form-control"></div>
                        <div class="form-group"><label>Email</label><input type="text" name="lead_email" id="lead_email" value="" class="form-control"></div>
                        <div class="form-group"><label>Phone</label><input type="text" name="lead_phone" id="lead_phone" value="" class="form-control"></div>
                        <div class="form-group"><label>Message</label><input type="text" name="lead_message" id="lead_message" value="" class="form-control"></div>
                        <?php  if($leadForm) { 
                            foreach ($leadForm as $form) {
                                $label_name = ucfirst($form->field_name);
                                $label_id = $form->id;
                                echo '<div class="form-group"><label>'.$label_name.'</label><input type="text" name="lead_extra[]" id="lead_extra-'.$label_id.'" value="" class="form-control"></div>';
                            }
                        }
                        ?>
                        
                        </form>
<textarea id="form_html_script" readonly="" class="pre-custom rounded">
<?php echo '<iframe  style="border: none; width: 100%;" src="' . route('leadpublic.get-form-data',user()->company_id) . '" height="400" ></iframe>';?>
</textarea>               
            </div>
            </div>
        </div>
    </div>
</div>
<div style="text-align : center;" class="modal-footer">
    <button type="button" id="copy-group" class="btn btn-copy" data-clipboard-target="#form_html_script" data-copied="Copied!"> <i class="fa fa-code"></i> Copy Form</button>
   
</div>
<script src="{{ asset('plugins/clipboard/clipboard.min.js') }}"></script>

<script>
    var clipboard = new ClipboardJS('.btn-copy');

    clipboard.on('success', function(e) {
        var copied = "<?php echo __("app.copied") ?>";
        // $('#copy_payment_text').html(copied);
        $.toast({
            heading: 'Success',
            text: copied,
            position: 'top-right',
            loaderBg:'#ff6849',
            icon: 'success',
            hideAfter: 3500
        });
    });
</script>
