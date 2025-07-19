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

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
