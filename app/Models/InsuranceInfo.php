<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class InsuranceInfo extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'policy_number',
        'start_date',
        'end_date',
        'company_name',
        'fgis_number',
        'fgis_date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
