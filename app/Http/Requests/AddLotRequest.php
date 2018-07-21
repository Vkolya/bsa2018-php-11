<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddLotRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'currency' =>  'required|min:2|max:255',
            'price'       =>  'required|numeric|min:0',
            'date_open'    =>  'required|date_format:"d/m/Y H:i"',
            'date_close'    =>  'required|date_format:"d/m/Y H:i"'
        ];
    }
}
