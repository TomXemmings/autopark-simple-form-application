<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AddressInfo extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'city',
        'address',
        'email',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
