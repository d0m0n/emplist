<?php

namespace App\Exports;

use App\Models\Employee;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class EmployeesExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Employee::active()->get();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'ID',
            '氏名',
            'ふりがな',
            '職種',
            '部署名',
            '役職',
            '雇入年月日',
            '生年月日',
            '国籍',
            '性別',
            '配偶者の有無',
            '在留資格',
            '在留カード有効期限',
            '郵便番号',
            '現住所',
            '家族住所',
            '家族名',
            '家族名ふりがな',
            '続柄',
            '家族名2',
            '家族名ふりがな2',
            '続柄2',
            '家族住所2',
            '家族電話番号2',
            '電話番号',
            '家族電話番号',
            '最近の健康診断日',
            '血圧',
            '血液型',
            '特殊健康診断日',
            '特殊健康診断種類',
            '協会けんぽ番号',
            '厚生年金番号',
            '雇用保険番号',
            '雇入･職長特別教育',
            '技能講習',
            '免許',
            '受入教育実施年月日',
            '建退共手帳所有の有無',
            '作成日',
            '更新日'
        ];
    }

    /**
     * @param Employee $employee
     * @return array
     */
    public function map($employee): array
    {
        return [
            $employee->id,
            $employee->full_name,
            $employee->furigana,
            $employee->job_category,
            $employee->department,
            $employee->position,
            $employee->hire_date ? $employee->hire_date->format('Y-m-d') : '',
            $employee->birth_date ? $employee->birth_date->format('Y-m-d') : '',
            $employee->nationality,
            $employee->gender,
            $employee->has_spouse ? 'はい' : 'いいえ',
            $employee->residence_status,
            $employee->residence_card_expiry ? $employee->residence_card_expiry->format('Y-m-d') : '',
            $employee->postal_code,
            $employee->current_address,
            $employee->family_address,
            $employee->family_name,
            $employee->family_name_furigana,
            $employee->family_relationship,
            $employee->family_name_2,
            $employee->family_name_furigana_2,
            $employee->family_relationship_2,
            $employee->family_address_2,
            $employee->family_phone_number_2,
            $employee->phone_number,
            $employee->family_phone_number,
            $employee->last_health_checkup_date ? $employee->last_health_checkup_date->format('Y-m-d') : '',
            $employee->blood_pressure,
            $employee->blood_type,
            $employee->special_health_checkup_date ? $employee->special_health_checkup_date->format('Y-m-d') : '',
            $employee->special_health_checkup_type,
            $employee->kyokai_kenpo_number,
            $employee->employees_pension_number,
            $employee->employment_insurance_number,
            $employee->hire_foreman_special_education,
            $employee->skill_training,
            $employee->licenses,
            $employee->orientation_education_date ? $employee->orientation_education_date->format('Y-m-d') : '',
            $employee->kentaikyo_handbook_owned ? 'はい' : 'いいえ',
            $employee->created_at->format('Y-m-d H:i:s'),
            $employee->updated_at->format('Y-m-d H:i:s')
        ];
    }

    /**
     * @param Worksheet $sheet
     * @return array
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // ヘッダー行をボールドにする
            1 => ['font' => ['bold' => true]],
        ];
    }
}