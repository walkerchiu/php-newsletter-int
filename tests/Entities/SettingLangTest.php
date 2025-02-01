<?php

namespace WalkerChiu\Newsletter;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use WalkerChiu\Newsletter\Models\Entities\Setting;
use WalkerChiu\Newsletter\Models\Entities\SettingLang;

class SettingLangTest extends \Orchestra\Testbench\TestCase
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
     * A basic functional test on SettingLang.
     *
     * For WalkerChiu\Core\Models\Entities\Lang
     *     WalkerChiu\Newsletter\Models\Entities\SettingLang
     *
     * @return void
     */
    public function testSettingLang()
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

        // Give
        $db_morph_1 = factory(Setting::class)->create(['host_id' => $group_id_1, 'host_type' => config('wk-core.class.group.group')]);
        $db_morph_2 = factory(Setting::class)->create(['host_id' => $group_id_2, 'host_type' => config('wk-core.class.group.group')]);
        factory(SettingLang::class)->create(['morph_id' => 1, 'morph_type' => Setting::class, 'code' => 'en_us', 'key' => 'name', 'value' => 'Hello']);
        factory(SettingLang::class)->create(['morph_id' => 1, 'morph_type' => Setting::class, 'code' => 'en_us', 'key' => 'description']);
        factory(SettingLang::class)->create(['morph_id' => 1, 'morph_type' => Setting::class, 'code' => 'zh_tw', 'key' => 'description']);
        factory(SettingLang::class)->create(['morph_id' => 1, 'morph_type' => Setting::class, 'code' => 'en_us', 'key' => 'name']);
        factory(SettingLang::class)->create(['morph_id' => 2, 'morph_type' => Setting::class, 'code' => 'en_us', 'key' => 'name']);
        factory(SettingLang::class)->create(['morph_id' => 2, 'morph_type' => Setting::class, 'code' => 'zh_tw', 'key' => 'description']);

        // Get records after creation
            // When
            $records = SettingLang::all();
            // Then
            $this->assertCount(6, $records);

        // Get record's morph
            // When
            $record = SettingLang::find(1);
            // Then
            $this->assertNotNull($record);
            $this->assertInstanceOf(Setting::class, $record->morph);

        // Scope query on whereCode
            // When
            $records = SettingLang::ofCode('en_us')
                                  ->get();
            // Then
            $this->assertCount(4, $records);

        // Scope query on whereKey
            // When
            $records = SettingLang::ofKey('name')
                                  ->get();
            // Then
            $this->assertCount(3, $records);

        // Scope query on whereCodeAndKey
            // When
            $records = SettingLang::ofCodeAndKey('en_us', 'name')
                                  ->get();
            // Then
            $this->assertCount(3, $records);

        // Scope query on whereMatch
            // When
            $records = SettingLang::ofMatch('en_us', 'name', 'Hello')
                                  ->get();
            // Then
            $this->assertCount(1, $records);
            $this->assertTrue($records->contains('id', 1));
    }

    /**
     * A basic functional test on SettingLang.
     *
     * For WalkerChiu\Core\Models\Entities\LangTrait
     *     WalkerChiu\Newsletter\Models\Entities\SettingLang
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

        // Give
        $db_morph_1 = factory(Setting::class)->create(['host_id' => $group_id_1, 'host_type' => config('wk-core.class.group.group')]);
        $db_morph_2 = factory(Setting::class)->create(['host_id' => $group_id_2, 'host_type' => config('wk-core.class.group.group')]);
        factory(SettingLang::class)->create(['morph_id' => 1, 'morph_type' => Setting::class, 'code' => 'en_us', 'key' => 'name', 'value' => 'Hello']);
        factory(SettingLang::class)->create(['morph_id' => 1, 'morph_type' => Setting::class, 'code' => 'en_us', 'key' => 'description']);
        factory(SettingLang::class)->create(['morph_id' => 1, 'morph_type' => Setting::class, 'code' => 'zh_tw', 'key' => 'description']);
        factory(SettingLang::class)->create(['morph_id' => 1, 'morph_type' => Setting::class, 'code' => 'en_us', 'key' => 'name']);
        factory(SettingLang::class)->create(['morph_id' => 2, 'morph_type' => Setting::class, 'code' => 'en_us', 'key' => 'name']);
        factory(SettingLang::class)->create(['morph_id' => 2, 'morph_type' => Setting::class, 'code' => 'zh_tw', 'key' => 'description']);

        // Get lang of record
            // When
            $record_1 = Setting::find(1);
            $lang_1   = SettingLang::find(1);
            $lang_4   = SettingLang::find(4);
            // Then
            $this->assertNotNull($record_1);
            $this->assertTrue(!$lang_1->is_current);
            $this->assertTrue($lang_4->is_current);
            $this->assertCount(4, $record_1->langs);
            $this->assertInstanceOf(SettingLang::class, $record_1->findLang('en_us', 'name', 'entire'));
            $this->assertEquals(4, $record_1->findLang('en_us', 'name', 'entire')->id);
            $this->assertEquals(4, $record_1->findLangByKey('name', 'entire')->id);
            $this->assertEquals(2, $record_1->findLangByKey('description', 'entire')->id);

        // Get lang's histories of record
            // When
            $histories_1 = $record_1->getHistories('en_us', 'name');
            $record_2 = Setting::find(2);
            $histories_2 = $record_2->getHistories('en_us', 'name');
            // Then
            $this->assertCount(1, $histories_1);
            $this->assertCount(0, $histories_2);
    }
}
