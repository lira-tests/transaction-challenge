<?php

namespace App\Jobs;

use App\Exceptions\AuthorizationFailException;
use App\Jobs\Job;
use App\Models\Transaction;
use App\Models\User;
use App\Services\AuthorizationService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProcessTransactionJob extends Job
{
    protected Transaction $transaction;

    protected AuthorizationService $authorizationService;

    public function __construct(Transaction $transaction)
    {
        $this->transaction = $transaction;
        $this->authorizationService = new AuthorizationService();
    }

    public function handle() {
        try {
            DB::beginTransaction();

            if (!$this->authorizationService->approved($this->transaction->id)) {
                throw new AuthorizationFailException();
            }

            $payerObj = User::with('wallets')->findOrFail($this->transaction->payer_user_id);
            $payeeObj = User::with('wallets')->findOrFail($this->transaction->payee_user_id);

            $payeeObj->wallets->amount = bcadd($payeeObj->wallets->amount, $this->transaction->amount, 2);
            $payerObj->wallets->amount = bcsub($payerObj->wallets->amount, $this->transaction->amount, 2);

            $payeeObj->push();
            $payerObj->push();

            $this->transaction->status = Transaction::STATUS_EXECUTED;
            $this->transaction->reason = 'Success';
            $this->transaction->save();

            DB::commit();

            Log::info(sprintf('Transaction %s executed with %s', $this->transaction->id, 'success'));

            dispatch(new NotifyJobQueue(['to' => $payerObj->email, 'message' => 'Your payment was confirmed']));
            dispatch(new NotifyJobQueue(['to' => $payeeObj->email, 'message' => 'You received a payment']));

            return true;
        } catch(\Throwable $e) {
            DB::rollBack();

            $payerObj = User::with('wallets')->findOrFail($this->transaction->payer_user_id);

            $this->transaction->status = Transaction::STATUS_FAILED;
            $this->transaction->reason = $e->getMessage();
            $this->transaction->save();

            Log::info(sprintf('Transaction %s executed with %s', $this->transaction->id, 'fail'));

            dispatch(new NotifyJobQueue(['to' => $payerObj->email, 'message' => 'Your payment was failed']));

            throw $e;
        }
    }
}
