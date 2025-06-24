<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
//        'name',
//        'email',
        'password',
        'user_code',
        'phone',
        'last_name',
        'first_name',
        'middle_name',
        'inn',
        'current_step',
        'service_agreement_number',
        'service_agreement_start_date',
        'service_agreement_end_date',
        'driver_license_number',
        'driver_license_start_date',
        'driver_license_end_date',
        'yandex_contract_number',
        'yandex_contract_start_date',
        'yandex_contract_end_date',
        'signature'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
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
            'password' => 'hashed',
        ];
    }

    public function addressInfo()
    {
        return $this->hasOne(AddressInfo::class);
    }

    public function insuranceInfo()
    {
        return $this->hasOne(InsuranceInfo::class);
    }

    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    public static function generateUserCode(): string
    {
        $lastUser = self::orderBy('id', 'desc')->first();
        $number = $lastUser ? (int) filter_var($lastUser->user_code, FILTER_SANITIZE_NUMBER_INT) + 1 : 1;
        return str_pad($number, 3, '0', STR_PAD_LEFT) . 'М';
    }
}
