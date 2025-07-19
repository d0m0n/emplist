<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEmployeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->is_admin;
    }
    
    public function rules(): array
    {
        return [
            // 基本情報（必須）
            'full_name' => 'required|string|max:100',
            'furigana' => 'required|string|max:100|regex:/^[ぁ-んー\s]+$/u',
            'job_category' => 'required|string|max:50',
            'department' => 'nullable|string|max:100',
            'position' => 'nullable|string|max:100',
            'hire_date' => 'required|date|after:birth_date',
            'birth_date' => 'required|date|before:18 years ago',
            'nationality' => 'required|string|max:50',
            'residence_status' => 'nullable|required_if:nationality,!=,日本|string|max:100',
            'residence_card_expiry' => 'nullable|required_if:nationality,!=,日本|date|after:today',
            'current_address' => 'required|string',
            'postal_code' => 'nullable|string|max:10|regex:/^\d{3}-\d{4}$/',
            'phone_number' => 'required|string|regex:/^[0-9\-]+$/',
            'gender' => 'nullable|in:男性,女性,その他',
            'has_spouse' => 'boolean',
            
            // ファイルアップロード
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            
            // 任意項目
            'family_address' => 'nullable|string',
            'family_name' => 'nullable|string|max:100',
            'family_name_furigana' => 'nullable|string|max:100|regex:/^[ぁ-んー\s]*$/u',
            'family_relationship' => 'nullable|in:代表取締役社長,配偶者,妻,夫,父,母,子,息子,娘,兄,姉,弟,妹,叔父,叔母,兄弟姉妹,その他',
            'family_name_2' => 'nullable|string|max:100',
            'family_name_furigana_2' => 'nullable|string|max:100|regex:/^[ぁ-んー\s]*$/u',
            'family_relationship_2' => 'nullable|in:代表取締役社長,配偶者,妻,夫,父,母,子,息子,娘,兄,姉,弟,妹,叔父,叔母,兄弟姉妹,その他',
            'family_address_2' => 'nullable|string',
            'family_phone_number_2' => 'nullable|string|regex:/^[0-9\-]+$/',
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
            
            // 運転免許証・退職日
            'driving_license_expiry' => 'nullable|date|after:today',
            'retirement_date' => 'nullable|date|after:hire_date',
            
            // 所属会社
            'company_id' => 'nullable|exists:companies,id',
        ];
    }
    
    public function messages(): array
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
            'postal_code.regex' => '郵便番号は「123-4567」の形式で入力してください。',
            'gender.in' => '性別は男性、女性、その他のいずれかを選択してください。',
            'family_name_furigana.regex' => '家族名ふりがなはひらがなで入力してください。',
            'family_relationship.in' => '続柄は代表取締役社長、配偶者、妻、夫、父、母、子、息子、娘、兄、姉、弟、妹、叔父、叔母、兄弟姉妹、その他のいずれかを選択してください。',
            'family_name_2.max' => '家族名2は100文字以下で入力してください。',
            'family_name_furigana_2.regex' => '家族名ふりがな2はひらがなで入力してください。',
            'family_relationship_2.in' => '続柄2は代表取締役社長、配偶者、妻、夫、父、母、子、息子、娘、兄、姉、弟、妹、叔父、叔母、兄弟姉妹、その他のいずれかを選択してください。',
            'family_phone_number_2.regex' => '家族電話番号2の形式が正しくありません。',
            'profile_photo.image' => '顔写真は画像ファイルである必要があります。',
            'profile_photo.mimes' => '顔写真はjpeg、png、jpg形式のみ対応しています。',
            'profile_photo.max' => '顔写真のファイルサイズは2MB以下である必要があります。',
            'blood_pressure.regex' => '血圧は「XXX/YYY」の形式で入力してください。',
            'blood_type.in' => '血液型はA、B、AB、Oのいずれかを選択してください。',
            'driving_license_expiry.after' => '運転免許証有効期限は今日より後の日付である必要があります。',
            'retirement_date.after' => '退職日は雇入年月日より後の日付である必要があります。',
            'company_id.exists' => '選択された会社が存在しません。',
        ];
    }
}
