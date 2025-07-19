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
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->comment('会社名');
            $table->string('representative_name', 100)->nullable()->comment('代表者名');
            $table->text('address')->nullable()->comment('住所');
            $table->string('phone_number', 20)->nullable()->comment('電話番号');
            $table->string('fax_number', 20)->nullable()->comment('FAX番号');
            $table->string('email', 100)->nullable()->comment('Eメールアドレス');
            $table->string('website', 200)->nullable()->comment('ホームページアドレス');
            $table->string('logo_image')->nullable()->comment('ロゴ画像ファイルパス');
            $table->boolean('is_active')->default(true)->comment('有効フラグ');
            $table->timestamps();
            
            // インデックス
            $table->index('name');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
