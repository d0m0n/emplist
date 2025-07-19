<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('会社詳細') }} - {{ $company->name }}
            </h2>
            <div class="flex space-x-2">
                @if(auth()->user()->is_admin)
                    <a href="{{ route('companies.edit', $company) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        編集
                    </a>
                @endif
                <a href="{{ route('companies.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    一覧に戻る
                </a>
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
            
            <!-- 会社基本情報 -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">基本情報</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700">会社名</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $company->name }}</p>
                            </div>
                            
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700">代表者名</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $company->representative_name ?? '-' }}</p>
                            </div>
                            
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700">住所</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $company->address ?? '-' }}</p>
                            </div>
                            
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700">電話番号</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $company->phone_number ?? '-' }}</p>
                            </div>
                            
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700">FAX番号</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $company->fax_number ?? '-' }}</p>
                            </div>
                        </div>
                        
                        <div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700">ロゴ画像</label>
                                <div class="mt-1">
                                    @if($company->logo_image_url)
                                        <img src="{{ $company->logo_image_url }}" alt="{{ $company->name }}" class="w-32 h-32 object-cover rounded-lg shadow">
                                    @else
                                        <div class="w-32 h-32 bg-gray-200 rounded-lg flex items-center justify-center">
                                            <span class="text-gray-500">ロゴなし</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700">Eメールアドレス</label>
                                <p class="mt-1 text-sm text-gray-900">
                                    @if($company->email)
                                        <a href="mailto:{{ $company->email }}" class="text-blue-600 hover:underline">{{ $company->email }}</a>
                                    @else
                                        -
                                    @endif
                                </p>
                            </div>
                            
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700">ホームページ</label>
                                <p class="mt-1 text-sm text-gray-900">
                                    @if($company->website)
                                        <a href="{{ $company->website }}" target="_blank" class="text-blue-600 hover:underline">{{ $company->website }}</a>
                                    @else
                                        -
                                    @endif
                                </p>
                            </div>
                            
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700">登録日</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $company->created_at->format('Y年m月d日') }}</p>
                            </div>
                            
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700">更新日</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $company->updated_at->format('Y年m月d日') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- 所属従業員一覧 -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">所属従業員一覧 ({{ $company->employees->count() }}人)</h3>
                    
                    @if($company->employees->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full table-auto">
                                <thead>
                                    <tr class="bg-gray-50">
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            氏名
                                        </th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            職種
                                        </th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            雇入年月日
                                        </th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            電話番号
                                        </th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            操作
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($company->employees as $employee)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-2 whitespace-nowrap">
                                            <div class="flex items-center">
                                                @if($employee->profile_photo_url)
                                                    <div class="flex-shrink-0 h-8 w-8">
                                                        <img class="h-8 w-8 rounded-full" src="{{ $employee->profile_photo_url }}" alt="{{ $employee->full_name }}">
                                                    </div>
                                                @endif
                                                <div class="ml-2">
                                                    <div class="text-sm font-medium text-gray-900">
                                                        {{ $employee->full_name }}
                                                    </div>
                                                    <div class="text-sm text-gray-500">
                                                        {{ $employee->furigana }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">
                                            {{ $employee->job_category }}
                                        </td>
                                        <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">
                                            {{ $employee->hire_date->format('Y/m/d') }}
                                        </td>
                                        <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">
                                            {{ $employee->phone_number }}
                                        </td>
                                        <td class="px-4 py-2 whitespace-nowrap text-sm font-medium">
                                            <a href="{{ route('employees.show', $employee) }}" class="text-indigo-600 hover:text-indigo-900">
                                                詳細
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-8">
                            <p class="text-gray-500">所属従業員はいません。</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>