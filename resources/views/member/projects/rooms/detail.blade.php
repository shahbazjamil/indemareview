<div id="event-detail">

        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title"><i class="fa fa-flag"></i> Room @lang('app.details')</h4>
        </div>
        <div class="modal-body">
            {!! Form::open(['id'=>'updateEvent','class'=>'ajax-form','method'=>'GET']) !!}
            <div class="form-body">
                <div class="row">
                    <div class="col-md-6 ">
                        <div class="form-group">
                            <label>Room Title</label>
                            <p>
                                {{ $room->room_title }}
                            </p>
                        </div>
                    </div>
                    <div class="col-md-6 ">
                        <div class="form-group">
                            <label>Room Cost</label>
                            <p>
                                {{ $room->total_cost }}
                            </p>
                        </div>
                    </div>
    
                </div>
                
                <div class="row">
                    <div class="col-md-12 ">
                        <div class="form-group">
                            <label>Room product(s)</label>
                            <ul>
                            @foreach ($room_products as $item)
                            <li>{{ $item->product->name }}</li>
                            @endforeach
                            </ul>
                        </div>
                    </div>
    
                </div>
    
                <div class="row">
                    <div class="col-xs-12 ">
                        <div class="form-group">
                            <label>Room Summary</label>
                            <p>{{ $room->summary }}</p>
                        </div>
                    </div>
                </div>
            </div>
            {!! Form::close() !!}
    
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-white waves-effect" data-dismiss="modal">Close</button>
        </div>
    
    </div>
  