<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class PromoCodeUser
 * 
 * @property int $id
 * @property int $user_id
 * @property int $promo_code_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property PromoCode $promo_code
 * @property User $user
 *
 * @package App\Models
 */
class PromoCodeUser extends Model
{
	protected $table = 'promo_code_users';

	protected $casts = [
		'user_id' => 'int',
		'promo_code_id' => 'int'
	];

	protected $fillable = [
		'user_id',
		'promo_code_id'
	];

	public function promo_code()
	{
		return $this->belongsTo(PromoCode::class);
	}

	public function user()
	{
		return $this->belongsTo(User::class);
	}
}
