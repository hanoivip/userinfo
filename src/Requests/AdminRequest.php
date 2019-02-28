<?php

namespace Hanoivip\Userinfo\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdminRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'uid' => 'required|string'
        ];
    }
}
