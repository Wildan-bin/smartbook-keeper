<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BalanceResource;
use App\Services\FinancialService;
use Illuminate\Http\Request;

class FinancialController extends Controller
{
    protected $financialService;

    public function __construct(FinancialService $financialService)
    {
        $this->financialService = $financialService;
    }

    public function balances(Request $request)
    {
        $balances = $this->financialService->getUserBalances(
            $request->user()->id,
            $request->get('month')
        );

        return BalanceResource::collection($balances);
    }

    public function totalBalance(Request $request)
    {
        $totalBalance = $this->financialService->getUserTotalBalance(
            $request->user()->id
        );

        return response()->json($totalBalance);
    }
}