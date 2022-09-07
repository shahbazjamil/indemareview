<link rel="stylesheet" href="{{ asset('plugins/bower_components/summernote/dist/summernote.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-tagsinput/dist/bootstrap-tagsinput.css') }}">


<!--<div class="rpanel-title"> @lang('app.task') <span><i class="ti-close right-side-toggle"></i></span> </div>-->
<div class="r-panel-body">
<span><i class="ti-close right-side-toggle"></i></span>
    <div class="row">
		<div class="col-md-8">
        <div class="row">
			<div class="col-xs-12">
            <ul class="nav customtab nav-tabs" role="tablist">
                <li role="presentation" class="active"><a href="#home1" aria-controls="home" role="tab" data-toggle="tab" aria-expanded="true">@lang('app.task')</a></li>
                <li role="presentation" class=""><a href="#profile1" aria-controls="profile" role="tab" data-toggle="tab" aria-expanded="false">@lang('modules.tasks.subTask')({{ count($task->subtasks) }})</a></li>
                <li style="display : none;" role="presentation" class=""><a href="#messages1" aria-controls="messages" role="tab" data-toggle="tab" aria-expanded="false">@lang('app.file') ({{ sizeof($task->files) }})</a></li>
                <li style="display : none;" role="presentation" class=""><a href="#settings1" aria-controls="settings" role="tab" data-toggle="tab" aria-expanded="false">@lang('modules.tasks.comment') ({{ count($task->comments) }})</a></li>
                <li role="presentation" class=""><a href="#notes1" aria-controls="note" role="tab" data-toggle="tab" aria-expanded="false">@lang('app.notes') ({{ count($task->notes) }})</a></li>
                <li role="presentation" class="pull-right">  <a href="javascript:;" id="view-task-history" data-task-id="{{ $task->id }}" class="pull-right text-muted font-12 btn btn-sm btn-default btn-outline"> <i class="fa fa-history"></i> <span class="hidden-xs">@lang('app.view') @lang('modules.tasks.history')</span></a></li>
                <li role="presentation" class="pull-right" >  <a href="javascript:;" class="close-task-history pull-right text-muted" style="display:none"><span class="hidden-xs">@lang('app.close') @lang('modules.tasks.history')</span> <i class="fa fa-times"></i></a></li>
            </ul>
			</div>
            <div class="col-xs-12">
                <h5>{{ ucwords($task->heading) }}
                    

                </h5>
                @if(!is_null($task->project_id))
                    <p><i class="icon-layers"></i> {{ ucfirst($task->project->project_name) }}</p>
                @endif
            </div>
    
            <div class="tab-content" id="task-detail-section">
                <div role="tabpanel" class="tab-pane fade active in" id="home1">
    
                    <div class="col-xs-12" >
                        <div class="row">
                            <div class="col-xs-6 col-md-3 font-12">
                                <div><label class="font-12" for="">@lang('app.description')</label><a href="javascript:void(0)" id="description_edit_btn" class="btn btn-info btn-sm">Edit Description</a></div>
                                <div id="description_text" class="col-xs-12 task-description p-10 m-t-20">
                                    {!! $task->description !!}
                                </div>
                                <div style="display: none;" id="description_editor" class="col-xs-12">
                                    <textarea id="description" name="description" class="summernote_description">{{ $task->description }}</textarea>
                                    <a href="javascript:;" id="close_description" class="btn btn-info btn-sm">Close</a>
                                    <a href="javascript:;" id="submit_description" class="btn btn-info btn-sm">@lang('app.submit')</a>
                                </div>
                            </div>
                            
                            <div class="col-xs-6 col-md-3 font-12">
                                <label class="font-12" for="">File Attachments</label><br>
                                <div class="col-xs-12 task-description p-10 m-t-20">
                                   
                                    <div class="row" id="list">
                                        <ul class="list-group" id="files-list">
                                            @forelse($taskFiles as $file)
                                            <li class="list-group-item">
                                                <div class="row">
                                                    <div class="col-md-9">
                                                        {{ $file->filename }}
                                                    </div>
                                                    <div class="col-md-3">

                                                            <a target="_blank" href="{{ $file->file_url }}"
                                                               data-toggle="tooltip" data-original-title="View"
                                                               class="btn btn-info btn-circle"><i
                                                                        class="fa fa-search"></i></a>


                                                        @if(is_null($file->external_link))
                                                        <a href="{{ route('admin.task-files.download', $file->id) }}"
                                                           data-toggle="tooltip" data-original-title="Download"
                                                           class="btn btn-default btn-circle"><i
                                                                    class="fa fa-download"></i></a>
                                                        @endif

                                                        <a href="javascript:;" data-toggle="tooltip" data-original-title="Delete" data-file-id="{{ $file->id }}"
                                                           data-pk="list" class="btn btn-danger btn-circle sa-delete"><i class="fa fa-times"></i></a>
                                                        <span class="clearfix m-l-10">{{ $file->created_at->diffForHumans() }}</span>
                                                    </div>
                                                </div>
                                            </li>
                                            @empty
                                                <li class="list-group-item">
                                                    <div class="row">
                                                        <div class="col-md-10">
                                                            @lang('messages.noFileUploaded')
                                                        </div>
                                                    </div>
                                                </li>
                                            @endforelse
                                        </ul>
                                    </div>
                                    
                                    
                                </div>
                            </div>
                            
                            <div class="col-xs-6 col-md-3 font-12">
                                <label class="font-12" for="">Comments</label><br>
                                <div class="col-xs-12 task-description p-10 m-t-20">
                                <div class="col-xs-12" id="comment-container">
                                        <div id="comment-list">
                                            @forelse($task->comments as $comment)
                                                <div class="row b-b m-b-5 font-12">
                                                    <div class="col-xs-12">
                                                        <h5>{{ ucwords($comment->user->name) }} <span class="text-muted font-12">{{ ucfirst($comment->created_at->diffForHumans()) }}</span></h5>
                                                    </div>
                                                    <div class="col-xs-10">
                                                        {!! ucfirst($comment->comment)  !!}
                                                    </div>
                                                    <div class="col-xs-2 text-right">
                                                        <a href="javascript:;" data-comment-id="{{ $comment->id }}" class="text-danger" onclick="deleteComment('{{ $comment->id }}');return false;">@lang('app.delete')</a>
                                                    </div>
                                                </div>
                                            @empty
                                                <div class="col-xs-12">
                                                    @lang('messages.noCommentFound')
                                                </div>
                                            @endforelse
                                        </div>
                                    </div>
    
                            <div class="form-group" id="comment-box">
                                <div class="col-xs-12">
                                    <textarea name="comment" id="task-comment" class="summernote" placeholder="@lang('modules.tasks.comment')"></textarea>
                                </div>
                                <div class="col-xs-12">
                                    <a href="javascript:;" id="submit-comment" class="btn btn-info btn-sm">@lang('app.submit')</a>
                                </div>
                            </div>
                                </div>
                            </div>
    
                        </div>
                    </div>
                </div>
                <div role="tabpanel" class="tab-pane" id="profile1">
                    <div class="col-xs-12 m-t-5">
                        <h5><i class="ti-check-box"></i> @lang('modules.tasks.subTask')
                            @if (count($task->subtasks) > 0)
                                <span class="pull-right"><span class="donut" data-peity='{ "fill": ["#00c292", "#eeeeee"],    "innerRadius": 5, "radius": 8 }'>{{ count($task->completedSubtasks) }}/{{ count($task->subtasks) }}</span> <span class="text-muted font-12">{{ floor((count($task->completedSubtasks)/count($task->subtasks))*100) }}%</span></span>
                            @endif
                        </h5>
                        <ul class="list-group b-t" id="sub-task-list">
                            @foreach($task->subtasks as $subtask)
                                <li class="list-group-item row">
                                    <div class="col-xs-9">
                                        <div class="checkbox checkbox-success checkbox-circle task-checkbox">
                                            <input class="task-check" data-sub-task-id="{{ $subtask->id }}" id="checkbox{{ $subtask->id }}" type="checkbox"
                                                @if($subtask->status == 'complete') checked @endif>
                                            <label for="checkbox{{ $subtask->id }}">&nbsp;</label>
                                            <span>{{ ucfirst($subtask->title) }}</span>
                                        </div>
                                        @if($subtask->due_date)<span class="text-muted m-l-5"> - @lang('modules.invoices.due'): {{ $subtask->due_date->format($global->date_format) }}</span>@endif
                                    </div>
    
                                    <div class="col-xs-3 text-right">
                                        <a href="javascript:;" data-sub-task-id="{{ $subtask->id }}" class="edit-sub-task"><i class="fa fa-pencil"></i></a>&nbsp;
                                        <a href="javascript:;" data-sub-task-id="{{ $subtask->id }}" class="delete-sub-task"><i class="fa fa-trash"></i></a>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                    <div class="col-xs-12 m-t-20 m-b-10">
                        <a href="javascript:;"  data-task-id="{{ $task->id }}" class="add-sub-task"><i class="icon-plus"></i> @lang('app.add') @lang('modules.tasks.subTask')</a>
                    </div>
                </div>
                <div role="tabpanel" class="tab-pane" id="messages1">
                    <div class="col-xs-12">
                        <a href="javascript:;" id="uploadedFiles" class="btn btn-primary btn-sm btn-rounded btn-outline"><i class="fa fa-image"></i> @lang('modules.tasks.uplodedFiles') (<span id="totalUploadedFiles">{{ sizeof($task->files) }}</span>) </a>
                    </div>
                </div>
                <div role="tabpanel" class="tab-pane" id="settings1">
                    <div class="col-xs-12">
                        <h5>@lang('modules.tasks.comment')</h5>
                    </div>
    
                    <div class="col-xs-12" id="comment-container">
                        <div id="comment-list">
                            @forelse($task->comments as $comment)
                                <div class="row b-b m-b-5 font-12">
                                    <div class="col-xs-12">
                                        <h5>{{ ucwords($comment->user->name) }} <span class="text-muted font-12">{{ ucfirst($comment->created_at->diffForHumans()) }}</span></h5>
                                    </div>
                                    <div class="col-xs-10">
                                        {!! ucfirst($comment->comment)  !!}
                                    </div>
                                    <div class="col-xs-2 text-right">
                                        <a href="javascript:;" data-comment-id="{{ $comment->id }}" class="text-danger" onclick="deleteComment('{{ $comment->id }}');return false;">@lang('app.delete')</a>
                                    </div>
                                </div>
                            @empty
                                <div class="col-xs-12">
                                    @lang('messages.noCommentFound')
                                </div>
                            @endforelse
                        </div>
                    </div>
    
                    <div class="form-group" id="comment-box">
                        <div class="col-xs-12">
                            <textarea name="comment" id="task-comment" class="summernote" placeholder="@lang('modules.tasks.comment')"></textarea>
                        </div>
                        <div class="col-xs-12">
                            <a href="javascript:;" id="submit-comment" class="btn btn-info btn-sm"><i class="fa fa-send"></i> @lang('app.submit')</a>
                        </div>
                    </div>
                    
                
    
                </div>
                
                <div role="tabpanel" class="tab-pane" id="notes1">
                    <div class="col-xs-12">
                        <h4>@lang('app.notes')</h4>
                    </div>
    
                    <div class="col-xs-12" id="note-container">
                        <div id="note-list">
                            @forelse($task->notes as $note)
                                <div class="row b-b m-b-5 font-12">
                                    <div class="col-xs-12 m-b-5">
                                        <span class="font-semi-bold">{{ ucwords($note->user->name) }}</span> <span class="text-muted font-12">{{ ucfirst($note->created_at->diffForHumans()) }}</span>
                                    </div>
                                    <div class="col-xs-10">
                                        {!! ucfirst($note->note)  !!}
                                    </div>
                                    <div class="col-xs-2 text-right">
                                        <a href="javascript:;" data-comment-id="{{ $note->id }}" class="btn btn-xs  btn-outline btn-default" onclick="deleteNote('{{ $note->id }}');return false;"><i class="fa fa-trash"></i> @lang('app.delete')</a>
                                    </div>
                                </div>
                            @empty
                                <div class="col-xs-12">
                                    @lang('messages.noNoteFound')
                                </div>
                            @endforelse
                        </div>
                    </div>
    
                    <div class="form-group" id="note-box">
                        <div class="col-xs-12 m-t-10">
                            <textarea name="note" id="task-note" class="summernote" placeholder="@lang('app.notes')"></textarea>
                        </div>
                        <div class="col-xs-12">
                            <a href="javascript:;" id="submit-note" class="btn btn-info btn-sm"><i class="fa fa-send"></i> @lang('app.submit')</a>
                        </div>
                    </div>
    
                </div>
                    
                <div role="tabpanel" class="tab-pane" id="history1">
                    <div class="col-xs-12">
                        <label class="font-bold">@lang('modules.tasks.history')</label>
                    </div>
                    <div class="col-xs-12" id="task-history-section">
                    </div>
                </div>
            </div>
    
    
            <div class="col-xs-12" id="task-history-section">
            </div>
        </div>
</div><!--end of col-8-->
       <div class="col-md-4">			
			<div class="sidebar-section user">
				<h3>Assigned Users</h3>
                                <div>
                                    <span id='assigned_users'>
                                        @foreach ($task->users as $memb)
                                            @if($memb->image && $memb->image !='')
                                                <img data-toggle="tooltip" data-original-title="{{ucwords($memb->name)}}" src="{{ $memb->image_url }}" />
                                            @else
                                                <span data-toggle="tooltip" data-original-title="{{ucwords($memb->name)}}" class="nameletter">{{$company_initials}}</span>
                                            @endif
                                        @endforeach
                                    </span>
                                    <span class="new-user">+</span>
                                </div>
				<div class="user-dropdown" style="display:none">
					<span><i class="ti-close user-modal-close"></i></span>
					<div class="popover-header">Assign Users</div>
					<div class="alert alert-info">Only users assigned to the project are shown in this list</div>
					<div class="user-list">
                                            
                                                @if(is_null($task->project_id))
                                                    @foreach($employees as $employee)
                                                    
                                                   
                                                        @php
                                                            $selected = '';
                                                        @endphp

                                                        @foreach ($task->users as $item)
                                                        @if($item->id == $employee->id)
                                                            @php
                                                                $selected = 'checked';
                                                            @endphp
                                                        @endif

                                                @endforeach
                                                
                                                
                                               
                                                

                                                        <label class="custom-control custom-checkbox">
                                                            <input {{ $selected }} type="checkbox" name="user_id" id="user_id_{{ $employee->id }}" class="custom-control-input" value="{{ $employee->id }}">
                                                            <span class="custom-control-description">  
                                                                @if($employee->image && $employee->image !='')
                                                                    <img src="{{$employee->image_url}}" class="img-circle avatar-xsmall">
                                                                @else
                                                                <span class="nameletter">{{$company_initials}}</span>
                                                                 @endif
                                                                {{ ucwords($employee->name) }}
                                                            </span>
                                                            
                                                            
                                                        </label>

                                                    @endforeach
                                                @else
                                                @foreach($task->project->members as $member)
                                                
                                                        @php
                                                            $selected = '';
                                                        @endphp

                                                        @foreach ($task->users as $item)
                                                            @if($item->id == $member->user->id)
                                                                @php
                                                                    $selected = 'checked';
                                                                @endphp
                                                            @endif

                                                        @endforeach
                                                
                                                    <label class="custom-control custom-checkbox">
                                                        <input {{ $selected }} type="checkbox" name="user_id" id="user_id_{{ $member->user->id }}" class="custom-control-input" value="{{ $member->user->id }}">
                                                        <span class="custom-control-description">
                                                            
                                                            @if($member->image_url && $member->image_url !='')
                                                                <img src="{{$member->image_url}}" class="img-circle avatar-xsmall">
                                                            @else
                                                                <span class="nameletter">{{$company_initials}}</span>
                                                            @endif
                                                            
                                                            {{ ucwords($member->user->name) }}
                                                        
                                                        </span>
                                                    </label>
                                                
                                                
                                                @endforeach
                                                @endif
                                            
						
                                            
					</div><!--end of user-list-->
                                        <div><a href="javascript:void(0)" class="update" id="update_users">Update</a></div>
				</div><!--end of user-dropdown-->
			</div><!--end of sidebar-section-->
                        
                        
                        <div class="sidebar-section timer">
				<h3>My Timer <i class="ti-info-alt"></i></h3>
				<div>
                                    <?php
                                    
                                    if (is_null($activeTimerID)){
                                        $timer_running = 'style="display: none"';
                                        $timer_stop = '';
                                    } else {
                                        $timer_running = '';
                                        $timer_stop = 'style="display: none"';
                                    }
                                    ?>
                                    
                                    <div id="timer_running" {!! $timer_running !!}>
                                        <span id="active-timer-task-modal"> {{$activeTimer}}</span>
                                        <a data-timelog-id="{{$activeTimerID}}" href="javascript:void(0)" class="task-timer-stop-modal"><i class="fa fa-stop"></i></a>
                                    </div>

                                    <div id="timer_stop" {!! $timer_stop !!}>
                                        <span id='stop-timer-task-modal'> {{$total_time_format}}</span>
                                        <a href="javascript:void(0)" class="task-timer-start-modal" ><i class="fa fa-play"></i></a>
                                    </div>
                                 
				</div>
			</div><!--end of sidebar-section-->
                        
			<div class="sidebar-section settings">
				<h3>Settings</h3>
				<ul>
					<li>
                                            <i class="ti-calendar"></i> Start Date: <input type="text" name="start_date" id="start_date2"  autocomplete="off" value="@if($task->start_date != '-0001-11-30 00:00:00' && $task->start_date != null) {{ $task->start_date->format($global->date_format) }} @endif">
					</li>
					<li>
						<i class="ti-calendar"></i> Due Date:  <input type="text" name="due_date" id="due_date2" autocomplete="off" value="@if($task->due_date != '-0001-11-30 00:00:00') {{ $task->due_date->format($global->date_format) }} @endif">
					</li>
					<li>
						<i class="ti-flag-alt"></i> Status: 
							<div class="custom-select status">
                                                <select name="board_column_id" id="board_column_id">
                                                    @foreach($taskBoardColumns as $taskBoardColumn)
                                                        <option @if($task->board_column_id == $taskBoardColumn->id) selected @endif value="{{$taskBoardColumn->id}}">{{ $taskBoardColumn->column_name }}</option>
                                                    @endforeach
                                                </select>
												</div>
					</li>
					<li>
						<i class="ti-shield"></i> Priority:
						<div class="custom-select priority">
                                                <select name="priority" id="priority">
                                                    <option @if($task->priority == 'high') selected=""  @endif value="high">@lang('modules.tasks.high')</option>
                                                    <option @if($task->priority == 'medium') selected=""  @endif value="medium">@lang('modules.tasks.medium')</option>
                                                    <option @if($task->priority == 'low') selected=""  @endif value="low">@lang('modules.tasks.low')</option>
						</select>
						</div>
					</li>
					<li>
						<i class="ti-eye"></i> Client:
						<div class="custom-select client">
                                                <select name="is_client" id="is_client">
							<option @if($task->is_client == 'hidden') selected=""  @endif value="hidden">Hidden</option>
							<option @if($task->is_client == 'visible') selected=""  @endif value="visible">Visible</option>
						</select>
						</div>
					</li>
                                        
                                        <li> 
                                             @php $pin = $task->pinned() @endphp
                                            <a href="javascript:;" class=""  data-placement="bottom"  data-toggle="tooltip" data-original-title="@if($pin) Unpin @else Pin @endif"  data-pinned="@if($pin) pinned @else unpinned @endif" id="pinnedItem" > <i class="icon-pin icon-2 pin-icon  @if($pin) pinned @else unpinned @endif"></i> <span id="pinnedItemS"> @if($pin) Pinned @else UnPinned @endif </span></a>
                                        </li>
                                        
                                        @if($task->board_column->slug != 'completed')
                                        
                                        <li class="reminder"> 
                                           <a href="javascript:;" id="reminderButton" title="@lang('messages.remindToAssignedEmployee')"> <i class="ti-alarm-clock"></i> @lang('modules.tasks.reminder') </a>
                                        </li>
                                        @endif
                                        
                                        
				</ul>				
			</div><!--end of sidebar-section-->
			<div class="sidebar-section tags">
				<h3>Tags</h3>
                                @if(sizeof($task->tags))
				<ul class="tags-list">
                                    @foreach($task->tags as $key => $tag)
					<li>{{ ucwords($tag->tag->tag_name) }}</li>
                                    @endforeach
				</ul>
                                @endif
                                <div class="edit-tags" style="display: none" >
					<ul>
                                            <li class="new">
                                                <select multiple data-role="tagsinput" name="tags[]" id="tags">
                                                    @foreach($task->tags as $tag)
                                                        <option value="{{ $tag->tag->tag_name }}">{{ $tag->tag->tag_name }}</option>
                                                    @endforeach
                                                </select>
                                            </li>
					</ul>
					<div>
                                            <a href="javascript:void(0)" class="close-edit" id="close_edit">Close</a>
                                            <a href="javascript:void(0)" class="save" id="save_tags">Save</a>
					</div>
				</div>
                            <a href="javascript:void(0)" id="edit_tags_a">Edit Tags</a>
			</div><!--end of sidebar-section-->
			<div class="sidebar-section action">
				<h3>Actions</h3>
				<ul>
                                        <li>
						<i class="ti-flag-alt"></i> Change Project: 
							<div class="custom-select project">
                                                <select name="project_id" id="project_id">
                                                    @foreach($projects as $project)
                                                        <option @if($task->project_id == $project->id) selected @endif value="{{$project->id}}">{{ ucwords($project->project_name) }}</option>
                                                    @endforeach
                                                </select>
												</div>
					</li>
                                        <li>
						<i class="ti-flag-alt"></i> Change Category: 
							<div class="custom-select category">
                                                <select name="category_id" id="category_id">
                                                    @foreach($categories as $category)
                                                        <option @if($task->task_category_id == $category->id) selected @endif value="{{$category->id}}">{{ ucwords($category->category_name) }}</option>
                                                    @endforeach
                                                </select>
												</div>
					</li>
                                        <li>
                                            <i class="ti-flag-alt"></i> Change Milestone: 
                                            <div class="custom-select milestone">
                                                <select name="milestone_id" id="milestone_id">
                                                    <option value="">--</option>
                                                    @foreach($milestones as $milestone)
                                                        <option @if($task->milestone_id == $milestone->id) selected @endif value="{{ $milestone->id }}">{{ $milestone->milestone_title }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
					</li>
                                        <li style="display: none">
						<a href="javascript:void(0)"><i class="ti-timer stop"></i> Stop All Timers</a>
					</li>
                                        <li style="display: none;">
						<a href="javascript:void(0)"><i class="ti-trash"></i> Delete</a>
					</li>
                                        <li> 
                                           <a href="{{route('front.task-share',[md5($task->id)])}}" target="_blank" data-toggle="tooltip" data-placement="bottom" data-original-title="@lang('app.share')" "> <i class="fa fa-share-alt"></i> Share </a>
                                        </li>
                                        
                                        
                                        
				</ul>
			</div><!--end of sidebar-section-->
			<div class="sidebar-section">
				<h3>Information</h3>
				<div class="table-container">
					<table class="table table-bordered table-sm">
						<tbody>
							<tr>
								<td>Task ID</td>
								<td><strong># {{$task->id}}</strong></td>
							</tr>
                                                         @if($task->create_by)
							<tr>
								<td>Created By</td>
								<td><strong>{{ ucwords($task->create_by->name) }}</strong></td>
							</tr>
                                                         @endif
                                                        
							<tr>
								<td>Date Created</td>
								<td><strong> {{$task->created_at->format($global->date_format)}}</strong></td>
							</tr>
												<tr>
								<td>Total Time</td>
								<td><strong><span id="task_timer_all_total_time">{{$total_time}}</span></strong>
								</td>
							</tr>
							<tr>
								<td>Earning</td>
								<td><strong><span id="task_all_earning">{{$earning}}</strong>
								</td>
							</tr>
							<tr>
								<td>Project</td>
                                                                
								<td>
                                                                    @if (!is_null($task->project_id)) 
                                                                    <strong><a href="{{route('admin.projects.show', $task->project_id)}}" target="_blank"># {{$task->project_id}}</a></strong>
                                                                    @else
                                                                    &nbsp;
                                                                    @endif
								</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div><!--end of sidebar-section-->
		</div><!--end of col-3-->
    </div>

</div>

<script src="{{ asset('plugins/bower_components/moment/moment.js') }}"></script>
<script src="{{ asset('plugins/bower_components/summernote/dist/summernote.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/bootstrap-tagsinput/dist/bootstrap-tagsinput.min.js') }}"></script>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

<script>
    
    
    jQuery('#due_date2, #start_date2').datepicker({
        autoclose: true,
        todayHighlight: true,
        weekStart:'{{ $global->week_start }}',
        format: '{{ $global->date_picker_format }}',
    });
    
    $('#due_date2 , #start_date2').change(function () {
        
        var id = {{ $task->id }};
        var token = "{{ csrf_token() }}";
        var start_date = $('#start_date2').val();
        var due_date = $('#due_date2').val();

        var url = "{{route('admin.all-tasks.live-update',':id')}}";
        url = url.replace(':id', id);
        $.easyAjax({
            url: url,
            type: "POST",
            data: {'_token': token, start_date: start_date , due_date: due_date},
            success: function (data) {
                if (data.status == "success") {
                    window.LaravelDataTables["allTasks-table"].draw();
                }
            }
        })
    });
    
    
    
    $("#edit_tags_a").click(function(){
        $('.edit-tags').show();
        $('#edit_tags_a').hide();
        $('.tags-list').hide();
        
    });
    
    $("#close_edit").click(function(){
        $('.edit-tags').hide();
        $('#edit_tags_a').show();
        $('.tags-list').show();
    });
    
     $("#save_tags").click(function(){
         
        var id = {{ $task->id }};
        var token = "{{ csrf_token() }}";
        var tags = $('#tags').val();

        var url = "{{route('admin.all-tasks.live-update',':id')}}";
        url = url.replace(':id', id);
        $.easyAjax({
            url: url,
            type: "POST",
            data: {'_token': token, tags: tags},
            success: function (data) {
                if (data.status == "success") {
                    $('.edit-tags').hide();
                    $('#edit_tags_a').show();
                    $('.tags-list').empty();
                    if(data.tagsArr) {
                        var tagsArr = data.tagsArr;
                        var i;
                        for (i = 0; i < tagsArr.length; ++i) {
                            $('.tags-list').append("<li>"+tagsArr[i]+"</li>");
                        }
                    }
                    $('.tags-list').show();
                    window.LaravelDataTables["allTasks-table"].draw();
                }
            }
        })
    });
    
    
     $("#description_edit_btn").click(function(){
        $('#description_text').hide();
        $('#description_edit_btn').hide();
        $('#description_editor').show();
    });
    
    $("#close_description").click(function(){
        $('#description_editor').hide();
        $('#description_text').show();
        $('#description_edit_btn').show();
    });
    
     $("#submit_description").click(function(){
         
        var id = {{ $task->id }};
        var token = "{{ csrf_token() }}";
        var description = $('#description').val();

        var url = "{{route('admin.all-tasks.live-update',':id')}}";
        url = url.replace(':id', id);
        $.easyAjax({
            url: url,
            type: "POST",
            data: {'_token': token, description: description},
            success: function (data) {
                if (data.status == "success") {
                    $('#description_editor').hide();
                    $('#description_text').html(data.description);
                    $('#description_text').show();
                    $('#description_edit_btn').show();
                    
                }
            }
        })
    });
    
     $("#update_users").click(function(){
         
        var id = {{ $task->id }};
        var token = "{{ csrf_token() }}";
        var selectedID = new Array();
        
        $("input:checkbox[name=user_id]:checked").each(function(){
            selectedID.push($(this).val());
        });
        
        var url = "{{route('admin.all-tasks.live-update',':id')}}";
        url = url.replace(':id', id);
        $.easyAjax({
            url: url,
            type: "POST",
            data: {'_token': token, user_id: selectedID},
            success: function (data) {
                if (data.status == "success") {
                    $('#assigned_users').html(data.assigned_users);
                    $(".user-modal-close").click();
                    window.LaravelDataTables["allTasks-table"].draw();
                }
            }
        })
    });
    
    
    
    
    
    
    

    
    $(".custom-select.status").click(function(){
        
        var id = {{ $task->id }};
        var token = "{{ csrf_token() }}";
        var board_column_id = $(this).find('select').find(":selected").val()

        var url = "{{route('admin.all-tasks.live-update',':id')}}";
        url = url.replace(':id', id);
        $.easyAjax({
            url: url,
            type: "POST",
            data: {'_token': token, board_column_id: board_column_id},
            success: function (data) {
                if (data.status == "success") {
                    window.LaravelDataTables["allTasks-table"].draw();
                }
            }
        })
       
    });
    
    $(".custom-select.priority").click(function(){

        var id = {{ $task->id }};
        var token = "{{ csrf_token() }}";
        var priority = $(this).find('select').find(":selected").val();

        var url = "{{route('admin.all-tasks.live-update',':id')}}";
        url = url.replace(':id', id);
        $.easyAjax({
            url: url,
            type: "POST",
            data: {'_token': token, priority: priority},
            success: function (data) {
                if (data.status == "success") {
                    window.LaravelDataTables["allTasks-table"].draw();
                }
            }
        })
    });
    
     $(".custom-select.client").click(function(){
        var id = {{ $task->id }};
        var token = "{{ csrf_token() }}";
        var is_client = $(this).find('select').find(":selected").val();
        
        var url = "{{route('admin.all-tasks.live-update',':id')}}";
        url = url.replace(':id', id);
        $.easyAjax({
            url: url,
            type: "POST",
            data: {'_token': token, is_client: is_client},
            success: function (data) {
                if (data.status == "success") {
                    window.LaravelDataTables["allTasks-table"].draw();
                }
            }
        })
    });
    
    $(".custom-select.project").click(function(){
        var id = {{ $task->id }};
        var token = "{{ csrf_token() }}";
        var project_id = $(this).find('select').find(":selected").val();
        
        var url = "{{route('admin.all-tasks.live-update',':id')}}";
        url = url.replace(':id', id);
        $.easyAjax({
            url: url,
            type: "POST",
            data: {'_token': token, project_id: project_id},
            success: function (data) {
                if (data.status == "success") {
                    $('.milestone  .select-selected').remove();
                   $('.milestone .select-items.select-hide').remove();
                    $('#milestone_id').html(data.milestone_select);
                    customSelect();
                    
                    window.LaravelDataTables["allTasks-table"].draw();    
                }
            }
        })
    });
    
     $(".custom-select.category").click(function(){
        var id = {{ $task->id }};
        var token = "{{ csrf_token() }}";
        var category_id = $(this).find('select').find(":selected").val();
        
        var url = "{{route('admin.all-tasks.live-update',':id')}}";
        url = url.replace(':id', id);
        $.easyAjax({
            url: url,
            type: "POST",
            data: {'_token': token, category_id: category_id},
            success: function (data) {
                if (data.status == "success") {
                    window.LaravelDataTables["allTasks-table"].draw();
                }
            }
        })
    });
    
    $(".custom-select.milestone").click(function(){
        var id = {{ $task->id }};
        var token = "{{ csrf_token() }}";
        var milestone_id = $(this).find('select').find(":selected").val();
        
        var url = "{{route('admin.all-tasks.live-update',':id')}}";
        url = url.replace(':id', id);
        $.easyAjax({
            url: url,
            type: "POST",
            data: {'_token': token, milestone_id: milestone_id},
            success: function (data) {
                if (data.status == "success") {
                    window.LaravelDataTables["allTasks-table"].draw();
                }
            }
        })
    });
    
    
    
    
    
    
    
    
    $('#uploadedFiles').click(function () {

        var url = '{{ route("admin.all-tasks.show-files", ':id') }}';

        var id = {{ $task->id }};
        url = url.replace(':id', id);

        $('#subTaskModelHeading').html('Sub Task');
        $.ajaxModal('#subTaskModal', url);
    });
    $('body').on('click', '.edit-sub-task', function () {
        var id = $(this).data('sub-task-id');
        var url = '{{ route('admin.sub-task.edit', ':id')}}';
        url = url.replace(':id', id);

        $('#subTaskModelHeading').html('Sub Task');
        $.ajaxModal('#subTaskModal', url);
    })

    $('.add-sub-task').click(function () {
        var id = $(this).data('task-id');
        var url = '{{ route('admin.sub-task.create')}}?task_id='+id;

        $('#subTaskModelHeading').html('Sub Task');
        $.ajaxModal('#subTaskModal', url);
    })

    $('#reminderButton').click(function () {
        swal({
            title: "Are you sure?",
            text: "Do you want to send reminder to assigned employee?",
            dangerMode: true,
            icon: 'warning',
            buttons: {
                cancel: "No, cancel please!",
                confirm: {
                    text: "Yes, send it!",
                    value: true,
                    visible: true,
                    className: "danger",
                }
            }
        }).then( function (isConfirm) {
            if (isConfirm) {

                var url = '{{ route('admin.all-tasks.reminder', $task->id)}}';

                $.easyAjax({
                    type: 'GET',
                    url: url,
                    success: function (response) {
                       //
                    }
                });
            }
        });
    })

    $('body').on('click', '.delete-sub-task', function () {
        var id = $(this).data('sub-task-id');
        swal({
            title: "Are you sure?",
            text: "You will not be able to recover the deleted sub task!",
            dangerMode: true,
            icon: 'warning',
            buttons: {
                cancel: "No, cancel please!",
                confirm: {
                    text: "Yes, delete it!",
                    value: true,
                    visible: true,
                    className: "danger",
                }
            }
        }).then(function (isConfirm) {
            if (isConfirm) {

                var url = "{{ route('admin.sub-task.destroy',':id') }}";
                url = url.replace(':id', id);

                var token = "{{ csrf_token() }}";

                $.easyAjax({
                    type: 'POST',
                    url: url,
                    data: {'_token': token, '_method': 'DELETE'},
                    success: function (response) {
                        if (response.status == "success") {
                            $('#sub-task-list').html(response.view);
                        }
                    }
                });
            }
        });
    });

    $('#view-task-history').click(function () {
        var id = $(this).data('task-id');

        var url = '{{ route('admin.all-tasks.history', ':id')}}';
        url = url.replace(':id', id);
        $.easyAjax({
            url: url,
            type: "GET",
            success: function (response) {
                $('#task-detail-section').hide();
                $('#task-history-section').html(response.view)
                $('#view-task-history').hide();
                $('.close-task-history').show();
            }
        })

    })

    $('.close-task-history').click(function () {
        $(this).hide();
        $('#task-detail-section').show();
        $('#view-task-history').show();
        $('#task-history-section').html('');
    })

    function saveSubTask() {
        $.easyAjax({
            url: '{{route('admin.sub-task.store')}}',
            container: '#createSubTask',
            type: "POST",
            data: $('#createSubTask').serialize(),
            success: function (response) {
                $('#subTaskModal').modal('hide');
                $('#sub-task-list').html(response.view)
            }
        })
    }

    function updateSubTask(id) {
        var url = '{{ route('admin.sub-task.update', ':id')}}';
            url = url.replace(':id', id);
        $.easyAjax({
            url: url,
            container: '#createSubTask',
            type: "POST",
            data: $('#createSubTask').serialize(),
            success: function (response) {
                $('#subTaskModal').modal('hide');
                $('#sub-task-list').html(response.view)
            }
        })
    }
    
    $('.summernote_description').summernote({
        height: 200,                 // set editor height
        minHeight: null,             // set minimum height of editor
        maxHeight: null,             // set maximum height of editor
        focus: false,                 // set focus to editable area after initializing summernote,
        toolbar: [
            // [groupName, [list of button]]
            ['style', ['bold', 'italic', 'underline', 'clear']],
            ['font', ['strikethrough']],
            ['fontsize', ['fontsize']],
            ['para', ['ul', 'ol', 'paragraph']],
            ["view", ["fullscreen", "codeview"]]
        ]
    });

    $('.summernote').summernote({
        height: 100,                 // set editor height
        minHeight: null,             // set minimum height of editor
        maxHeight: null,             // set maximum height of editor
        focus: false,                 // set focus to editable area after initializing summernote,
        toolbar: [
            // [groupName, [list of button]]
            ['style', ['bold', 'italic', 'underline', 'clear']],
            ['font', ['strikethrough']],
            ['fontsize', ['fontsize']],
            ['para', ['ul', 'ol', 'paragraph']],
            ["view", ["fullscreen", "codeview"]]
        ]
    });

    //    change sub task status
    $('#sub-task-list').on('click', '.task-check', function () {
        if ($(this).is(':checked')) {
            var status = 'complete';
        }else{
            var status = 'incomplete';
        }

        var id = $(this).data('sub-task-id');
        var url = "{{route('admin.sub-task.changeStatus')}}";
        var token = "{{ csrf_token() }}";

        $.easyAjax({
            url: url,
            type: "POST",
            data: {'_token': token, subTaskId: id, status: status},
            success: function (response) {
                if (response.status == "success") {
                    $('#sub-task-list').html(response.view);
                }
            }
        })
    });

    $('#submit-comment').click(function () {
        var comment = $('#task-comment').val();
        var token = '{{ csrf_token() }}';
        $.easyAjax({
            url: '{{ route("admin.task-comment.store") }}',
            type: "POST",
            data: {'_token': token, comment: comment, taskId: '{{ $task->id }}'},
            success: function (response) {
                if (response.status == "success") {
                    $('#comment-list').html(response.view);
                    $('.note-editable').html('');
                    $('#task-comment').val('');
                }
            }
        })
    })

    function deleteComment(id) {
        var commentId = id;
        var token = '{{ csrf_token() }}';

        var url = '{{ route("admin.task-comment.destroy", ':id') }}';
        url = url.replace(':id', commentId);

        $.easyAjax({
            url: url,
            type: "POST",
            container: '#comment-list',
            data: {'_token': token, '_method': 'DELETE', commentId: commentId},
            success: function (response) {
                if (response.status == "success") {
                    $('#comment-list').html(response.view);
                    $('.note-editable').html('');
                }
            }
        })
    }
    //    change task status
   function markComplete(status) {

        var id = '{{ $task->id }}';

        if(status == 'completed'){
            var checkUrl = '{{route('admin.tasks.checkTask', ':id')}}';
            checkUrl = checkUrl.replace(':id', id);
            $.easyAjax({
                url: checkUrl,
                type: "GET",
                container: '#task-list-panel',
                data: {},
                success: function (data) {
                    if(data.taskCount > 0){
                        swal({
                            title: "Are you sure?",
                            text: "There is a incomplete sub-task in this task do you want to mark complete!",
                            dangerMode: true,
                            icon: 'warning',
                            buttons: {
                                cancel: "No, cancel please!",
                                confirm: {
                                    text: "Yes, complete it!",
                                    value: true,
                                    visible: true,
                                    className: "danger",
                                }
                            }
                        }).then(function (isConfirm) {
                            if (isConfirm) {
                                updateTask(id,status)
                            }
                        });
                    }
                    else{
                        updateTask(id,status)
                    }

                }
            });
        }
        else{
            updateTask(id,status)
        }


    }

    // Update Task
    function updateTask(id,status){
        var url = "{{route('admin.tasks.changeStatus')}}";
        var token = "{{ csrf_token() }}";
        $.easyAjax({
            url: url,
            type: "POST",
            container: '.r-panel-body',
            data: {'_token': token, taskId: id, status: status, sortBy: 'id'},
            success: function (data) {
                $('#columnStatus').css('color', data.textColor);
                $('#columnStatus').html(data.column);
                if(status == 'completed'){

                    $('#inCompletedButton').removeClass('hidden');
                    $('#completedButton').addClass('hidden');
                    $('#reminderButton').addClass('hidden');
                }
                else{
                    $('#completedButton').removeClass('hidden');
                    $('#inCompletedButton').addClass('hidden');
                    $('#reminderButton').removeClass('hidden');
                }

                if( typeof table !== 'undefined'){
                    window.LaravelDataTables["allTasks-table"].draw();
                }
                else if(typeof loadData !== 'undefined' && $.isFunction(loadData)){
                    loadData();
                }
            }
        })
    }
    
    $('body').on('click', '#pinnedItem', function(){
        var type = $('#pinnedItem').attr('data-pinned');
        var id = {{ $task->id }};
        var pinType = 'task';

        var dataPin = type.trim(type);
        if(dataPin == 'pinned'){
            swal({
                title: "Are you sure?",
                text: "You want to unpin task!",
                dangerMode: true,
                icon: 'warning',
                buttons: {
                    cancel: "No, cancel please!",
                    confirm: {
                        text: "Yes, unpin it!",
                        value: true,
                        visible: true,
                        className: "danger",
                    }
                }
            }).then(function (isConfirm) {
                if (isConfirm) {
                    var url = "{{ route('admin.pinned.destroy',':id') }}";
                    url = url.replace(':id', id);

                    var token = "{{ csrf_token() }}";
                    $.easyAjax({
                        type: 'POST',
                        url: url,
                        data: {'_token': token, '_method': 'DELETE','type':pinType},
                        success: function (response) {
                            if (response.status == "success") {
                                $.unblockUI();
                                $('.pin-icon').removeClass('pinned');
                                $('.pin-icon').addClass('unpinned');
                                $('#pinnedItem').attr('data-pinned','unpinned');
                                $('#pinnedItem').attr('data-original-title','Pin');
                                $('#pinnedItemS').html('UnPinned');
                                
                                $("#pinnedItem").tooltip("hide");
                                window.LaravelDataTables["allTasks-table"].draw();
                            }
                        }
                    })
                }
            });
        }
        else {
            swal({
                title: "Are you sure?",
                text: "You want to pin this task!",
                dangerMode: true,
                icon: 'warning',
                buttons: {
                    cancel: "No, cancel please!",
                    confirm: {
                        text: "Yes, pin it!",
                        value: true,
                        visible: true,
                        className: "danger",
                    }
                }
            }).then(function (isConfirm) {
                if (isConfirm) {
                    var url = "{{ route('admin.pinned.store') }}?type="+pinType;

                    var token = "{{ csrf_token() }}";
                    $.easyAjax({
                        type: 'POST',
                        url: url,
                        data: {'_token': token,'task_id':id},
                        success: function (response) {
                            if (response.status == "success") {
                                $.unblockUI();
                                $('.pin-icon').removeClass('unpinned');
                                $('.pin-icon').addClass('pinned');
                                $('#pinnedItem').attr('data-pinned','pinned');
                                $('#pinnedItem').attr('data-original-title','Unpin');
                                 $('#pinnedItemS').html('Pinned');
                                
                                $("#pinnedItem").tooltip("hide");
                                window.LaravelDataTables["allTasks-table"].draw();
                            }
                        }
                    });
                }
            });
        }
    });
    
    $('#submit-note').click(function () {
        var note = $('#task-note').val();
        var token = '{{ csrf_token() }}';
        $.easyAjax({
            url: '{{ route("admin.task-note.store") }}',
            type: "POST",
            data: {'_token': token, note: note, taskId: '{{ $task->id }}'},
            success: function (response) {
                if (response.status == "success") {
                    $('#note-list').html(response.view);
                    $('.summernote').summernote("reset");
                    $('.note-editable').html('');
                    $('#task-note').val('');
                }
            }
        })
    });
    
    
    $('body').on('click', '.task-timer-start-modal', function () {
          var id = {{ $task->id }};
          var project_id = $(this).data('project-id');
          var url = "{{route('admin.all-tasks.live-timeLog',':id')}}";
          url = url.replace(':id', id);
          var token = "{{ csrf_token() }}";
            $.easyAjax({
                url: url,
                type: "POST",
                data: {'_token': token, 'task_id' : id, 'project_id' : project_id},
                success: function (data) {
                    $('#timer_running').show();
                    $('#timer_stop').hide();
                    $('#active-timer-task-modal').html(data.activeTimer);
                    $('.task-timer-stop-modal').attr('data-timelog-id',data.activeTimerID);
                   
                    window.LaravelDataTables["allTasks-table"].draw();
                    
                }
            })
      });
      
      $('body').on('click', '.task-timer-stop-modal', function () {
          var id = {{ $task->id }};
          var timeId = $(this).data('timelog-id');
          var url = "{{route('admin.all-tasks.live-timeLog-stop',':id')}}";
          url = url.replace(':id', id);
          var token = "{{ csrf_token() }}";
            $.easyAjax({
                url: url,
                type: "POST",
                data: {'_token': token, 'task_id' : id, 'timeId' : timeId},
                success: function (data) {
                    $('#timer_running').hide();
                    $('#stop-timer-task-modal').html(data.total_time_format);
                    $('#task_timer_all_total_time').html(data.total_time);
                    $('#task_all_earning').html(data.earning);
                    
                    $('#timer_stop').show();
                    
                    window.LaravelDataTables["allTasks-table"].draw();
                    
                }
            })
      });
    
    
    
    $('body').on('click', '.sa-delete', function () {
        var id = $(this).data('file-id');
        swal({
            title: "Are you sure?",
            text: "You will not be able to recover the deleted file!",
            dangerMode: true,
            icon: 'warning',
            buttons: {
                cancel: "No, cancel please!",
                confirm: {
                    text: "Yes, delete it!",
                    value: true,
                    visible: true,
                    className: "danger",
                }
            }
        }).then(function (isConfirm) {
            if (isConfirm) {

                var url = "{{ route('admin.task-files.destroy',':id') }}";
                url = url.replace(':id', id);

                var token = "{{ csrf_token() }}";

                $.easyAjax({
                    type: 'POST',
                    url: url,
                    data: {'_token': token, '_method': 'DELETE'},
                    success: function (response) {
                        if (response.status == "success") {
                            $('#list ul.list-group').html(response.html);
                        }
                    }
                });
            }
        });
    });
    
    
    
    
    $(document).ready(function(e) {
        
        var $worked = $("#active-timer-task-modal");
          function updateTimerModal() {
                if ($worked.length){
                    var myTime = $worked.html();
                    var ss = myTime.split(":");
        //            console.log(ss);

                    var hours = ss[0];
                    var mins = ss[1];
                    var secs = ss[2];
                    secs = parseInt(secs)+1;

                    if(secs > 59){
                        secs = '00';
                        mins = parseInt(mins)+1;
                    }

                    if(mins > 59){
                        secs = '00';
                        mins = '00';
                        hours = parseInt(hours)+1;
                    }

                    if(hours.toString().length < 2) {
                        hours = '0'+hours;
                    }
                    if(mins.toString().length < 2) {
                        mins = '0'+mins;
                    }
                    if(secs.toString().length < 2) {
                        secs = '0'+secs;
                    }
                    var ts = hours+':'+mins+':'+secs;

                    $worked.html(ts);
                    setTimeout(updateTimerModal, 1000);
                }
            }
            setTimeout(updateTimerModal, 1000);
    });
    
    
    function customSelect (){
        
        
        var x, i, j, l, ll, selElmnt, a, b, c;
/*look for any elements with the class "custom-select":*/
x = document.getElementsByClassName("milestone");
l = x.length;
for (i = 0; i < l; i++) {
  selElmnt = x[i].getElementsByTagName("select")[0];
  ll = selElmnt.length;
  /*for each element, create a new DIV that will act as the selected item:*/
  a = document.createElement("DIV");
  a.setAttribute("class", "select-selected");
  a.innerHTML = selElmnt.options[selElmnt.selectedIndex].innerHTML;
  x[i].appendChild(a);
  /*for each element, create a new DIV that will contain the option list:*/
  b = document.createElement("DIV");
  b.setAttribute("class", "select-items select-hide");
  for (j = 0; j < ll; j++) {
    /*for each option in the original select element,
    create a new DIV that will act as an option item:*/
    c = document.createElement("DIV");
    c.innerHTML = selElmnt.options[j].innerHTML;
    c.addEventListener("click", function(e) {
        /*when an item is clicked, update the original select box,
        and the selected item:*/
        var y, i, k, s, h, sl, yl;
        s = this.parentNode.parentNode.getElementsByTagName("select")[0];
        sl = s.length;
        h = this.parentNode.previousSibling;
        for (i = 0; i < sl; i++) {
          if (s.options[i].innerHTML == this.innerHTML) {
            s.selectedIndex = i;
            h.innerHTML = this.innerHTML;
            y = this.parentNode.getElementsByClassName("same-as-selected");
            yl = y.length;
            for (k = 0; k < yl; k++) {
              y[k].removeAttribute("class");
            }
            this.setAttribute("class", "same-as-selected");
            break;
          }
        }
        h.click();
    });
    b.appendChild(c);
  }
  x[i].appendChild(b);
  a.addEventListener("click", function(e) {
      /*when the select box is clicked, close any other select boxes,
      and open/close the current select box:*/
      e.stopPropagation();
      closeAllSelect(this);
      this.nextSibling.classList.toggle("select-hide");
      this.classList.toggle("select-arrow-active");
    });
}
        
        
    }
        
        
       
    
    
    
    
	var x, i, j, l, ll, selElmnt, a, b, c;
/* Look for any elements with the class "custom-select": */
x = document.getElementsByClassName("custom-select");
l = x.length;
for (i = 0; i < l; i++) {
  selElmnt = x[i].getElementsByTagName("select")[0];
  ll = selElmnt.length;
  /* For each element, create a new DIV that will act as the selected item: */
  a = document.createElement("DIV");
  a.setAttribute("class", "select-selected");
  a.innerHTML = selElmnt.options[selElmnt.selectedIndex].innerHTML;
  x[i].appendChild(a);
  /* For each element, create a new DIV that will contain the option list: */
  b = document.createElement("DIV");
  b.setAttribute("class", "select-items select-hide");
  for (j = 0; j < ll; j++) {
    /* For each option in the original select element,
    create a new DIV that will act as an option item: */
    c = document.createElement("DIV");
    c.innerHTML = selElmnt.options[j].innerHTML;
    c.addEventListener("click", function(e) {
        /* When an item is clicked, update the original select box,
        and the selected item: */
        var y, i, k, s, h, sl, yl;
        s = this.parentNode.parentNode.getElementsByTagName("select")[0];
        sl = s.length;
        h = this.parentNode.previousSibling;
        for (i = 0; i < sl; i++) {
          if (s.options[i].innerHTML == this.innerHTML) {
            s.selectedIndex = i;
            h.innerHTML = this.innerHTML;
            y = this.parentNode.getElementsByClassName("same-as-selected");
            yl = y.length;
            for (k = 0; k < yl; k++) {
              y[k].removeAttribute("class");
            }
            this.setAttribute("class", "same-as-selected");
            break;
          }
        }
        h.click();
    });
    b.appendChild(c);
  }
  x[i].appendChild(b);
  a.addEventListener("click", function(e) {
    /* When the select box is clicked, close any other select boxes,
    and open/close the current select box: */
    e.stopPropagation();
    closeAllSelect(this);
    this.nextSibling.classList.toggle("select-hide");
    this.classList.toggle("select-arrow-active");
  });
}

function closeAllSelect(elmnt) {
  /* A function that will close all select boxes in the document,
  except the current select box: */
  var x, y, i, xl, yl, arrNo = [];
  x = document.getElementsByClassName("select-items");
  y = document.getElementsByClassName("select-selected");
  xl = x.length;
  yl = y.length;
  for (i = 0; i < yl; i++) {
    if (elmnt == y[i]) {
      arrNo.push(i)
    } else {
      y[i].classList.remove("select-arrow-active");
    }
  }
  for (i = 0; i < xl; i++) {
    if (arrNo.indexOf(i)) {
      x[i].classList.add("select-hide");
    }
  }
}

/* If the user clicks anywhere outside the select box,
then close all select boxes: */
document.addEventListener("click", closeAllSelect);
$(".new-user").click(function(){$(".sidebar-section.user .user-dropdown").css("display","block")});
$(".user-modal-close").click(function(){$(".sidebar-section.user .user-dropdown").css("display","none")});


</script>
