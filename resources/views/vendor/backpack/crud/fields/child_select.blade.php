<!-- select2 -->
<div clas="col-md-12" style="padding: 3px">

<!--    --><?php //$entity_model = $crud->model; ?>
    <select 
        ng-model="item.{{ $field['name'] }}"
        @include('crud::inc.field_attributes', ['default_class' =>  'form-control select2'])
        >
            @if (isset($field['allows_null']) && $field['allows_null']==true)
                <option value="">-</option>
            @endif

            @if (isset($field['data']))
                @foreach ($field['data'] as $connected_entity_entry)
                    <option value="{{ $connected_entity_entry }}"
                    >{{ $connected_entity_entry }}</option>
                @endforeach
            @endif
    </select>

    {{-- HINT --}}
    @if (isset($field['hint']))
        <p class="help-block">{!! $field['hint'] !!}</p>
    @endif
</div>

{{-- ########################################## --}}
{{-- Extra CSS and JS for this particular field --}}
{{-- If a field type is shown multiple times on a form, the CSS and JS will only be loaded once --}}
{{-- FIELD CSS - will be loaded in the after_styles section --}}
@push('crud_fields_styles')
    <!-- include select2 css-->
    <link href="{{ asset('vendor/backpack/select2/select2.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('vendor/backpack/select2/select2-bootstrap-dick.css') }}" rel="stylesheet" type="text/css" />
@endpush

{{-- FIELD JS - will be loaded in the after_scripts section --}}
@push('crud_fields_scripts')
    <!-- include select2 js-->
    <script src="{{ asset('vendor/backpack/select2/select2.js') }}"></script>
@endpush
{{-- End of Extra CSS and JS --}}
{{-- ########################################## --}}