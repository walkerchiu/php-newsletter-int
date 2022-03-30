<?php

namespace WalkerChiu\Newsletter\Models\Forms;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
use Illuminate\Validation\Rule;
use WalkerChiu\Core\Models\Forms\FormRequest;

class SettingFormRequest extends FormRequest
{
    /**
     * @Override Illuminate\Foundation\Http\FormRequest::getValidatorInstance
     */
    protected function getValidatorInstance()
    {
        $request = Request::instance();
        $data = $this->all();
        if (
            $request->isMethod('put')
            && empty($data['id'])
            && isset($request->id)
        ) {
            $data['id'] = (int) $request->id;
            $this->getInputSource()->replace($data);
        }

        return parent::getValidatorInstance();
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return Array
     */
    public function attributes()
    {
        return [
            'host_type'       => trans('php-newsletter::setting.host_type'),
            'host_id'         => trans('php-newsletter::setting.host_id'),
            'serial'          => trans('php-newsletter::setting.serial'),
            'identifier'      => trans('php-newsletter::setting.identifier'),
            'theme'           => trans('php-newsletter::setting.theme'),
            'is_enabled'      => trans('php-newsletter::setting.is_enabled'),

            'smtp_host'       => trans('php-newsletter::setting.smtp_host'),
            'smtp_port'       => trans('php-newsletter::setting.smtp_port'),
            'smtp_encryption' => trans('php-newsletter::setting.smtp_encryption'),
            'smtp_username'   => trans('php-newsletter::setting.smtp_username'),
            'smtp_password'   => trans('php-newsletter::setting.smtp_password'),

            'name'            => trans('php-newsletter::setting.name'),
            'description'     => trans('php-newsletter::setting.description'),
            'keywords'        => trans('php-newsletter::setting.keywords')
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return Array
     */
    public function rules()
    {
        $rules = [
            'host_type'       => 'required_with:host_id|string',
            'host_id'         => 'required_with:host_type|integer|min:1',
            'serial'          => '',
            'identifier'      => '',
            'theme'           => '',
            'is_enabled'      => 'boolean',

            'smtp_host'       => 'nullable|required_with:smtp_port|string|min:7|max:255',
            'smtp_port'       => 'nullable|required_with:smtp_encryption|numeric|min:1|max:65535',
            'smtp_encryption' => 'nullable|required_with:smtp_username|string|min:2|max:5',
            'smtp_username'   => 'nullable|required_with:smtp_password|string|min:2|max:255',
            'smtp_password'   => 'nullable|required_with:smtp_username|string|min:4|max:255',

            'name'            => 'required|string|max:255',
            'description'     => '',
            'keywords'        => ''
        ];

        $request = Request::instance();
        if (
            $request->isMethod('put')
            && isset($request->id)
        ) {
            $rules = array_merge($rules, ['id' => ['required','integer','min:1','exists:'.config('wk-core.table.newsletter.settings').',id']]);
        }

        return $rules;
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return Array
     */
    public function messages()
    {
        return [
            'id.required'             => trans('php-core::validation.required'),
            'id.integer'              => trans('php-core::validation.integer'),
            'id.min'                  => trans('php-core::validation.min'),
            'id.exists'               => trans('php-core::validation.exists'),
            'host_type.required_with' => trans('php-core::validation.required_with'),
            'host_type.string'        => trans('php-core::validation.string'),
            'host_id.required_with'   => trans('php-core::validation.required_with'),
            'host_id.integer'         => trans('php-core::validation.integer'),
            'host_id.min'             => trans('php-core::validation.min'),
            'is_enabled.boolean'      => trans('php-core::validation.boolean'),

            'smtp_host.required_with'       => trans('php-core::validation.required_with'),
            'smtp_host.string'              => trans('php-core::validation.string'),
            'smtp_host.min'                 => trans('php-core::validation.min'),
            'smtp_host.max'                 => trans('php-core::validation.max'),
            'smtp_port.required_with'       => trans('php-core::validation.required_with'),
            'smtp_port.numeric'             => trans('php-core::validation.string'),
            'smtp_port.min'                 => trans('php-core::validation.min'),
            'smtp_port.max'                 => trans('php-core::validation.max'),
            'smtp_encryption.required_with' => trans('php-core::validation.required_with'),
            'smtp_encryption.string'        => trans('php-core::validation.string'),
            'smtp_encryption.min'           => trans('php-core::validation.min'),
            'smtp_encryption.max'           => trans('php-core::validation.max'),
            'smtp_username.required_with'   => trans('php-core::validation.required_with'),
            'smtp_username.string'          => trans('php-core::validation.string'),
            'smtp_username.min'             => trans('php-core::validation.min'),
            'smtp_username.max'             => trans('php-core::validation.max'),
            'smtp_password.required_with'   => trans('php-core::validation.required_with'),
            'smtp_password.string'          => trans('php-core::validation.string'),
            'smtp_password.min'             => trans('php-core::validation.min'),
            'smtp_password.max'             => trans('php-core::validation.max'),

            'name.required'       => trans('php-core::validation.required'),
            'name.string'         => trans('php-core::validation.string'),
            'name.max'            => trans('php-core::validation.max')
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator)
    {
        $validator->after( function ($validator) {
            $data = $validator->getData();
            if (
                isset($data['host_type'])
                && isset($data['host_id'])
            ) {
                if (
                    config('wk-newsletter.onoff.site')
                    && !empty(config('wk-core.class.site.site'))
                    && $data['host_type'] == config('wk-core.class.site.site')
                ) {
                    $result = DB::table(config('wk-core.table.site.sites'))
                                ->where('id', $data['host_id'])
                                ->exists();
                    if (!$result)
                        $validator->errors()->add('host_id', trans('php-core::validation.exists'));
                } elseif (
                    config('wk-newsletter.onoff.group')
                    && !empty(config('wk-core.class.group.group'))
                    && $data['host_type'] == config('wk-core.class.group.group')
                ) {
                    $result = DB::table(config('wk-core.table.group.groups'))
                                ->where('id', $data['host_id'])
                                ->exists();
                    if (!$result)
                        $validator->errors()->add('host_id', trans('php-core::validation.exists'));
                }
            }
            if (isset($data['identifier'])) {
                $result = config('wk-core.class.newsletter.setting')::where('identifier', $data['identifier'])
                                ->when(isset($data['host_type']), function ($query) use ($data) {
                                    return $query->where('host_type', $data['host_type']);
                                  })
                                ->when(isset($data['host_id']), function ($query) use ($data) {
                                    return $query->where('host_id', $data['host_id']);
                                  })
                                ->when(isset($data['id']), function ($query) use ($data) {
                                    return $query->where('id', '<>', $data['id']);
                                  })
                                ->exists();
                if ($result)
                    $validator->errors()->add('identifier', trans('php-core::validation.unique', ['attribute' => trans('php-newsletter::setting.identifier')]));
            }
        });
    }
}
