<?php

namespace App\Console\Commands;

use App\Models\UsersEvaluation;
use Illuminate\Console\Command;
// use Illuminate\Support\Facades\Log;

class AutoSign extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'evaluation:auto-sign';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Auto signed evaluations status after 6 days';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Log::info('message', 'test');
        UsersEvaluation::where(
                            fn($q)
                            =>
                            $q->where('created_at' , '<=', now()->subDays(6))
                                ->where('status', 'pending')
                                ->whereNull('employeeApprovedAt')
                            )
                        ->update(
                            [
                                'status'                =>  'completed',
                                'employeeApprovedAt'    =>  now()
                            ]
                        );
    }
}
