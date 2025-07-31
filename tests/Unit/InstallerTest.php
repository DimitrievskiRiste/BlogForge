<?php

namespace Tests\Unit;

use App\Installer\Installer;
use PHPUnit\Framework\TestCase;

class InstallerTest extends TestCase
{
    /**
     * A basic unit test example.
     */
    public function test_if_env_instance_loaded(): void
    {
        $this->assertIsObject(Installer::env());
    }
    public function test_if_db_host_isEmpty(): void
    {
        $this->assertIsBool(Installer::env()->isDBHostEmpty());
    }
    public function test_if_can_get_defaultTables_static():void
    {
        $this->assertIsArray(Installer::db()->getDefaultTables(),'Successfully');
    }
}
