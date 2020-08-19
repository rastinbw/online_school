<!-- This file is used to store sidebar items, starting with Backpack\Base 0.9.0 -->
{{--<li><a href="{{ backpack_url('dashboard') }}"><span>{{ trans('backpack::base.dashboard') }}</span> <i class="fa fa-dashboard"></i> </a></li>--}}
<?php
$one_record_id = 1;
?>

<li><a href='{{ backpack_url('student') }}'><span>دانش آموزان</span> <i class='fa fa-graduation-cap'></i> </a></li>
<li><a href='{{ backpack_url('course') }}'><span>کلاس ها</span> <i class='fa fa-calendar'></i> </a></li>
<li><a href='{{ backpack_url('plan') }}'><span>طرح ها</span> <i class='fa fa-square'></i> </a></li>
<li><a href='{{ backpack_url('teacher') }}'><span>اساتید</span> <i class='fa fa-users'></i> </a></li>
<li><a href="{{ backpack_url('tag') }}"><span>تگ ها</span> <i class="fa fa-tags "></i></a></li>
<li><a href="{{ backpack_url('grade') }}"><span>پایه های تحصیلی</span> <i class="fa fa-th-large "></i></a></li>
<li><a href="{{ backpack_url('field') }}"><span>رشته های تحصیلی</span> <i class="fa fa-th-large "></i></a></li>
<li><a href="{{ backpack_url('category') }}"><span>دسته بندی ها</span> <i class="fa fa-th-large "></i></a></li>
<li><a href="{{ backpack_url('installmentType') }}"><span>مدل های قسطی</span> <i class="fa fa-bank "></i></a></li>
<li><a href="{{ backpack_url('discountCode') }}"><span>کدهای تخفیف</span> <i class="fa fa-behance"></i></a></li>
<li><a href="{{ backpack_url('help') }}"><span>آموزش ها</span> <i class="fa fa-tree"></i></a></li>
<li><a href="{{ backpack_url('sliderPlan') }}"><span>طرح های اسلایدر</span> <i class="fa fa-image"></i></a></li>
<li><a href="{{ backpack_url('smsTemplate') }}"><span>قالب های پیامکی</span> <i class="fa fa-envelope"></i></a></li>
<li><a href="{{ backpack_url('landingPage') }}"><span>صفحات فرود</span> <i class="fa fa-anchor"></i></a></li>
<li><a href='{{ backpack_url('transaction') }}'><span>تراکنش ها</span> <i class='fa fa-money'></i></a></li>
<li><a href="{{url(URL::to('admin/link/'.$one_record_id.'/edit'))}}"><span>لینک های ارتباطی</span> <i class="fa fa-link"></i></a></li>
<li><a href="{{url(URL::to('admin/about/'.$one_record_id.'/edit'))}}"><span>درباره ما</span> <i class="fa fa-info-circle"></i></a></li>
<li><a href="{{url(URL::to('admin/schoolConfig/'.$one_record_id.'/edit'))}}"><span>تنظیمات</span> <i class="fa fa-image "></i></a></li>
