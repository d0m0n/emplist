<?php

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $companies = [
            [
                'name' => '株式会社建設太郎',
                'representative_name' => '建設太郎',
                'address' => '東京都新宿区西新宿1-1-1',
                'phone_number' => '03-1234-5678',
                'fax_number' => '03-1234-5679',
                'email' => 'info@kensetsu-taro.co.jp',
                'website' => 'https://kensetsu-taro.co.jp',
                'is_active' => true,
            ],
            [
                'name' => '工事株式会社',
                'representative_name' => '工事花子',
                'address' => '大阪府大阪市中央区本町1-1-1',
                'phone_number' => '06-1234-5678',
                'fax_number' => '06-1234-5679',
                'email' => 'info@kouji-corp.co.jp',
                'website' => 'https://kouji-corp.co.jp',
                'is_active' => true,
            ],
            [
                'name' => '建築工業株式会社',
                'representative_name' => '建築次郎',
                'address' => '愛知県名古屋市中区栄1-1-1',
                'phone_number' => '052-1234-5678',
                'fax_number' => '052-1234-5679',
                'email' => 'info@kenchiku-kogyo.co.jp',
                'website' => 'https://kenchiku-kogyo.co.jp',
                'is_active' => true,
            ]
        ];

        foreach ($companies as $company) {
            Company::create($company);
        }
    }
}
