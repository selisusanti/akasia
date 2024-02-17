<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use App\Http\Requests\LoanShowRequest;
use App\Http\Requests\LoanCreateRequest;
use App\Models\Loan;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response as HttpResponse;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Services\LoanService;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
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
     * @param LoanShowRequest $request
     *
     * @return JsonResponse
     */
    public function store(LoanCreateRequest $request)
    {
        $loan = $this->loanService->createLoan(Auth::user(), $request->input('amount'), $request->input('currency_code'), $request->input('terms'), $request->input('processed_at'));
        return Response::success($loan);
    }
}
