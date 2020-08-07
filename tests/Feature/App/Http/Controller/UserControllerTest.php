<?php

namespace Tests\Feature\App\Http\Controller;

use App\Models\User;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use DatabaseMigrations;

    public function testGetUsers()
    {
        /** @var User $user */
        $user = factory(User::class)->create()->refresh();
        $user->wallets = null;

        $this->get('/v1/users');

        $this->assertEquals(
            json_encode([$user]), $this->response->getContent()
        );
    }
}
