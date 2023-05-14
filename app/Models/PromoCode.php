<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class PromoCode
 * 
 * @property int $id
 * @property string $code
 * @property Carbon|null $expiry_date
 * @property string $status
 * @property string $type
 * @property float $discount
 * @property string $usage_type
 * @property int|null $usage_count
 * @property int|null $usage_count_per_user
 * @property string|null $deleted_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Collection|PromoCodeUsage[] $promo_code_usages
 * @property Collection|User[] $users
 *
 * @package App\Models
 */
class PromoCode extends Model
{
	use SoftDeletes;
	protected $table = 'promo_codes';

	protected $casts = [
		'expiry_date' => 'datetime',
		'discount' => 'float',
		'usage_count' => 'int',
		'usage_count_per_user' => 'int'
	];

	protected $fillable = [
		'code',
		'expiry_date',
		'status',
		'type',
		'discount',
		'usage_type',
		'usage_count',
		'usage_count_per_user'
	];

	public function promo_code_usages()
	{
		return $this->hasMany(PromoCodeUsage::class);
	}

	public function users()
	{
		return $this->belongsToMany(User::class, 'promo_code_users')
					->withPivot('id')
					->withTimestamps();
	}
}
