<?php

namespace App\Console\Commands;

use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Console\Command;

class DeleteNotVerifiedStudents extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'online-school:delete_not_verified_students';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "This command would remove those students who registered but did not succeed to verify
                              themselves in a certain limited period of time.";

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $students = Student::where([
            ['verified', '=', 0],
            ['created_at', '<=', Carbon::now()->subMinutes(1)->toDateTimeString()]
        ]);

        $students->delete();
    }
}
