<?php

namespace WalkerChiu\Newsletter\Models\Repositories;

use Illuminate\Support\Facades\App;
use WalkerChiu\Core\Models\Forms\FormTrait;
use WalkerChiu\Core\Models\Repositories\Repository;
use WalkerChiu\Core\Models\Repositories\RepositoryTrait;
use WalkerChiu\Core\Models\Services\PackagingFactory;

class ArticleRepository extends Repository
{
    use FormTrait;
    use RepositoryTrait;

    protected $instance;



    /**
     * Create a new repository instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->instance = App::make(config('wk-core.class.newsletter.article'));
    }

    /**
     * @param Array  $data
     * @param Bool   $auto_packing
     * @return Array|Collection|Eloquent
     */
    public function list(array $data, ?bool $is_enabled)
    {
        $instance = $this->instance;
        if ($is_enabled === true)      $instance = $instance->ofEnabled();
        elseif ($is_enabled === false) $instance = $instance->ofDisabled();

        $data = array_map('trim', $data);
        $repository = $instance->when($data, function ($query, $data) {
                                    return $query->unless(empty($data['id']), function ($query) use ($data) {
                                                return $query->where('id', $data['id']);
                                            })
                                            ->unless(empty($data['setting_id']), function ($query) use ($data) {
                                                return $query->where('setting_id', $data['setting_id']);
                                            })
                                            ->unless(empty($data['serial']), function ($query) use ($data) {
                                                return $query->where('serial', $data['serial']);
                                            })
                                            ->unless(empty($data['style']), function ($query) use ($data) {
                                                return $query->where('style', 'LIKE', "%".$data['style']."%");
                                            })
                                            ->unless(empty($data['subject']), function ($query) use ($data) {
                                                return $query->where('subject', 'LIKE', "%".$data['subject']."%");
                                            })
                                            ->unless(empty($data['content']), function ($query) use ($data) {
                                                return $query->where('content', 'LIKE', "%".$data['content']."%");
                                            });
                                })
                                ->orderBy('updated_at', 'DESC');

        if ($auto_packing) {
            $factory = new PackagingFactory(config('wk-newsletter.output_format'), config('wk-newsletter.pagination.pageName'), config('wk-newsletter.pagination.perPage'));
            return $factory->output($repository);
        }

        return $repository;
    }

    /**
     * @param Article  $instance
     * @return Array
     */
    public function show($instance): array
    {
        if (empty($instance))
            return [
                'id'         => '',
                'setting_id' => '',
                'serial'     => '',
                'style'      => '',
                'subject'    => '',
                'content'    => '',
                'is_enabled' => '',
                'updated_at' => ''
            ];

        $this->setEntity($instance);

        return [
            'id'         => $instance->id,
            'setting_id' => $instance->setting_id,
            'serial'     => $instance->serial,
            'style'      => $instance->style,
            'subject'    => $instance->subject,
            'content'    => $instance->content,
            'is_enabled' => $instance->is_enabled,
            'updated_at' => $instance->updated_at
        ];
    }
}
