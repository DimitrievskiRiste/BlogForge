<?php

namespace Tests\Unit;

use App\Installer\Installer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\Rules\In;
use Tests\TestCase;
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
        $this->assertIsArray(Installer::db()->getDefaultTables(), 'Successfully');
    }
    public function test_db_connection():void {
        var_dump(Installer::db()->getConnection());
        $this->assertTrue(Installer::db()->getConnection());
    }
    public function test_no_missing_tables():void {
        $installer = app(Installer::class);
        $this->assertSame([], $installer->db()->hasMissingTables());
    }
    public function test_missing_table_settings():void {
        $missingTables = array(0 => 'jobs', 1 => 'website_settings', 2 => 'content_translations', 3 => 'comments');
        $this->assertSame($missingTables, Installer::db()->hasMissingTables());
    }
}
