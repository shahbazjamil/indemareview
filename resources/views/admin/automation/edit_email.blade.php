<div class="modal fade bs-modal-md in editEmailTemplate" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md" id="modal-data-application">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
                <span class="caption-subject font-red-sunglo bold uppercase edit-email-preview-heading" id="modelHeading">Edit email</span>
            </div>
            <div class="modal-body">
                    {!! Form::open(['id'=>'editEmailTemplateForm', 'class'=>'ajax-form', 'method'=>'POST', 'enctype' => 'multipart/form-data']) !!}
                    @csrf
                    <div class="row">
                        <div class="col-md-12">
                            <span class="d-flex" style="font-size: 20px;font-weight: 700;">
                            <div class="edit-email-modal-mail-icon">
                                <i class="icon-envelope fa-fw"></i>
                            </div>
                                Edit email
                            </span>
                            <span class="d-block m-b-15">By editing this email here, you are creating a new template and disconnecting this template from the original.</span>
                            <div class="edit-email-modal">
                                <input type="hidden" name="edit_email_template_id" class="edit-email-template-id">
                                <div class="d-flex" style="padding: 12px;    align-items: center;border-bottom: 1px solid rgb(238, 239, 241);">
                                    <span class="text-muted" style="font-weight: 700;width: 16%;">SUBJECT :</span>
                                    <input class="edit-email-preview-subject form-control" name="edit_email_template_subject" />
                                </div>
                                <div class="" style="padding: 10px;border-bottom: 1px solid rgb(238, 239, 241);">
                                    <textarea class="edit-email-preview-body summernote" name="edit_email_template_body">

                                    </textarea>
                                </div>
                                <div class="">
                                    <div class="row">
                                        <div class="col-xs-4">
                                            <select class="form-control edit-email-preview_email-template" name="edit_email_template">
                                                @foreach($emailTemplates as $emailTemplate)
                                                    <option value="{{ $emailTemplate->id }}">{{ $emailTemplate->template_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-xs-6">
                                            <div class="edit-email-template-image-upload-div">
                                                <div class="col-xs-3" style="text-align:start">
                                                    <a href="#" class="edit-email-template-link-tag" target="_blank">
                                                        <img src="" alt="file" class="edit-email-template-img-preview" style="width: 40px;height: 40px;object-fit: cover;padding: 2px;">
                                                    </a>
                                                </div>
                                                <div class="" style="text-align: end">
                                                    <label style="margin: 0;padding: 10px; cursor: pointer;">
                                                        <i class="fa fa-paperclip" style="font-size: 20px;"></i>
                                                        <input type="file" name="file" class="edit-email-template-file" style="display: none">
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xs-2">
                                            <button type="button" class="btn blue edit-email-template-btn" id="save" style="margin-top: 3px;">Done</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                {!! Form::close() !!}
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->.
</div>