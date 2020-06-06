@extends('backpack::layout')

@section('header')
    <section style="padding-top: 5px" class="content-header">
        <h1 style="text-align: right;">
            <span  style="font-size: 25px" >{{ $crud->entity_name }} جدید </span>
        </h1>
    </section>
@endsection

@section('content')
    <div class="row" style="margin-right: 60px;margin-left: 60px">
        <div  class="col-md-12 col-md-offset-2" style="margin: auto; text-align: right">
            <!-- Default box -->
            @if ($crud->hasAccess('list'))
                <a href="{{ url($crud->route) }}">{{ trans('backpack::crud.back_to_all') }} <span>{{ $crud->entity_name_plural }}</span>  &nbsp<i class="fa fa-angle-double-right"></i></a><br><br>
            @endif

            @if($errors->get('custom_fail'))
                @if($messages = $errors->get('errors'))
                    <ul style="list-style-type: none;">
                        @foreach ($messages as $message)
                            <li>
                                <div style="padding: 10px" class="alert alert-error
						alert-dismissible fade in" role="alert">
                                    <label>{{ $message }}</label>
                                    <label data-dismiss="alert" style="cursor: pointer;margin-left: 10px; color: #ffffffff">&#10006;</label>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            @endif
            {!! Session::forget('errors') !!}
            {!! Session::forget('custom_fail') !!}

{{--		    @include('crud::inc.grouped_errors')--}}

		  <form method="post"
		  		action="{{ url($crud->route) }}"
				@if ($crud->hasUploadFields('create'))
				enctype="multipart/form-data"
				@endif
		  		>
		  {!! csrf_field() !!}
		  <div class="col-md-12">

		    <div class="row display-flex-wrap">
		      <!-- load the view from the application if it exists, otherwise load the one in the package -->
		      @if(view()->exists('vendor.backpack.crud.form_content'))
		      	@include('vendor.backpack.crud.form_content', [ 'fields' => $fields, 'action' => 'create' ])
		      @else
		      	@include('crud::form_content', [ 'fields' => $fields, 'action' => 'create' ])
		      @endif
		    </div><!-- /.box-body -->
		    <div class="">

                @include('crud::inc.form_save_buttons')

		    </div><!-- /.box-footer-->

		  </div><!-- /.box -->
		  </form>
	</div>
</div>

@endsection
