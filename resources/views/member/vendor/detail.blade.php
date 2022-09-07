<div id="event-detail">

    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title"><i class="fa fa-flag"></i> Vendor @lang('app.details')</h4>
    </div>
    <div class="modal-body">
        {!! Form::open(['id'=>'updateClient','class'=>'ajax-form','method'=>'PUT']) !!}
        <div class="form-body">
            <h3 class="box-title ">Vendor Details</h3>
            <hr>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Vendor Rep Name</label>
                        <p>{{ $vendorDetail->vendor_rep_name }}</p>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label>Rep Email Address</label>
                        <p>{{ $vendorDetail->rep_email }}</p>
                    </div>
                </div>
                <!--/span-->
            </div>

            <h3 class="box-title m-t-20">COMPANY OTHER DETAILS</h3>
            <hr>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label">Company Name</label>
                        <p>{{ $vendorDetail->company_name ?? '' }}</p>
                    </div>
                </div>
                <!--/span-->
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label">Website</label>
                        <p>{{ $vendorDetail->company_website ?? '' }}</p>
                    </div>
                </div>
                <!--/span-->
            </div>
            <!--/row-->
            <div class="row">
                <div class="col-xs-12">
                    <div class="form-group">
                        <label class="control-label">Address</label>
                        <p>{{ $vendorDetail->company_address ?? '' }}</p>
                    </div>
                </div>
                <!--/span-->

            </div>
            <!--/row-->
            <h3 class="box-title m-t-20">VENDOR OTHER DETAILS</h3>
            <hr>

            <!--/row-->

            <div class="row" style="display: none;">

                <div class="col-md-3">
                    <div class="form-group">
                        <label>Skype</label>
                        <p>{{ $vendorDetail->vendor_skype ?? '' }}</p>
                    </div>
                </div>
                <!--/span-->

                <div class="col-md-3">
                    <div class="form-group">
                        <label>Linkedin</label>
                        <p>{{ $vendorDetail->vendor_linkedIn ?? '' }}</p>
                    </div>
                </div>
                <!--/span-->

                <div class="col-md-3">
                    <div class="form-group">
                        <label>Twitter</label>
                        <p>{{ $vendorDetail->vendor_twitter ?? '' }}</p>
                    </div>
                </div>
                <!--/span-->

                <div class="col-md-3">
                    <div class="form-group">
                        <label>Facebook</label>
                        <p>{{ $vendorDetail->vendor_facebook ?? '' }}</p>
                    </div>
                </div>
                <!--/span-->
            </div>
            <!--/row-->
            <!--row gst number-->
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="gst_number">GST Number</label>
                        <p>{{ $vendorDetail->vendor_gst_number ?? '' }}</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Phone Number</label>
                        <p>{{ $vendorDetail->rep_phone }}</p>
                    </div>
                </div>
                <div class="col-md-4">
                        <div class="form-group">
                            <label>Vendor Status</label>
                            <p>{{ ucfirst($vendorDetail->status) }}</p>
                        </div>
                </div>
                <!--/span-->
            </div>
            <div class="row">
                
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="vendor_category">Category</label>
                        <p>{{ $vendorDetail->vendor_category ?? '' }}</p>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="vendor_category">Markup %</label>
                        <p>{{ $vendorDetail->vendor_markup ?? 0 }}</p>
                    </div>
                </div>
                
                
                
            </div>
            
            <!--/row-->

            <div class="row">
                <div class="col-md-12">
                    <label>Shipping Address</label>
                    <div class="form-group">
                        <p>{{$vendorDetail->shipping_address ?? ''}}</p>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <label>Note</label>
                    <div class="form-group">
                        <p>{{ $vendorDetail->vendor_note ?? '' }}</p>
                    </div>
                </div>
            </div>
        </div>
        {!! Form::close() !!}
    </div> <div class="modal-footer">
        <button type="button" class="btn btn-white waves-effect" data-dismiss="modal">Close</button>
    </div>

</div>
