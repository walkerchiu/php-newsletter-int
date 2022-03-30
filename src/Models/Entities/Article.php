<?php

namespace WalkerChiu\Newsletter\Models\Entities;

use WalkerChiu\Core\Models\Entities\Entity;

class Article extends Entity
{
    /**
     * Create a new instance.
     *
     * @param Array  $attributes
     * @return void
     */
    public function __construct(array $attributes = [])
    {
        $this->table = config('wk-core.table.newsletter.articles');

        $this->fillable = array_merge($this->fillable, [
            'setting_id',
            'serial',
            'style', 'subject', 'content'
        ]);

        parent::__construct($attributes);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function setting()
    {
        return $this->belongsTo(config('wk-core.class.newsletter.setting'), 'setting_id', 'id');
    }
}
