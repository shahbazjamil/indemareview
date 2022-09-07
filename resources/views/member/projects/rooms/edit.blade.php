<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h4 class="modal-title">+ @lang('app.update') Room</h4>
</div>
<div class="modal-body">
    <div class="portlet-body">
        <div class="row">
            <div class="col-md-12">
                {!! Form::open(['id'=>'updateProjectRoom','class'=>'ajax-form','method'=>'PUT']) !!}
                <div class="form-body">
                        <div class="row">
                                <div class="col-md-12">

                                    {!! Form::hidden('project_id', $room->project_id) !!}

                                    <div class="form-body">
                                        <div class="row m-t-30">
                                            
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Room Title</label>
                                                    <input id="room_title" name="room_title" type="text"
                                                class="form-control" value="{{ $room->room_title }}">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Products</label>
                                                         <select id="product_id" name="product_id[]"  multiple="multiple" class="selectpicker form-control">
                                                            <option value="">--</option>
                                                            @foreach ($products as $item)
                                                                <option 
                                                                @if(in_array($item->id,$room_product_ids))
           selected
        @endif
                                                                    value="{{ $item->id }}">{{ $item->name }}</option>           
                                                            @endforeach
                                                        </select>
                                                    </div>
                                            </div>
                                            
<!--                                            <div class="col-md-3 ">
                                                <div class="form-group">
                                                    <label>Room Total Cost</label>
                                                    <input id="total_cost" name="total_cost" type="number" value="{{ $room->total_cost }}"
                                                           class="form-control" value="0" min="0" step=".01">
                                                </div>
                                            </div>-->
                                            
                                        </div>
                                        
                                        <div class="row m-t-20">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="memo">Room Summary</label>
                                                    <textarea name="summary" id="" rows="4" class="form-control">{{ $room->summary }}</textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                

                                    <hr>
                                </div>
                            </div>
                </div>
                <div class="form-actions m-t-30">
                    <button type="button" id="update-form" class="btn btn-success"><i class="fa fa-check"></i> Save
                    </button>
                </div>
                {!! Form::close() !!}

            </div>
        </div>

    </div>
</div>


<script>
 $(function() {
        $(".selectpicker").selectpicker();
      });

    $('#update-form').click(function () {
        $.easyAjax({
            url: '{{route('member.rooms.update', $room->id)}}',
            container: '#updateProjectRoom',
            type: "POST",
            data: $('#updateProjectRoom').serialize(),
            success: function (response) {
                $('#editProjectRoomModal').modal('hide');
                table._fnDraw();
            }
        })
    });
</script>