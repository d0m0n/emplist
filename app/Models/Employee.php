<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'full_name',
        'furigana', 
        'job_category',
        'department',
        'position',
        'hire_date',
        'birth_date',
        'nationality',
        'residence_status',
        'residence_card_expiry',
        'profile_photo',
        'current_address',
        'postal_code',
        'family_address',
        'family_name',
        'family_name_furigana',
        'family_relationship',
        'family_name_2',
        'family_name_furigana_2',
        'family_relationship_2',
        'family_address_2',
        'family_phone_number_2',
        'phone_number',
        'family_phone_number',
        'gender',
        'has_spouse',
        'last_health_checkup_date',
        'blood_pressure',
        'blood_type',
        'special_health_checkup_date',
        'special_health_checkup_type',
        'kyokai_kenpo_number',
        'employees_pension_number',
        'employment_insurance_number',
        'hire_foreman_special_education',
        'skill_training',
        'licenses',
        'orientation_education_date',
        'kentaikyo_handbook_owned',
        'is_active',
        'driving_license_expiry',
        'retirement_date',
        'company_id'
    ];

    protected $casts = [
        'hire_date' => 'date',
        'birth_date' => 'date',
        'residence_card_expiry' => 'date',
        'last_health_checkup_date' => 'date',
        'special_health_checkup_date' => 'date',
        'orientation_education_date' => 'date',
        'kentaikyo_handbook_owned' => 'boolean',
        'has_spouse' => 'boolean',
        'is_active' => 'boolean',
        'driving_license_expiry' => 'date',
        'retirement_date' => 'date',
    ];

    // リレーション
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function documents()
    {
        return $this->hasMany(EmployeeDocument::class);
    }

    public function licenses()
    {
        return $this->documents()->where('document_type', 'license');
    }

    public function qualifications()
    {
        return $this->documents()->where('document_type', 'qualification');
    }

    public function passport()
    {
        return $this->documents()->where('document_type', 'passport')->first();
    }

    public function residenceCard()
    {
        return $this->documents()->where('document_type', 'residence_card')->first();
    }

    // アクセサ
    public function getAgeAttribute()
    {
        return $this->birth_date->age;
    }

    public function getYearsOfServiceAttribute()
    {
        return $this->hire_date->diffInYears(now());
    }

    public function getProfilePhotoUrlAttribute()
    {
        return $this->profile_photo ? asset('storage/' . $this->profile_photo) : null;
    }

    public function getIsForeignerAttribute()
    {
        return $this->nationality !== '日本';
    }

    public function getResidenceCardExpiryStatusAttribute()
    {
        if (!$this->residence_card_expiry) return null;
        
        $daysUntilExpiry = now()->diffInDays($this->residence_card_expiry, false);
        
        if ($daysUntilExpiry < 0) return 'expired';
        if ($daysUntilExpiry <= 30) return 'expiring_soon';
        if ($daysUntilExpiry <= 90) return 'expiring_within_3months';
        
        return 'valid';
    }

    public function getDrivingLicenseExpiryStatusAttribute()
    {
        if (!$this->driving_license_expiry) return null;
        
        $daysUntilExpiry = now()->diffInDays($this->driving_license_expiry, false);
        
        if ($daysUntilExpiry < 0) return 'expired';
        if ($daysUntilExpiry <= 30) return 'expiring_soon';
        if ($daysUntilExpiry <= 90) return 'expiring_within_3months';
        
        return 'valid';
    }

    public function getIsRetiredAttribute()
    {
        return $this->retirement_date !== null;
    }

    // スコープ
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByJobCategory($query, $category)
    {
        return $query->where('job_category', $category);
    }

    public function scopeForeigners($query)
    {
        return $query->where('nationality', '!=', '日本');
    }

    public function scopeExpiringResidenceCards($query, $days = 90)
    {
        return $query->whereNotNull('residence_card_expiry')
                    ->where('residence_card_expiry', '<=', now()->addDays($days));
    }

    public function scopeRetired($query)
    {
        return $query->whereNotNull('retirement_date');
    }

    public function scopeIncludeRetired($query)
    {
        return $query->withTrashed();
    }
}
