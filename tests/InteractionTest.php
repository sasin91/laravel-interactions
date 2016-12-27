<?php

namespace Tests\Feature;

use Illuminate\Contracts\Foundation\Application;
use Sasin91\LaravelInteractions\Interaction;
use Sasin91\LaravelInteractions\Interactions\DemoInteraction;
use TestCase;

class TrinityCoreAccountTest extends TestCase
{
    /** @test */
    public function can_swap_interaction_for_closure_with_dependencies()
    {
        Interaction::swap(DemoInteraction::class, function (Application $dependency, $primitive) {
            return [$dependency, $primitive];
        });

        $interaction = Interaction::call(DemoInteraction::class, [
            'hello world'
        ]);

        $this->assertTrue(is_array($interaction));
        $this->assertInstanceOf(Application::class, $interaction[0]);
        $this->assertEquals('hello world', $interaction[1]);
    }
}
