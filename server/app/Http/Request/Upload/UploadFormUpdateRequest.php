<?php

namespace App\Http\Request\Upload;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\ValidationException;
use App\Enums\ResponseStatus;

class UploadFormUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'file' => 'file',
            'title' => 'string',
            'account_id' => 'string',
        ];
    }

    public function queryParameters()
    {
        return [
            'hotelId' => [
                'description' => 'ホテルID',
            ],
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $errors = (new ValidationException($validator))->errors();
        throw new HttpResponseException(response()->json([
            'message' => 'Failed validation',
            'errors' => $errors,
        ], 500, [], JSON_UNESCAPED_UNICODE));
    }
}
