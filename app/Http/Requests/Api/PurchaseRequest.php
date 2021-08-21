<?php

namespace App\Http\Requests\Api;

use System\Request\Request;

class PurchaseRequest extends Request
{
    public function rules(){
        return [
            'token'     => 'required|min:20|max:100',
            'receipt'   => 'required|min:3|max:100',
        ];
    }
}