<?php

namespace App\Services;

use App\Models\Loan;
use App\Models\ReceivedRepayment;
use App\Models\User;
use App\Models\ScheduledRepayment;
use Carbon\Carbon;

class LoanService
{
    /**
     * Create a Loan
     *
     * @param  User  $user
     * @param  int  $amount
     * @param  string  $currencyCode
     * @param  int  $terms
     * @param  string  $processedAt
     *
     * @return Loan
     */
    public function createLoan(User $user, int $amount, string $currencyCode, int $terms, string $processedAt): Loan
    {
        // return $user;
        $loan = Loan::create([
            'user_id'               => $user->id,
            'amount'                => $amount,
            'terms'                 => $terms,
            'outstanding_amount'    => $amount,
            'currency_code'         => $currencyCode,
            'processed_at'          => $processedAt,
            'status'                => Loan::STATUS_DUE,
        ]);

        $perbulan    = $amount / $terms;
        $date_now    = $processedAt;
        for($i = 1; $i <= $terms; $i++){
            $newDateTime = Carbon::createFromFormat("Y-m-d", $date_now)->addMonth($i);
            $scheduled = ScheduledRepayment::create([
                'amount'                => $perbulan,
                'loan_id'               => $loan->id,
                'outstanding_amount'    => $perbulan,
                'processed_at'          => $newDateTime,
                'currency_code'         => ScheduledRepayment::STATUS_DUE,
            ]);
        }
        $loan_detail    = Loan::with(["scheduledRepayments"])
                        ->where('id', $loan->id)
                        ->first();
        return $loan_detail;
    }

    /**
     * Repay Scheduled Repayments for a Loan
     *
     * @param  Loan  $loan
     * @param  int  $amount
     * @param  string  $currencyCode
     * @param  string  $receivedAt
     *
     * @return ReceivedRepayment
     */
    public function repayLoan(Loan $loan, int $amount, string $currencyCode, string $receivedAt): ReceivedRepayment
    {
        //
    }
}
