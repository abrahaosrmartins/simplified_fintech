<?php

namespace App\Http\Controllers;

use App\Application\Transaction\UseCases\CreateTransactionUseCase;
use App\Application\Transaction\UseCases\Dto\TransactionInputDto;
use App\Http\Requests\CreateTransactionRequest;
use App\Http\Resources\TransactionInvoiceResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        abort(405, 'Method Not Allowed');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        abort(405, 'Method Not Allowed');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateTransactionRequest $request)
    {
        DB::beginTransaction();
        try {
            $transactionInputDto = new TransactionInputDto(
                $request->input('value'),
                $request->input('payer'),
                $request->input('payee'),
            );

            $useCase = resolve(CreateTransactionUseCase::class);
            $invoice = $useCase->execute($request->user(), $transactionInputDto);

            DB::commit();

            return response()->json(TransactionInvoiceResource::toArray($invoice));

        } catch (\Exception $e) {
            DB::rollBack();
            //throw $e;
        } catch (\Throwable $th) {
            DB::rollBack();
            //throw $th;
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        abort(405, 'Method Not Allowed');
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
