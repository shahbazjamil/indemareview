@section('other-section')

<ul class="nav tabs-vertical">
    <li class="tab">
        <a href="{{ route('admin.settings.index') }}" class="text-danger"><i class="ti-arrow-left"></i> @lang('app.menu.settings')</a></li>
        <li class="tab @if(\Illuminate\Support\Facades\Route::currentRouteName() == 'admin.salary-components.index') active @endif">
            <a href="{{ route('admin.salary-components.index') }}">@lang('payroll::app.menu.salaryComponents')</a></li>
        <li class="tab @if(\Illuminate\Support\Facades\Route::currentRouteName() == 'admin.salary-groups.index') active @endif">
        <a href="{{ route('admin.salary-groups.index') }}">@lang('payroll::app.menu.salaryGroup')</a></li>
    <li class="tab @if(\Illuminate\Support\Facades\Route::currentRouteName() == 'admin.salary-tds.index') active @endif">
        <a href="{{ route('admin.salary-tds.index') }}">@lang('payroll::app.menu.salaryTds')</a></li>
    <li class="tab @if(\Illuminate\Support\Facades\Route::currentRouteName() == 'admin.payment-methods.index') active @endif">
                <a href="{{ route('admin.payment-methods.index') }}">@lang('payroll::modules.payroll.salaryPaymentMethod')</a></li>
    <li class="tab @if(\Illuminate\Support\Facades\Route::currentRouteName() == 'admin.employee-salary.index') active @endif">
                <a target="_blank" href="{{ route('admin.employee-salary.index') }}">@lang('payroll::app.menu.employeeSalary')</a></li>
            
</ul>

<script src="{{ asset('plugins/bower_components/jquery/dist/jquery.min.js') }}"></script>
<script>
    var screenWidth = $(window).width();
    if(screenWidth <= 768){

        $('.tabs-vertical').each(function() {
            var list = $(this), select = $(document.createElement('select')).insertBefore($(this).hide()).addClass('settings_dropdown form-control');

            $('>li a', this).each(function() {
                var target = $(this).attr('target'),
                    option = $(document.createElement('option'))
                        .appendTo(select)
                        .val(this.href)
                        .html($(this).html())
                        .click(function(){
                            if(target==='_blank') {
                                window.open($(this).val());
                            }
                            else {
                                window.location.href = $(this).val();
                            }
                        });

                if(window.location.href == option.val()){
                    option.attr('selected', 'selected');
                }
            });
            list.remove();
        });

        $('.settings_dropdown').change(function () {
            window.location.href = $(this).val();
        })

    }
</script>
@endsection