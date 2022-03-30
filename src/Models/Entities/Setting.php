<?php

namespace WalkerChiu\Newsletter\Models\Entities;

use WalkerChiu\Core\Models\Entities\Entity;
use WalkerChiu\Core\Models\Entities\LangTrait;

class Setting extends Entity
{
    use LangTrait;



    /**
     * Create a new instance.
     *
     * @param Array  $attributes
     * @return void
     */
    public function __construct(array $attributes = [])
    {
        $this->table = config('wk-core.table.newsletter.settings');

        $this->fillable = array_merge($this->fillable, [
            'host_type', 'host_id',
            'serial', 'identifier', 'theme',
            'smtp_host', 'smtp_port', 'smtp_encryption', 'smtp_username', 'smtp_password'
        ]);

        parent::__construct($attributes);
    }

    /**
     * Get it's lang entity.
     *
     * @return Lang
     */
    public function lang()
    {
        if (
            config('wk-core.onoff.core-lang_core')
            || config('wk-newsletter.onoff.core-lang_core')
        ) {
            return config('wk-core.class.core.langCore');
        } else {
            return config('wk-core.class.newsletter.settingLang');
        }
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function langs()
    {
        if (
            config('wk-core.onoff.core-lang_core')
            || config('wk-newsletter.onoff.core-lang_core')
        ) {
            return $this->langsCore();
        } else {
            return $this->hasMany(config('wk-core.class.newsletter.settingLang'), 'morph_id', 'id');
        }
    }

    /**
     * Get the owning host model.
     */
    public function host()
    {
        return $this->morphTo();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function articles()
    {
        return $this->hasMany(config('wk-core.class.newsletter.article'), 'setting_id', 'id');
    }
}
