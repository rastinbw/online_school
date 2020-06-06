<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Backpack Crud Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used by the CRUD interface.
    | You are free to change them to anything
    | you want to customize your views to better match your application.
    |
    */

    // Forms
    'save_action_save_and_new' => 'ذخیره و آیتم جدید',
    'save_action_save_and_edit' => 'ذخیره و ویرایش',
    'save_action_save_and_back' => 'ذخیره و بازگشت',
    'save_action_changed_notification' => 'Default behaviour after saving has been changed.',

    // Create form
    'add'                 => 'اضافه کردن',
    'back_to_all'         => 'بازگشت به لیست ',
    'cancel'              => 'لغو',
    'add_a_new'           => 'اضافه کردن ',

    // Edit form
    'edit'                 => 'ویرایش',
    'save'                 => 'ذخیره',

    // Revisions
    'revisions'            => 'Revisions',
    'no_revisions'         => 'No revisions found',
    'created_this'         => 'created this',
    'changed_the'          => 'changed the',
    'restore_this_value'   => 'Restore this value',
    'from'                 => 'from',
    'to'                   => 'to',
    'undo'                 => 'Undo',
    'revision_restored'    => 'Revision successfully restored',
    'guest_user'           => 'Guest User',

    // Translatable models
    'edit_translations' => 'EDIT TRANSLATIONS',
    'language'          => 'Language',

    // CRUD table view
    'all'                       => 'همه',
    'in_the_database'           => 'in the database',
    'list'                      => 'List',
    'actions'                   => 'عملیات',
    'preview'                   => 'Preview',
    'delete'                    => 'حذف',
    'admin'                     => 'Admin',
    'details_row'               => 'This is the details row. Modify as you please.',
    'details_row_loading_error' => 'There was an error loading the details. Please retry.',

    // Confirmation messages and bubbles
    'delete_confirm'                              => 'آیا از حذف این رکورد اطمینان دارید؟',
    'delete_confirmation_title'                   => 'حذف شد',
    'delete_confirmation_message'                 => 'رکورد با موفقیت حذف شد',
    'delete_confirmation_not_title'               => 'حذف نشد',
    'delete_confirmation_not_message'             => "خطایی رخ داد، رکورد شما ممکن است حذف نشده باشد",
    'delete_confirmation_not_deleted_title'       => 'حذف نشد',
    'delete_confirmation_not_deleted_message'     => 'هیچ رویدادی رخ نداد، رکورد شما حذف نشد',

    'ajax_error_title' => 'Error',
    'ajax_error_text'  => 'Error loading page. Please refresh the page.',

    // DataTables translation
    'emptyTable'     => 'هیچ موردی برای نمایش وجود ندارد',
    'info'           => 'تعداد کل نتایج: _TOTAL_',
    'infoEmpty'      => 'بدون نتیجه',
    'infoFiltered'   => '',
    'infoPostFix'    => '',
    'thousands'      => ',',
    'lengthMenu'     => ' _MENU_ تعداد رکورد در هر صفحه',
    'loadingRecords' => 'Loading...',
    'processing'     => 'Processing...',
    'search'         => 'جست و جو',
    'zeroRecords'    => 'No matching records found',
    'paginate'       => [
        'first'    => 'اولین',
        'last'     => 'آخرین',
        'next'     => 'بعدی',
        'previous' => 'قبلی',
    ],
    'aria' => [
        'sortAscending'  => ': activate to sort column ascending',
        'sortDescending' => ': activate to sort column descending',
    ],
    'export' => [
        'copy'              => 'Copy',
        'excel'             => 'خروجی اکسل',
        'csv'               => 'CSV',
        'pdf'               => 'PDF',
        'print'             => 'Print',
        'column_visibility' => 'Column visibility',
    ],

    // global crud - errors
    'unauthorized_access' => 'دسترسی غیر مجاز، شما مجاز به رویت این صفحه نیستید.',
    'please_fix' => 'Please fix the following errors:',

    // global crud - success / error notification bubbles
    'insert_success' => 'آیتم با موفقیت اضافه شد',
    'update_success' => 'آیتم با موفقیت ویرایش شد',

    // CRUD reorder view
    'reorder'                      => 'Reorder',
    'reorder_text'                 => 'Use drag&drop to reorder.',
    'reorder_success_title'        => 'Done',
    'reorder_success_message'      => 'Your order has been saved.',
    'reorder_error_title'          => 'Error',
    'reorder_error_message'        => 'Your order has not been saved.',

    // CRUD yes/no
    'yes' => 'بله',
    'no' => 'خیر',

    // CRUD filters navbar view
    'filters' => 'فیلتر ها',
    'toggle_filters' => 'لیست فیلتر ها',
    'remove_filters' => 'حذف فیتر ها',

    // Fields
    'browse_uploads' => 'Browse uploads',
    'select_all' => 'انتخاب همه',
    'select_files' => 'انتخاب فایل ها',
    'select_file' => 'انتخاب فایل',
    'clear' => 'Clear',
    'page_link' => 'Page link',
    'page_link_placeholder' => 'http://example.com/your-desired-page',
    'internal_link' => 'Internal link',
    'internal_link_placeholder' => 'Internal slug. Ex: \'admin/page\' (no quotes) for \':url\'',
    'external_link' => 'External link',
    'choose_file' => 'انتخاب فایل',

    //Table field
    'table_cant_add' => 'Cannot add new :entity',
    'table_max_reached' => 'Maximum number of :max reached',

    // File manager
    'file_manager' => 'File Manager',
];
