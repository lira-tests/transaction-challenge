<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessTransactionJob;
use App\Jobs\NotifyJobQueue;
use App\Models\Transaction;
use App\Models\User;
use App\Rules\PayerHasAmount;
use App\Rules\PayerIsCompany;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Psr\Log\LoggerInterface;

class TransactionController extends Controller
{
    protected Transaction $transaction;

    protected LoggerInterface $log;

    protected $notificationService;

    public function __construct(
        Transaction $transaction,
        LoggerInterface $log,
        NotificationService $notificationService
    )
    {
        $this->transaction = $transaction;
        $this->log = $log;
        $this->notificationService = $notificationService;
    }

    public function index()
    {
        return $this->transaction->get();
    }

    public function create(Request $request)
    {
        $payer = $request->input('payer');
        $payee = $request->input('payee');
        $amount = $request->input('amount', 0.00);

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
        )->refresh();

        $this->log->info(sprintf('Created transaction: %s', $transaction->id));

        dispatch(new ProcessTransactionJob($transaction));

        return $transaction;
    }
}
