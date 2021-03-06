<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testExample()
    {
        $this->get('/');

        $expected = json_encode([
            'application' => config('app.name'),
            'version' => $this->app->version(),
        ]);

        $this->assertEquals(
            $expected, $this->response->getContent()
        );
    }
}
