<?php

namespace App\Helpers;

use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class Helper
{
    use ApiResponser;

    public static function validate(Request $request, array $rule)
    {
        $validation = Validator::make($request->all(), $rule);

        if ($validation->fails()) {
            return (new self)->error('Validation Error.', 422, $validation->errors());
        }
    }

}
