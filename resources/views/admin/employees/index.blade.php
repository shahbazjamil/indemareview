@extends('layouts.app')

@push('head-script')
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.13/css/dataTables.bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.1.1/css/responsive.bootstrap.min.css">
<link rel="stylesheet" href="//cdn.datatables.net/buttons/1.2.2/css/buttons.dataTables.min.css">
<link rel="stylesheet" href="//cdn.datatables.net/buttons/1.2.2/css/buttons.dataTables.min.css">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/custom-select/custom-select.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/multiselect/css/multi-select.css') }}">

<script src="https://js.chargebee.com/v2/chargebee.js" data-cb-site="indema" ></script>
@endpush

@section('page-title')
    <div class="row bg-title">
        <!-- .page title -->
        <div class="border-bottom col-xs-12">
            <!--<h4 class="page-title"><i class="{{ $pageIcon }}"></i> {{ __($pageTitle) }} </h4>-->
            <h4 class="page-title"><i class="{{ $pageIcon }}"></i> Your Team</h4>
        </div>
        <!-- /.page title -->
        <!-- .breadcrumb -->
        <div @if($totalRecords == 0) style="display: none;"  @endif class="border-bottom col-xs-12 p-t-10 p-b-10">
            @if($isMaxLimit == 1)
<!--                <a href="javascript::void(0)" class="btn btn-outline btn-success btn-sm" data-cb-type="checkout" data-cb-plan-id="additional-user" data-cb-plan-quantity="1" >+ @lang('modules.employees.addNewEmployee')</a>-->
<a href="javascript:;" id="create-additional-user"  class="btn btn-outline btn-success btn-sm" >+ @lang('modules.employees.addNewEmployee')</a>

            @else
                <a href="{{ route('admin.employees.create') }}" class="btn btn-outline btn-success btn-sm">+ @lang('modules.employees.addNewEmployee') </a>
            @endif
            
            <ol class="breadcrumb">
                <li><a href="{{ route('admin.dashboard') }}">@lang('app.menu.home')</a></li>
                <li class="active">{{ __($pageTitle) }}</li>
            </ol>
        </div>
        <!-- /.breadcrumb -->
    </div>
@endsection

@section('filter-section')
                <div class="row"  id="ticket-filters">
                   
                    <form action="" id="filter-form">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="control-label">@lang('app.status')</label>
                                <select class="form-control select2" name="status" id="status" data-style="form-control">
                                    <option value="all">@lang('modules.client.all')</option>
                                    <option value="active">@lang('app.active')</option>
                                    <option value="deactive">@lang('app.inactive')</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="control-label">@lang('modules.employees.title')</label>
                                <select class="form-control select2" name="employee" id="employee" data-style="form-control">
                                    <option value="all">@lang('modules.client.all')</option>
                                    @forelse($employees as $employee)
                                        <option value="{{$employee->id}}">{{ ucfirst($employee->name) }}</option>
                                    @empty
                                    @endforelse
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="control-label">@lang('app.skills')</label>
                                <select class="select2 select2-multiple " multiple="multiple"
                                        data-placeholder="Choose Skills" name="skill[]" id="skill" data-style="form-control">
                                    <option value="all">@lang('modules.client.all')</option>
                                    @forelse($skills as $skill)
                                        <option value="{{$skill->id}}">{{ ucfirst($skill->name) }}</option>
                                    @empty
                                    @endforelse
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="control-label">@lang('modules.employees.role')</label>
                                <select class="form-control select2" name="role" id="role" data-style="form-control">
                                    <option value="all">@lang('modules.client.all')</option>
                                    @forelse($roles as $role)
                                        @if ($role->id <= 3)
                                            <option value="{{$role->id}}">{{ __('app.' . $role->name) }}</option>
                                        @else
                                            <option value="{{$role->id}}">{{ ucfirst($role->name )}}</option>
                                        @endif
                                    @empty
                                    @endforelse
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="control-label">@lang('app.designation')</label>
                                <select class="form-control select2" name="designation" id="designation" data-style="form-control">
                                    <option value="all">@lang('modules.client.all')</option>
                                    @forelse($designations as $designation)
                                        <option value="{{$designation->id}}">{{ ucfirst($designation->name) }}</option>
                                    @empty
                                    @endforelse
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="control-label">@lang('app.department')</label>
                                <select class="form-control select2" name="department" id="department" data-style="form-control">
                                    <option value="all">@lang('modules.client.all')</option>
                                    @forelse($departments as $department)
                                        <option value="{{$department->id}}">{{ ucfirst($department->team_name) }}</option>
                                    @empty
                                    @endforelse
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group ">
                                <button type="button" id="apply-filters" class="btn btn-success col-md-6"><i class="fa fa-check"></i> @lang('app.apply')</button>
                                <button type="button" id="reset-filters" class="btn btn-inverse col-md-5 col-md-offset-1"><i class="fa fa-refresh"></i> @lang('app.reset')</button>
                            </div>
                        </div>
                    </form>
                </div>
                @endsection

@section('content')

    <div @if($totalRecords == 0) style="display: none;"  @endif class="row dashboard-stats front-dashboard">
        <div class="col-md-12 m-b-30">
            <div class="white-box p-0">
                <div class="col-sm-6 text-center">
                    <h4 class="white-box"><span class="text-info-">{{ $totalEmployees }}</span> <span class="font-12 text-muted- m-l-5"> @lang('modules.dashboard.totalEmployees')</span></h4>
                </div>
                <div class="col-sm-6 text-center">
                    <h4 class="white-box"><span class="text-danger-">{{ $freeEmployees }}</span> <span class="font-12 text-muted- m-l-5"><a href="{{ route('admin.employees.freeEmployees') }}" > @lang('modules.dashboard.freeEmployees')</a></span></h4>
                </div>
                
            </div>
        </div>

    </div>

    <div @if($totalRecords == 0) style="display: none;"  @endif class="row">
       
        <div class="col-md-12">
            <div class="white-box">
                
                <div class="table-responsive">
                    {!! $dataTable->table(['class' => 'table table-bordered table-hover toggle-circle default footable-loaded footable']) !!}
                </div>
            </div>
        </div>
    </div>
    <!-- .row -->
    
    <div @if($totalRecords > 0) style="display: none;"  @endif class="row flex-row flex-wrap nolist-content flex-align-start">
		<div class="col-md-4"><img src="{{ asset('img/employee-management.jpg') }}" class="img-responsive" alt="" /></div>
		<div class="col-md-8">
			<h1 class="page-title m-b-30">Employee Management</h1>
			<p class="m-b-30">'Users' are added into indema as employees. You can see all information, activity and progress for employees/users. Set custom roles and permissions to determine what each user sees within the platform.</p>
			<a href="{{ route('admin.employees.create') }}" class="btn-black">+ @lang('modules.employees.addNewEmployee') </a>
			<a href="javascript:;" onclick="$('#video-modal').modal('show')" class="btn-black">See how it works <i class="fa fa-play"></i></a>
		</div><!--end of col-5-->
		<div class="col-md-12 text-right">
			Have Questions? <a href="mailto:support@indema.co">Contact Support</a>
		</div><!--end of col-12-->
	</div><!--end of row-->
    
    <div class="modal fade bs-modal-md in" id="video-modal" tabindex="-1" role="dialog" aria-labelledby="video-modal"
         aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
				<div class="modal-header p-t-15 p-b-15 p-r-15">
					<h4 class="modal-title">Employee Management</h4>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				</div>
                <div class="modal-body p-2"></div>
            </div>
        </div>
    </div>
    
    
    <div class="modal fade accounts-modal" id="add-additional-user" tabindex="-1" role="dialog" aria-labelledby="add-management">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="sm-management-heading">Additional Users</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row flex-row">
                            <div class="col-sm-12 col-xs-12 text-center">
                                <div class="border">
                                    <h2>HUB<span>$7/User</span></h2>
                                    <div class="form-group">
                                        <label class="required">Number of Users</label>
                                        <input type="number" name="number_of_users" id="number_of_users" value="1" min="1" class="form-control" autocomplete="nope">
                                    </div>
                                    <div>
                                        <span>&nbsp;</span>
                                    </div>
                                    
                                </div><!--end of border-->
                            </div>
                        </div><!--end of row-->
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn default" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-success" id="additionalUserBtn">Save</button>
                    </div>
                </div>
            </div>
    </div>
        
    
    

@endsection

@push('footer-script')
<script src="{{ asset('plugins/bower_components/datatables/jquery.dataTables.min.js') }}"></script>
<script src="https://cdn.datatables.net/1.10.13/js/dataTables.bootstrap.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.1.1/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.1.1/js/responsive.bootstrap.min.js"></script>
<script src="{{ asset('plugins/bower_components/custom-select/custom-select.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/multiselect/js/jquery.multi-select.js') }}"></script>
<script src="https://cdn.datatables.net/buttons/1.0.3/js/dataTables.buttons.min.js"></script>
<script src="{{ asset('js/datatables/buttons.server-side.js') }}"></script>

{!! $dataTable->scripts() !!}
<script>
    
    function updateUerNumbers(obj){
        var qty = obj.value;
       $('#additional_user_btn').attr('data-cb-plan-quantity',qty);
    }

    $(".select2").select2({
        formatNoMatches: function () {
            return "{{ __('messages.noRecordFound') }}";
        }
    });
    var table;

    $(function() {
        loadTable();

        $('body').on('click', '.sa-params', function(){
            var id = $(this).data('user-id');
            swal({
                title: "Are you sure?",
                text: "You will not be able to recover the deleted user!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes, delete it!",
                cancelButtonText: "No, cancel please!",
                closeOnConfirm: true,
                closeOnCancel: true
            }, function(isConfirm){
                if (isConfirm) {

                    var url = "{{ route('admin.employees.destroy',':id') }}";
                    url = url.replace(':id', id);

                    var token = "{{ csrf_token() }}";

                    $.easyAjax({
                        type: 'POST',
                            url: url,
                            data: {'_token': token, '_method': 'DELETE'},
                        success: function (response) {
                            if (response.status == "success") {
                                $.easyBlockUI('#employees-table');
                                loadTable();
                                $.easyUnblockUI('#employees-table');
                            }
                        }
                    });
                }
            });
        });

        $('body').on('click', '.assign_role', function(){
            var id = $(this).data('user-id');
            var role = $(this).data('role-id');
            var token = "{{ csrf_token() }}";


            $.easyAjax({
                url: '{{route('admin.employees.assignRole')}}',
                type: "POST",
                data: {role: role, userId: id, _token : token},
                success: function (response) {
                    if(response.status == "success"){
                        $.easyBlockUI('#employees-table');
                        loadTable();
                        $.easyUnblockUI('#employees-table');
                    }
                }
            })

        });
    });
    function loadTable(){
        window.LaravelDataTables["employees-table"].draw();
    }

    $('.toggle-filter').click(function () {
        $('#ticket-filters').toggle('slide');
    })

    $('#apply-filters').click(function () {
        $('#employees-table').on('preXhr.dt', function (e, settings, data) {
            var employee = $('#employee').val();
            var status   = $('#status').val();
            var role     = $('#role').val();
            var skill     = $('#skill').val();
            var designation     = $('#designation').val();
            var department     = $('#department').val();
            data['employee'] = employee;
            data['status'] = status;
            data['role'] = role;
            data['skill'] = skill;
            data['designation'] = designation;
            data['department'] = department;
        });
        loadTable();
    });

    $('#reset-filters').click(function () {
        $('#filter-form')[0].reset();
        $('#status').val('all');
        $('.select2').val('all');
        $('#filter-form').find('select').select2();
        loadTable();
    })

    function exportData(){

        var employee = $('#employee').val();
        var status   = $('#status').val();
        var role     = $('#role').val();

        var url = '{{ route('admin.employees.export', [':status' ,':employee', ':role']) }}';
        url = url.replace(':role', role);
        url = url.replace(':status', status);
        url = url.replace(':employee', employee);

        window.location.href = url;
    }

     $('#video-modal').on('show.bs.modal', function (e) {
      var idVideo = $(e.relatedTarget).data('id');
      $('#video-modal .modal-body').html('<div class="embed-responsive embed-responsive-16by9"><iframe width="560" height="315" src="https://www.youtube.com/embed/DbS2rMSMvd4" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope;" allowfullscreen></iframe></div>');
    });
    //on close remove
    $('#video-modal').on('hidden.bs.modal', function () {
       $('#video-modal .modal-body').empty();
    }); 
    
    
    $(document).on('click', '#create-additional-user', function (){
            $('#add-additional-user').modal('show');
    });
    
    $(document).on('click', '#additionalUserBtn', function (){
            var number_of_users = $('#number_of_users').val();
            $.easyAjax({
                url: '{{route('admin.billing.add-additional-users', $additionalUserPlan->stripe_monthly_plan_id)}}',
                container: '#companySettings',
                type: "POST",
                data: { 'number_of_users' : number_of_users, '_token': '{{ csrf_token() }}' },
                file:true,
                success: function (response) {
                    if(response.status == 'success'){
                        location.reload();
                    }
                }
            })
        });
    
    
</script>
@endpush