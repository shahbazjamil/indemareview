<div class="modal fade bs-modal-lg in createEmailTemplate" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">
    <div class="modal-dialog modal-lg" id="modal-data-application">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i>
                </button>
                <span class="caption-subject font-red-sunglo bold uppercase"
                      id="modelHeading">Create email template</span>
            </div>
            <div class="modal-body">
                {!! Form::open(['id'=>'createEmailTemplateForm', 'class'=>'ajax-form', 'method'=>'POST', 'enctype' => 'multipart/form-data']) !!}
                <div class="row">
                    <div class="col-md-12">
                        <div class="edit-email-modal" style="padding: 15px;">
                            <label for="">Template Name</label>
                            <div class="form-group">
                                <input type="text" name="template_name" class="form-control">
                            </div>
                            <hr>
                            <label for="">Subject</label>
                            <div class="form-group">
                                <input type="text" name="subject" class="form-control">
                            </div>
                            <hr>
                            <label for="">Select email type</label>
                            <div class="form-group">
                                <select name="email_type" class="form-control select-email">
                                    <option value="1">Send email</option>
                                    <option value="2">Send file via email</option>
                                </select>
                            </div>

                            <div class="display-none email-file-div">
                                <hr>
                                <div class="row">
                                    <div class="col-xs-6">
                                        <label for="">File</label>
                                        <div class="form-group">
                                            <input type="file" class="file form-control" name="file">
                                        </div>
                                    </div>
                                    <div class="col-xs-6">
                                        <div class="form-group">
                                            <img src="{{ asset('img/img-dummy.jpg') }}" class="display-file-preview"
                                                 alt="file" width="100" height="100">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <hr>
                            <p class="text-muted m-b-30 font-13">You can use following variables in your email template.</p>
                            <div class="display:block">&lbrace;&lbrace;client.first.name&rbrace;&rbrace;</div>
{{--                            <div class="display:block">&lbrace;&lbrace;client.email&rbrace;&rbrace;</div>--}}
{{--                            <div class="display:block">&lbrace;&lbrace;client.password&rbrace;&rbrace;</div>--}}
{{--                            <div class="display:block">&lbrace;&lbrace;client.login_to_dashboard&rbrace;&rbrace;</div>--}}
                            <div class="display:block m-b-15">&lbrace;&lbrace;lead.name&rbrace;&rbrace;</div>
                            <label for="">Body</label>
                            <div class="form-group">
                                <textarea name="body" id="" cols="30" rows="10"
                                          class="summernote create-email-template-body">
                                </textarea>
                            </div>
                        </div>
                    </div>
                </div>
                {!! Form::close() !!}
            </div>
            <div class="modal-footer">
                <button type="button" class="btn default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-success" id="creatEmailTemplateBtn">Save</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->.
</div>
