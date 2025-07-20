# 従業員管理アプリ開発プロジェクト

## プロジェクト概要
建設業向けの従業員管理システムをLaravel 12で開発します。労働安全衛生法、雇用保険法、厚生年金保険法などの法的要件に対応した包括的な従業員データベースを構築します。外国人従業員の在留資格管理、各種書類のファイル管理機能を含む、現代的なWebアプリケーションです。

### 主な特徴
- **外国人労働者対応**: 在留資格・在留カード管理
- **書類管理**: PDF・画像ファイルのアップロード・管理
- **健康管理**: 健康診断・特殊健康診断の履歴管理
- **資格管理**: 免許・技能講習・教育履歴の管理
- **セキュリティ**: 暗号化・アクセス制御・監査ログ
- **法的コンプライアンス**: 労働関連法規への対応

## 技術スタック
- **フレームワーク**: Laravel 12.x (PHP 8.2+)
- **フロントエンド**: Blade + Alpine.js + Tailwind CSS
- **データベース**: MySQL 8.0
- **キャッシュ**: Redis
- **認証**: Laravel Breeze
- **バリデーション**: Laravel Form Requests
- **ファイルストレージ**: Laravel Storage
- **アセット管理**: Vite

## データベース設計

### employeesテーブル - Laravelマイグレーション
```php
<?php
// database/migrations/create_employees_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            
            // 基本情報
            $table->string('full_name', 100)->comment('氏名');
            $table->string('furigana', 100)->comment('ふりがな');
            $table->string('job_category', 50)->comment('職種');
            $table->date('hire_date')->comment('雇入年月日');
            $table->date('birth_date')->comment('生年月日');
            $table->string('nationality', 50)->default('日本')->comment('国籍');
            $table->string('residence_status', 100)->nullable()->comment('在留資格');
            $table->date('residence_card_expiry')->nullable()->comment('在留カード有効期限');
            $table->string('profile_photo')->nullable()->comment('顔写真ファイルパス');
            
            // 連絡先情報
            $table->text('current_address')->comment('現住所');
            $table->text('family_address')->nullable()->comment('家族住所');
            $table->string('family_name', 100)->nullable()->comment('家族名');
            $table->string('phone_number', 20)->comment('電話番号');
            $table->string('family_phone_number', 20)->nullable()->comment('家族電話番号');
            
            // 健康管理情報
            $table->date('last_health_checkup_date')->nullable()->comment('最近の健康診断日');
            $table->string('blood_pressure', 20)->nullable()->comment('血圧');
            $table->enum('blood_type', ['A', 'B', 'AB', 'O'])->nullable()->comment('血液型');
            $table->date('special_health_checkup_date')->nullable()->comment('特殊健康診断日');
            $table->string('special_health_checkup_type', 100)->nullable()->comment('特殊健康診断種類');
            
            // 保険・年金情報
            $table->string('kyokai_kenpo_number', 20)->nullable()->comment('協会けんぽ番号');
            $table->string('employees_pension_number', 20)->nullable()->comment('厚生年金番号');
            $table->string('employment_insurance_number', 20)->nullable()->comment('雇用保険番号');
            
            // 教育・資格情報
            $table->text('hire_foreman_special_education')->nullable()->comment('雇入･職長特別教育');
            $table->text('skill_training')->nullable()->comment('技能講習');
            $table->text('licenses')->nullable()->comment('免許');
            $table->date('orientation_education_date')->nullable()->comment('受入教育実施年月日');
            $table->boolean('kentaikyo_handbook_owned')->default(false)->comment('建退共手帳所有の有無');
            
            // システム情報
            $table->boolean('is_active')->default(true)->comment('在籍状況');
            $table->timestamps();
            
            // インデックス
            $table->index(['full_name', 'furigana']);
            $table->index('job_category');
            $table->index('hire_date');
            $table->index('is_active');
        });
    }

    public function down()
    {
        Schema::dropIfExists('employees');
    }
};
```

### employee_documentsテーブル - ファイル管理用
```php
<?php
// database/migrations/create_employee_documents_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('employee_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->enum('document_type', [
                'license',           // 免許証
                'qualification',     // 資格証
                'passport',          // パスポート
                'residence_card',    // 在留カード
                'other'             // その他
            ])->comment('書類種別');
            $table->string('document_name')->comment('書類名');
            $table->string('file_path')->comment('ファイルパス');
            $table->string('original_filename')->comment('元のファイル名');
            $table->string('mime_type')->comment('MIMEタイプ');
            $table->unsignedInteger('file_size')->comment('ファイルサイズ（bytes）');
            $table->date('expiry_date')->nullable()->comment('有効期限');
            $table->text('notes')->nullable()->comment('備考');
            $table->timestamps();
            
            // インデックス
            $table->index(['employee_id', 'document_type']);
            $table->index('expiry_date');
        });
    }

    public function down()
    {
        Schema::dropIfExists('employee_documents');
    }
};
```

### Employeeモデル
```php
<?php
// app/Models/Employee.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'full_name', 'furigana', 'job_category', 'hire_date', 'birth_date',
        'nationality', 'residence_status', 'residence_card_expiry', 'profile_photo',
        'current_address', 'family_address', 'family_name', 'phone_number', 'family_phone_number',
        'last_health_checkup_date', 'blood_pressure', 'blood_type',
        'special_health_checkup_date', 'special_health_checkup_type',
        'kyokai_kenpo_number', 'employees_pension_number', 'employment_insurance_number',
        'hire_foreman_special_education', 'skill_training', 'licenses',
        'orientation_education_date', 'kentaikyo_handbook_owned', 'is_active'
    ];

    protected $casts = [
        'hire_date' => 'date',
        'birth_date' => 'date',
        'residence_card_expiry' => 'date',
        'last_health_checkup_date' => 'date',
        'special_health_checkup_date' => 'date',
        'orientation_education_date' => 'date',
        'kentaikyo_handbook_owned' => 'boolean',
        'is_active' => 'boolean',
    ];

    // リレーション
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
}
```

### EmployeeDocumentモデル
```php
<?php
// app/Models/EmployeeDocument.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class EmployeeDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id', 'document_type', 'document_name', 'file_path',
        'original_filename', 'mime_type', 'file_size', 'expiry_date', 'notes'
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
```

## 機能要件

### 1. 従業員情報管理
- 従業員の新規登録・編集・削除
- 一覧表示・検索・フィルタリング
- 詳細情報表示
- 外国人従業員の在留資格管理

### 2. ファイル管理機能
- 顔写真のアップロード・表示
- 免許証・資格証・パスポート・在留カードのファイル管理
- ファイルの安全なダウンロード
- ファイルの有効期限管理

### 3. 健康管理機能
- 健康診断履歴管理
- 特殊健康診断スケジュール管理
- 健康診断期限アラート

### 4. 資格・教育管理
- 各種資格・免許の有効期限管理
- 教育履歴の記録
- 更新期限通知機能

### 5. 保険・年金管理
- 各種保険番号の管理
- 加入状況の確認

### 6. レポート機能
- 従業員一覧のCSVエクスポート
- 外国人従業員の在留資格レポート
- 各種統計情報の表示
- 期限切れアラート一覧

## バリデーション - Laravel Form Requests

### 従業員登録バリデーション
```php
<?php
// app/Http/Requests/StoreEmployeeRequest.php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEmployeeRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }
    
    public function rules()
    {
        return [
            // 基本情報（必須）
            'full_name' => 'required|string|max:100',
            'furigana' => 'required|string|max:100|regex:/^[ぁ-んー\s]+$/u',
            'job_category' => 'required|string|max:50',
            'hire_date' => 'required|date|after:birth_date',
            'birth_date' => 'required|date|before:18 years ago',
            'nationality' => 'required|string|max:50',
            'residence_status' => 'nullable|required_if:nationality,!=,日本|string|max:100',
            'residence_card_expiry' => 'nullable|required_if:nationality,!=,日本|date|after:today',
            'current_address' => 'required|string',
            'phone_number' => 'required|string|regex:/^[0-9\-]+$/',
            
            // ファイルアップロード
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'documents.*' => 'nullable|file|mimes:pdf,jpeg,png,jpg|max:10240',
            'document_types.*' => 'nullable|in:license,qualification,passport,residence_card,other',
            'document_names.*' => 'nullable|string|max:255',
            'document_expiry_dates.*' => 'nullable|date|after:today',
            'document_notes.*' => 'nullable|string',
            
            // 健康管理情報
            'last_health_checkup_date' => 'nullable|date|before_or_equal:today',
            'blood_pressure' => 'nullable|string|regex:/^\d{2,3}\/\d{2,3}$/',
            'blood_type' => 'nullable|in:A,B,AB,O',
            
            // 保険・年金情報
            'kyokai_kenpo_number' => 'nullable|string|max:20',
            'employees_pension_number' => 'nullable|string|max:20',
            'employment_insurance_number' => 'nullable|string|max:20',
            
            // その他
            'kentaikyo_handbook_owned' => 'boolean',
        ];
    }
    
    public function messages()
    {
        return [
            'full_name.required' => '氏名は必須です。',
            'furigana.required' => 'ふりがなは必須です。',
            'furigana.regex' => 'ふりがなはひらがなで入力してください。',
            'nationality.required' => '国籍は必須です。',
            'residence_status.required_if' => '外国籍の場合、在留資格は必須です。',
            'residence_card_expiry.required_if' => '外国籍の場合、在留カード有効期限は必須です。',
            'profile_photo.max' => '顔写真のファイルサイズは2MB以下である必要があります。',
            'documents.*.max' => 'ファイルサイズは10MB以下である必要があります。',
        ];
    }
}
```

### ファイルアップロード専用リクエスト
```php
<?php
// app/Http/Requests/UploadDocumentRequest.php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UploadDocumentRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }
    
    public function rules()
    {
        return [
            'employee_id' => 'required|exists:employees,id',
            'document_type' => 'required|in:license,qualification,passport,residence_card,other',
            'document_name' => 'required|string|max:255',
            'file' => 'required|file|mimes:pdf,jpeg,png,jpg|max:10240',
            'expiry_date' => 'nullable|date|after:today',
            'notes' => 'nullable|string|max:1000',
        ];
    }
}
```

## ルーティング設計

### Webルート (routes/web.php)
```php
<?php
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ReportController;

Route::middleware(['auth', 'verified'])->group(function () {
    // 従業員管理
    Route::resource('employees', EmployeeController::class);
    Route::get('employees/search', [EmployeeController::class, 'search'])->name('employees.search');
    
    // ファイル管理
    Route::post('employees/{employee}/documents', [EmployeeController::class, 'uploadDocument'])->name('employees.documents.upload');
    Route::delete('documents/{document}', [EmployeeController::class, 'deleteDocument'])->name('documents.delete');
    Route::get('documents/{document}/download', [EmployeeController::class, 'downloadDocument'])->name('documents.download');
    
    // レポート
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('health', [ReportController::class, 'health'])->name('health');
        Route::get('licenses', [ReportController::class, 'licenses'])->name('licenses');
        Route::get('foreigners', [ReportController::class, 'foreigners'])->name('foreigners');
        Route::get('expiring-documents', [ReportController::class, 'expiringDocuments'])->name('expiring-documents');
        Route::get('export', [ReportController::class, 'export'])->name('export');
    });
});
```

## セキュリティ要件

### データ保護
- 個人情報の暗号化
- ファイルの安全な保存
- アクセス権限の厳格な管理
- 監査ログの記録

### 法的コンプライアンス
- 個人情報保護法への対応
- 労働安全衛生法関連データの適切な管理
- 入管法に基づく在留資格管理
- データ保持期間の設定

### ファイルセキュリティ
```php
// config/filesystems.php
'disks' => [
    'employee_documents' => [
        'driver' => 'local',
        'root' => storage_path('app/private/employee_documents'),
        'throw' => false,
    ],
    
    'employee_photos' => [
        'driver' => 'local',
        'root' => storage_path('app/public/employee_photos'),
        'url' => env('APP_URL').'/storage/employee_photos',
        'visibility' => 'public',
        'throw' => false,
    ],
],
```

## 開発フェーズ

### Phase 1: コア機能開発
1. 従業員管理CRUD機能の実装
2. Bladeテンプレートの作成
3. バリデーション（Form Requests）の実装
4. 基本的な検索・フィルタリング機能

### Phase 2: ファイル管理機能
1. 顔写真アップロード機能
2. 書類ファイル管理システム
3. ファイルダウンロード・削除機能
4. ファイルセキュリティの実装

### Phase 3: 外国人従業員対応
1. 在留資格管理機能
2. 在留カード期限管理
3. 外国人従業員専用レポート
4. アラート機能

### Phase 4: 高度な機能
1. 健康管理・資格管理機能
2. Excel/CSVエクスポート機能
3. 期限切れ通知システム
4. 統計レポート機能

### Phase 5: セキュリティ・最適化
1. 認可（Policy）の実装
2. セキュリティ強化
3. パフォーマンス最適化
4. テストの実装

## 法的要件への対応

### 入管法関連
- 在留資格の適正管理
- 在留期限の監視とアラート機能
- 就労可能な在留資格の確認

### 労働基準法関連
- 外国人労働者届出の義務
- 労働条件の記録管理
- 適正な賃金支払いの記録

### 個人情報保護法
- パスポート情報の適切な管理
- 在留カード情報の暗号化保存
- 第三国への情報提供制限

## 必要パッケージ

```bash
# Excel/CSVエクスポート
composer require maatwebsite/excel

# 画像処理
composer require intervention/image

# 日本語バリデーション
composer require laravel-lang/lang

# 権限管理
composer require spatie/laravel-permission

# 開発ツール（開発環境のみ）
composer require --dev barryvdh/laravel-debugbar
composer require --dev pestphp/pest
composer require --dev pestphp/pest-plugin-laravel
```

## 開発時の留意点

### Laravel 12の活用
- 最新の機能とベストプラクティスの採用
- セキュリティ機能の活用
- パフォーマンス最適化機能の利用

### セキュリティ
- ファイルアップロードの検証強化
- SQLインジェクション対策
- CSRF保護の確認
- 適切なアクセス制御

### ユーザビリティ
- 直感的なUI/UXの設計
- レスポンシブデザインの実装
- アクセシビリティの考慮

### 保守性
- コードの可読性と保守性
- 適切なコメントとドキュメント
- テストカバレッジの確保