<?php

namespace App\Console;

use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        '\App\Console\Commands\CheckStudentsInstallments',
        '\App\Console\Commands\DeleteNotVerifiedStudents',
        '\App\Console\Commands\CheckTestsDate',
        '\App\Console\Commands\CheckStudentsProfileCompletion',
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // command: php artisan schedule:run

        // $schedule->command('online-school:delete_not_verified_students')->everyMinute();
        // $schedule->command('online-school:check_students_installments')->everyMinute();
        // $schedule->command('online-school:check_students_profile_completion')->everyMinute();
        // $schedule->command('online-school:check_tests_date')->everyMinute();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
