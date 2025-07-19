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
        Schema::table('employees', function (Blueprint $table) {
            $table->string('postal_code', 10)->nullable()->comment('郵便番号');
            $table->string('mobile_phone', 20)->nullable()->comment('携帯電話番号');
            $table->enum('gender', ['男性', '女性', 'その他'])->nullable()->comment('性別');
            $table->boolean('has_spouse')->nullable()->comment('配偶者の有無');
            $table->string('family_name_furigana', 100)->nullable()->comment('家族名ふりがな');
            $table->string('family_relationship', 50)->nullable()->comment('続柄');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn([
                'postal_code',
                'mobile_phone',
                'gender',
                'has_spouse',
                'family_name_furigana',
                'family_relationship'
            ]);
        });
    }
};
