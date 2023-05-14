<?php

namespace App\Business;

use App\Models\PromoCode;
use App\Models\PromoCodeUsage;
use App\Models\PromoCodeUser;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class PromoCodes
{
    public static function createPromoCode($data)
    {
        $code = $data->code;
        if (!$code) {
            $code = self::generateRandomPromoCode();
            $check_code = PromoCode::where('code', $code)->first();
            if ($check_code) {
                self::createPromoCode($data);
            }
        }

        $promo_code = new PromoCode();
        $promo_code->code = $code;
        $promo_code->expiry_date = $data->expiry_date;
        $promo_code->status = 'active';
        $promo_code->type = $data->type;
        $promo_code->usage_count = $data->number_of_usage;
        $promo_code->usage_count_per_user = $data->number_of_usage_per_user;
        $promo_code->save();

        if ($data->users) {
            $users_ids = explode(',', $data->users);
            $insert_users = [];

            foreach ($users_ids as $user_id) {
                $insert_users[] = [
                    "user_id" => $user_id,
                    "promo_code_id" => $promo_code->id
                ];
            }

            PromoCodeUser::insert($insert_users);
        }

        return $promo_code;
    }

    public static function usePromoCode($data)
    {
        $promo_code = PromoCode::where('code', $data->code)->first();

        $is_valid = self::validatePromoCode($promo_code);



    }

    private static function validatePromoCode(PromoCode $promo_code){
        if (!$promo_code) {
            throw new \Exception('Promo Code Unavailable', 404);
        }

        if ($promo_code->usage_type == 'private') {
            $promo_code_users = PromoCodeUser::where('promo_code_id', $promo_code->id)->get()->pluck('user_id')->toArray();
            if (!in_array(Auth::id(), $promo_code_users)) {
                throw new \Exception('Promo Code Unavailable', 404);
            }
        }

        if($promo_code->expiry_date && $promo_code->expiry_date < Carbon::now()){
            throw new \Exception('Promo Code Unavailable', 404);
        }

        $promo_code_usages = PromoCodeUsage::where('promo_code_id', $promo_code->id)->get();

        if($promo_code->usage_count){
            $sum_of_usage = $promo_code_usages->sum('usage_count');

            if($sum_of_usage >= $promo_code->usage_count){
                throw new \Exception('Promo Code Unavailable', 404);
            }
        }

        if($promo_code->usage_count_per_user){
            $user_usage = $promo_code_usages->where('user_id', Auth::id())->first();

            if($user_usage->usage_count >= $promo_code->usage_count){
                throw new \Exception('Promo Code Unavailable', 404);
            }
        }
    }

    private static function generateRandomPromoCode()
    {
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $random_promo_code = '';

        for ($i = 0; $i < 5; $i++) {
            $index = rand(0, strlen($characters) - 1);
            $random_promo_code .= $characters[$index];
        }

        return $random_promo_code;
    }
}
