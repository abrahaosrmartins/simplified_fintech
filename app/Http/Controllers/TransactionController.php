<?php

namespace App\Http\Controllers;

use App\Application\Transaction\UseCases\CreateTransactionUseCase;
use App\Application\Transaction\UseCases\Dto\PaginationParamsDto;
use App\Application\Transaction\UseCases\Dto\TransactionInputDto;
use App\Application\Transaction\UseCases\GetTransactionInvoiceUseCase;
use App\Application\Transaction\UseCases\GetTransactionsExtractUseCase;
use App\Http\Requests\CreateTransactionRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
                'transfer',
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
            Log::error($th->getMessage(), ['exception' => $th]);
            return response()->json([
                'status' => 'fail',
                'data' => [
                    'message' => 'Oops! Algo deu errado. Por favor contate a administração do sistema, ou tente mais tarde.',
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
            Log::error($th->getMessage(), ['exception' => $th]);
            return response()->json([
                'status' => 'fail',
                'data' => [
                    'message' => 'Oops! Algo deu errado. Por favor contate a administração do sistema, ou tente mais tarde.',
                ],
            ]);
        }
    }

    public function getExtract(Request $request)
    {
        try {
            $paginationParamsDto = new PaginationParamsDto(
                50,
                request()->get('page', 1),
                request()->url(),
                request()->query()
            );
            $useCase = resolve(GetTransactionsExtractUseCase::class);
            $extract = $useCase->execute($request->user(), $paginationParamsDto);

            return response()->json([
                'data' => $extract
            ]);
        } catch (\Throwable $th) {
            Log::error($th->getMessage(), ['exception' => $th]);
            return response()->json([
                'status' => 'fail',
                'data' => [
                    'message' => 'Oops! Algo deu errado. Por favor contate a administração do sistema, ou tente mais tarde.',
                ],
            ]);
        }
    }
}
