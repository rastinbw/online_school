<?php

namespace App\Includes;

class Constant
{
    public static $SUCCESS = 1000;
    public static $INVALID_PHONE_NUMBER = 1101;
    public static $INVALID_PASSWORD = 1102;
    public static $INVALID_TOKEN = 1103;
    public static $REPETITIVE_NATIONAL_CODE = 1107;
    public static $REPETITIVE_PHONE_NUMBER = 1108;
    public static $INVALID_VERIFICATION_CODE = 1109;
    public static $INVALID_PARENT_CODE = 1110;
    public static $INVALID_REQUEST = 1112;
    public static $INVALID_EMAIL = 1113;
    public static $INVALID_FILE = 1114;
    public static $SERVER_ISSUE = 1115;
    public static $SERVER_NOT_AVAILABLE = 1119;
    public static $INVALID_ID = 1120;
    public static $VIDEO_UNAVAILABLE = 1121;
    public static $SMS_NOT_SENT = 1122;
    public static $PLAN_NOT_FREE = 1123;
    public static $INVALID_INSTALLMENT_ID = 1124;
    public static $DOWNLOAD_UNAVAILABLE = 1125;
    public static $NO_WORKBOOK = 1126 ;
    public static $NO_RECORD = 1127;
    public static $NO_ANSWER_FILE = 1128;

    public static $GENDER_MALE = 1;
    public static $GENDER_MALE_TITLE = "پسر";
    public static $GENDER_FEMALE = 0;
    public static $GENDER_FEMALE_TITLE = "دختر";

    public static $DISCOUNT_DISABLE_TRUE = 1;
    public static $DISCOUNT_DISABLE_FALSE = 0;

    public static $PAYMENT_SUCCEEDED = 1;
    public static $PAYMENT_FAILED = 0;

    public static $PAYMENT_TYPE_COMPLETE = 1;
    public static $PAYMENT_TYPE_COMPLETE_TITLE = "کامل";
    public static $PAYMENT_TYPE_INSTALLMENT = 0;
    public static $PAYMENT_TYPE_INSTALLMENT_TITLE = "قسطی";

    public static $SPECIAL_DATE_AND_TIME = 1;
    public static $FREE_DATE_AND_TYPE = 0;

    public static $DISCOUNT_TYPE_PERCENT = 0;
    public static $DISCOUNT_TYPE_PRICE = 1;

    public static $ACCESS_DENY_REASON_INSTALLMENT_NOT_PAID = 1;
    public static $ACCESS_DENY_REASON_PROFILE_NOT_COMPLETED = 2;
    public static $ACCESS_DENY_REASON_REMAINING_DEBT_NOT_PAID = 3;

    public static $ACCESS_DENY_REASONS = [
        0 => "نا معلوم",
        1 => "عدم پرداخت قسط",
        2 => "عدم ثبت مدارک",
        3 => "عدم پرداخت ماوتفاوت طرح خریداری شده"
    ];


    public static $SATURDAY = "شنبه";
    public static $SUNDAY = "یکشنبه";
    public static $MONDAY = "دوشنبه";
    public static $TUESDAY = "سه شنبه";
    public static $WEDNESDAY = "چهارشنبه";
    public static $THURSDAY = "پنجشنبه";
    public static $FRIDAY = "جمعه";

    public static $DAYS = ["شنبه", "یکشنبه", "دوشنبه", "سه شنبه", "چهارشنبه", "پنجشنبه", "جمعه"];

    public static $RUNNING_TESTS = "running_tests";
    public static $TAKEN_TESTS = "taken_tests";
    public static $FREE_TESTS = "free_tests";
    public static $REMAINING_TESTS = "remaining_tests";

    public static $REGION_ONE = 1;
    public static $REGION_TWO = 2;
    public static $REGION_THREE = 3;

    public static $TEST_NOT_TAKEN = "test_not_taken";
    public static $TEST_IS_TAKING = "test_is_taking";
    public static $TEST_TAKEN = "test_taken";

    public static $RANK = "rank";
    public static $QUESTIONS_COUNT = "questions_count";
    public static $CORRECT_COUNT = "correct_count";
    public static $WRONG_COUNT = "wrong_count";
    public static $EMPTY_COUNT = "empty_count";
    public static $PERCENT = "percent";
    public static $AVERAGE_PERCENT = "average_percent";
    public static $MAX_PERCENT = "max_percent";
    public static $LEVEL = "level";
    public static $EMPTY = "empty";
    public static $CORRECT = "correct";
    public static $WRONG = "wrong";
    public static $TOTAL = "total";
}
