<?php

namespace Tests\Feature;

use App\Installer\DBEnvironment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class DBEnvironmentTest extends TestCase
{
    /**
     * Test isDbHostEmpty method
     */
    public function test_isDBHostEmpty() :void {
        $env = new DBEnvironment();
        $this->assertIsBool($env->isDBHostEmpty(),'Testing if db host env variable is true or false');
    }
    public function test_isDBUserEmpty():void {
        $env = new DBEnvironment();
        $test = $env->isDBUsernameEmpty();
        $this->assertSame(false, $test);
    }
    public function test_isDBPortBool() :void {
        $env = new DBEnvironment();
        $this->assertIsBool($env->isDBPortEmpty());
    }
}
