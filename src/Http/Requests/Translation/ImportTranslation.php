<?php

namespace Brackets\CraftablePro\Http\Requests\Translation;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class ImportTranslation extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return Gate::allows('craftable-pro.translation.import');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'importLanguage' => 'string|required',
            'onlyMissing' => 'string',
            'fileImport' => 'required|file',
        ];
    }

    public function getChosenLanguage()
    {
        return strtolower($this->importLanguage);
    }
}
