<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class PromoCodeUsage
 * 
 * @property int $id
 * @property int $user_id
 * @property int $promo_code_id
 * @property int $usage_count
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property PromoCode $promo_code
 * @property User $user
 *
 * @package App\Models
 */
class PromoCodeUsage extends Model
{
	protected $table = 'promo_code_usages';

	protected $casts = [
		'user_id' => 'int',
		'promo_code_id' => 'int',
		'usage_count' => 'int'
	];

	protected $fillable = [
		'user_id',
		'promo_code_id',
		'usage_count'
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
