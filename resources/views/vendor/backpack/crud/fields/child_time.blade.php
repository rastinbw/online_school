
<div class="col-md-12" style="padding: 3px">
    <input
            name="{{ $field['name'] }}"
            value="{{ old($field['name']) ? old($field['name']) : (isset($field['value']) ? $field['value'] : (isset($field['default']) ? $field['default'] : '' )) }}"
            class="form-control time"
            placeholder="00 : 00"
            dir="ltr"
    >
</div>

{{-- ########################################## --}}
{{-- Extra CSS and JS for this particular field --}}
{{-- If a field type is shown multiple times on a form, the CSS and JS will only be loaded once --}}
{{-- FIELD CSS - will be loaded in the after_styles section --}}
@push('crud_fields_styles')
    <link rel="stylesheet" href="{{ asset('vendor/adminlte') }}/bower_components/wickedpicker/dist/wickedpicker.min.css"/>
@endpush

{{-- FIELD JS - will be loaded in the after_scripts section --}}
@push('crud_fields_scripts')
    <!-- include select2 js-->
    <script type="text/javascript" src="{{ asset('vendor/adminlte') }}/bower_components/jquery/dist/jquery.min.js"></script>
    <script type="text/javascript" src="{{ asset('vendor/adminlte') }}/bower_components/wickedpicker/dist/wickedpicker.min.js"></script>
    <script type='text/javascript' src="https://rawgit.com/RobinHerbots/jquery.inputmask/3.x/dist/jquery.inputmask.bundle.js"></script>


    <script>

        var options = {
            twentyFour: true,  //Display 24 hour format, defaults to false
            upArrow: 'wickedpicker__controls__control-up',  //The up arrow class selector to use, for custom CSS
            downArrow: 'wickedpicker__controls__control-down', //The down arrow class selector to use, for custom CSS
            close: 'wickedpicker__close', //The close class selector to use, for custom CSS
            hoverState: 'hover-state', //The hover state class to use, for custom CSS
            title: '{!! $field["label"] !!}', //The Wickedpicker's title,
            showSeconds: false, //Whether or not to show seconds,
            timeSeparator: ' : ', // The string to put in between hours and minutes (and seconds)
            secondsInterval: 1, //Change interval for seconds, defaults to 1,
            minutesInterval: 1, //Change interval for minutes, defaults to 1
            beforeShow: null, //A function to be called before the Wickedpicker is shown
            afterShow: null, //A function to be called after the Wickedpicker is closed/hidden
            show: null, //A function to be called when the Wickedpicker is shown
            clearable: false, //Make the picker's input clearable (has clickable "x")
        };

        //$('#time').wickedpicker(options);
    </script>
@endpush


{{-- End of Extra CSS and JS --}}
{{-- ########################################## --}}