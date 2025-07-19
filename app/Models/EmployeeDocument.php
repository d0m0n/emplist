<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class EmployeeDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'document_type',
        'document_name',
        'file_path',
        'original_filename',
        'mime_type',
        'file_size',
        'expiry_date',
        'notes'
    ];

    protected $casts = [
        'expiry_date' => 'date',
    ];

    // リレーション
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    // アクセサ
    public function getFileUrlAttribute()
    {
        return Storage::url($this->file_path);
    }

    public function getFileSizeHumanAttribute()
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    public function getIsImageAttribute()
    {
        return str_starts_with($this->mime_type, 'image/');
    }

    public function getIsPdfAttribute()
    {
        return $this->mime_type === 'application/pdf';
    }

    public function getExpiryStatusAttribute()
    {
        if (!$this->expiry_date) return null;
        
        $daysUntilExpiry = now()->diffInDays($this->expiry_date, false);
        
        if ($daysUntilExpiry < 0) return 'expired';
        if ($daysUntilExpiry <= 30) return 'expiring_soon';
        if ($daysUntilExpiry <= 90) return 'expiring_within_3months';
        
        return 'valid';
    }

    // スコープ
    public function scopeExpiring($query, $days = 90)
    {
        return $query->whereNotNull('expiry_date')
                    ->where('expiry_date', '<=', now()->addDays($days));
    }

    public function scopeByType($query, $type)
    {
        return $query->where('document_type', $type);
    }
}
