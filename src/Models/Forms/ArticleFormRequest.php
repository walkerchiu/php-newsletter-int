<?php

namespace WalkerChiu\Newsletter\Models\Forms;

use Illuminate\Support\Facades\Request;
use Illuminate\Validation\Rule;
use WalkerChiu\Core\Models\Forms\FormRequest;

class ArticleFormRequest extends FormRequest
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
            'setting_id' => trans('php-newsletter::article.setting_id'),
            'serial'     => trans('php-newsletter::article.serial'),
            'style'      => trans('php-newsletter::article.style'),
            'subject'    => trans('php-newsletter::article.subject'),
            'content'    => trans('php-newsletter::article.content'),
            'is_enabled' => trans('php-newsletter::article.is_enabled')
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
            'setting_id' => ['required','integer','min:1','exists:'.config('wk-core.table.newsletter.settings').',id'],
            'serial'     => '',
            'style'      => '',
            'subject'    => 'required|string|max:255',
            'content'    => 'required',
            'is_enabled' => 'boolean'
        ];

        $request = Request::instance();
        if (
            $request->isMethod('put')
            && isset($request->id)
        ) {
            $rules = array_merge($rules, ['id' => ['required','integer','min:1','exists:'.config('wk-core.table.newsletter.articles').',id']]);
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
            'id.required'         => trans('php-core::validation.required'),
            'id.integer'          => trans('php-core::validation.integer'),
            'id.min'              => trans('php-core::validation.min'),
            'id.exists'           => trans('php-core::validation.exists'),
            'setting_id.required' => trans('php-core::validation.required'),
            'setting_id.integer'  => trans('php-core::validation.integer'),
            'setting_id.min'      => trans('php-core::validation.min'),
            'setting_id.exists'   => trans('php-core::validation.exists'),

            'subject.required'    => trans('php-core::validation.required'),
            'subject.string'      => trans('php-core::validation.string'),
            'subject.max'         => trans('php-core::validation.max'),
            'content.required'    => trans('php-core::validation.required'),

            'is_enabled.boolean'  => trans('php-core::validation.boolean')
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
    }
}
