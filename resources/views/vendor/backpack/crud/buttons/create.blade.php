@if ($crud->hasAccess('create'))
    <a href="{{ url($crud->route.'/create') }}" class="btn btn-success ladda-button" data-style="zoom-in"><span style="font-weight: bold" class="ladda-label">اضافه کردن</span></a>
@endif
