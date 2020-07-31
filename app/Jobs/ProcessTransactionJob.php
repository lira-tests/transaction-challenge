<?php

namespace App\Jobs;

use App\Exceptions\AuthorizationFailException;
use App\Exceptions\PayerInsufficientAmountException;
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

    protected User $payer;

    protected User $payee;

    public function __construct(Transaction $transaction)
    {
        $this->transaction = $transaction;
        $this->payer = $this->getUser($transaction->payer->id);
        $this->payee = $this->getUser($transaction->payee->id);
        $this->authorizationService = new AuthorizationService();
    }

    public function handle() {
        try {
            DB::beginTransaction();

            $this->updateUsers();

            if (!$this->authorizationService->approved($this->transaction->id)) {
                throw new AuthorizationFailException();
            }

            if ($this->transaction->amount > $this->payer->wallets->amount) {
                throw new PayerInsufficientAmountException();
            }

            $this->updateWallets();

            $this->updateTransaction(Transaction::STATUS_EXECUTED, 'Success');

            DB::commit();

            Log::info(sprintf('Transaction %s executed with %s', $this->transaction->id, 'success'));

            dispatch(new NotifyJobQueue(['to' => $this->payer->email, 'message' => 'Your payment was confirmed']));
            dispatch(new NotifyJobQueue(['to' => $this->payee->email, 'message' => 'You received a payment']));

            return true;
        } catch(\Throwable $e) {
            DB::rollBack();

            $this->updateTransaction(Transaction::STATUS_FAILED, $e->getMessage());

            Log::info(sprintf('Transaction %s executed with %s', $this->transaction->id, 'fail'));

            dispatch(new NotifyJobQueue(['to' => $this->payer->email, 'message' => 'Your payment was failed']));

            throw $e;
        }
    }

    protected function getUser($id) : User
    {
        return User::with('wallets')->findOrFail($id);
    }

    protected function updateUsers()
    {
        $this->payer->refresh();
        $this->payee->refresh();
    }

    protected function updateWallets()
    {
        $this->payee->wallets->amount = bcadd(
            $this->payee->wallets->amount,
            $this->transaction->amount,
            2
        );
        $this->payer->wallets->amount = bcsub(
            $this->payer->wallets->amount,
            $this->transaction->amount,
            2
        );

        $this->payee->push();
        $this->payer->push();
    }

    protected function updateTransaction($status, $reason) : void
    {
        $this->transaction->status = $status;
        $this->transaction->reason = $reason;
        $this->transaction->save();
    }
}
