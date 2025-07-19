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
            // 2人目の家族情報
            $table->string('family_name_2', 100)->nullable()->comment('家族名2')->after('family_relationship');
            $table->string('family_name_furigana_2', 100)->nullable()->comment('家族名ふりがな2')->after('family_name_2');
            $table->string('family_relationship_2', 50)->nullable()->comment('続柄2')->after('family_name_furigana_2');
            $table->text('family_address_2')->nullable()->comment('家族住所2')->after('family_relationship_2');
            $table->string('family_phone_number_2', 20)->nullable()->comment('家族電話番号2')->after('family_address_2');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn([
                'family_name_2',
                'family_name_furigana_2', 
                'family_relationship_2',
                'family_address_2',
                'family_phone_number_2'
            ]);
        });
    }
};
