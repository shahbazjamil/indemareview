<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title"><i class="ti-eye"></i> @lang('app.menu.expenses') @lang('app.details') </h4>
</div>
<div class="modal-body">
    {!! Form::open(['id'=>'updateEvent','class'=>'ajax-form','method'=>'GET']) !!}
    <div class="form-body">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>@lang('modules.expenses.itemName')</label>
                    <p>
                        {{ $expense->item_name }}
                    </p>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <label>@lang('app.price')</label>
                    <p>{{ $expense->total_amount }}</p>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <label>@lang('app.employee')</label>
                    <p>
                        <img src="{{ $expense->user->image_url }}" class="img-circle" width="25" height="25" alt="">
                        {{ ucwords($expense->user->name) }}
                    </p>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>@lang('modules.expenses.purchaseDate')</label>
                    <p>
                        @if (!is_null($expense->purchase_date))
                            {{ $expense->purchase_date->format($global->date_format) }}
                        @endif
                    </p>
                </div>
            </div>
        </div>
        <div class="row">
            
            <div class="col-md-6">
                <div class="form-group">
                    <label>@lang('modules.expenses.purchaseFrom')</label>
                    <p>
                        {{ $expense->purchase_from ?? '--' }}
                    </p>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <label>@lang('app.status')</label>
                    <p>
                        @if ($expense->status == 'pending')
                            <label class="label label-warning">{{ strtoupper($expense->status) }}</label>
                        @elseif ($expense->status == 'approved')
                            <label class="label label-success">{{ strtoupper($expense->status) }}</label>
                        @else
                            <label class="label label-danger">{{ strtoupper($expense->status) }}</label>
                        @endif
                    </p>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <label>@lang('app.invoice')</label>
                    <p>
                        @if(!is_null($expense->bill))
                            <a target="_blank"  href="{{ asset_url('expense-invoice/'.$expense->bill) }}">@lang('app.view') @lang('app.invoice') <i class="fa fa-external-link"></i></a>
                        @else
                        --
                        @endif
                    </p>
                </div>
            </div>

        </div>
       
    </div>
    {!! Form::close() !!}

</div>
<div class="modal-footer">
    <button type="button" class="btn btn-white waves-effect" data-dismiss="modal">@lang('app.close')</button>
</div>