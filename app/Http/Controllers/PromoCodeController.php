<?php

namespace App\Http\Controllers;

use App\Business\PromoCodes;
use App\Helpers\Helper;
use App\Http\Resources\PromoCodeResource;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;

class PromoCodeController extends Controller
{
    use ApiResponser;

    public function createPromoCode(Request $request)
    {
        Helper::validate($request, [
            "promo_code" => "nullable|min:3|unique:promo_codes,code",
            "expiry_date" => "nullable|date",
            "number_of_usage" => "nullable|integer",
            "number_of_usage_per_user" => "nullable|integer",
            "users" => "nullable",
            "type" => "required|in:percentage,value",
            "value" => "required|float",
        ]);

        $promo_code = PromoCodes::createPromoCode($request->all());

        return $this->success(PromoCodeResource::collection($promo_code), 'Promo Code Created');
    }

    public function usePromoCode(Request $request)
    {
        Helper::validate($request, [
            "promo_code" => "required|min:3",
            "price" => "required|float"
        ]);

        PromoCodes::usePromoCode($request->all());

    }
}
