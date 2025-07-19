<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('従業員一覧') }}
            </h2>
            <div class="flex space-x-2">
                <!-- CSVエクスポート -->
                <a href="{{ route('employees.export') }}" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                    CSVエクスポート
                </a>
                @if(auth()->user()->is_admin)
                    <!-- CSVインポート -->
                    <a href="{{ route('employees.import.form') }}" class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded">
                        CSVインポート
                    </a>
                    <!-- 新規登録 -->
                    <a href="{{ route('employees.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        新規登録
                    </a>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- 検索・フィルタ -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <form method="GET" action="{{ route('employees.index') }}">
                        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-4">
                            <div>
                                <x-input-label for="search" :value="__('検索')" />
                                <x-text-input id="search" class="block mt-1 w-full" type="text" name="search" :value="request('search')" placeholder="氏名・ふりがな" />
                            </div>
                            
                            <div>
                                <x-input-label for="job_category" :value="__('職種')" />
                                <select id="job_category" name="job_category" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="">全て</option>
                                    <option value="現場作業員" {{ request('job_category') == '現場作業員' ? 'selected' : '' }}>現場作業員</option>
                                    <option value="職長" {{ request('job_category') == '職長' ? 'selected' : '' }}>職長</option>
                                    <option value="技術者" {{ request('job_category') == '技術者' ? 'selected' : '' }}>技術者</option>
                                    <option value="管理者" {{ request('job_category') == '管理者' ? 'selected' : '' }}>管理者</option>
                                </select>
                            </div>
                            
                            <div>
                                <x-input-label for="nationality" :value="__('国籍')" />
                                <select id="nationality" name="nationality" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="">全て</option>
                                    <option value="日本" {{ request('nationality') == '日本' ? 'selected' : '' }}>日本</option>
                                    <option value="中国" {{ request('nationality') == '中国' ? 'selected' : '' }}>中国</option>
                                    <option value="ベトナム" {{ request('nationality') == 'ベトナム' ? 'selected' : '' }}>ベトナム</option>
                                    <option value="フィリピン" {{ request('nationality') == 'フィリピン' ? 'selected' : '' }}>フィリピン</option>
                                    <option value="その他" {{ request('nationality') == 'その他' ? 'selected' : '' }}>その他</option>
                                </select>
                            </div>
                            
                            <div>
                                <x-input-label for="company_id" :value="__('所属会社')" />
                                <select id="company_id" name="company_id" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="">全て</option>
                                    @foreach($companies as $company)
                                        <option value="{{ $company->id }}" {{ request('company_id') == $company->id ? 'selected' : '' }}>{{ $company->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div>
                                <div class="flex items-center">
                                    <input id="include_retired" type="checkbox" name="include_retired" value="1" {{ request('include_retired') ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                    <label for="include_retired" class="ml-2 text-sm text-gray-600">退職者を含む</label>
                                </div>
                            </div>
                            
                            <div class="flex items-end">
                                <x-primary-button class="mr-2">
                                    {{ __('検索') }}
                                </x-primary-button>
                                <a href="{{ route('employees.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                    リセット
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- 従業員一覧 -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if(session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                            {{ session('success') }}
                        </div>
                    @endif
                    
                    @if($employees->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full table-auto">
                                <thead>
                                    <tr class="bg-gray-50">
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            氏名
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            職種
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            部署
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            役職
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            所属会社
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            国籍
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            雇入年月日
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            電話番号
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            在留期限
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            免許証期限
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            退職日
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            操作
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($employees as $employee)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                @if($employee->profile_photo_url)
                                                    <div class="flex-shrink-0 h-10 w-10">
                                                        <img class="h-10 w-10 rounded-full" src="{{ $employee->profile_photo_url }}" alt="{{ $employee->full_name }}">
                                                    </div>
                                                @endif
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900">
                                                        {{ $employee->full_name }}
                                                    </div>
                                                    <div class="text-sm text-gray-500">
                                                        {{ $employee->furigana }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $employee->job_category }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $employee->department ?: '-' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $employee->position ?: '-' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $employee->company ? $employee->company->name : '-' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $employee->nationality }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $employee->hire_date->format('Y/m/d') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $employee->phone_number }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            @if($employee->residence_card_expiry)
                                                <span class="
                                                    @if($employee->residence_card_expiry_status == 'expired') text-red-600 font-bold
                                                    @elseif($employee->residence_card_expiry_status == 'expiring_soon') text-orange-600 font-bold
                                                    @elseif($employee->residence_card_expiry_status == 'expiring_within_3months') text-yellow-600
                                                    @else text-green-600
                                                    @endif
                                                ">
                                                    {{ $employee->residence_card_expiry->format('Y/m/d') }}
                                                </span>
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            @if($employee->driving_license_expiry)
                                                <span class="
                                                    @if($employee->driving_license_expiry_status == 'expired') text-red-600 font-bold
                                                    @elseif($employee->driving_license_expiry_status == 'expiring_soon') text-orange-600 font-bold
                                                    @elseif($employee->driving_license_expiry_status == 'expiring_within_3months') text-yellow-600
                                                    @else text-green-600
                                                    @endif
                                                ">
                                                    {{ $employee->driving_license_expiry->format('Y/m/d') }}
                                                </span>
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            @if($employee->retirement_date)
                                                <span class="text-red-600 font-bold">
                                                    {{ $employee->retirement_date->format('Y/m/d') }}
                                                </span>
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <a href="{{ route('employees.show', $employee) }}" class="text-indigo-600 hover:text-indigo-900 mr-2">
                                                詳細
                                            </a>
                                            @if(auth()->user()->is_admin)
                                                <a href="{{ route('employees.edit', $employee) }}" class="text-blue-600 hover:text-blue-900 mr-2">
                                                    編集
                                                </a>
                                                <form method="POST" action="{{ route('employees.destroy', $employee) }}" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('削除してもよろしいですか？')">
                                                        削除
                                                    </button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- ページネーション -->
                        <div class="mt-4">
                            {{ $employees->appends(request()->query())->links() }}
                        </div>
                    @else
                        <div class="text-center py-8">
                            <p class="text-gray-500">従業員が見つかりませんでした。</p>
                            @if(auth()->user()->is_admin)
                                <a href="{{ route('employees.create') }}" class="text-blue-600 hover:text-blue-900">
                                    新規登録
                                </a>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>