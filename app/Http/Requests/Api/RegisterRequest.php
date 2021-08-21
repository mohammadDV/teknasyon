<?php

namespace App\Http\Requests\Api;

use System\Request\Request;

class RegisterRequest extends Request
{
    public function rules(){
        return [
            'deviceID'  => 'required|max:191',
            'mobile'    => 'required|min:11|max:11',
//            'cat_id' => 'required|exist:categories,id',
//            'image' => 'required|file|mimes:jpeg,jpg,png,gif',
//            'published_at' => 'required|date',
        ];
    }
}