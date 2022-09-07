<div class="white-box p-0">
    <div class="row">
        <div class="col-md-12 p-r-0">
            <nav>
                <ul class="showProjectTabs">
                    <li class="projects">
                        <a href="{{ route('admin.projects.show', $project->id) }}"><i class="icon-grid"></i> <span>@lang('modules.projects.overview')</span></a>
                    </li>
                        <li class="projectMembers">
                            <a href="{{ route('admin.project-members.show', $project->id) }}"><i class="icon-people"></i> <span>@lang('modules.projects.members')</span></a>
                        </li>
                    
                    <li class="projectProducts">
                        <a href="{{ route('admin.products-project.show', $project->id) }}"><i class="icon-basket"></i> <span>@lang('app.menu.products')</span></a>
                    </li>
                    
                    @if(in_array('expenses',$modules))
                    <li class="projectExpenses"><a href="{{ route('admin.project-expenses.show', $project->id) }}"><i class="ti-shopping-cart" aria-hidden="true"></i> @lang('app.menu.expenses')</a></li>
                    @endif
                    
                    @if(in_array('tasks',$modules))
                        <li class="projectTasks">
                            <a href="{{ route('admin.tasks.show', $project->id) }}"><i class="ti-check-box"></i> <span>@lang('app.menu.tasks')</span></a>
                        </li>
                        <li class="projectTasksBoard">
                            <a href="{{ route('admin.taskboard.show', $project->id) }}"><i class="ti-check-box"></i> <span>Task Board</span></a>
                        </li>
                    @endif
                    
<!--                    <li style="display: none" class="projectRooms">
                        <a href="{{ route('admin.rooms.show', $project->id) }}"><i class="icon-plus"></i> <span>@lang('modules.projects.rooms')</span></a>
                    </li>-->
                     <li class="discussion">
                        <a href="{{ route('admin.projects.discussion', $project->id) }}"><i class="ti-comments"></i>
                            <span>@lang('modules.projects.discussion')</span></a>
                    </li>
                    @if(in_array('timelogs',$modules))
                        <li class="projectTimelogs">
                            <a href="{{ route('admin.time-logs.show', $project->id) }}"><i class="ti-alarm-clock"></i> <span>@lang('app.menu.timeLogs')</span></a>
                        </li>
                    @endif
                    
                    
                    <li class="projectEstimates">
                        <a href="{{ route('admin.estimates-project.show', $project->id) }}"><i class="ti-file"></i> <span>Estimates</span></a>
                    </li>
                    
                    @if(in_array('invoices',$modules))
                        <li class="projectInvoices">
                            <a href="{{ route('admin.invoices-project.show', $project->id) }}"><i class="ti-file"></i> <span>@lang('app.menu.invoices')</span></a>
                        </li>
                    @endif 
                    
                    <li class="projectPurchaseOrders">
                        <a href="{{ route('admin.purchase-orders-project.show', $project->id) }}"><i class="ti-receipt"></i> <span>Purchase Order</span></a>
                    </li>
                    
                    @if(in_array('payments',$modules))
                    <li class="projectPayments"><a href="{{ route('admin.project-payments.show', $project->id) }}"><i class="fa fa-money" aria-hidden="true"></i> @lang('app.menu.payments')</a></li>
                    @endif

                    <li class="projectMilestones">
                        <a href="{{ route('admin.milestones.show', $project->id) }}"><i class="icon-flag"></i> <span>@lang('modules.projects.milestones')</span></a>
                    </li>
                    
                    <li class="projectFiles">
                        <a href="{{ route('admin.files.show', $project->id) }}"><i class="ti-files"></i> <span>Project Files</span></a>
                    </li>
                    <li class="projectProductReview">
                        <a href="{{ route('admin.product-review-project.show', $project->id) }}"><i class="ti-receipt"></i> <span>Product Review</span></a>
                    </li>
<!--                    <li class="projectNotes">
                        <a href="{{ route('admin.projects.notes', $project->id) }}"><i class="ti-notepad"></i>
                            <span>Project Notes</span></a>
                    </li>
                <li class="burndownChart"><a href="{{ route('admin.projects.burndown-chart', $project->id) }}"><i class="icon-graph" aria-hidden="true"></i>Burndown</a>
                </li>-->

                <li class="gantt">
                    <a href="{{ route('admin.projects.gantt', $project->id) }}"><i class="fa fa-bar-chart"></i>
                        <span>@lang('modules.projects.viewGanttChart')</span></a>
                </li>
                
                <li class="creditNotes">
                    <a href="{{ route('admin.project.credit-notes', $project->id) }}"><i class="fa fa-bar-chart"></i>
                        <span>@lang('app.menu.credit-note')</span></a>
                </li>
                
<!--                 <li class="projectVisionboards">
                    <a href="{{ route('admin.visionboards.show', $project->id) }}"><i class="fa fa-bar-chart"></i>
                        <span>Visionboard</span></a>
                </li>-->
                
                </ul>
            </nav>
        </div>

        <div class="col-md-1 text-center tabs-more" style="display:none">
            <div class="btn-group dropdown m-r-10">
                <button aria-expanded="false" data-toggle="dropdown" class="btn dropdown-toggle waves-effect waves-light" type="button"><i class="ti-more"></i></button>
                <ul role="menu" class="dropdown-menu pull-right">
                <li><a href="{{ route('admin.projects.burndown-chart', $project->id) }}"><i class="icon-graph" aria-hidden="true"></i> @lang('modules.projects.burndownChart')</a>
                </li>
                @if(in_array('expenses',$modules))
                  <li><a href="{{ route('admin.project-expenses.show', $project->id) }}"><i class="ti-shopping-cart" aria-hidden="true"></i> @lang('app.menu.expenses')</a></li>
                @endif
    
                @if(in_array('payments',$modules))
                    <li><a href="{{ route('admin.project-payments.show', $project->id) }}"><i class="fa fa-money" aria-hidden="true"></i> @lang('app.menu.payments')</a></li>
                @endif

                <li class="gantt">
                    <a href="{{ route('admin.projects.gantt', $project->id) }}"><i class="fa fa-bar-chart"></i>
                        <span>@lang('modules.projects.viewGanttChart')</span></a>
                </li>
                </ul>
            </div>
        </div>
    </div>
    
   
</div>