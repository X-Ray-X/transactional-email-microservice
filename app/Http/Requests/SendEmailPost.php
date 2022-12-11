<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SendEmailPost extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'from'          => 'required|array',
            'from.email'    => 'required|email|max:255',
            'from.name'     => 'required|string|min:1|max:255',
            'to'            => 'required|array',
            'to.email'      => 'required|email|max:255',
            'to.name'       => 'required|string|min:1|max:255',
            'subject'       => 'required|string|min:1|max:255',
            'htmlPart'      => 'required|string|min:1',
        ];
    }
}
