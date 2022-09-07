<link rel="stylesheet" href="{{ asset('plugins/bower_components/summernote/dist/summernote.css') }}">

<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h4 class="modal-title">Location Notes</h4>
</div>
<div class="modal-body">
    <div class="portlet-body">

        <div class="form-body">
            <div class="panel-wrapper collapse in">
                <div class="panel-body">
                    <div class="steamline">
                        <div class="row">

                            <div class="col-md-12">
                                <div class="white-box p-0">
                                    <h3 class="box-title b-b"><i class="fa fa-layers"></i> @lang('app.notes')</h3>
                                        <div class="col-xs-12" id="note-container">
                                            <div id="note-list">
                                                @forelse($notes as $note)
                                                    <div class="row b-b m-b-5 font-12">
                                                    <div class="col-xs-12 m-b-5">
                                                        <span class="font-semi-bold">{{ ucwords($note->user->name) }}</span> <span class="text-muted font-12">{{ ucfirst($note->created_at->diffForHumans()) }}</span>
                                                    </div>
                                                    <div class="col-xs-10">
                                                        {!! ucfirst($note->note)  !!}
                                                    </div>
                                                </div>
                                                @empty
                                                    <div class="col-xs-12">
                                                        @lang('messages.noNoteFound')
                                                    </div>
                                                @endforelse
                                            </div>
                                        </div>
                                </div>
                            </div>
                        </div>                                   
                </div>
            </div>
            </div>
           
            
        </div>
        
       
    </div>
</div>

<script src="{{ asset('plugins/bower_components/custom-select/custom-select.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/summernote/dist/summernote.min.js') }}"></script>

<script>

  
</script>