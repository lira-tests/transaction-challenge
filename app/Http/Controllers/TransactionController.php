<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\User;
use App\Rules\PayerHasAmount;
use App\Rules\PayerIsCompany;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Psr\Log\LoggerInterface;

class TransactionController extends Controller
{
    protected Transaction $transaction;

    protected LoggerInterface $log;

    public function __construct(Transaction $transaction, LoggerInterface $log)
    {
        $this->transaction = $transaction;
        $this->log = $log;
    }

    public function index()
    {
        return $this->transaction->get();
    }

    public function create(Request $request)
    {
        $payer = $request->input('payer');
        $payee = $request->input('payee');
        $amount = $request->input('amount');

        $this->log->info(sprintf('Requested: %s', implode(',', $request->input())));

        $this->validate(
            $request,
            [
                'amount' => ['required','numeric','regex:/^\d+(\.\d{1,2})?$/'],
                'payer' => ['required','exists:users,id', new PayerIsCompany, new PayerHasAmount($amount)],
                'payee' => ['required','exists:users,id','different:payer'],
            ]
        );

        $transaction = Transaction::create(
            [
                'payer_user_id' => $payer,
                'payee_user_id' => $payee,
                'amount' => $amount,
                'reason' => '',
            ]
        );

        $this->log->info(sprintf('Created transaction: %s', $transaction->id));

        try {
            DB::beginTransaction();

            $payerObj = User::with('wallets')->findOrFail($payer);
            $payeeObj = User::with('wallets')->findOrFail($payee);

            $payeeObj->wallets->amount = bcadd($payeeObj->wallets->amount, $amount, 2);
            $payerObj->wallets->amount = bcsub($payerObj->wallets->amount, $amount, 2);

            $payeeObj->push();
            $payerObj->push();

            $transaction->status = Transaction::STATUS_EXECUTED;
            $transaction->reason = 'Success';
            $transaction->save();

            DB::commit();

            $this->log->info(sprintf('Transaction %s executed with %s', $transaction->id, 'success'));

            return $transaction->toArray();
        } catch(\Throwable $e) {
            DB::rollBack();

            $transaction->status = Transaction::STATUS_FAILED;
            $transaction->reason = $e->getMessage();
            $transaction->save();

            $this->log->info(sprintf('Transaction %s executed with %s', $transaction->id, 'fail'));

            throw $e;
        }
    }
}
