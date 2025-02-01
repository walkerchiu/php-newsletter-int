<?php

namespace WalkerChiu\Newsletter;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use WalkerChiu\Newsletter\Models\Entities\Setting;
use WalkerChiu\Newsletter\Models\Entities\SettingLang;

class SettingTest extends \Orchestra\Testbench\TestCase
{
    use RefreshDatabase;

    /**
     * Setup the test environment.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->loadMigrationsFrom(__DIR__ .'/../migrations');
        $this->withFactories(__DIR__ .'/../../src/database/factories');
    }

    /**
     * To load your package service provider, override the getPackageProviders.
     *
     * @param \Illuminate\Foundation\Application  $app
     * @return Array
     */
    protected function getPackageProviders($app)
    {
        return [\WalkerChiu\Core\CoreServiceProvider::class,
                \WalkerChiu\Newsletter\NewsletterServiceProvider::class];
    }

    /**
     * Define environment setup.
     *
     * @param \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
    }

    /**
     * A basic functional test on Setting.
     *
     * For WalkerChiu\Newsletter\Models\Entities\Setting
     * 
     * @return void
     */
    public function testSetting()
    {
        // Config
        Config::set('wk-core.onoff.core-lang_core', 0);
        Config::set('wk-newsletter.onoff.core-lang_core', 0);
        Config::set('wk-core.lang_log', 1);
        Config::set('wk-newsletter.lang_log', 1);
        Config::set('wk-core.soft_delete', 1);
        Config::set('wk-newsletter.soft_delete', 1);

        $faker = \Faker\Factory::create();

        $group_id_1 = 1;
        $group_id_2 = 2;
        $group_id_3 = 3;
        DB::table(config('wk-core.table.group.groups'))->insert([
            'id'         => $group_id_1,
            'serial'     => $faker->username,
            'identifier' => $faker->slug,
            'is_enabled' => 1
        ]);
        DB::table(config('wk-core.table.group.groups'))->insert([
            'id'         => $group_id_2,
            'serial'     => $faker->username,
            'identifier' => $faker->slug,
            'is_enabled' => 1
        ]);
        DB::table(config('wk-core.table.group.groups'))->insert([
            'id'         => $group_id_3,
            'serial'     => $faker->username,
            'identifier' => $faker->slug,
            'is_enabled' => 1
        ]);

        // Give
        $db_morph_1 = factory(Setting::class)->create(['host_id' => $group_id_1, 'host_type' => config('wk-core.class.group.group')]);
        $db_morph_2 = factory(Setting::class)->create(['host_id' => $group_id_2, 'host_type' => config('wk-core.class.group.group')]);
        $db_morph_3 = factory(Setting::class)->create(['host_id' => $group_id_3, 'host_type' => config('wk-core.class.group.group'), 'is_enabled' => 1]);

        // Get records after creation
            // When
            $records = Setting::all();
            // Then
            $this->assertCount(3, $records);

        // Delete someone
            // When
            $db_morph_2->delete();
            $records = Setting::all();
            // Then
            $this->assertCount(2, $records);

        // Resotre someone
            // When
            Setting::withTrashed()
                   ->find(2)
                   ->restore();
            $record_2 = Setting::find(2);
            $records = Setting::all();
            // Then
            $this->assertNotNull($record_2);
            $this->assertCount(3, $records);

        // Return Lang class
            // When
            $class = $record_2->lang();
            // Then
            $this->assertEquals($class, SettingLang::class);

        // Scope query on enabled records
            // When
            $records = Setting::ofEnabled()
                              ->get();
            // Then
            $this->assertCount(1, $records);

        // Scope query on disabled records
            // When
            $records = Setting::ofDisabled()
                              ->get();
            // Then
            $this->assertCount(2, $records);
    }
}
