@if ($crud->reorder)
	@if ($crud->hasAccess('reorder'))
	  <a href="{{ url($crud->route.'/reorder') }}" style="font-weight: bold" class="btn btn-primary ladda-button" data-style="zoom-in"><span class="ladda-label"> {{ trans('backpack::crud.reorder') }} {{ $crud->entity_name_plural }}</span></a>
	@endif
@endif
