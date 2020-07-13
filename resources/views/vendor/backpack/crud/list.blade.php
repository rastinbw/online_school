@extends('backpack::layout')

@section('header')
    <style>
        td, th {
            text-align: right;
            font-size: 16px;
        }

    </style>

    <section class="content-header" style="padding-top: 15px">
        <h1 style="text-align: right; font-size: 22px;">
            <span class="text-capitalize">{{ $crud->entity_name_plural }}</span>
        </h1>
    </section>
{{--	<section class="content-header">--}}
{{--	  <h1>--}}
{{--      <span class="text-capitalize">{!! $crud->getHeading() ?? $crud->entity_name_plural !!}</span>--}}
{{--      <small id="datatable_info_stack">{!! $crud->getSubheading() ?? trans('backpack::crud.all').'<span>'.$crud->entity_name_plural.'</span> '.trans('backpack::crud.in_the_database') !!}.</small>--}}
{{--	  </h1>--}}
{{--	  <ol class="breadcrumb">--}}
{{--	    <li><a href="{{ url(config('backpack.base.route_prefix'), 'dashboard') }}">{{ trans('backpack::crud.admin') }}</a></li>--}}
{{--	    <li><a href="{{ url($crud->route) }}" class="text-capitalize">{{ $crud->entity_name_plural }}</a></li>--}}
{{--	    <li class="active">{{ trans('backpack::crud.list') }}</li>--}}
{{--	  </ol>--}}
{{--	</section>--}}
@endsection

@section('content')
<!-- Default box -->
    <?php
        $url = explode('/', Request::url());
        $last_section = $url[sizeof($url) - 1];
        $is_prev_courses = $last_section === 'session' || $last_section === 'test';
        $is_prev_students = $last_section === 'courseaccess' || $last_section === 'testaccess';
        $is_prev_plans = $last_section === 'message';
    ?>

    @if($is_prev_courses)
        <a style="float:right" href="{{ url(URL::to('/admin/course')) }}">
            {{ trans('backpack::crud.back_to_all') }} کلاس ها &nbsp<i class="fa fa-angle-double-right"></i>
        </a><br><br>
    @elseif($is_prev_students)
        <a style="float:right" href="{{ url(URL::to('/admin/student')) }}">
            {{ trans('backpack::crud.back_to_all') }} دانش آموزان &nbsp<i class="fa fa-angle-double-right"></i>
        </a><br><br>
    @elseif($is_prev_plans)
        <a style="float:right" href="{{ url(URL::to('/admin/plan')) }}">
            {{ trans('backpack::crud.back_to_all') }} طرح ها &nbsp<i class="fa fa-angle-double-right"></i>
        </a><br><br>
    @endif

  <div class="row">

    <!-- THE ACTUAL CONTENT -->
    <div class="{{ $crud->getListContentClass() }}">
        @if($errors->get('custom_fail'))
            @if($messages = $errors->get('errors'))
                <ul style="list-style-type: none;">
                    @foreach ($messages as $message)
                        <li>
                            <div style="padding: 10px;text-align: right" class="alert alert-error
					alert-dismissible fade in" role="alert">
                                <label>{{ $message }}</label>
                                <label data-dismiss="alert"
                                       style="cursor: pointer;margin-left: 10px; color: #ffffffff">&#10006;</label>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @endif
        @endif
        {!! Session::forget('errors') !!}
        {!! Session::forget('custom_fail') !!}

      <div class="">
        <div class="row m-b-10">
            <div class="col-xs-6">
                <div id="datatable_search_stack" class="pull-left"></div>
            </div>
          <div class="col-xs-6" style="text-align: right">
            @if ( $crud->buttons->where('stack', 'top')->count() ||  $crud->exportButtons())
            <div class="hidden-print {{ $crud->hasAccess('create')?'with-border':'' }}">

              @include('crud::inc.button_stack', ['stack' => 'top'])

            </div>
            @endif
          </div>
        </div>

        {{-- Backpack List Filters --}}
        @if ($crud->filtersEnabled())
          @include('crud::inc.filters_navbar')
        @endif

        <div class="overflow-hidden">

        <table dir="rtl" id="crudTable" class="box table table-striped table-hover display responsive nowrap m-t-0" cellspacing="0">
            <thead>
              <tr>
                {{-- Table columns --}}
                @foreach ($crud->columns as $column)
                  <th
                    data-orderable="{{ var_export($column['orderable'], true) }}"
                    data-priority="{{ $column['priority'] }}"
                    data-visible="{{ var_export($column['visibleInTable'] ?? true) }}"
                    data-visible-in-modal="{{ var_export($column['visibleInModal'] ?? true) }}"
                    data-visible-in-export="{{ var_export($column['visibleInExport'] ?? true) }}"
                    >
                    {!! $column['label'] !!}
                  </th>
                @endforeach

                @if ( $crud->buttons->where('stack', 'line')->count() )
                  <th data-orderable="false" data-priority="{{ $crud->getActionsColumnPriority() }}" data-visible-in-export="false">{{ trans('backpack::crud.actions') }}</th>
                @endif
              </tr>
            </thead>
            <tbody>
            </tbody>
            <tfoot>
              <tr>
                {{-- Table columns --}}
                @foreach ($crud->columns as $column)
                  <th>{!! $column['label'] !!}</th>
                @endforeach

                @if ( $crud->buttons->where('stack', 'line')->count() )
                  <th>{{ trans('backpack::crud.actions') }}</th>
                @endif
              </tr>
            </tfoot>
        </table>

          @if ( $crud->buttons->where('stack', 'bottom')->count() )
          <div id="bottom_buttons" class="hidden-print">
            @include('crud::inc.button_stack', ['stack' => 'bottom'])

            <div id="datatable_button_stack" class="pull-right text-right hidden-xs"></div>
          </div>
          @endif

           <p style="margin-top: 10px; float: right" id="datatable_info_stack">{!! $crud->getSubheading() ?? trans('backpack::crud.all').'<span>'.$crud->entity_name_plural.'</span> '.trans('backpack::crud.in_the_database') !!}.</p>

        </div><!-- /.box-body -->

      </div><!-- /.box -->
    </div>

  </div>

@endsection

@section('after_styles')
  <!-- DATA TABLES -->
  <link href="https://cdn.datatables.net/1.10.16/css/dataTables.bootstrap.min.css" rel="stylesheet" type="text/css" />
  <link rel="stylesheet" href="https://cdn.datatables.net/fixedheader/3.1.5/css/fixedHeader.dataTables.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.1/css/responsive.bootstrap.min.css">

  <link rel="stylesheet" href="{{ asset('vendor/backpack/crud/css/crud.css') }}">
  <link rel="stylesheet" href="{{ asset('vendor/backpack/crud/css/form.css') }}">
  <link rel="stylesheet" href="{{ asset('vendor/backpack/crud/css/list.css') }}">

  <!-- CRUD LIST CONTENT - crud_list_styles stack -->
  @stack('crud_list_styles')
@endsection

@section('after_scripts')
	@include('crud::inc.datatables_logic')

  <script src="{{ asset('vendor/backpack/crud/js/crud.js') }}"></script>
  <script src="{{ asset('vendor/backpack/crud/js/form.js') }}"></script>
  <script src="{{ asset('vendor/backpack/crud/js/list.js') }}"></script>

  <!-- CRUD LIST CONTENT - crud_list_scripts stack -->
  @stack('crud_list_scripts')
@endsection
