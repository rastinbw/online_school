<!DOCTYPE html>
<html lang="fa">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>
        فراموشی رمز عبور
    </title>

    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <!-- Bootstrap 3.3.7 -->
    <link rel="stylesheet" href="{{ asset('vendor/adminlte/') }}/bower_components/bootstrap/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">

    <link rel="stylesheet" href="{{ asset('vendor/adminlte/') }}/dist/css/AdminLTE.min.css">
    <!-- AdminLTE Skins. Choose a skin from the css/skins folder instead of downloading all of them to reduce the load. -->
    <link rel="stylesheet" href="{{ asset('vendor/adminlte/') }}/dist/css/skins/_all-skins.min.css">

    <link rel="stylesheet" href="{{ asset('vendor/adminlte/') }}/plugins/pace/pace.min.css">
    <link rel="stylesheet" href="{{ asset('vendor/backpack/pnotify/pnotify.custom.min.css') }}">

    <link rel="stylesheet"
          href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">

    <!-- BackPack Base CSS -->
    <link rel="stylesheet" href="{{ asset('vendor/backpack/backpack.base.css') }}?v=2">
    <link rel="stylesheet" href="{{ asset('vendor/backpack/overlays/backpack.bold.css') }}">

</head>

<style>
    @font-face {
        font-family: 'IranNastaliq';
        src: url("{{asset('fonts/IranNastaliq.eot?#')}}") format('eot'),
        url("{{asset('fonts/IranNastaliq.ttf')}}") format('truetype'),
        url("{{asset('fonts/IranNastaliq.woff')}}") format('woff');
    }

    @font-face {
        font-family: 'nazanin';
        src: url("{{ asset('fonts/BNazanin.tff') }}") format('truetype'),
        url("{{ asset('fonts/BNazanin.eot') }}") format('eot'),
        url("{{ asset('fonts/BNazanin.woff') }}") format('woff');
    }

    * {
        font-family: 'nazanin';
    }
</style>


<body>
    <div style="margin-top: 20px">
        <div class="row">
            <div class="col-md-10 col-md-offset-1" style="text-align: right;">
                <div class="box box-default">
                    <div class="box-header with-border" style="background: #2e8b57">
                        <div style="font-size: 18px;color: #ffffff;" class="box-title">ایجاد رمز عبور جدید</div>
                    </div>


                    @if(Session::get('ok'))
                        <div style="padding: 10px;margin-top: 10px;margin-left: 5px;margin-right: 5px" class="alert alert-success alert-dismissible fade in" role="alert">
                            <label>{!!  Session::get('ok'); !!}</label>
                            <label data-dismiss="alert" style="cursor: pointer;margin-left: 10px; color: #ffffffff">&#10006;</label>
                        </div>
                    @endif
                    {!! Session::forget('ok') !!}

                    @if($message = $errors->first('error'))
                        <div style="padding: 10px;margin-top: 10px;margin-left: 5px;margin-right: 5px" class="alert alert-error alert-dismissible fade in" role="alert">
                            <label>{{ $message }}</label>
                            <label data-dismiss="alert" style="cursor: pointer;margin-left: 10px; color: #ffffffff">&#10006;</label>
                        </div>
                    @endif
                    {!! Session::forget('error') !!}
                    <br />

                    <form style="margin:10px 5px 5px 5px;padding: 2px" id="form-change-password" role="form" method="POST" action="{{ URL::to(url('/api/student/password/reset') )}}"
                          novalidate class="form-horizontal">

                        {{ csrf_field() }}

                        <input name="national_code" type="hidden" value="{{$student->national_code}}">

                        <div class="col-md-9">
                            <label for="password" class="col-sm-4 control-label">رمز عبور جدید</label>

                            <div class="col-sm-8" >
                                <div class="form-group">
                                    <input type="password" class="form-control" id="password" name="password">
                                </div>
                            </div>


                            <label for="password_confirmation" class="col-sm-4 control-label">تکرار رمز عبور</label>

                            <div class="col-sm-8" style="text-align: right">
                                <div class="form-group">
                                    <input type="password" class="form-control" id="password_confirmation"
                                           name="password_confirmation">
                                </div>
                            </div>
                        </div>
                        <div class="form-group" >
                            <div class="col-sm-offset-4 col-sm-5">
                                <button style="width: 100px" type="submit" class="btn btn-danger">تایید</button>
                            </div>
                        </div>
                    </form>

                </div>
            </div>
        </div>

    </div>

</body>

<!-- jQuery 2.2.3 -->
<script src="{{ asset('vendor/adminlte') }}/bower_components/jquery/dist/jquery.min.js"></script>
{{-- <script src="https://code.jquery.com/jquery-2.2.3.min.js"></script>
<script>window.jQuery || document.write('<script src="{{ asset('vendor/adminlte') }}/plugins/jQuery/jQuery-2.2.3.min.js"><\/script>')</script> --}}
<!-- Bootstrap 3.3.7 -->
<script src="{{ asset('vendor/adminlte') }}/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<script src="{{ asset('vendor/adminlte') }}/plugins/pace/pace.min.js"></script>
<script src="{{ asset('vendor/adminlte') }}/bower_components/jquery-slimscroll/jquery.slimscroll.min.js"></script>
{{-- <script src="{{ asset('vendor/adminlte') }}/bower_components/fastclick/lib/fastclick.js"></script> --}}
<script src="{{ asset('vendor/adminlte') }}/dist/js/adminlte.min.js"></script>

</html>

