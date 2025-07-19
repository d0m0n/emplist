<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('ユーザー詳細') }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('users.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    一覧に戻る
                </a>
                <a href="{{ route('users.edit', $user) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    編集
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
            
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="bg-gray-50 p-4 rounded">
                            <label class="block text-sm font-medium text-gray-700">ユーザーID</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $user->id }}</p>
                        </div>
                        
                        <div class="bg-gray-50 p-4 rounded">
                            <label class="block text-sm font-medium text-gray-700">名前</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $user->name }}</p>
                        </div>
                        
                        <div class="bg-gray-50 p-4 rounded">
                            <label class="block text-sm font-medium text-gray-700">メールアドレス</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $user->email }}</p>
                        </div>
                        
                        <div class="bg-gray-50 p-4 rounded">
                            <label class="block text-sm font-medium text-gray-700">権限</label>
                            <p class="mt-1 text-sm text-gray-900">
                                @if($user->is_admin)
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                        管理者
                                    </span>
                                @else
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                        一般ユーザー
                                    </span>
                                @endif
                            </p>
                        </div>
                        
                        <div class="bg-gray-50 p-4 rounded">
                            <label class="block text-sm font-medium text-gray-700">登録日</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $user->created_at->format('Y年m月d日 H:i') }}</p>
                        </div>
                        
                        <div class="bg-gray-50 p-4 rounded">
                            <label class="block text-sm font-medium text-gray-700">最終更新日</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $user->updated_at->format('Y年m月d日 H:i') }}</p>
                        </div>
                        
                        @if($user->email_verified_at)
                            <div class="bg-gray-50 p-4 rounded">
                                <label class="block text-sm font-medium text-gray-700">メール認証日</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $user->email_verified_at->format('Y年m月d日 H:i') }}</p>
                            </div>
                        @else
                            <div class="bg-yellow-50 p-4 rounded">
                                <label class="block text-sm font-medium text-yellow-700">メール認証</label>
                                <p class="mt-1 text-sm text-yellow-900">未認証</p>
                            </div>
                        @endif
                    </div>
                    
                    @if($user->is_admin)
                        <div class="mt-6 p-4 bg-red-50 rounded">
                            <h3 class="text-lg font-medium text-red-900 mb-2">管理者権限について</h3>
                            <p class="text-sm text-red-700">このユーザーは管理者権限を持っています。以下の操作が可能です：</p>
                            <ul class="mt-2 text-sm text-red-700 list-disc list-inside">
                                <li>従業員情報の登録・編集・削除</li>
                                <li>ユーザー管理（作成・編集・削除）</li>
                                <li>システム設定の変更</li>
                            </ul>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>