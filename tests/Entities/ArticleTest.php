<?php

namespace WalkerChiu\Newsletter;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use WalkerChiu\Newsletter\Models\Entities\Setting;
use WalkerChiu\Newsletter\Models\Entities\SettingLang;
use WalkerChiu\Newsletter\Models\Entities\Article;

class ArticleTest extends \Orchestra\Testbench\TestCase
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
     * For WalkerChiu\Newsletter\Models\Entities\Article
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

        $db_setting = factory(Setting::class)->create();

        // Give
        $db_morph_1 = factory(Article::class)->create(['setting_id' => $db_setting->id]);
        $db_morph_2 = factory(Article::class)->create(['setting_id' => $db_setting->id]);
        $db_morph_3 = factory(Article::class)->create(['setting_id' => $db_setting->id, 'is_enabled' => 1]);

        // Get records after creation
            // When
            $records = Article::all();
            // Then
            $this->assertCount(3, $records);

        // Delete someone
            // When
            $db_morph_2->delete();
            $records = Article::all();
            // Then
            $this->assertCount(2, $records);

        // Resotre someone
            // When
            Article::withTrashed()
                   ->find($db_morph_2->id)
                   ->restore();
            $record_2 = Article::find($db_morph_2->id);
            $records = Article::all();
            // Then
            $this->assertNotNull($record_2);
            $this->assertCount(3, $records);

        // Scope query on enabled records
            // When
            $records = Article::ofEnabled()
                              ->get();
            // Then
            $this->assertCount(1, $records);

        // Scope query on disabled records
            // When
            $records = Article::ofDisabled()
                              ->get();
            // Then
            $this->assertCount(2, $records);
    }
}
