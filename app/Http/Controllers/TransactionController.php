<?php

namespace App\Http\Controllers;

use App\Application\Transaction\UseCases\CreateTransactionUseCase;
use App\Application\Transaction\UseCases\Dto\TransactionInputDto;
use App\Http\Requests\CreateTransactionRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function createTransaction(CreateTransactionRequest $request)
    {
        DB::beginTransaction();
        try {
            $transactionInputDto = new TransactionInputDto(
                $request->input('value'),
                $request->input('payer'),
                $request->input('payee'),
            );

            $useCase = resolve(CreateTransactionUseCase::class);
            $transaction = $useCase->execute($request->user(), $transactionInputDto);

            DB::commit();

            return response()->json([
                'data' => $transaction
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'fail',
                'data' => [
                    'message' => $e->getMessage(),
                ],
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'status' => 'fail',
                'data' => [
                    'message' => $th->getMessage(),
                ],
            ]);
        }
    }

    public function getInvoice(Request $request, int $transactionId)
    {
        try {
            $useCase = resolve(GetTransactionInvoiceUseCase::class);
            $invoice = $useCase->execute($request->user(), $transactionId);
            return response()->json([
                'data' => $invoice
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'fail',
                'data' => [
                    'message' => $th->getMessage(),
                ],
            ]);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        abort(405, 'Method Not Allowed');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        abort(405, 'Method Not Allowed');
    }
}
