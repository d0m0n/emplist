# 従業員管理アプリ開発プロジェクト（Docker + Laravel 12）

## プロジェクト概要
建設業向けの従業員管理システムをDocker環境でLaravel 12を使用して開発します。労働安全衛生法、雇用保険法、厚生年金保険法などの法的要件に対応した包括的な従業員データベースを構築します。外国人従業員の在留資格管理、各種書類のファイル管理機能を含む、現代的なWebアプリケーションです。

### 主な特徴
- **Docker環境**: 開発・本番環境の統一化
- **Laravel 12**: 最新のLaravelフレームワーク
- **外国人労働者対応**: 在留資格・在留カード管理
- **書類管理**: PDF・画像ファイルのアップロード・管理
- **セキュリティ**: 暗号化・アクセス制御・監査ログ
- **法的コンプライアンス**: 労働関連法規への対応

## 技術スタック
- **フレームワーク**: Laravel 12.x (PHP 8.2+)
- **開発環境**: Docker + Docker Compose
- **Webサーバー**: Nginx (Docker Container)
- **データベース**: MySQL 8.0 (Docker Container)
- **キャッシュ**: Redis (Docker Container)
- **フロントエンド**: Blade + Alpine.js + Tailwind CSS
- **アセット管理**: Vite
- **ORM**: Eloquent ORM
- **認証**: Laravel Breeze
- **バリデーション**: Laravel Form Requests
- **ファイルストレージ**: Laravel Storage (Docker Volume)

## Docker環境設定

### docker-compose.yml
```yaml
version: '3.8'

services:
  app:
    build:
      context: .
      dockerfile: Dockerfile
    container_name: employee_management_app
    restart: unless-stopped
    working_dir: /var/www/html
    volumes:
      - ./:/var/www/html
      - ./docker/php/php.ini:/usr/local/etc/php/php.ini
    depends_on:
      - db
      - redis
    networks:
      - employee_network

  nginx:
    image: nginx:alpine
    container_name: employee_management_nginx
    restart: unless-stopped
    ports:
      - "80:80"
      - "443:443"
    volumes:
      - ./:/var/www/html
      - ./docker/nginx/nginx.conf:/etc/nginx/nginx.conf
      - ./docker/nginx/default.conf:/etc/nginx/conf.d/default.conf
      - ./docker/nginx/ssl:/etc/nginx/ssl
    depends_on:
      - app
    networks:
      - employee_network

  db:
    image: mysql:8.0
    container_name: employee_management_db
    restart: unless-stopped
    environment:
      MYSQL_DATABASE: employee_management
      MYSQL_ROOT_PASSWORD: root_password
      MYSQL_PASSWORD: password
      MYSQL_USER: employee_user
    volumes:
      - db_data:/var/lib/mysql
      - ./docker/mysql/my.cnf:/etc/mysql/my.cnf
    ports:
      - "3306:3306"
    networks:
      - employee_network

  redis:
    image: redis:7-alpine
    container_name: employee_management_redis
    restart: unless-stopped
    ports:
      - "6379:6379"
    volumes:
      - redis_data:/data
    networks:
      - employee_network

  mailhog:
    image: mailhog/mailhog:latest
    container_name: employee_management_mailhog
    restart: unless-stopped
    ports:
      - "1025:1025"
      - "8025:8025"
    networks:
      - employee_network

volumes:
  db_data:
    driver: local
  redis_data:
    driver: local

networks:
  employee_network:
    driver: bridge
```

### Dockerfile
```dockerfile
FROM php:8.2-fpm

# 作業ディレクトリを設定
WORKDIR /var/www/html

# システムの依存関係をインストール
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libmcrypt-dev \
    libgd-dev \
    zip \
    unzip \
    nodejs \
    npm \
    supervisor

# PHP拡張機能をインストール
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Composerをインストール
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Node.jsの最新版をインストール
RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash - \
    && apt-get install -y nodejs

# アプリケーションファイルをコピー
COPY . .

# 依存関係をインストール
RUN composer install --optimize-autoloader --no-dev
RUN npm install && npm run build

# ファイルの権限を設定
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
RUN chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Supervisorの設定
COPY ./docker/supervisor/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# ポート9000を公開
EXPOSE 9000

# コンテナ起動時のコマンド
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
```

### .env.docker（Docker環境用設定）
```env
APP_NAME="Employee Management System"
APP_ENV=local
APP_KEY=base64:your-generated-key-here
APP_DEBUG=true
APP_TIMEZONE=Asia/Tokyo
APP_URL=http://localhost

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=employee_management
DB_USERNAME=employee_user
DB_PASSWORD=password

BROADCAST_DRIVER=log
CACHE_DRIVER=redis
FILESYSTEM_DRIVER=local
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis
SESSION_LIFETIME=120

MEMCACHED_HOST=127.0.0.1

REDIS_HOST=redis
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=mailhog
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="noreply@employee-management.local"
MAIL_FROM_NAME="${APP_NAME}"

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_USE_PATH_STYLE_ENDPOINT=false

PUSHER_APP_ID=
PUSHER_APP_KEY=
PUSHER_APP_SECRET=
PUSHER_APP_CLUSTER=mt1

VITE_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
VITE_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"
```

### Nginx設定 (docker/nginx/default.conf)
```nginx
server {
    listen 80;
    server_name localhost;
    root /var/www/html/public;
    index index.php index.html;

    # ファイルサイズ制限
    client_max_body_size 100M;

    # ログ設定
    access_log /var/log/nginx/access.log;
    error_log /var/log/nginx/error.log;

    # 静的ファイルの処理
    location ~* \.(js|css|png|jpg|jpeg|gif|ico|svg)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
        try_files $uri =404;
    }

    # PHP処理
    location ~ \.php$ {
        try_files $uri =404;
        fastcgi_split_path_info ^(.+\.php)(/.+)$;
        fastcgi_pass app:9000;
        fastcgi_index index.php;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        fastcgi_param PATH_INFO $fastcgi_path_info;
        fastcgi_read_timeout 600;
    }

    # Laravel のルーティング
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    # セキュリティ設定
    location ~ /\. {
        deny all;
    }

    # プライベートファイルの保護
    location ~* /storage/app/private/ {
        deny all;
    }
}
```

### Supervisor設定 (docker/supervisor/supervisord.conf)
```ini
[supervisord]
nodaemon=true
user=root
logfile=/var/log/supervisor/supervisord.log
pidfile=/var/run/supervisord.pid

[program:php-fpm]
command=php-fpm
stdout_logfile=/dev/stdout
stdout_logfile_maxbytes=0
stderr_logfile=/dev/stderr
stderr_logfile_maxbytes=0
autorestart=false
startretries=0

[program:laravel-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/html/artisan queue:work --sleep=3 --tries=3
autostart=true
autorestart=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/html/storage/logs/worker.log
stopwaitsecs=3600

[program:laravel-schedule]
command=php /var/www/html/artisan schedule:run
autostart=true
autorestart=true
user=www-data
redirect_stderr=true
stdout_logfile=/var/www/html/storage/logs/schedule.log
```

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
        'full_name',
        'furigana', 
        'job_category',
        'hire_date',
        'birth_date',
        'nationality',
        'residence_status',
        'residence_card_expiry',
        'profile_photo',
        'current_address',
        'family_address',
        'family_name',
        'phone_number',
        'family_phone_number',
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
        'is_active'
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
```

## 機能要件

### 1. 従業員情報管理
- 従業員の新規登録・編集・削除
- 一覧表示・検索・フィルタリング
- 詳細情報表示

### 2. 健康管理機能
- 健康診断履歴管理
- 特殊健康診断スケジュール管理
- 健康診断期限アラート

### 3. 資格・教育管理
- 各種資格・免許の有効期限管理
- 教育履歴の記録
- 更新期限通知機能

### 4. 保険・年金管理
- 各種保険番号の管理
- 加入状況の確認

### 5. レポート機能
- 従業員一覧のCSVエクスポート
- 各種統計情報の表示
- 期限切れアラート一覧

## セキュリティ要件

### データ保護
- 個人情報の暗号化
- アクセス権限の厳格な管理
- 監査ログの記録

### 法的コンプライアンス
- 個人情報保護法への対応
- 労働安全衛生法関連データの適切な管理
- データ保持期間の設定

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

### APIルート (routes/api.php)
```php
<?php
use App\Http\Controllers\Api\EmployeeController;
use App\Http\Controllers\Api\ReportController;

Route::middleware(['auth:sanctum'])->group(function () {
    // 従業員管理API
    Route::apiResource('employees', EmployeeController::class);
    Route::get('employees/search', [EmployeeController::class, 'search']);
    
    // レポートAPI
    Route::prefix('reports')->group(function () {
        Route::get('health', [ReportController::class, 'health']);
        Route::get('licenses', [ReportController::class, 'licenses']);
    });
});
```

### コントローラー例（ファイルアップロード対応）
```php
<?php
// app/Http/Controllers/EmployeeController.php
namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\EmployeeDocument;
use App\Http\Requests\StoreEmployeeRequest;
use App\Http\Requests\UpdateEmployeeRequest;
use App\Http\Requests\UploadDocumentRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $query = Employee::with(['documents']);
        
        // 検索・フィルタリング
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('full_name', 'like', '%' . $request->search . '%')
                  ->orWhere('furigana', 'like', '%' . $request->search . '%');
            });
        }
        
        if ($request->filled('job_category')) {
            $query->where('job_category', $request->job_category);
        }
        
        if ($request->filled('nationality')) {
            $query->where('nationality', $request->nationality);
        }
        
        // 在留カード期限切れ間近フィルター
        if ($request->filled('expiring_residence_cards')) {
            $query->expiringResidenceCards();
        }
        
        $employees = $query->active()->paginate(20);
        
        return view('employees.index', compact('employees'));
    }
    
    public function store(StoreEmployeeRequest $request)
    {
        $validatedData = $request->validated();
        
        // 顔写真のアップロード処理
        if ($request->hasFile('profile_photo')) {
            $validatedData['profile_photo'] = $this->uploadProfilePhoto($request->file('profile_photo'));
        }
        
        $employee = Employee::create($validatedData);
        
        // 書類ファイルのアップロード処理
        $this->handleDocumentUploads($employee, $request);
        
        return redirect()->route('employees.show', $employee)
                        ->with('success', '従業員情報を登録しました。');
    }
    
    public function update(UpdateEmployeeRequest $request, Employee $employee)
    {
        $validatedData = $request->validated();
        
        // 顔写真の更新処理
        if ($request->hasFile('profile_photo')) {
            // 古い写真を削除
            if ($employee->profile_photo) {
                Storage::delete('public/' . $employee->profile_photo);
            }
            $validatedData['profile_photo'] = $this->uploadProfilePhoto($request->file('profile_photo'));
        }
        
        $employee->update($validatedData);
        
        // 新しい書類ファイルのアップロード処理
        $this->handleDocumentUploads($employee, $request);
        
        return redirect()->route('employees.show', $employee)
                        ->with('success', '従業員情報を更新しました。');
    }
    
    // ファイルアップロード専用エンドポイント
    public function uploadDocument(UploadDocumentRequest $request)
    {
        $employee = Employee::findOrFail($request->employee_id);
        $file = $request->file('file');
        
        // ファイルの保存
        $filePath = $this->storeDocument($file, $employee->id);
        
        // データベースに記録
        $document = $employee->documents()->create([
            'document_type' => $request->document_type,
            'document_name' => $request->document_name,
            'file_path' => $filePath,
            'original_filename' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
            'expiry_date' => $request->expiry_date,
            'notes' => $request->notes,
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'ファイルがアップロードされました。',
            'document' => $document
        ]);
    }
    
    // 書類削除
    public function deleteDocument(EmployeeDocument $document)
    {
        // ファイルを削除
        Storage::delete($document->file_path);
        
        // データベースから削除
        $document->delete();
        
        return back()->with('success', '書類を削除しました。');
    }
    
    // 書類ダウンロード
    public function downloadDocument(EmployeeDocument $document)
    {
        if (!Storage::exists($document->file_path)) {
            abort(404, 'ファイルが見つかりません。');
        }
        
        return Storage::download($document->file_path, $document->original_filename);
    }
    
    // プライベートメソッド
    private function uploadProfilePhoto($file)
    {
        $filename = 'profile_' . Str::uuid() . '.' . $file->getClientOriginalExtension();
        return $file->storeAs('employee_photos', $filename, 'public');
    }
    
    private function storeDocument($file, $employeeId)
    {
        $filename = 'doc_' . $employeeId . '_' . Str::uuid() . '.' . $file->getClientOriginalExtension();
        return $file->storeAs('employee_documents', $filename, 'private');
    }
    
    private function handleDocumentUploads(Employee $employee, Request $request)
    {
        if (!$request->hasFile('documents')) {
            return;
        }
        
        $documents = $request->file('documents');
        $documentTypes = $request->input('document_types', []);
        $documentNames = $request->input('document_names', []);
        $expiryDates = $request->input('document_expiry_dates', []);
        $notes = $request->input('document_notes', []);
        
        foreach ($documents as $index => $file) {
            if ($file && $file->isValid()) {
                $filePath = $this->storeDocument($file, $employee->id);
                
                $employee->documents()->create([
                    'document_type' => $documentTypes[$index] ?? 'other',
                    'document_name' => $documentNames[$index] ?? $file->getClientOriginalName(),
                    'file_path' => $filePath,
                    'original_filename' => $file->getClientOriginalName(),
                    'mime_type' => $file->getMimeType(),
                    'file_size' => $file->getSize(),
                    'expiry_date' => $expiryDates[$index] ?? null,
                    'notes' => $notes[$index] ?? null,
                ]);
            }
        }
    }
}
```

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
            
            // 任意項目
            'family_address' => 'nullable|string',
            'family_name' => 'nullable|string|max:100',
            'family_phone_number' => 'nullable|string|regex:/^[0-9\-]+$/',
            
            // 健康管理情報
            'last_health_checkup_date' => 'nullable|date|before_or_equal:today',
            'blood_pressure' => 'nullable|string|regex:/^\d{2,3}\/\d{2,3}$/',
            'blood_type' => 'nullable|in:A,B,AB,O',
            'special_health_checkup_date' => 'nullable|date|before_or_equal:today',
            'special_health_checkup_type' => 'nullable|string|max:100',
            
            // 保険・年金情報
            'kyokai_kenpo_number' => 'nullable|string|max:20',
            'employees_pension_number' => 'nullable|string|max:20',
            'employment_insurance_number' => 'nullable|string|max:20',
            
            // 教育・資格情報
            'hire_foreman_special_education' => 'nullable|string',
            'skill_training' => 'nullable|string',
            'licenses' => 'nullable|string',
            'orientation_education_date' => 'nullable|date|before_or_equal:today',
            'kentaikyo_handbook_owned' => 'boolean',
        ];
    }
    
    public function messages()
    {
        return [
            'full_name.required' => '氏名は必須です。',
            'furigana.required' => 'ふりがなは必須です。',
            'furigana.regex' => 'ふりがなはひらがなで入力してください。',
            'job_category.required' => '職種は必須です。',
            'hire_date.required' => '雇入年月日は必須です。',
            'hire_date.after' => '雇入年月日は生年月日より後の日付である必要があります。',
            'birth_date.required' => '生年月日は必須です。',
            'birth_date.before' => '18歳以上である必要があります。',
            'nationality.required' => '国籍は必須です。',
            'residence_status.required_if' => '外国籍の場合、在留資格は必須です。',
            'residence_card_expiry.required_if' => '外国籍の場合、在留カード有効期限は必須です。',
            'residence_card_expiry.after' => '在留カード有効期限は今日より後の日付である必要があります。',
            'current_address.required' => '現住所は必須です。',
            'phone_number.required' => '電話番号は必須です。',
            'phone_number.regex' => '電話番号の形式が正しくありません。',
            'profile_photo.image' => '顔写真は画像ファイルである必要があります。',
            'profile_photo.mimes' => '顔写真はjpeg、png、jpg形式のみ対応しています。',
            'profile_photo.max' => '顔写真のファイルサイズは2MB以下である必要があります。',
            'documents.*.file' => 'アップロードされたファイルが無効です。',
            'documents.*.mimes' => 'ファイルはPDF、JPEG、PNG、JPG形式のみ対応しています。',
            'documents.*.max' => 'ファイルサイズは10MB以下である必要があります。',
            'blood_pressure.regex' => '血圧は「XXX/YYY」の形式で入力してください。',
            'blood_type.in' => '血液型はA、B、AB、Oのいずれかを選択してください。',
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
    
    public function messages()
    {
        return [
            'employee_id.required' => '従業員IDは必須です。',
            'employee_id.exists' => '指定された従業員が存在しません。',
            'document_type.required' => '書類種別は必須です。',
            'document_type.in' => '書類種別が無効です。',
            'document_name.required' => '書類名は必須です。',
            'file.required' => 'ファイルは必須です。',
            'file.mimes' => 'ファイルはPDF、JPEG、PNG、JPG形式のみ対応しています。',
            'file.max' => 'ファイルサイズは10MB以下である必要があります。',
            'expiry_date.after' => '有効期限は今日より後の日付である必要があります。',
        ];
    }
}
```

## 開発フェーズ

### Phase 1: Docker環境構築とLaravel基盤構築
1. Docker環境の設定とコンテナ起動
2. Laravel 12プロジェクトの初期化
3. 認証システムの設定（Laravel Breeze）
4. データベース設定とマイグレーション作成
5. Employeeモデルの実装

### Phase 2: コア機能開発
1. 従業員管理CRUD機能の実装
2. Bladeテンプレートの作成
3. バリデーション（Form Requests）の実装
4. 検索・フィルタリング機能
5. ファイルアップロード機能

### Phase 3: 拡張機能
1. 健康管理・資格管理機能
2. レポート機能（Excel/CSVエクスポート）
3. アラート機能（期限切れ通知）
4. 外国人従業員特有機能

### Phase 4: セキュリティ・最適化・本番対応
1. 認可（Policy）の実装
2. セキュリティ強化
3. パフォーマンス最適化
4. テストの実装
5. 本番環境Docker設定

## 初期開発タスク

### 1. Docker環境セットアップ
```bash
# プロジェクトディレクトリ作成
mkdir employee-management-app
cd employee-management-app

# Docker設定ファイル作成
mkdir -p docker/{nginx,mysql,php,supervisor}
# 上記のdocker-compose.yml、Dockerfile等を配置

# Laravel 12プロジェクト作成
docker run --rm -v $(pwd):/app composer create-project laravel/laravel:^11.0 .

# 環境設定ファイル準備
cp .env.example .env
# .env.dockerの内容を.envにコピー

# Dockerコンテナ起動
docker-compose up -d

# コンテナ内でアプリケーションキー生成
docker-compose exec app php artisan key:generate

# 必要パッケージのインストール
docker-compose exec app composer require laravel/breeze
docker-compose exec app php artisan breeze:install blade
docker-compose exec app npm install && npm run build

# データベースマイグレーション
docker-compose exec app php artisan migrate
```

### 2. 従業員管理機能の基本実装
```bash
# モデル・マイグレーション・コントローラー・リソース作成
docker-compose exec app php artisan make:model Employee -mrc
docker-compose exec app php artisan make:model EmployeeDocument -mrc
docker-compose exec app php artisan make:request StoreEmployeeRequest
docker-compose exec app php artisan make:request UpdateEmployeeRequest
docker-compose exec app php artisan make:request UploadDocumentRequest

# ストレージリンク作成
docker-compose exec app php artisan storage:link

# マイグレーション実行
docker-compose exec app php artisan migrate

# Seederの作成（テストデータ用）
docker-compose exec app php artisan make:seeder EmployeeSeeder
docker-compose exec app php artisan make:seeder EmployeeDocumentSeeder
docker-compose exec app php artisan make:factory EmployeeFactory
docker-compose exec app php artisan make:factory EmployeeDocumentFactory
```

### 3. Docker環境での開発ワークフロー
```bash
# コンテナ起動
docker-compose up -d

# コンテナログ確認
docker-compose logs -f app

# コンテナ内でコマンド実行
docker-compose exec app php artisan [command]
docker-compose exec app composer [command]
docker-compose exec app npm [command]

# データベース接続
docker-compose exec db mysql -u employee_user -p employee_management

# Redis接続
docker-compose exec redis redis-cli

# メール確認（MailHog）
# http://localhost:8025 でメール確認可能

# コンテナ停止
docker-compose down

# コンテナ停止（ボリュームも削除）
docker-compose down -v
```

### 4. ファイルストレージ設定（Docker Volume対応）
```php
// config/filesystems.php
'disks' => [
    'local' => [
        'driver' => 'local',
        'root' => storage_path('app'),
        'throw' => false,
    ],

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

### 5. テストデータの投入
```bash
# Seederの実行
docker-compose exec app php artisan db:seed --class=EmployeeSeeder

# ファクトリーを使用したテストデータ生成
docker-compose exec app php artisan tinker
# >>> Employee::factory(50)->create();
```

## Docker環境の最適化

### 開発環境向け設定
```yaml
# docker-compose.override.yml (開発環境用)
version: '3.8'

services:
  app:
    environment:
      - APP_DEBUG=true
      - APP_ENV=local
    volumes:
      - ./:/var/www/html
      - /var/www/html/vendor
      - /var/www/html/node_modules

  nginx:
    volumes:
      - ./:/var/www/html

  db:
    environment:
      - MYSQL_ROOT_PASSWORD=root_password
    ports:
      - "3306:3306"
```

### 本番環境向け設定
```yaml
# docker-compose.prod.yml (本番環境用)
version: '3.8'

services:
  app:
    environment:
      - APP_DEBUG=false
      - APP_ENV=production
    volumes:
      - app_storage:/var/www/html/storage
      - app_bootstrap:/var/www/html/bootstrap/cache

  nginx:
    volumes:
      - app_public:/var/www/html/public:ro
      - ./docker/nginx/ssl:/etc/nginx/ssl:ro

  db:
    environment:
      - MYSQL_ROOT_PASSWORD=${DB_ROOT_PASSWORD}
    volumes:
      - db_data:/var/lib/mysql
    # 本番環境ではポートを公開しない
    ports: []

volumes:
  app_storage:
  app_bootstrap:
  app_public:
  db_data:
```

## Laravel 12の新機能活用

### 簡素化されたディレクトリ構造
```php
// Laravel 12では以下の変更があります
// - app/Http/Kernel.php が削除
// - app/Console/Kernel.php が削除
// - bootstrap/app.php でミドルウェア設定
// - config/app.php の簡素化
```

### 新しい認証機能
```php
// Laravel 12の認証機能
// - より簡潔な認証設定
// - 改良されたパスワードリセット機能
// - 二要素認証の標準サポート
```

### パフォーマンスの最適化
```php
// Laravel 12のパフォーマンス改善
// - より高速な起動時間
// - 改良されたキャッシュ機能
// - 最適化されたデータベースクエリ
```

### 3. 基本画面の作成
- 従業員一覧画面（resources/views/employees/index.blade.php）
- 従業員詳細画面（resources/views/employees/show.blade.php）
- 従業員登録画面（resources/views/employees/create.blade.php）
- 従業員編集画面（resources/views/employees/edit.blade.php）

### 4. テストデータの投入
```php
// database/seeders/EmployeeSeeder.php
public function run()
{
    Employee::factory(50)->create();
}
```

## Laravel特有の開発時留意点

### Eloquent ORMの活用
- モデルのリレーション定義
- アクセサ・ミューテータの活用
- スコープの実装

### Bladeテンプレートの最適化
- コンポーネントの活用
- レイアウトの継承
- セクションの適切な使用

### セキュリティ
- CSRF保護の確認
- Mass Assignment対策
- SQLインジェクション対策

### パフォーマンス
- Eager Loadingの実装
- インデックスの適切な設定
- キャッシュの活用

#### 必要パッケージ

### 開発・運用に推奨されるパッケージ（Docker環境）
```bash
# Excel/CSVエクスポート
docker-compose exec app composer require maatwebsite/excel

# 画像処理
docker-compose exec app composer require intervention/image

# ファイルアップロード管理
docker-compose exec app composer require spatie/laravel-medialibrary

# 日本語バリデーション
docker-compose exec app composer require laravel-lang/lang

# 権限管理
docker-compose exec app composer require spatie/laravel-permission

# API開発用（必要に応じて）
docker-compose exec app composer require laravel/sanctum

# 開発ツール（開発環境のみ）
docker-compose exec app composer require --dev barryvdh/laravel-debugbar
docker-compose exec app composer require --dev barryvdh/laravel-ide-helper
docker-compose exec app composer require --dev laravel/telescope
docker-compose exec app composer require --dev pestphp/pest
docker-compose exec app composer require --dev pestphp/pest-plugin-laravel

# フロントエンド開発ツール
docker-compose exec app npm install --save-dev @tailwindcss/forms
docker-compose exec app npm install --save-dev @tailwindcss/typography
docker-compose exec app npm install alpinejs
```

## Docker環境でのセキュリティ・ファイル管理

### セキュリティ強化設定
```dockerfile
# セキュリティ強化されたDockerfile
FROM php:8.2-fpm

# セキュリティアップデート
RUN apt-get update && apt-get upgrade -y

# 非特権ユーザーの作成
RUN groupadd -r appgroup && useradd -r -g appgroup appuser

# セキュリティパッケージ
RUN apt-get install -y \
    fail2ban \
    clamav \
    clamav-daemon

# PHPセキュリティ設定
RUN echo "expose_php = Off" >> /usr/local/etc/php/php.ini
RUN echo "display_errors = Off" >> /usr/local/etc/php/php.ini
RUN echo "log_errors = On" >> /usr/local/etc/php/php.ini

# ファイル権限の厳格化
RUN chown -R appuser:appgroup /var/www/html
RUN chmod -R 755 /var/www/html

USER appuser
```

### Docker Compose用セキュリティ設定
```yaml
# docker-compose.security.yml
version: '3.8'

services:
  app:
    security_opt:
      - no-new-privileges:true
    read_only: true
    tmpfs:
      - /tmp
      - /var/run
    cap_drop:
      - ALL
    cap_add:
      - CHOWN
      - DAC_OVERRIDE
      - SETGID
      - SETUID

  nginx:
    security_opt:
      - no-new-privileges:true
    read_only: true
    tmpfs:
      - /var/cache/nginx
      - /var/run

  db:
    security_opt:
      - no-new-privileges:true
    environment:
      - MYSQL_RANDOM_ROOT_PASSWORD=yes
```

### バックアップ・復旧（Docker環境）
```bash
#!/bin/bash
# docker-backup.sh

DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="/backups/employee_management"

# データベースバックアップ
docker-compose exec -T db mysqldump -u employee_user -p${DB_PASSWORD} employee_management > $BACKUP_DIR/db_$DATE.sql

# ファイルバックアップ（Docker Volume）
docker run --rm -v employee_management_app_storage:/source -v $BACKUP_DIR:/backup alpine tar czf /backup/storage_$DATE.tar.gz -C /source .

# Dockerイメージバックアップ
docker save employee_management_app:latest | gzip > $BACKUP_DIR/app_image_$DATE.tar.gz

# 古いバックアップの削除
find $BACKUP_DIR -name "*.sql" -mtime +30 -delete
find $BACKUP_DIR -name "*.tar.gz" -mtime +30 -delete
```

## 監視・ログ管理（Docker環境）

### ログ収集設定
```yaml
# docker-compose.logging.yml
version: '3.8'

services:
  app:
    logging:
      driver: "json-file"
      options:
        max-size: "10m"
        max-file: "3"
        
  nginx:
    logging:
      driver: "json-file"
      options:
        max-size: "10m"
        max-file: "3"

  # ログ監視用
  fluentd:
    image: fluent/fluentd:v1.16-debian-1
    volumes:
      - ./docker/fluentd/fluent.conf:/fluentd/etc/fluent.conf
      - /var/log:/var/log
    depends_on:
      - app
    networks:
      - employee_network
```

### 監視ダッシュボード
```yaml
# docker-compose.monitoring.yml
version: '3.8'

services:
  prometheus:
    image: prom/prometheus:latest
    ports:
      - "9090:9090"
    volumes:
      - ./docker/prometheus/prometheus.yml:/etc/prometheus/prometheus.yml
    networks:
      - employee_network

  grafana:
    image: grafana/grafana:latest
    ports:
      - "3000:3000"
    environment:
      - GF_SECURITY_ADMIN_PASSWORD=admin
    volumes:
      - grafana_data:/var/lib/grafana
    depends_on:
      - prometheus
    networks:
      - employee_network

volumes:
  grafana_data:
```

## 本番環境デプロイ

### 本番環境用Docker設定
```yaml
# docker-compose.prod.yml
version: '3.8'

services:
  app:
    build:
      context: .
      dockerfile: Dockerfile.prod
    environment:
      - APP_ENV=production
      - APP_DEBUG=false
    volumes:
      - app_storage:/var/www/html/storage
    restart: unless-stopped

  nginx:
    volumes:
      - ./docker/nginx/ssl:/etc/nginx/ssl:ro
      - ./docker/nginx/prod.conf:/etc/nginx/conf.d/default.conf:ro
    restart: unless-stopped

  db:
    environment:
      - MYSQL_ROOT_PASSWORD=${DB_ROOT_PASSWORD}
      - MYSQL_PASSWORD=${DB_PASSWORD}
    volumes:
      - db_data:/var/lib/mysql
    restart: unless-stopped

volumes:
  app_storage:
  db_data:
```

### CI/CD設定例（GitHub Actions）
```yaml
# .github/workflows/deploy.yml
name: Deploy to Production

on:
  push:
    branches: [ main ]

jobs:
  deploy:
    runs-on: ubuntu-latest
    
    steps:
    - uses: actions/checkout@v3
    
    - name: Set up Docker Buildx
      uses: docker/setup-buildx-action@v2
    
    - name: Login to DockerHub
      uses: docker/login-action@v2
      with:
        username: ${{ secrets.DOCKER_USERNAME }}
        password: ${{ secrets.DOCKER_PASSWORD }}
    
    - name: Build and push
      uses: docker/build-push-action@v4
      with:
        context: .
        file: ./Dockerfile.prod
        push: true
        tags: ${{ secrets.DOCKER_USERNAME }}/employee-management:latest
    
    - name: Deploy to server
      uses: appleboy/ssh-action@v0.1.4
      with:
        host: ${{ secrets.HOST }}
        username: ${{ secrets.USERNAME }}
        key: ${{ secrets.SSH_KEY }}
        script: |
          cd /opt/employee-management
          docker-compose -f docker-compose.prod.yml pull
          docker-compose -f docker-compose.prod.yml up -d
          docker-compose -f docker-compose.prod.yml exec app php artisan migrate --force
          docker-compose -f docker-compose.prod.yml exec app php artisan config:cache
          docker-compose -f docker-compose.prod.yml exec app php artisan route:cache
          docker-compose -f docker-compose.prod.yml exec app php artisan view:cache
```

## Docker環境での開発時留意点

### パフォーマンス最適化
- **Docker Volume**: データベースファイルは名前付きボリュームを使用
- **ファイル同期**: 開発時は bind mount、本番時は COPY を使用
- **キャッシュ活用**: Composer、NPM のキャッシュを適切に設定

### セキュリティ対策
- **最小権限の原則**: 各コンテナに必要最小限の権限のみ付与
- **ネットワーク分離**: アプリケーション用の専用ネットワーク使用
- **シークレット管理**: 本番環境では Docker secrets を使用

### 監視・運用
- **ヘルスチェック**: 各サービスにヘルスチェックを設定
- **ログ管理**: 構造化ログとログローテーションの設定
- **リソース制限**: CPU・メモリ使用量の制限設定

### 開発効率化
- **ホットリロード**: 開発時はファイル変更の即座反映
- **デバッグ環境**: Xdebug の適切な設定
- **テスト環境**: 独立したテスト用データベースの設定

## セキュリティ・ファイル管理要件

### ファイルアップロードセキュリティ
```php
// config/app.php でアップロード制限設定
'upload_max_filesize' => '10M',
'post_max_size' => '50M',

// ファイルタイプ検証の強化
'allowed_document_types' => [
    'application/pdf',
    'image/jpeg',
    'image/png',
    'image/jpg'
],

// ウイルススキャン（本番環境推奨）
'enable_virus_scan' => env('ENABLE_VIRUS_SCAN', false),
```

### アクセス制御
```php
// app/Policies/EmployeePolicy.php
class EmployeePolicy
{
    public function viewDocuments(User $user, Employee $employee)
    {
        // 管理者または本人のみ書類閲覧可能
        return $user->isAdmin() || $user->employee_id === $employee->id;
    }
    
    public function downloadDocument(User $user, EmployeeDocument $document)
    {
        // 管理者または本人のみダウンロード可能
        return $user->isAdmin() || $user->employee_id === $document->employee_id;
    }
}
```

### ファイル暗号化（本番環境推奨）
```php
// app/Services/DocumentEncryptionService.php
class DocumentEncryptionService
{
    public function encryptFile($filePath)
    {
        $content = Storage::get($filePath);
        $encrypted = encrypt($content);
        Storage::put($filePath . '.enc', $encrypted);
        Storage::delete($filePath);
        
        return $filePath . '.enc';
    }
    
    public function decryptFile($encryptedPath)
    {
        $encrypted = Storage::get($encryptedPath);
        return decrypt($encrypted);
    }
}
```

## 外国人従業員管理の法的要件

### 入管法関連
- 在留資格の適正管理
- 在留期限の監視とアラート機能
- 就労可能な在留資格の確認

### 労働基準法関連
- 外国人労働者届出の義務
- 労働条件の母国語での説明
- 適正な賃金支払いの記録

### 技能実習法・特定技能関連
- 技能実習計画の管理
- 特定技能評価試験の記録
- 受入機関としての義務履行

### 個人情報保護法
- パスポート情報の適切な管理
- 在留カード情報の暗号化保存
- 第三国への情報提供制限

## データ保護・バックアップ戦略

### 定期バックアップ
```bash
# 日次バックアップスクリプト例
#!/bin/bash
DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="/backups/employee_management"

# データベースバックアップ
mysqldump -u user -p database_name > $BACKUP_DIR/db_$DATE.sql

# ファイルバックアップ
tar -czf $BACKUP_DIR/files_$DATE.tar.gz storage/app/private/employee_documents/
tar -czf $BACKUP_DIR/photos_$DATE.tar.gz storage/app/public/employee_photos/

# 古いバックアップの削除（30日以前）
find $BACKUP_DIR -name "*.sql" -mtime +30 -delete
find $BACKUP_DIR -name "*.tar.gz" -mtime +30 -delete
```

### 災害復旧
- オフサイトバックアップの実装
- RTO（目標復旧時間）：4時間以内
- RPO（目標復旧時点）：1日以内

## アラート・通知機能

### 在留期限アラート
```php
// app/Console/Commands/CheckExpiringDocuments.php
class CheckExpiringDocuments extends Command
{
    public function handle()
    {
        // 在留カード期限切れ間近の従業員を取得
        $expiringEmployees = Employee::expiringResidenceCards(30)->get();
        
        foreach ($expiringEmployees as $employee) {
            // 管理者へメール通知
            Mail::to(config('app.admin_email'))
                ->send(new ResidenceCardExpiringMail($employee));
        }
        
        // 資格・免許の期限切れチェック
        $expiringDocuments = EmployeeDocument::expiring(30)->get();
        
        foreach ($expiringDocuments as $document) {
            Mail::to(config('app.admin_email'))
                ->send(new DocumentExpiringMail($document));
        }
    }
}
```

## 監査ログ

### アクセスログの記録
```php
// app/Models/AuditLog.php
class AuditLog extends Model
{
    protected $fillable = [
        'user_id',
        'employee_id', 
        'action',
        'description',
        'ip_address',
        'user_agent'
    ];
    
    public static function log($action, $employeeId = null, $description = '')
    {
        self::create([
            'user_id' => auth()->id(),
            'employee_id' => $employeeId,
            'action' => $action,
            'description' => $description,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);
    }
}
```