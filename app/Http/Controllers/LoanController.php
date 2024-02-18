<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use App\Http\Requests\LoanShowRequest;
use App\Http\Requests\LoanCreateRequest;
use App\Http\Requests\LoanPaymentRequest;
use App\Models\Loan;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response as HttpResponse;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Services\LoanService;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\ScheduledRepayment;
use App\Models\ReceivedRepayment;
use App\Services\Response;

class LoanController extends BaseController
{
    use RefreshDatabase;
    protected LoanService $loanService;


    public function __construct()
    {
        $this->loanService = new LoanService();
    }
    //
    /**
     * Get loan
     *
     * @param LoanShowRequest $request
     *
     * @return JsonResponse
     */
    public function index(LoanShowRequest $request): JsonResponse
    {
        $loan = $request->user()
                    ->loan()
                    ->with(["scheduledRepayments"])
                    ->get();
        return Response::success($loan);
    }

    /**
     * Create a loan
     *
     * @param LoanCreateRequest $request
     *
     * @return JsonResponse
     */
    public function store(LoanCreateRequest $request)
    {
        $loan = $this->loanService->createLoan(Auth::user(), $request->input('amount'), $request->input('currency_code'), $request->input('terms'), $request->input('processed_at'));
        return Response::success($loan);
    }

    /**
     * Create a loan
     *
     * @param LoanCreateRequest $request
     *
     * @return JsonResponse
     */
    public function payment(LoanPaymentRequest $request)
    {
        $loans    = Loan::select("id")
                    ->where('user_id', Auth::user()->id)
                    ->get();

        $array    = json_decode(json_encode($loans), true);

        $repair    = ScheduledRepayment::with('loan')
                    ->whereIn('loan_id', $array)
                    ->where('status',ScheduledRepayment::STATUS_DUE)
                    ->get();

        $total_saldo = $request->input('amount');
        foreach($repair as $bayar){
            if($total_saldo > $bayar->outstanding_amount){
                $total_saldo = $total_saldo - $bayar->outstanding_amount ;
                $bayars      = $bayar->outstanding_amount;
                $sisa_utang  = 0 ;
                $repair      = $bayar->update([
                                'outstanding_amount' => $sisa_utang,
                                'status' => ScheduledRepayment::STATUS_REPAID,
                            ]); 

                $received    = ReceivedRepayment::create([          
                    'loan_id' => $bayar->loan_id,
                    'amount' => $bayar->outstanding_amount,
                    'currency_code' => $bayar->currency_code,
                    'received_at' => $request->input('receivedAt'),
                ]);
            }else{
                $bayars = $total_saldo;
                $sisa  =  $bayar->outstanding_amount - $bayars ;
                $repair    = $bayar->update([
                    'outstanding_amount' => $sisa,
                    'status' => ScheduledRepayment::STATUS_DUE,
                ]); 

                $received    = ReceivedRepayment::create([          
                    'loan_id' => $bayar->loan_id,
                    'amount' => $bayars,
                    'currency_code' => $bayar->currency_code,
                    'received_at' => $request->input('receivedAt'),
                ]);
                break;
            }
        }

        // $loan = $this->loanService->repayLoan($loans, $request->input('amount'), $request->input('currencyCode'), $request->input('receivedAt'));
        return Response::success("success bayar");
    }
}
