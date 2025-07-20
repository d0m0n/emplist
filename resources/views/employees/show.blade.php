<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('従業員詳細') }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('employees.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    一覧に戻る
                </a>
                @if(auth()->user()->is_admin)
                    <a href="{{ route('employees.edit', $employee) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        編集
                    </a>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif
            
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <!-- 基本情報 -->
                    <div class="mb-8">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">基本情報</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @if($employee->profile_photo_url)
                                <div class="md:col-span-2 mb-4">
                                    <img src="{{ $employee->profile_photo_url }}" alt="{{ $employee->full_name }}" class="w-32 h-32 rounded-full object-cover">
                                </div>
                            @endif
                            
                            <div class="bg-gray-50 p-4 rounded">
                                <label class="block text-sm font-medium text-gray-700">氏名</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $employee->full_name }}</p>
                            </div>
                            
                            <div class="bg-gray-50 p-4 rounded">
                                <label class="block text-sm font-medium text-gray-700">ふりがな</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $employee->furigana }}</p>
                            </div>
                            
                            <div class="bg-gray-50 p-4 rounded">
                                <label class="block text-sm font-medium text-gray-700">職種</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $employee->job_category }}</p>
                            </div>
                            
                            @if($employee->department)
                                <div class="bg-gray-50 p-4 rounded">
                                    <label class="block text-sm font-medium text-gray-700">部署名</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $employee->department }}</p>
                                </div>
                            @endif
                            
                            @if($employee->position)
                                <div class="bg-gray-50 p-4 rounded">
                                    <label class="block text-sm font-medium text-gray-700">役職</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $employee->position }}</p>
                                </div>
                            @endif
                            
                            @if($employee->company)
                                <div class="bg-gray-50 p-4 rounded">
                                    <label class="block text-sm font-medium text-gray-700">所属会社</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $employee->company->name }}</p>
                                </div>
                            @endif
                            
                            <div class="bg-gray-50 p-4 rounded">
                                <label class="block text-sm font-medium text-gray-700">雇入年月日</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $employee->hire_date->format('Y年m月d日') }}</p>
                            </div>
                            
                            <div class="bg-gray-50 p-4 rounded">
                                <label class="block text-sm font-medium text-gray-700">生年月日</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $employee->birth_date->format('Y年m月d日') }} ({{ $employee->age }}歳)</p>
                            </div>
                            
                            <div class="bg-gray-50 p-4 rounded">
                                <label class="block text-sm font-medium text-gray-700">勤続年数</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $employee->years_of_service }}年</p>
                            </div>
                            
                            <div class="bg-gray-50 p-4 rounded">
                                <label class="block text-sm font-medium text-gray-700">国籍</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $employee->nationality }}</p>
                            </div>
                            
                            @if($employee->gender)
                                <div class="bg-gray-50 p-4 rounded">
                                    <label class="block text-sm font-medium text-gray-700">性別</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $employee->gender }}</p>
                                </div>
                            @endif
                            
                            <div class="bg-gray-50 p-4 rounded">
                                <label class="block text-sm font-medium text-gray-700">配偶者の有無</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $employee->has_spouse ? 'あり' : 'なし' }}</p>
                            </div>
                            
                            @if($employee->residence_status)
                                <div class="bg-gray-50 p-4 rounded">
                                    <label class="block text-sm font-medium text-gray-700">在留資格</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $employee->residence_status }}</p>
                                </div>
                            @endif
                            
                            @if($employee->residence_card_expiry)
                                <div class="bg-gray-50 p-4 rounded">
                                    <label class="block text-sm font-medium text-gray-700">在留カード有効期限</label>
                                    <p class="mt-1 text-sm 
                                        @if($employee->residence_card_expiry_status == 'expired') text-red-600 font-bold
                                        @elseif($employee->residence_card_expiry_status == 'expiring_soon') text-orange-600 font-bold
                                        @elseif($employee->residence_card_expiry_status == 'expiring_within_3months') text-yellow-600
                                        @else text-green-600
                                        @endif
                                    ">
                                        {{ $employee->residence_card_expiry->format('Y年m月d日') }}
                                    </p>
                                </div>
                            @endif
                        </div>
                    </div>
                    
                    <!-- 連絡先情報 -->
                    <div class="mb-8">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">連絡先情報</h3>
                        <div class="grid grid-cols-1 gap-4">
                            @if($employee->postal_code)
                                <div class="bg-gray-50 p-4 rounded">
                                    <label class="block text-sm font-medium text-gray-700">郵便番号</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $employee->postal_code }}</p>
                                </div>
                            @endif
                            
                            <div class="bg-gray-50 p-4 rounded">
                                <label class="block text-sm font-medium text-gray-700">現住所</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $employee->current_address }}</p>
                            </div>
                            
                            @if($employee->family_address)
                                <div class="bg-gray-50 p-4 rounded">
                                    <label class="block text-sm font-medium text-gray-700">家族住所</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $employee->family_address }}</p>
                                </div>
                            @endif
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="bg-gray-50 p-4 rounded">
                                    <label class="block text-sm font-medium text-gray-700">電話番号</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $employee->phone_number }}</p>
                                </div>
                                
                                @if($employee->family_phone_number)
                                    <div class="bg-gray-50 p-4 rounded">
                                        <label class="block text-sm font-medium text-gray-700">家族電話番号</label>
                                        <p class="mt-1 text-sm text-gray-900">{{ $employee->family_phone_number }}</p>
                                    </div>
                                @endif
                            </div>
                            
                            @if($employee->family_name)
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div class="bg-gray-50 p-4 rounded">
                                        <label class="block text-sm font-medium text-gray-700">家族名</label>
                                        <p class="mt-1 text-sm text-gray-900">{{ $employee->family_name }}</p>
                                    </div>
                                    
                                    @if($employee->family_name_furigana)
                                        <div class="bg-gray-50 p-4 rounded">
                                            <label class="block text-sm font-medium text-gray-700">家族名ふりがな</label>
                                            <p class="mt-1 text-sm text-gray-900">{{ $employee->family_name_furigana }}</p>
                                        </div>
                                    @endif
                                    
                                    @if($employee->family_relationship)
                                        <div class="bg-gray-50 p-4 rounded">
                                            <label class="block text-sm font-medium text-gray-700">続柄</label>
                                            <p class="mt-1 text-sm text-gray-900">{{ $employee->family_relationship }}</p>
                                        </div>
                                    @endif
                                </div>
                            @endif
                            
                            @if($employee->family_name_2)
                                <div class="mt-6 pt-6 border-t border-gray-200">
                                    <h4 class="text-md font-medium text-gray-700 mb-4">家族情報（2人目）</h4>
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                                        <div class="bg-gray-50 p-4 rounded">
                                            <label class="block text-sm font-medium text-gray-700">家族名2</label>
                                            <p class="mt-1 text-sm text-gray-900">{{ $employee->family_name_2 }}</p>
                                        </div>
                                        
                                        @if($employee->family_name_furigana_2)
                                            <div class="bg-gray-50 p-4 rounded">
                                                <label class="block text-sm font-medium text-gray-700">家族名ふりがな2</label>
                                                <p class="mt-1 text-sm text-gray-900">{{ $employee->family_name_furigana_2 }}</p>
                                            </div>
                                        @endif
                                        
                                        @if($employee->family_relationship_2)
                                            <div class="bg-gray-50 p-4 rounded">
                                                <label class="block text-sm font-medium text-gray-700">続柄2</label>
                                                <p class="mt-1 text-sm text-gray-900">{{ $employee->family_relationship_2 }}</p>
                                            </div>
                                        @endif
                                    </div>
                                    
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        @if($employee->family_address_2)
                                            <div class="bg-gray-50 p-4 rounded">
                                                <label class="block text-sm font-medium text-gray-700">家族住所2</label>
                                                <p class="mt-1 text-sm text-gray-900">{{ $employee->family_address_2 }}</p>
                                            </div>
                                        @endif
                                        
                                        @if($employee->family_phone_number_2)
                                            <div class="bg-gray-50 p-4 rounded">
                                                <label class="block text-sm font-medium text-gray-700">家族電話番号2</label>
                                                <p class="mt-1 text-sm text-gray-900">{{ $employee->family_phone_number_2 }}</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                    
                    <!-- 健康管理情報 -->
                    <div class="mb-8">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">健康管理情報</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @if($employee->last_health_checkup_date)
                                <div class="bg-gray-50 p-4 rounded">
                                    <label class="block text-sm font-medium text-gray-700">最近の健康診断日</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $employee->last_health_checkup_date->format('Y年m月d日') }}</p>
                                </div>
                            @endif
                            
                            @if($employee->blood_pressure)
                                <div class="bg-gray-50 p-4 rounded">
                                    <label class="block text-sm font-medium text-gray-700">血圧</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $employee->blood_pressure }}</p>
                                </div>
                            @endif
                            
                            @if($employee->blood_type)
                                <div class="bg-gray-50 p-4 rounded">
                                    <label class="block text-sm font-medium text-gray-700">血液型</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $employee->blood_type }}型</p>
                                </div>
                            @endif
                            
                            @if($employee->special_health_checkup_date)
                                <div class="bg-gray-50 p-4 rounded">
                                    <label class="block text-sm font-medium text-gray-700">特殊健康診断日</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $employee->special_health_checkup_date->format('Y年m月d日') }}</p>
                                </div>
                            @endif
                            
                            @if($employee->special_health_checkup_type)
                                <div class="bg-gray-50 p-4 rounded">
                                    <label class="block text-sm font-medium text-gray-700">特殊健康診断種類</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $employee->special_health_checkup_type }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                    
                    <!-- 保険・年金情報 -->
                    <div class="mb-8">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">保険・年金情報</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @if($employee->kyokai_kenpo_number)
                                <div class="bg-gray-50 p-4 rounded">
                                    <label class="block text-sm font-medium text-gray-700">協会けんぽ番号</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $employee->kyokai_kenpo_number }}</p>
                                </div>
                            @endif
                            
                            @if($employee->employees_pension_number)
                                <div class="bg-gray-50 p-4 rounded">
                                    <label class="block text-sm font-medium text-gray-700">厚生年金番号</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $employee->employees_pension_number }}</p>
                                </div>
                            @endif
                            
                            @if($employee->employment_insurance_number)
                                <div class="bg-gray-50 p-4 rounded">
                                    <label class="block text-sm font-medium text-gray-700">雇用保険番号</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $employee->employment_insurance_number }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                    
                    <!-- 教育・資格情報 -->
                    <div class="mb-8">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">教育・資格情報</h3>
                        <div class="grid grid-cols-1 gap-4">
                            @if($employee->hire_foreman_special_education)
                                <div class="bg-gray-50 p-4 rounded">
                                    <label class="block text-sm font-medium text-gray-700">雇入･職長特別教育</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $employee->hire_foreman_special_education }}</p>
                                </div>
                            @endif
                            
                            @if($employee->skill_training)
                                <div class="bg-gray-50 p-4 rounded">
                                    <label class="block text-sm font-medium text-gray-700">技能講習</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $employee->skill_training }}</p>
                                </div>
                            @endif
                            
                            @if($employee->licenses)
                                <div class="bg-gray-50 p-4 rounded">
                                    <label class="block text-sm font-medium text-gray-700">免許</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $employee->licenses }}</p>
                                </div>
                            @endif
                            
                            @if($employee->orientation_education_date)
                                <div class="bg-gray-50 p-4 rounded">
                                    <label class="block text-sm font-medium text-gray-700">受入教育実施年月日</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $employee->orientation_education_date->format('Y年m月d日') }}</p>
                                </div>
                            @endif
                            
                            <div class="bg-gray-50 p-4 rounded">
                                <label class="block text-sm font-medium text-gray-700">建退共手帳所有</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $employee->kentaikyo_handbook_owned ? '所有' : '未所有' }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- 管理者用操作ボタン -->
                    @if(auth()->user()->is_admin)
                        <div class="mt-8 pt-6 border-t border-gray-200">
                            <div class="flex justify-center space-x-4">
                                <a href="{{ route('employees.edit', $employee) }}" 
                                   class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg transition duration-200">
                                    <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                    従業員情報を編集
                                </a>
                                
                                <form method="POST" action="{{ route('employees.destroy', $employee) }}" class="inline" 
                                      onsubmit="return confirm('{{ $employee->full_name }}の従業員情報を削除してもよろしいですか？\n\nこの操作は取り消すことができません。')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="bg-red-500 hover:bg-red-700 text-white font-bold py-3 px-6 rounded-lg transition duration-200">
                                        <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                        従業員情報を削除
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>