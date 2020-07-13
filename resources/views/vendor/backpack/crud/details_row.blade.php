<style>
    @font-face {
        font-family: 'nazanin';
        src: url("{{ asset('fonts/BNazanin.tff') }}") format('truetype'),
        url("{{ asset('fonts/BNazanin.eot') }}") format('eot'),
        url("{{ asset('fonts/BNazanin.woff') }}") format('woff');
    }

    .title {
        font-family: 'nazanin';
        font-size: 18px;
        color: black;
        display: inline;
        font-weight: bold;
    }

    .value {
        line-height: 1.5;
        font-family: 'nazanin';
        font-size: 17px;
        display: inline;
        color: #062f70;
        white-space: normal;
        word-wrap: break-word;
    }

    #main {
        text-align: center;
    }

</style>

<div id="main" class="m-t-10 m-b-10 p-l-10 p-r-10 p-t-10 p-b-10">
    @if(isset($student))
        <div class="row">
            <div class="col-md-12" dir="rtl">
                <h5 class="title">کد ملی : </h5> <h5 class="value">{{ $student->national_code }}</h5>  <br>
            </div>
        </div>

        <hr/>

        <div class="row">
            <div class="col-md-12" dir="rtl">
                <h5 class="title">کد اولیا : </h5> <h5 class="value">{{ $student->parent_code }}</h5>  <br>
            </div>
        </div>

        <hr/>

        <div class="row">
            <div class="col-md-12" dir="rtl">
                <h5 class="title">رشته : </h5> <h5 class="value">{{ $field }}</h5>  <br>
            </div>
        </div>

        <hr/>

        <div class="row">
            <div class="col-md-12" dir="rtl">
                <h5 class="title">پایه : </h5> <h5 class="value">{{ $grade }}</h5>  <br>
            </div>
        </div>

        <hr/>

        <div class="row">
            <div class="col-md-12" dir="rtl">
                <h5 class="title">جنسیت : </h5> <h5 class="value">{{ $gender }}</h5>  <br>
            </div>
        </div>

        <hr/>

        <div class="row">
            <div class="col-md-12" dir="rtl">
                <h5 class="title">شماره تلفن همراه : </h5> <h5 class="value">{{ $student->phone_number }}</h5>  <br>
            </div>
        </div>

        <hr/>

        <div class="row">
            <div class="col-md-12" dir="rtl">
                <h5 class="title">شماره تلفن ثابت : </h5> <h5 class="value">{{ $student->home_number}}</h5>  <br>
            </div>
        </div>

        <hr/>

        <div class="row">
            <div class="col-md-12" dir="rtl">
                <h5 class="title">شماره تماس ناظر تحصیلی : </h5> <h5
                    class="value">{{ $student->parent_phone_number}}</h5>  <br>
            </div>
        </div>

        <hr/>

        <div class="row">
            <div class="col-md-12" dir="rtl">
                <h5 class="title">ایمیل : </h5> <h5 class="value">{{ $student->email}}</h5>  <br>
            </div>
        </div>

        <hr/>

        <div class="row">
            <div class="col-md-12" dir="rtl">
                <h5 class="title">آدرس : </h5> <h5 class="value">{{ $student->address}}</h5>  <br>
            </div>
        </div>

    @endif

    @if(isset($course))
        <div class="row">
            <div class="col-md-12" dir="rtl">
                <h5 class="title">استاد : </h5> <h5 class="value">{{ $course->teacher->list_title }}</h5>  <br>
            </div>
        </div>

        <hr/>

        <div class="row">
            <div class="col-md-12" dir="rtl">
                <h5 class="title">رایگان : </h5> <h5 class="value">{{ $is_free }}</h5>  <br>
            </div>
        </div>

        <hr/>

        <div class="row">
            <div class="col-md-12" dir="rtl">
                <h5 class="title">روز برگزاری : </h5> <h5 class="value">{{ $course->online_day }}</h5>  <br>
            </div>
        </div>

        <hr/>

        <div class="row">
            <div class="col-md-12" dir="rtl">
                <h5 class="title">تگ : </h5> <h5 class="value">{{ $course->tag->title }}</h5>  <br>
            </div>
        </div>

        <hr/>

        <div class="row">
            <div class="col-md-12" dir="rtl">
                <h5 class="title">تاریخ شروع کلاس : </h5> <h5 class="value"
                                                              dir="ltr">{{ $course->getLaunchDate() }}</h5>  <br>
            </div>
        </div>

        <hr/>

        <div class="row">
            <div class="col-md-12" dir="rtl">
                <h5 class="title">زمان شروع : </h5> <h5 class="value">{{ $course->getStartTime() }}</h5>  <br>
            </div>
        </div>

        <hr/>

        <div class="row">
            <div class="col-md-12" dir="rtl">
                <h5 class="title">زمان پایان : </h5> <h5 class="value">{{ $course->getFinishTime() }}</h5>  <br>
            </div>
        </div>

        <hr/>

        <div class="row">
            <div class="col-md-12" dir="rtl">
                <h5 class="title">ورود الزامی اپراتور : </h5> <h5 class="value">{{ $op_login_first }}</h5>  <br>
            </div>
        </div>

        <hr/>

        <div class="row">
            <div class="col-md-12" dir="rtl">
                <h5 class="title">ورود مهمان : </h5> <h5 class="value">{{ $guest_login }}</h5>  <br>
            </div>
        </div>

        <hr/>

        @if(isset($guest_limit))
            <div class="row">
                <div class="col-md-12" dir="rtl">
                    <h5 class="title">سقف تعداد مهمان ها : </h5> <h5 class="value">{{ $guest_limit }}</h5>  <br>
                </div>
            </div>

            <hr/>
        @endif

        <div class="row">
            <div class="col-md-12" dir="rtl">
                <h5 class="title">توضیحات : </h5> <h5 class="value">{{ $course->description }}</h5>  <br>
            </div>
        </div>

        <hr/>
    @endif

    @if(isset($plan))
        <div class="row">
            <div class="col-md-12" dir="rtl">
                <h5 class="title">دسته بندی : </h5> <h5 class="value">{{ $plan->category->title }}</h5>  <br>
            </div>
        </div>

        <hr/>

        <div class="row">
            <div class="col-md-12" dir="rtl">
                <h5 class="title">پایه : </h5> <h5 class="value">{{ $grade }}</h5>  <br>
            </div>
        </div>

        <hr/>

        <div class="row">
            <div class="col-md-12" dir="rtl">
                <h5 class="title">رشته : </h5> <h5 class="value"
                                                   dir="ltr">{{ $field }}</h5>  <br>
            </div>
        </div>

        <hr/>

        <div class="row">
            <div class="col-md-12" dir="rtl">
                <h5 class="title">قیمت منطقه یک : </h5> <h5 class="value">{{ $plan->region_one_price }}</h5>  <br>
            </div>
        </div>

        <hr/>

        <div class="row">
            <div class="col-md-12" dir="rtl">
                <h5 class="title">قیمت منطقه دو : </h5> <h5 class="value">{{ $plan->region_two_price }}</h5>  <br>
            </div>
        </div>

        <hr/>

        <div class="row">
            <div class="col-md-12" dir="rtl">
                <h5 class="title">قیمت منطقه سه : </h5> <h5 class="value">{{ $plan->region_three_price }}</h5>  <br>
            </div>
        </div>

        <hr/>

        <div class="row">
            <div class="col-md-12" dir="rtl">
                <h5 class="title">درصد تخفیف : </h5> <h5 class="value">{{ $plan->discount }}</h5>  <br>
            </div>
        </div>

        <hr/>
    @endif
</div>
<div class="clearfix"></div>
