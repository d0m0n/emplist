<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'representative_name',
        'address',
        'phone_number',
        'fax_number',
        'email',
        'website',
        'logo_image',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // リレーション
    public function employees()
    {
        return $this->hasMany(Employee::class);
    }

    // アクセサ
    public function getLogoImageUrlAttribute()
    {
        return $this->logo_image ? asset('storage/' . $this->logo_image) : null;
    }

    // スコープ
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
