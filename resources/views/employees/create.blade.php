<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('従業員登録') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('employees.store') }}" enctype="multipart/form-data">
                        @csrf
                        
                        <!-- 基本情報 -->
                        <div class="mb-8">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">基本情報</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <x-input-label for="full_name" :value="__('氏名 *')" />
                                    <x-text-input id="full_name" class="block mt-1 w-full" type="text" name="full_name" :value="old('full_name')" required autofocus />
                                    <x-input-error :messages="$errors->get('full_name')" class="mt-2" />
                                </div>
                                
                                <div>
                                    <x-input-label for="furigana" :value="__('ふりがな *')" />
                                    <x-text-input id="furigana" class="block mt-1 w-full" type="text" name="furigana" :value="old('furigana')" required />
                                    <x-input-error :messages="$errors->get('furigana')" class="mt-2" />
                                </div>
                                
                                <div>
                                    <x-input-label for="job_category" :value="__('職種 *')" />
                                    <select id="job_category" name="job_category" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                        <option value="">選択してください</option>
                                        <option value="現場作業員" {{ old('job_category') == '現場作業員' ? 'selected' : '' }}>現場作業員</option>
                                        <option value="鳶工" {{ old('job_category') == '鳶工' ? 'selected' : '' }}>鳶工</option>
                                        <option value="職長" {{ old('job_category') == '職長' ? 'selected' : '' }}>職長</option>
                                        <option value="技術者" {{ old('job_category') == '技術者' ? 'selected' : '' }}>技術者</option>
                                        <option value="管理者" {{ old('job_category') == '管理者' ? 'selected' : '' }}>管理者</option>
                                    </select>
                                    <x-input-error :messages="$errors->get('job_category')" class="mt-2" />
                                </div>
                                
                                <div>
                                    <x-input-label for="department" :value="__('部署名')" />
                                    <x-text-input id="department" class="block mt-1 w-full" type="text" name="department" :value="old('department')" />
                                    <x-input-error :messages="$errors->get('department')" class="mt-2" />
                                </div>
                                
                                <div>
                                    <x-input-label for="position" :value="__('役職')" />
                                    <x-text-input id="position" class="block mt-1 w-full" type="text" name="position" :value="old('position')" />
                                    <x-input-error :messages="$errors->get('position')" class="mt-2" />
                                </div>
                                
                                <div>
                                    <x-input-label for="hire_date" :value="__('雇入年月日 *')" />
                                    <x-text-input id="hire_date" class="block mt-1 w-full" type="date" name="hire_date" :value="old('hire_date')" required />
                                    <x-input-error :messages="$errors->get('hire_date')" class="mt-2" />
                                </div>
                                
                                <div>
                                    <x-input-label for="birth_date" :value="__('生年月日 *')" />
                                    <x-text-input id="birth_date" class="block mt-1 w-full" type="date" name="birth_date" :value="old('birth_date')" required />
                                    <x-input-error :messages="$errors->get('birth_date')" class="mt-2" />
                                </div>
                                
                                <div>
                                    <x-input-label for="nationality" :value="__('国籍 *')" />
                                    <x-text-input id="nationality" class="block mt-1 w-full" type="text" name="nationality" :value="old('nationality', '日本')" required />
                                    <x-input-error :messages="$errors->get('nationality')" class="mt-2" />
                                </div>
                                
                                <div>
                                    <x-input-label for="gender" :value="__('性別')" />
                                    <select id="gender" name="gender" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                        <option value="">選択してください</option>
                                        <option value="男性" {{ old('gender') == '男性' ? 'selected' : '' }}>男性</option>
                                        <option value="女性" {{ old('gender') == '女性' ? 'selected' : '' }}>女性</option>
                                        <option value="その他" {{ old('gender') == 'その他' ? 'selected' : '' }}>その他</option>
                                    </select>
                                    <x-input-error :messages="$errors->get('gender')" class="mt-2" />
                                </div>
                                
                                <div>
                                    <label for="has_spouse" class="inline-flex items-center">
                                        <input id="has_spouse" type="checkbox" name="has_spouse" value="1" {{ old('has_spouse') ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                        <span class="ml-2 text-sm text-gray-600">配偶者がいる</span>
                                    </label>
                                </div>
                                
                                <div id="residence_status_field">
                                    <x-input-label for="residence_status" :value="__('在留資格')" />
                                    <x-text-input id="residence_status" class="block mt-1 w-full" type="text" name="residence_status" :value="old('residence_status')" />
                                    <x-input-error :messages="$errors->get('residence_status')" class="mt-2" />
                                    <p class="mt-1 text-sm text-gray-500">外国籍の方は必須です</p>
                                </div>
                                
                                <div id="residence_card_expiry_field">
                                    <x-input-label for="residence_card_expiry" :value="__('在留カード有効期限')" />
                                    <x-text-input id="residence_card_expiry" class="block mt-1 w-full" type="date" name="residence_card_expiry" :value="old('residence_card_expiry')" />
                                    <x-input-error :messages="$errors->get('residence_card_expiry')" class="mt-2" />
                                    <p class="mt-1 text-sm text-gray-500">外国籍の方は必須です</p>
                                </div>
                                
                                <div>
                                    <x-input-label for="company_id" :value="__('所属会社')" />
                                    <select id="company_id" name="company_id" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                        <option value="">選択してください</option>
                                        @foreach($companies as $company)
                                            <option value="{{ $company->id }}" {{ old('company_id') == $company->id ? 'selected' : '' }}>{{ $company->name }}</option>
                                        @endforeach
                                    </select>
                                    <x-input-error :messages="$errors->get('company_id')" class="mt-2" />
                                </div>
                                
                                <div>
                                    <x-input-label for="profile_photo" :value="__('顔写真')" />
                                    <input id="profile_photo" class="block mt-1 w-full" type="file" name="profile_photo" accept="image/*" />
                                    <x-input-error :messages="$errors->get('profile_photo')" class="mt-2" />
                                </div>
                            </div>
                        </div>
                        
                        <!-- 連絡先情報 -->
                        <div class="mb-8">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">連絡先情報</h3>
                            <div class="grid grid-cols-1 gap-4">
                                <div>
                                    <x-input-label for="postal_code" :value="__('郵便番号')" />
                                    <x-text-input id="postal_code" class="block mt-1 w-full" type="text" name="postal_code" :value="old('postal_code')" placeholder="例: 123-4567" />
                                    <x-input-error :messages="$errors->get('postal_code')" class="mt-2" />
                                </div>
                                
                                <div>
                                    <x-input-label for="current_address" :value="__('現住所 *')" />
                                    <textarea id="current_address" name="current_address" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" rows="3" required>{{ old('current_address') }}</textarea>
                                    <x-input-error :messages="$errors->get('current_address')" class="mt-2" />
                                </div>
                                
                                <div>
                                    <x-input-label for="phone_number" :value="__('電話番号 *')" />
                                    <x-text-input id="phone_number" class="block mt-1 w-full" type="tel" name="phone_number" :value="old('phone_number')" required />
                                    <x-input-error :messages="$errors->get('phone_number')" class="mt-2" />
                                </div>
                                
                                <!-- 1人目の家族情報 -->
                                <div class="mt-6">
                                    <h4 class="text-md font-medium text-gray-700 mb-4">家族情報（1人目）</h4>
                                    
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                                        <div>
                                            <x-input-label for="family_name" :value="__('家族名')" />
                                            <x-text-input id="family_name" class="block mt-1 w-full" type="text" name="family_name" :value="old('family_name')" />
                                            <x-input-error :messages="$errors->get('family_name')" class="mt-2" />
                                        </div>
                                        
                                        <div>
                                            <x-input-label for="family_name_furigana" :value="__('家族名ふりがな')" />
                                            <x-text-input id="family_name_furigana" class="block mt-1 w-full" type="text" name="family_name_furigana" :value="old('family_name_furigana')" />
                                            <x-input-error :messages="$errors->get('family_name_furigana')" class="mt-2" />
                                        </div>
                                        
                                        <div>
                                            <x-input-label for="family_relationship" :value="__('続柄')" />
                                            <select id="family_relationship" name="family_relationship" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                                <option value="">選択してください</option>
                                                <option value="代表取締役社長" {{ old('family_relationship') == '代表取締役社長' ? 'selected' : '' }}>代表取締役社長</option>
                                                <option value="配偶者" {{ old('family_relationship') == '配偶者' ? 'selected' : '' }}>配偶者</option>
                                                <option value="妻" {{ old('family_relationship') == '妻' ? 'selected' : '' }}>妻</option>
                                                <option value="夫" {{ old('family_relationship') == '夫' ? 'selected' : '' }}>夫</option>
                                                <option value="父" {{ old('family_relationship') == '父' ? 'selected' : '' }}>父</option>
                                                <option value="母" {{ old('family_relationship') == '母' ? 'selected' : '' }}>母</option>
                                                <option value="子" {{ old('family_relationship') == '子' ? 'selected' : '' }}>子</option>
                                                <option value="息子" {{ old('family_relationship') == '息子' ? 'selected' : '' }}>息子</option>
                                                <option value="娘" {{ old('family_relationship') == '娘' ? 'selected' : '' }}>娘</option>
                                                <option value="兄" {{ old('family_relationship') == '兄' ? 'selected' : '' }}>兄</option>
                                                <option value="姉" {{ old('family_relationship') == '姉' ? 'selected' : '' }}>姉</option>
                                                <option value="弟" {{ old('family_relationship') == '弟' ? 'selected' : '' }}>弟</option>
                                                <option value="妹" {{ old('family_relationship') == '妹' ? 'selected' : '' }}>妹</option>
                                                <option value="叔父" {{ old('family_relationship') == '叔父' ? 'selected' : '' }}>叔父</option>
                                                <option value="叔母" {{ old('family_relationship') == '叔母' ? 'selected' : '' }}>叔母</option>
                                                <option value="兄弟姉妹" {{ old('family_relationship') == '兄弟姉妹' ? 'selected' : '' }}>兄弟姉妹</option>
                                                <option value="その他" {{ old('family_relationship') == 'その他' ? 'selected' : '' }}>その他</option>
                                            </select>
                                            <x-input-error :messages="$errors->get('family_relationship')" class="mt-2" />
                                        </div>
                                    </div>
                                    
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <x-input-label for="family_address" :value="__('家族住所')" />
                                            <textarea id="family_address" name="family_address" rows="3" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('family_address') }}</textarea>
                                            <x-input-error :messages="$errors->get('family_address')" class="mt-2" />
                                        </div>
                                                                            <div>
                                        <x-input-label for="family_phone_number" :value="__('家族電話番号')" />
                                        <x-text-input id="family_phone_number" class="block mt-1 w-full" type="tel" name="family_phone_number" :value="old('family_phone_number')" />
                                        <x-input-error :messages="$errors->get('family_phone_number')" class="mt-2" />
                                    </div>
                                    <div>
                                        <!-- 空のスペース -->
                                    </div>
                                </div>
                                
                                <!-- 2人目の家族情報 -->
                                <div class="mt-6 pt-6 border-t border-gray-200">
                                    <h4 class="text-md font-medium text-gray-700 mb-4">家族情報（2人目）</h4>
                                    
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                                        <div>
                                            <x-input-label for="family_name_2" :value="__('家族名2')" />
                                            <x-text-input id="family_name_2" class="block mt-1 w-full" type="text" name="family_name_2" :value="old('family_name_2')" />
                                            <x-input-error :messages="$errors->get('family_name_2')" class="mt-2" />
                                        </div>
                                        
                                        <div>
                                            <x-input-label for="family_name_furigana_2" :value="__('家族名ふりがな2')" />
                                            <x-text-input id="family_name_furigana_2" class="block mt-1 w-full" type="text" name="family_name_furigana_2" :value="old('family_name_furigana_2')" />
                                            <x-input-error :messages="$errors->get('family_name_furigana_2')" class="mt-2" />
                                        </div>
                                        
                                        <div>
                                            <x-input-label for="family_relationship_2" :value="__('続柄2')" />
                                            <select id="family_relationship_2" name="family_relationship_2" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                                <option value="">選択してください</option>
                                                <option value="代表取締役社長" {{ old('family_relationship_2') == '代表取締役社長' ? 'selected' : '' }}>代表取締役社長</option>
                                                <option value="配偶者" {{ old('family_relationship_2') == '配偶者' ? 'selected' : '' }}>配偶者</option>
                                                <option value="妻" {{ old('family_relationship_2') == '妻' ? 'selected' : '' }}>妻</option>
                                                <option value="夫" {{ old('family_relationship_2') == '夫' ? 'selected' : '' }}>夫</option>
                                                <option value="父" {{ old('family_relationship_2') == '父' ? 'selected' : '' }}>父</option>
                                                <option value="母" {{ old('family_relationship_2') == '母' ? 'selected' : '' }}>母</option>
                                                <option value="子" {{ old('family_relationship_2') == '子' ? 'selected' : '' }}>子</option>
                                                <option value="息子" {{ old('family_relationship_2') == '息子' ? 'selected' : '' }}>息子</option>
                                                <option value="娘" {{ old('family_relationship_2') == '娘' ? 'selected' : '' }}>娘</option>
                                                <option value="兄" {{ old('family_relationship_2') == '兄' ? 'selected' : '' }}>兄</option>
                                                <option value="姉" {{ old('family_relationship_2') == '姉' ? 'selected' : '' }}>姉</option>
                                                <option value="弟" {{ old('family_relationship_2') == '弟' ? 'selected' : '' }}>弟</option>
                                                <option value="妹" {{ old('family_relationship_2') == '妹' ? 'selected' : '' }}>妹</option>
                                                <option value="叔父" {{ old('family_relationship_2') == '叔父' ? 'selected' : '' }}>叔父</option>
                                                <option value="叔母" {{ old('family_relationship_2') == '叔母' ? 'selected' : '' }}>叔母</option>
                                                <option value="兄弟姉妹" {{ old('family_relationship_2') == '兄弟姉妹' ? 'selected' : '' }}>兄弟姉妹</option>
                                                <option value="その他" {{ old('family_relationship_2') == 'その他' ? 'selected' : '' }}>その他</option>
                                            </select>
                                            <x-input-error :messages="$errors->get('family_relationship_2')" class="mt-2" />
                                        </div>
                                    </div>
                                    
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <x-input-label for="family_address_2" :value="__('家族住所2')" />
                                            <textarea id="family_address_2" name="family_address_2" rows="3" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('family_address_2') }}</textarea>
                                            <x-input-error :messages="$errors->get('family_address_2')" class="mt-2" />
                                        </div>
                                        
                                        <div>
                                            <x-input-label for="family_phone_number_2" :value="__('家族電話番号2')" />
                                            <x-text-input id="family_phone_number_2" class="block mt-1 w-full" type="tel" name="family_phone_number_2" :value="old('family_phone_number_2')" />
                                            <x-input-error :messages="$errors->get('family_phone_number_2')" class="mt-2" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- 健康管理情報 -->
                        <div class="mb-8 mt-6 pt-6 border-t border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">健康管理情報</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <x-input-label for="last_health_checkup_date" :value="__('最近の健康診断日')" />
                                    <x-text-input id="last_health_checkup_date" class="block mt-1 w-full" type="date" name="last_health_checkup_date" :value="old('last_health_checkup_date')" />
                                    <x-input-error :messages="$errors->get('last_health_checkup_date')" class="mt-2" />
                                </div>
                                
                                <div>
                                    <x-input-label for="blood_pressure" :value="__('血圧')" />
                                    <x-text-input id="blood_pressure" class="block mt-1 w-full" type="text" name="blood_pressure" :value="old('blood_pressure')" placeholder="例: 120/80" />
                                    <x-input-error :messages="$errors->get('blood_pressure')" class="mt-2" />
                                </div>
                                
                                <div>
                                    <x-input-label for="blood_type" :value="__('血液型')" />
                                    <select id="blood_type" name="blood_type" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                        <option value="">選択してください</option>
                                        <option value="A" {{ old('blood_type') == 'A' ? 'selected' : '' }}>A</option>
                                        <option value="B" {{ old('blood_type') == 'B' ? 'selected' : '' }}>B</option>
                                        <option value="AB" {{ old('blood_type') == 'AB' ? 'selected' : '' }}>AB</option>
                                        <option value="O" {{ old('blood_type') == 'O' ? 'selected' : '' }}>O</option>
                                    </select>
                                    <x-input-error :messages="$errors->get('blood_type')" class="mt-2" />
                                </div>
                                
                                <div>
                                    <x-input-label for="special_health_checkup_date" :value="__('特殊健康診断日')" />
                                    <x-text-input id="special_health_checkup_date" class="block mt-1 w-full" type="date" name="special_health_checkup_date" :value="old('special_health_checkup_date')" />
                                    <x-input-error :messages="$errors->get('special_health_checkup_date')" class="mt-2" />
                                </div>
                                
                                <div class="md:col-span-2">
                                    <x-input-label for="special_health_checkup_type" :value="__('特殊健康診断種類')" />
                                    <x-text-input id="special_health_checkup_type" class="block mt-1 w-full" type="text" name="special_health_checkup_type" :value="old('special_health_checkup_type')" />
                                    <x-input-error :messages="$errors->get('special_health_checkup_type')" class="mt-2" />
                                </div>
                            </div>
                        </div>
                        
                        <!-- 保険・年金情報 -->
                        <div class="mb-8">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">保険・年金情報</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <x-input-label for="kyokai_kenpo_number" :value="__('協会けんぽ番号')" />
                                    <x-text-input id="kyokai_kenpo_number" class="block mt-1 w-full" type="text" name="kyokai_kenpo_number" :value="old('kyokai_kenpo_number')" />
                                    <x-input-error :messages="$errors->get('kyokai_kenpo_number')" class="mt-2" />
                                </div>
                                
                                <div>
                                    <x-input-label for="employees_pension_number" :value="__('厚生年金番号')" />
                                    <x-text-input id="employees_pension_number" class="block mt-1 w-full" type="text" name="employees_pension_number" :value="old('employees_pension_number')" />
                                    <x-input-error :messages="$errors->get('employees_pension_number')" class="mt-2" />
                                </div>
                                
                                <div>
                                    <x-input-label for="employment_insurance_number" :value="__('雇用保険番号')" />
                                    <x-text-input id="employment_insurance_number" class="block mt-1 w-full" type="text" name="employment_insurance_number" :value="old('employment_insurance_number')" />
                                    <x-input-error :messages="$errors->get('employment_insurance_number')" class="mt-2" />
                                </div>
                            </div>
                        </div>
                        
                        <!-- 教育・資格情報 -->
                        <div class="mb-8">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">教育・資格情報</h3>
                            <div class="grid grid-cols-1 gap-4">
                                <div>
                                    <x-input-label for="hire_foreman_special_education" :value="__('雇入･職長特別教育')" />
                                    <textarea id="hire_foreman_special_education" name="hire_foreman_special_education" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" rows="3">{{ old('hire_foreman_special_education') }}</textarea>
                                    <x-input-error :messages="$errors->get('hire_foreman_special_education')" class="mt-2" />
                                </div>
                                
                                <div>
                                    <x-input-label for="skill_training" :value="__('技能講習')" />
                                    <textarea id="skill_training" name="skill_training" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" rows="3">{{ old('skill_training') }}</textarea>
                                    <x-input-error :messages="$errors->get('skill_training')" class="mt-2" />
                                </div>
                                
                                <div>
                                    <x-input-label for="licenses" :value="__('免許')" />
                                    <textarea id="licenses" name="licenses" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" rows="3">{{ old('licenses') }}</textarea>
                                    <x-input-error :messages="$errors->get('licenses')" class="mt-2" />
                                </div>
                                
                                <div>
                                    <x-input-label for="orientation_education_date" :value="__('受入教育実施年月日')" />
                                    <x-text-input id="orientation_education_date" class="block mt-1 w-full" type="date" name="orientation_education_date" :value="old('orientation_education_date')" />
                                    <x-input-error :messages="$errors->get('orientation_education_date')" class="mt-2" />
                                </div>
                                
                                <div>
                                    <label for="kentaikyo_handbook_owned" class="inline-flex items-center">
                                        <input id="kentaikyo_handbook_owned" type="checkbox" name="kentaikyo_handbook_owned" value="1" {{ old('kentaikyo_handbook_owned') ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                        <span class="ml-2 text-sm text-gray-600">建退共手帳を所有している</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        
                        <!-- 運転免許証情報 -->
                        <div class="mb-8">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">運転免許証情報</h3>
                            <div class="grid grid-cols-1 gap-4">
                                <div>
                                    <x-input-label for="driving_license_expiry" :value="__('運転免許証有効期限')" />
                                    <x-text-input id="driving_license_expiry" class="block mt-1 w-full" type="date" name="driving_license_expiry" :value="old('driving_license_expiry')" />
                                    <x-input-error :messages="$errors->get('driving_license_expiry')" class="mt-2" />
                                </div>
                                
                            </div>
                        </div>
                        
                                                <!-- 退職情報 -->
                        <div class="mb-8">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">退職情報</h3>
                            <div class="grid grid-cols-1 gap-4">
                                <div>
                                    <x-input-label for="retirement_date" :value="__('退職日')" />
                                    <x-text-input id="retirement_date" class="block mt-1 w-full" type="date" name="retirement_date" :value="old('retirement_date')" />
                                    <x-input-error :messages="$errors->get('retirement_date')" class="mt-2" />
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('employees.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded mr-2">
                                キャンセル
                            </a>
                            <x-primary-button>
                                {{ __('登録') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const nationalityInput = document.getElementById('nationality');
            const residenceStatusField = document.getElementById('residence_status_field');
            const residenceCardExpiryField = document.getElementById('residence_card_expiry_field');
            const residenceStatusInput = document.getElementById('residence_status');
            const residenceCardExpiryInput = document.getElementById('residence_card_expiry');

            function toggleResidenceFields() {
                const isJapanese = nationalityInput.value === '日本';
                
                if (isJapanese) {
                    residenceStatusField.style.display = 'none';
                    residenceCardExpiryField.style.display = 'none';
                    residenceStatusInput.removeAttribute('required');
                    residenceCardExpiryInput.removeAttribute('required');
                } else {
                    residenceStatusField.style.display = 'block';
                    residenceCardExpiryField.style.display = 'block';
                    residenceStatusInput.setAttribute('required', 'required');
                    residenceCardExpiryInput.setAttribute('required', 'required');
                }
            }

            // 初期状態の設定
            toggleResidenceFields();

            // 国籍変更時の処理
            nationalityInput.addEventListener('input', toggleResidenceFields);
        });
    </script>
</x-app-layout>