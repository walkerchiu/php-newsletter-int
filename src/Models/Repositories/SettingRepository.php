<?php

namespace WalkerChiu\Newsletter\Models\Repositories;

use Illuminate\Support\Facades\App;
use WalkerChiu\Core\Models\Forms\FormHasHostTrait;
use WalkerChiu\Core\Models\Repositories\Repository;
use WalkerChiu\Core\Models\Repositories\RepositoryHasHostTrait;
use WalkerChiu\Core\Models\Services\PackagingFactory;

class SettingRepository extends Repository
{
    use FormHasHostTrait;
    use RepositoryHasHostTrait;

    protected $instance;



    /**
     * Create a new repository instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->instance = App::make(config('wk-core.class.newsletter.setting'));
    }

    /**
     * @param String  $host_type
     * @param Int     $host_id
     * @param String  $code
     * @param Array   $data
     * @param Bool    $is_enabled
     * @param String  $target
     * @param Bool    $target_is_enabled
     * @param Bool    $auto_packing
     * @return Array|Collection|Eloquent
     */
    public function list(?string $host_type, ?int $host_id, string $code, array $data, $is_enabled = null, $target = null, $target_is_enabled = null, $auto_packing = false)
    {
        if (
            empty($host_type)
            || empty($host_id)
        ) {
            $instance = $this->instance;
        } else {
            $instance = $this->baseQueryForRepository($host_type, $host_id, $target, $target_is_enabled);
        }
        if ($is_enabled === true)      $instance = $instance->ofEnabled();
        elseif ($is_enabled === false) $instance = $instance->ofDisabled();

        $data = array_map('trim', $data);
        $repository = $instance->with(['langs' => function ($query) use ($code) {
                                    $query->ofCurrent()
                                          ->ofCode($code);
                                    }])
                                ->whereHas('langs', function ($query) use ($code) {
                                        return $query->ofCurrent()
                                                     ->ofCode($code);
                                    })
                                ->when($data, function ($query, $data) {
                                        return $query->unless(empty($data['id']), function ($query) use ($data) {
                                                    return $query->where('id', $data['id']);
                                                })
                                                ->unless(empty($data['serial']), function ($query) use ($data) {
                                                    return $query->where('serial', $data['serial']);
                                                })
                                                ->unless(empty($data['identifier']), function ($query) use ($data) {
                                                    return $query->where('identifier', $data['identifier']);
                                                })
                                                ->unless(empty($data['theme']), function ($query) use ($data) {
                                                    return $query->where('theme', 'LIKE', "%".$data['theme']."%");
                                                })
                                                ->unless(empty($data['smtp_host']), function ($query) use ($data) {
                                                    return $query->where('smtp_host', $data['smtp_host']);
                                                })
                                                ->unless(empty($data['smtp_port']), function ($query) use ($data) {
                                                    return $query->where('smtp_port', $data['smtp_port']);
                                                })
                                                ->unless(empty($data['smtp_encryption']), function ($query) use ($data) {
                                                    return $query->where('smtp_encryption', $data['smtp_encryption']);
                                                })
                                                ->unless(empty($data['name']), function ($query) use ($data) {
                                                    return $query->whereHas('langs', function ($query) use ($data) {
                                                        $query->ofCurrent()
                                                              ->where('key', 'name')
                                                              ->where('value', 'LIKE', "%".$data['name']."%");
                                                    });
                                                })
                                                ->unless(empty($data['description']), function ($query) use ($data) {
                                                    return $query->whereHas('langs', function ($query) use ($data) {
                                                        $query->ofCurrent()
                                                              ->where('key', 'description')
                                                              ->where('value', 'LIKE', "%".$data['description']."%");
                                                    });
                                                });
                                    })
                                ->orderBy('updated_at', 'DESC');

        if ($auto_packing) {
            $factory = new PackagingFactory(config('wk-newsletter.output_format'), config('wk-newsletter.pagination.pageName'), config('wk-newsletter.pagination.perPage'));
            $factory->setFieldsLang(['name', 'description']);
            return $factory->output($repository);
        }

        return $repository;
    }

    /**
     * @param Setting       $instance
     * @param String|Array  $code
     * @return Array
     */
    public function show($instance, $code): array
    {
        $data = [
            'id' => $instance ? $instance->id : '',
            'basic' => []
        ];

        if (empty($instance))
            return $data;

        $this->setEntity($instance);

        if (is_string($code)) {
            $data['basic'] = [
                  'host_type'                 => $instance->host_type,
                  'host_id'                   => $instance->host_id,
                  'serial'                    => $instance->serial,
                  'identifier'                => $instance->identifier,
                  'theme'                     => $instance->theme,
                  'smtp_host'                 => $instance->smtp_host,
                  'smtp_port'                 => $instance->smtp_port,
                  'smtp_encryption'           => $instance->smtp_encryption,
                  'smtp_encryption_supported' => config('wk-newsletter.smtp.encryption_supported'),
                  'smtp_username'             => $instance->smtp_username,
                  'smtp_password'             => null,
                  'name'                      => $instance->findLang($code, 'name'),
                  'description'               => $instance->findLang($code, 'description'),
                  'is_enabled'                => $instance->is_enabled,
                  'updated_at'                => $instance->updated_at
            ];

        } elseif (is_array($code)) {
            foreach ($code as $language) {
                $data['basic'][$language] = [
                      'host_type'                 => $instance->host_type,
                      'host_id'                   => $instance->host_id,
                      'serial'                    => $instance->serial,
                      'identifier'                => $instance->identifier,
                      'theme'                     => $instance->theme,
                      'smtp_host'                 => $instance->smtp_host,
                      'smtp_port'                 => $instance->smtp_port,
                      'smtp_encryption'           => $instance->smtp_encryption,
                      'smtp_encryption_supported' => config('wk-newsletter.smtp.encryption_supported'),
                      'smtp_username'             => $instance->smtp_username,
                      'smtp_password'             => null,
                      'name'                      => $instance->findLang($language, 'name'),
                      'description'               => $instance->findLang($language, 'description'),
                      'is_enabled'                => $instance->is_enabled,
                      'updated_at'                => $instance->updated_at
                ];
            }
        }

        return $data;
    }
}
