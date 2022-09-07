@extends('layouts.client-app')

@section('page-title')
    <div class="row bg-title">
        <!-- .page title -->
        <div class="border-bottom col-xs-12">
            <h4 class="page-title"><i class="{{ $pageIcon }}"></i> {{ __($pageTitle) }} #{{ $project->id }} - <span
                        class="font-bold">{{ ucwords($project->project_name) }}</span></h4>
        </div>
        <!-- /.page title -->
        <!-- .breadcrumb -->
        <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12">
            <ol class="breadcrumb">
                <li><a href="{{ route('client.dashboard.index') }}">@lang('app.menu.home')</a></li>
                <li><a href="{{ route('client.projects.index') }}">{{ __($pageTitle) }}</a></li>
                <li class="active">@lang('app.menu.invoices')</li>
            </ol>
        </div>
        <!-- /.breadcrumb -->
    </div>
@endsection
@push('head-script')
<link rel="stylesheet" href="{{ asset('plugins/bower_components/custom-select/custom-select.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/multiselect/css/multi-select.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/summernote/dist/summernote.css') }}">
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.13/css/dataTables.bootstrap.min.css">
<link rel="stylesheet" href="//cdn.datatables.net/buttons/1.2.2/css/buttons.dataTables.min.css">
<style>
    .swal-footer {
        text-align: center !important;
    }
</style>
@endpush
@section('content')

    <div class="row">
        <div class="col-md-12">

            <section>
                <div class="sttabs tabs-style-line client-ptabs">

                    <div class="white-box p-0">
                        <nav>
                            <ul class="showProjectTabs">
                                <li><a href="{{ route('client.projects.show', $project->id) }}"><span>@lang('modules.projects.overview')</span></a>
                                </li>
                                @if(in_array('employees',$modules))
                                    <li><a href="{{ route('client.project-members.show', $project->id) }}"><span>@lang('modules.projects.members')</span></a></li>
                                @endif

                                @if(in_array('tasks',$modules))
                                    <li c><a href="{{ route('client.tasks.edit', $project->id) }}"><span>@lang('app.menu.tasks')</span></a></li>
                                @endif

                                <li><a href="{{ route('client.files.show', $project->id) }}"><span>@lang('modules.projects.files')</span></a></li>
                                @if(in_array('timelogs',$modules))
<!--                                    <li><a href="{{ route('client.time-log.show', $project->id) }}"><span>@lang('app.menu.timeLogs')</span></a></li>-->
                                @endif
                                
                                <li class="tab-current" ><a href="{{ route('client.product-review-project.edit', $project->id) }}"><span>Product Review</span></a></li>

                                @if(in_array('invoices',$modules))
                                    <li ><a href="{{ route('client.project-invoice.show', $project->id) }}"><span>@lang('app.menu.invoices')</span></a></li>
                                @endif
                                <li>
                                    <a href="{{ route('client.projects.discussion', $project->id) }}">
                                        <span>@lang('modules.projects.discussion')</span></a>
                                </li>

                            </ul>
                        </nav>
                    </div>

                    <div class="content-wrap">
                        <section id="section-line-3" class="show">
							<div class="row">
                                <div class="col-md-3 product-sidebar" id="task-list-panel">
									<div class="category-box">
										<h3>Master bedroom 1</h3>
										<div class="summary-area">
											<span>
												<img src="{{ asset('img/checkbox-product.png') }}" alt=""> 1/1
											</span>
											<span>
												<img src="{{ asset('img/commets-icon.png') }}" alt=""> 0
											</span>
										</div><!--end of sunmmary-area-->
									</div><!--end of category-box-->
									<div class="category-box">
										<h3>Bedroom 2</h3>
										<div class="summary-area">
											<span>
												<img src="{{ asset('img/checkbox-product.png') }}" alt=""> 1/1
											</span>
											<span>
												<img src="{{ asset('img/commets-icon.png') }}" alt=""> 0
											</span>
										</div><!--end of sunmmary-area-->
									</div><!--end of category-box-->
									<div class="category-box">
										<h3>Kitchen</h3>
										<div class="summary-area">
											<span>
												<img src="{{ asset('img/checkbox-product.png') }}" alt=""> 1/1
											</span>
											<span>
												<img src="{{ asset('img/commets-icon.png') }}" alt=""> 0
											</span>
										</div><!--end of sunmmary-area-->
									</div><!--end of category-box-->
									<div class="category-box">
										<h3>Bathroom</h3>
										<div class="summary-area">
											<span>
												<img src="{{ asset('img/checkbox-product.png') }}" alt=""> 1/1
											</span>
											<span>
												<img src="{{ asset('img/commets-icon.png') }}" alt=""> 0
											</span>
										</div><!--end of sunmmary-area-->
									</div><!--end of category-box-->
									<div class="category-box">
										<h3>Living room</h3>
										<div class="summary-area">
											<span>
												<img src="{{ asset('img/checkbox-product.png') }}" alt=""> 1/1
											</span>
											<span>
												<img src="{{ asset('img/commets-icon.png') }}" alt=""> 0
											</span>
										</div><!--end of sunmmary-area-->
									</div><!--end of category-box-->
                                </div><!--end of product-sidebar-->
								<div class="product-con-area col-md-9">
									<div class="w-100 product-review-f">
										<div>
											<label>Select Status</label>
											<select>
												<option value="all">All</option>
											</select>
										</div>
										<div>
											<a href="https://app.indema.co/admin/contracts/create" class="btn btn-outline btn-success btn-sm"><i class="fa fa-unlock"></i> Unlock from Client Portal</a>
											<a href="https://app.indema.co/admin/contracts/create" class="btn btn-outline btn-success btn-sm"><i class="fa fa-lock"></i> Lock from Client Portal</a>
											<a href="https://app.indema.co/admin/contracts/create" class="btn btn-outline btn-success btn-sm">+ Create Estimate of Approval</a>
										</div>
									</div><!--end of product-review-f-->
									<div class="row">
										<div class="col-md-3">
											<div class="category-box">
												<div data-toggle="modal" data-target="#subTaskModal"><img src="{{ asset('img/in-dproduct.jpg') }}" class="img-responsive" alt=""></div>
												<div class="summary-area">
													<span>
														<input type="checkbox" />
													</span>
													<span>
														<i class="fa fa-times-circle disable"></i>
														<i class="fa fa-check-circle"></i>
														<img src="{{ asset('img/commets-icon.png') }}" alt=""> 0
													</span>
													<span class="cost">Cost:$1,250.00</span>
												</div><!--end of sunmmary-area-->
											</div><!--end of category-box-->
										</div><!--end of col-3-->
										<div class="col-md-3">
											<div class="category-box">
												<img src="{{ asset('img/in-dproduct.jpg') }}" class="img-responsive" alt="">
												<div class="summary-area">
													<span>
														<input type="checkbox" />
													</span>
													<span>
														<i class="fa fa-times-circle disable"></i>
														<i class="fa fa-check-circle"></i>
														<img src="{{ asset('img/commets-icon.png') }}" alt=""> 0
													</span>
													<span class="cost">Cost:$1,250.00</span>
												</div><!--end of sunmmary-area-->
											</div><!--end of category-box-->
										</div><!--end of col-3-->
										<div class="col-md-3">
											<div class="category-box">
												<img src="{{ asset('img/in-dproduct.jpg') }}" class="img-responsive" alt="">
												<div class="summary-area">
													<span>
														<input type="checkbox" />
													</span>
													<span>
														<i class="fa fa-times-circle"></i>
														<i class="fa fa-check-circle disable"></i>
														<img src="{{ asset('img/commets-icon.png') }}" alt=""> 0
													</span>
													<span class="cost">Cost:$1,250.00</span>
												</div><!--end of sunmmary-area-->
											</div><!--end of category-box-->
										</div><!--end of col-3-->
										<div class="col-md-3">
											<div class="category-box">
												<img src="{{ asset('img/in-dproduct.jpg') }}" class="img-responsive" alt="">
												<div class="summary-area">
													<span>
														<input type="checkbox" />
													</span>
													<span>
														<i class="fa fa-times-circle disable"></i>
														<i class="fa fa-check-circle"></i>
														<img src="{{ asset('img/commets-icon.png') }}" alt=""> 0
													</span>
													<span class="cost">Cost:$1,250.00</span>
												</div><!--end of sunmmary-area-->
											</div><!--end of category-box-->
										</div><!--end of col-3-->
										<div class="col-md-3">
											<div class="category-box">
												<img src="{{ asset('img/in-dproduct.jpg') }}" class="img-responsive" alt="">
												<div class="summary-area">
													<span>
														<input type="checkbox" />
													</span>
													<span>
														<i class="fa fa-times-circle disable"></i>
														<i class="fa fa-check-circle"></i>
														<img src="{{ asset('img/commets-icon.png') }}" alt=""> 0
													</span>
													<span class="cost">Cost:$1,250.00</span>
												</div><!--end of sunmmary-area-->
											</div><!--end of category-box-->
										</div><!--end of col-3-->
										<div class="col-md-3">
											<div class="category-box">
												<img src="{{ asset('img/in-dproduct.jpg') }}" class="img-responsive" alt="">
												<div class="summary-area">
													<span>
														<input type="checkbox" />
													</span>
													<span>
														<i class="fa fa-times-circle disable"></i>
														<i class="fa fa-check-circle disable"></i>
														<img src="{{ asset('img/commets-icon.png') }}" alt=""> 0
													</span>
													<span class="cost">Cost:$1,250.00</span>
												</div><!--end of sunmmary-area-->
											</div><!--end of category-box-->
										</div><!--end of col-3-->
										<div class="col-md-3">
											<div class="category-box">
												<img src="{{ asset('img/in-dproduct.jpg') }}" class="img-responsive" alt="">
												<div class="summary-area">
													<span>
														<input type="checkbox" />
													</span>
													<span>
														<i class="fa fa-times-circle disable"></i>
														<i class="fa fa-check-circle"></i>
														<img src="{{ asset('img/commets-icon.png') }}" alt=""> 0
													</span>
													<span class="cost">Cost:$1,250.00</span>
												</div><!--end of sunmmary-area-->
											</div><!--end of category-box-->
										</div><!--end of col-3-->
										<div class="col-md-3">
											<div class="category-box">
												<img src="{{ asset('img/in-dproduct.jpg') }}" class="img-responsive" alt="">
												<div class="summary-area">
													<span>
														<input type="checkbox" />
													</span>
													<span>
														<i class="fa fa-times-circle disable"></i>
														<i class="fa fa-check-circle"></i>
														<img src="{{ asset('img/commets-icon.png') }}" alt=""> 0
													</span>
													<span class="cost">Cost:$1,250.00</span>
												</div><!--end of sunmmary-area-->
											</div><!--end of category-box-->
										</div><!--end of col-3-->
									</div><!--end of row-->
								</div><!--end of product-con-area-->
                            </div><!--end of row-->
                        </section>

                    </div><!-- /content -->
                </div><!-- /tabs -->
            </section>
        </div>


    </div>
    <!-- .row -->

    {{--Ajax Modal--}}
    <div class="modal fade bs-modal-md in"  id="subTaskModal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-md" id="modal-data-application">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times-circle"></i></button>
                </div>
                <div class="modal-body">
                    <span style="display:none">Loading...</span>
					<img src="{{ asset('img/in-dproduct.jpg') }}" class="img-responsive" alt="">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn default" data-dismiss="modal">Close</button>
                    <button type="button" class="btn blue">Save changes</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->.
    </div>
    {{--Ajax Modal Ends--}}


@endsection

@push('footer-script')
<script src="{{ asset('js/cbpFWTabs.js') }}"></script>
<script src="{{ asset('plugins/bower_components/custom-select/custom-select.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/summernote/dist/summernote.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/datatables/jquery.dataTables.min.js') }}"></script>
<script src="https://cdn.datatables.net/1.10.13/js/dataTables.bootstrap.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.1.1/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.1.1/js/responsive.bootstrap.min.js"></script>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

<script type="text/javascript">
    var newTaskpanel = $('#new-task-panel');
    var taskListPanel = $('#task-list-panel');
    var editTaskPanel = $('#edit-task-panel');

    $(".select2").select2({
        formatNoMatches: function () {
            return "{{ __('messages.noRecordFound') }}";
        }
    });

    $('.summernote').summernote({
        height: 100,                 // set editor height
        minHeight: null,             // set minimum height of editor
        maxHeight: null,             // set maximum height of editor
        focus: false,
        toolbar: [
            // [groupName, [list of button]]
            ['style', ['bold', 'italic', 'underline', 'clear']],
            ['font', ['strikethrough']],
            ['fontsize', ['fontsize']],
            ['para', ['ul', 'ol', 'paragraph']],
            ["view", ["fullscreen"]]
        ]
    });

   

    
    $('#tasks-table').on('click', '.show-task-detail', function () {
        $(".right-sidebar").slideDown(50).addClass("shw-rside");

        var id = $(this).data('task-id');
        var url = "{{ route('client.tasks.show',':id') }}";
        url = url.replace(':id', id);

        $.easyAjax({
            type: 'GET',
            url: url,
            success: function (response) {
                if (response.status == "success") {
                    $('#right-sidebar-content').html(response.view);
                }
            }
        });
    })

    jQuery('#due_date, #start_date').datepicker({
        format: '{{ $global->date_picker_format }}',
        autoclose: true,
        todayHighlight: true
    });

   

   

    //    save new task
    taskListPanel.on('click', '.edit-task', function () {
        var id = $(this).data('task-id');
        var url = "{{route('client.tasks.ajax-edit', ':id')}}";
        url = url.replace(':id', id);

        $.easyAjax({
            url: url,
            type: "GET",
            container: '#task-list-panel',
            data: {taskId: id},
            success: function (data) {
                editTaskPanel.html(data.html);
                // taskListPanel.switchClass("col-md-12", "col-md-6", 1000, "easeInOutQuad");
                newTaskpanel.addClass('hide').removeClass('show');
                editTaskPanel.switchClass("hide", "show", 300, "easeInOutQuad");
                $("body").tooltip({
                    selector: '[data-toggle="tooltip"]'
                });

                $('html, body').animate({
                    scrollTop: $("#task-list-panel").offset().top
                }, 1000);
            }
        })
    });

 $('#dependent-task').change(function () {
        if($(this).is(':checked')){
            $('#dependent-fields').show();
        }
        else{
            $('#dependent-fields').hide();
        }
    })

    $('#show-new-task-panel').click(function () {
        editTaskPanel.addClass('hide').removeClass('show');
        newTaskpanel.switchClass("hide", "show", 300, "easeInOutQuad");

        $('html, body').animate({
            scrollTop: $("#task-list-panel").offset().top
        }, 1000);
    });

    $('#hide-new-task-panel').click(function () {
        newTaskpanel.addClass('hide').removeClass('show');
        taskListPanel.switchClass("col-md-6", "col-md-12", 1000, "easeInOutQuad");
    });

    editTaskPanel.on('click', '#hide-edit-task-panel', function () {
        editTaskPanel.addClass('hide').removeClass('show');
        taskListPanel.switchClass("col-md-6", "col-md-12", 1000, "easeInOutQuad");
    });

</script>
@endpush
