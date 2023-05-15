<?php

namespace App\Http\Controllers;

use App\Business\PromoCodes;
use App\Helpers\Helper;
use App\Http\Resources\PromoCodeDiscountResource;
use App\Http\Resources\PromoCodeResource;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PromoCodeController extends Controller
{
    use ApiResponser;

    public function createPromoCode(Request $request)
    {
        $validation = Validator::make($request->all(), [
            "promo_code" => "nullable|min:3|unique:promo_codes,code",
            "expiry_date" => "nullable|date|after:tomorrow",
            "number_of_usage" => "nullable|integer",
            "number_of_usage_per_user" => "nullable|integer",
            "users" => "nullable",
            "type" => "required|in:percentage,value",
            "discount" => "required",
        ]);

        $validation->after(function ($validator) use ($request) {
            if ($request->type == 'percentage' && $request->discount > 1) {
                $validator->errors()->add(
                    'discount', 'Discount Must be less than 1'
                );
            }
        });


        if ($validation->fails()) {
            return $this->error('Validation Error.', 422, $validation->errors());
        }


        $promo_code = PromoCodes::createPromoCode($request->all());

        return $this->success(PromoCodeResource::make($promo_code), 'Promo Code Created');
    }

    public function usePromoCode(Request $request)
    {
        $validation = Validator::make($request->all(), [
            "promo_code" => "required|min:3|exists:promo_codes,code",
            "price" => "required"
        ]);

        if ($validation->fails()) {
            return $this->error('Validation Error.', 422, $validation->errors());
        }

        $promo_code = PromoCodes::usePromoCode($request->all());

        if(!$promo_code) {
            return $this->error( 'Invalid Promo Code', 404);
        }

        return $this->success(PromoCodeDiscountResource::make($promo_code), 'Promo Code Applied');
    }
}
