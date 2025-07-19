<?php

namespace App\Imports;

use App\Models\Employee;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Carbon\Carbon;

class EmployeesImport implements ToModel, WithValidation, SkipsOnError, SkipsOnFailure, WithBatchInserts, WithChunkReading
{
    use Importable, SkipsErrors, SkipsFailures;
    
    private $rows = 0;

    /**
     * @param array $row
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        $this->rows++;
        
        // ヘッダー行をスキップ
        if ($this->rows == 1) {
            return null;
        }
        
        // デバッグ用：最初の数行の内容をログに出力
        if ($this->rows <= 4) {
            \Log::info('CSV Row ' . $this->rows . ' content: ' . implode(', ', $row));
        }
        
        // インデックスベースでデータを読み込み（新しいテンプレート形式）
        return new Employee([
            'full_name' => $row[0] ?? null,  // 氏名
            'furigana' => $row[1] ?? null,   // ふりがな
            'job_category' => $row[2] ?? null,  // 職種
            'department' => $row[3] ?? null,    // 部署名
            'position' => $row[4] ?? null,      // 役職
            'hire_date' => $this->parseDate($row[5] ?? null),    // 雇入年月日
            'birth_date' => $this->parseDate($row[6] ?? null),   // 生年月日
            'nationality' => $row[7] ?? '日本',  // 国籍
            'gender' => $row[8] ?? null,      // 性別
            'has_spouse' => $this->parseBoolean($row[9] ?? null),    // 配偶者の有無
            'residence_status' => $row[10] ?? null,     // 在留資格
            'residence_card_expiry' => $this->parseDate($row[11] ?? null),  // 在留カード有効期限
            'postal_code' => $row[12] ?? null,          // 郵便番号
            'current_address' => $row[13] ?? null,      // 現住所
            'family_address' => $row[14] ?? null,       // 家族住所
            'family_name' => $row[15] ?? null,          // 家族名
            'family_name_furigana' => $row[16] ?? null, // 家族名ふりがな
            'family_relationship' => $row[17] ?? null,  // 続柄
            'family_name_2' => $row[18] ?? null,        // 家族名2
            'family_name_furigana_2' => $row[19] ?? null, // 家族名ふりがな2
            'family_relationship_2' => $row[20] ?? null,  // 続柄2
            'family_address_2' => $row[21] ?? null,     // 家族住所2
            'family_phone_number_2' => $row[22] ?? null, // 家族電話番号2
            'phone_number' => $row[23] ?? null,         // 電話番号
            'family_phone_number' => $row[24] ?? null,  // 家族電話番号
            'last_health_checkup_date' => $this->parseDate($row[25] ?? null),  // 最近の健康診断日
            'blood_pressure' => $row[26] ?? null,       // 血圧
            'blood_type' => $row[27] ?? null,           // 血液型
            'special_health_checkup_date' => $this->parseDate($row[28] ?? null), // 特殊健康診断日
            'special_health_checkup_type' => $row[29] ?? null,  // 特殊健康診断種類
            'kyokai_kenpo_number' => $row[30] ?? null,  // 協会けんぽ番号
            'employees_pension_number' => $row[31] ?? null,     // 厚生年金番号
            'employment_insurance_number' => $row[32] ?? null,  // 雇用保険番号
            'hire_foreman_special_education' => $row[33] ?? null,   // 雇入･職長特別教育
            'skill_training' => $row[34] ?? null,       // 技能講習
            'licenses' => $row[35] ?? null,             // 免許
            'orientation_education_date' => $this->parseDate($row[36] ?? null),     // 受入教育実施年月日
            'kentaikyo_handbook_owned' => $this->parseBoolean($row[37] ?? null),    // 建退共手帳所有の有無
            'is_active' => true,
        ]);
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            '0' => ['required', 'string', 'max:100'],     // 氏名
            '1' => ['required', 'string', 'max:100'],     // ふりがな
            '2' => ['required', 'string', 'max:50'],      // 職種
            '3' => ['nullable', 'string', 'max:100'],     // 部署名
            '4' => ['nullable', 'string', 'max:100'],     // 役職
            '5' => ['required', 'date_format:Y-m-d'],     // 雇入年月日
            '6' => ['required', 'date_format:Y-m-d'],     // 生年月日
            '7' => ['required', 'string', 'max:50'],      // 国籍
            '8' => ['nullable', 'in:男性,女性,その他'],      // 性別
            '9' => ['nullable', 'in:はい,いいえ,true,false,1,0'],  // 配偶者の有無
            '12' => ['nullable', 'string', 'max:10'],     // 郵便番号
            '13' => ['required', 'string'],               // 現住所
            '16' => ['nullable', 'string', 'max:100'],    // 家族名ふりがな
            '17' => ['nullable', 'in:代表取締役社長,配偶者,妻,夫,父,母,子,息子,娘,兄,姉,弟,妹,叔父,叔母,兄弟姉妹,その他'],  // 続柄
            '18' => ['nullable', 'string', 'max:100'],    // 家族名2
            '19' => ['nullable', 'string', 'max:100'],    // 家族名ふりがな2
            '20' => ['nullable', 'in:代表取締役社長,配偶者,妻,夫,父,母,子,息子,娘,兄,姉,弟,妹,叔父,叔母,兄弟姉妹,その他'],  // 続柄2
            '22' => ['nullable', 'string', 'max:20'],     // 家族電話番号2
            '23' => ['required', 'string', 'max:20'],     // 電話番号
            '27' => ['nullable', 'in:A,B,AB,O'],          // 血液型
            '37' => ['nullable', 'in:はい,いいえ,true,false,1,0'],  // 建退共手帳所有の有無
        ];
    }

    /**
     * @return array
     */
    public function customValidationMessages()
    {
        return [
            '0.required' => '氏名は必須です。',
            '1.required' => 'ふりがなは必須です。',
            '2.required' => '職種は必須です。',
            '3.max' => '部署名は100文字以下で入力してください。',
            '4.max' => '役職は100文字以下で入力してください。',
            '5.required' => '雇入年月日は必須です。',
            '5.date_format' => '雇入年月日は「YYYY-MM-DD」の形式で入力してください。',
            '6.required' => '生年月日は必須です。',
            '6.date_format' => '生年月日は「YYYY-MM-DD」の形式で入力してください。',
            '7.required' => '国籍は必須です。',
            '13.required' => '現住所は必須です。',
            '23.required' => '電話番号は必須です。',
            '27.in' => '血液型はA、B、AB、Oのいずれかを入力してください。',
        ];
    }

    /**
     * 日付文字列をパースする
     * @param mixed $date
     * @return \Carbon\Carbon|null
     */
    private function parseDate($date)
    {
        if (empty($date)) {
            return null;
        }

        // 数値の場合（Excel日付シリアル値）
        if (is_numeric($date)) {
            return Carbon::createFromFormat('Y-m-d', '1900-01-01')->addDays($date - 2);
        }

        // 文字列の場合
        try {
            return Carbon::parse($date);
        } catch (\Exception) {
            return null;
        }
    }

    /**
     * ブール値をパースする
     * @param mixed $value
     * @return bool
     */
    private function parseBoolean($value)
    {
        if (empty($value)) {
            return false;
        }

        $value = strtolower(trim($value));
        return in_array($value, ['はい', 'true', '1', 'yes', 'on']);
    }

    /**
     * バッチサイズを設定
     * @return int
     */
    public function batchSize(): int
    {
        return 100;
    }

    /**
     * チャンクサイズを設定
     * @return int
     */
    public function chunkSize(): int
    {
        return 100;
    }

    /**
     * 処理した行数を取得
     * @return int
     */
    public function getRowCount(): int
    {
        return $this->rows;
    }
}