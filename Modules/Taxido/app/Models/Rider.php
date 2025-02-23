<?php

namespace Modules\Taxido\Models;

use App\Models\User;
use App\Models\Address;
use Spatie\MediaLibrary\HasMedia;
use Spatie\Activitylog\LogOptions;
use Modules\Taxido\Enums\RoleEnum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Rider extends User implements HasMedia
{
    use SoftDeletes, HasFactory;

    protected $table = 'users';

    protected $guard_name = 'web';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username',
        'name',
        'email',
        'country_code',
        'phone',
        'system_reserve',
        'profile_image_id',
        'fcm_token',
        'is_verified',
        'password',
        'status',
        'referral_code',
        'referred_by_id',
        'created_by_id'
    ];

    protected $with = [
        'profile_image'
    ];

    protected $withCount = [
        'reviews'
    ];

    protected $appends = [
        'role',
        'rating_count'
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'is_verified' => 'integer',
            'password' => 'hashed',
            'phone' => 'integer',
            'status' => 'integer',
            'created_by_id' => 'integer',
            'reviews_count' => 'integer',
            'rating_count' => 'float',
        ];
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'roles',
        'password',
        'permissions',
        'remember_token',
        'deleted_at',
        'updated_at',
    ];

    public static function booted()
    {
        parent::boot();
        static::addGlobalScope('roles', function (Builder $builder) {
            $builder->role(RoleEnum::RIDER);
        });

        static::saving(function ($model) {
            $model->created_by_id = getCurrentUserId() ?? getAdmin()?->id;
        });

        static::deleted(function ($rider) {
            $rider->reviews()->delete();
            $rider->payment_account()->delete();
            $rider->address()->delete();
            $rider->wallet()->delete();
        });

        static::restored(function ($rider) {
            $rider->reviews()->restore();
            $rider->payment_account()->restore();
            $rider->address()->restore();
            $rider->wallet()->restore();
        });
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->useLogName('Rider')
            ->setDescriptionForEvent(fn(string $eventName) => "{$this->name} - Rider has been {$eventName}");
    }

    public function getMorphClass()
    {
        return 'App\Models\User';
    }


    /**
     * Get the user's role.
     */
    public function getRoleAttribute()
    {
        return $this->roles->first()?->makeHidden(['created_at', 'updated_at', 'pivot']);
    }

    /**
     * Get the user's all permissions.
     */
    public function getPermissionAttribute()
    {
        return $this->getAllPermissions();
    }

    public function getRatingCountAttribute()
    {
        return (float) $this->rider_reviews?->avg('rating');
    }

    /**
     * @return HasOne
     */
    public function wallet(): HasOne
    {
        return $this->hasOne(RiderWallet::class, 'rider_id');
    }

    /**
     * @return HasOne
     */
    public function address(): HasOne
    {
        return $this->hasOne(Address::class, 'user_id');
    }

    /**
     * @return HasOne
     */
    public function payment_account(): HasOne
    {
        return $this->hasOne(PaymentAccount::class, 'user_id');
    }

    /**
     * @return HasMany
     */
    public function rides(): HasMany
    {
        return $this->hasMany(Ride::class, 'rider_id');
    }

    /**
     * @return HasMany
     */
    public function referrals(): HasMany
    {
        return $this->hasMany(Rider::class, 'referred_by_id');
    }

    /**
     * @return HasMany
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(DriverReview::class, 'rider_id');
    }

    /**
     * @return BelongsToMany
     */
    public function coupons(): BelongsToMany
    {
        return $this->belongsToMany(Coupon::class, 'coupon_riders');
    }
}
