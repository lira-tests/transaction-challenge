<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Wallet;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class TransactionControllerTest extends TestCase
{
    use DatabaseMigrations;

    public function testInvalid()
    {
        /** @var User $user */
        $user = factory(User::class)->create()->refresh();
        $user->wallets = null;

        $this->post('/v1/transactions', []);

        $this->assertEquals(
            '{"amount":["The amount field is required."],"payer":["The payer field is required."],"payee":["The payee field is required."]}', $this->response->getContent()
        );
    }

    public function testCompanyPayerInvalid()
    {
        /** @var User $company */
        $company = factory(User::class)->create(
            [
                'type' => User::TYPE_COMPANY
            ]
        )->refresh();
        $company->wallets()->save(factory(Wallet::class)->make());

        /** @var User $user */
        $user = factory(User::class)->create()->refresh();
        $user->wallets()->save(factory(Wallet::class)->make());

        $this->post('/v1/transactions', [
            'payer' => $company->id,
            'payee' => $user->id,
            'amount' => 10.00
        ]);

        $this->assertEquals(
            '{"payer":["The payer can\'t be a company."]}', $this->response->getContent()
        );
    }

    public function testSameInvalid()
    {
        /** @var User $user */
        $user = factory(User::class)->create(
            [
                'type' => User::TYPE_PERSON
            ]
        )->refresh();
        $user->wallets()->save(factory(Wallet::class)->make());

        $this->post('/v1/transactions', [
            'payer' => $user->id,
            'payee' => $user->id,
            'amount' => 10.00
        ]);

        $this->assertEquals(
            '{"payee":["The payee and payer must be different."]}', $this->response->getContent()
        );
    }

    public function testAmountInvalid()
    {
        /** @var User $user */
        $user = factory(User::class)->create(
            [
                'type' => User::TYPE_PERSON
            ]
        )->refresh();
        $user->wallets()->save(factory(Wallet::class)->make());

        /** @var User $user2 */
        $user2 = factory(User::class)->create(
            [
                'type' => User::TYPE_PERSON
            ]
        )->refresh();
        $user2->wallets()->save(factory(Wallet::class)->make());

        $this->post('/v1/transactions', [
            'payer' => $user->id,
            'payee' => $user2->id,
            'amount' => 99.87878798
        ]);

        $this->assertEquals(
            '{"amount":["The amount format is invalid."]}', $this->response->getContent()
        );
    }

}
