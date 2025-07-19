<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
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

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_documents');
    }
};
