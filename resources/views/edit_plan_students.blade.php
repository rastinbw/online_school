@extends('list_layout')

@section('css')
    <style type="text/css">

        table {
            border-collapse: collapse;
        }

        p {
            font-size: 1.1em;
            direction: rtl;
        }


        .table-data {
            text-align: center;
        }

    </style>
@stop

@section('header')
    <section dir="rtl" class="content-header" style="padding-top: 15px">
        <h1 style="text-align: right; font-size: 22px;">
            <span class="text-capitalize">ویرایش لیست دانش آموزان طرح <strong>{{$plan->title}}</strong></span>
        </h1>
    </section>
@endsection

@section('content')
    <div class="box">

        <div class="box-header with-border ">
            <input style="text-align: center;width:250px; float: right;margin-left: 10px;height: 32px"
                   id="input_national_code"
                   placeholder="کد ملی"
                   type="text">

            <button style="width: 100px; float: right" id="btn_add_student" class="btn btn-success ladda-button" data-style="zoom-in">
                <span class="ladda-label"  style="font-weight: bold">اضافه</span>
            </button>

            <button style="width: 100px; float: left" id="btn_remove_students" class="btn btn-danger ladda-button" data-style="zoom-in">
                <span class="ladda-label"  style="font-weight: bold">حذف</span>
            </button>
        </div>

        <div class="box-body overflow-hidden">

            {{ csrf_field() }}
            <input name="url" type="hidden" value="{{URL::previous()}}">

            <div style="margin-top: 10px;margin-bottom: 10px;">
                <table dir="rtl" id="students_table"
                       class="table table-bordered table-striped table-hover display responsive nowrap"
                       cellspacing="0">
                    <tr>
                        <th class="table-data" style="width: 50px;">
                            ردیف
                        </th>

                        <th class="table-data" style="width: 200px;">
                            نام
                        </th>

                        <th class="table-data" style="width: 250px;">
                            نام خوانوادگی
                        </th>

                        <th class="table-data" style="width: 150px;">
                            کد ملی
                        </th>

                        <th class="table-data" style="width: 30px;">
                        </th>

                    </tr>

                    <!-- we put data here -->
                    <?php $counter = 1; ?>

                    @foreach($students as $s)
                        <tr>
                            <td class="table-data">
                                {{$counter}}
                            </td>
                            <td class="table-data">
                                {{$s->first_name}}
                            </td>
                            <td class="table-data">
                                {{$s->last_name}}
                            </td>
                            <td class="table-data">
                                {{$s->national_code}}
                            </td>
                            <td class="table-data">
                                <div>
                                    <label id="lb_{{$counter++}}">
                                        <input type="checkbox" id="ck_{{$s->id}}">
                                    </label>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                <!-- end of data -->

                </table>

            </div>

        </div>
    </div>
@endsection

@section('after_scripts')
    <script>
        let sid_list = [];
        for (var i = 1; i < $('#students_table tr').length; i++) {
            var sid = $('#lb_'+i).find('input[type="checkbox"]').attr('id').replace('ck_','');
            $("#ck_"+ sid).change(function() {
                var current_sid = $(this).attr('id').replace('ck_','');
                if ($(this).is(':checked')) {
                    sid_list.push(current_sid);
                    // console.log(sid_list);
                }else {
                    sid_list.splice( sid_list.indexOf(current_sid), 1 );
                    // console.log(sid_list);
                }

                // if(sid_list.length){
                //     $('#btn_remove_students')
                // }else{
                //
                // }
            });
        }


        $('#btn_add_student').click(function () {
            let nc = $('#input_national_code').val();
            function request_add_student_to_plan() {
                $.ajax({
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    type: "POST",
                    url: '{{ url("/add_student_to_plan") }}',
                    data: {
                        plan_id: '{{$plan->id}}',
                        national_code: nc,
                    },
                    success: function(data)
                    {
                        console.log(data);
                        location.reload();
                    },
                    error: function(jqxhr, status, exception) {
                        console.log(jqxhr);
                        console.log(status);
                        console.log(exception);
                    }
                });

            }

            request_add_student_to_plan();
        });

        $('#btn_remove_students').click(function () {
            function request_remove_students_from_plan() {
                $.ajax({
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    type: "POST",
                    url: '{{ url("/remove_students_from_plan") }}',
                    data: {
                        plan_id: '{{$plan->id}}',
                        student_id_list: sid_list,
                    },
                    success: function(data)
                    {
                        console.log(data);
                        location.reload();
                    },
                    error: function(jqxhr, status, exception) {
                        console.log(jqxhr);
                        console.log(status);
                        console.log(exception);
                    }
                });
            }
            if (window.confirm("آیا مطمئن هستید؟")) request_remove_students_from_plan();
        });

    </script>
@endsection
