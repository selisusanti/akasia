<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Helpers\NotifHelper;
use Illuminate\Support\Carbon;
use App\Services\LoanService;


class ScheduleRepayment extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'schedule:repayment';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'payment schedule';
    protected LoanService $loanService;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->loanService = new LoanService();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //belum selesai dikerjakan untuk query paymentnya
        \Log::info('Test oke '. Carbon::now());
    }
}
