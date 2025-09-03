<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BalanceResource;
use App\Services\FinancialService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class FinancialController extends Controller
{
    protected $financialService;

    public function __construct(FinancialService $financialService)
    {
        $this->financialService = $financialService;
    }

    // EXISTING METHODS - TETAP SAMA
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

    // NEW METHODS - TAMBAHAN BARU
    public function createBalance(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'currency' => 'required|string|size:3',
            ]);

            $balance = $this->financialService->createBalance(
                $request->user()->id,
                $validated
            );

            return response()->json([
                'status' => 'success',
                'message' => 'Wallet created successfully',
                'data' => new BalanceResource($balance)
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create wallet',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getBalance(Request $request, $id)
    {
        try {
            $balance = $this->financialService->getBalance(
                $request->user()->id,
                $id
            );

            if (!$balance) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Wallet not found'
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'data' => new BalanceResource($balance)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to get wallet',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function updateBalance(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'currency' => 'required|string|size:3',
            ]);

            $balance = $this->financialService->updateBalance(
                $request->user()->id,
                $id,
                $validated
            );

            if (!$balance) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Wallet not found'
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Wallet updated successfully',
                'data' => new BalanceResource($balance)
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update wallet',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function deleteBalance(Request $request, $id)
    {
        try {
            $result = $this->financialService->deleteBalance(
                $request->user()->id,
                $id
            );

            if (!$result) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Wallet not found or cannot be deleted'
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Wallet deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete wallet',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}