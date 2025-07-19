<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('ユーザー編集') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('users.update', $user) }}">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">ユーザー基本情報</h3>
                            
                            <div class="grid grid-cols-1 gap-4">
                                <div>
                                    <x-input-label for="name" :value="__('名前 *')" />
                                    <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name', $user->name)" required autofocus />
                                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                                </div>
                                
                                <div>
                                    <x-input-label for="email" :value="__('メールアドレス *')" />
                                    <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email', $user->email)" required />
                                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                                </div>
                                
                                <div>
                                    <x-input-label for="password" :value="__('パスワード')" />
                                    <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" />
                                    <p class="mt-1 text-sm text-gray-500">パスワードを変更する場合のみ入力してください。</p>
                                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                                </div>
                                
                                <div>
                                    <x-input-label for="password_confirmation" :value="__('パスワード確認')" />
                                    <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" />
                                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">権限設定</h3>
                            
                            <div class="space-y-2">
                                <label for="is_admin" class="inline-flex items-center">
                                    <input id="is_admin" type="checkbox" name="is_admin" value="1" {{ old('is_admin', $user->is_admin) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                    <span class="ml-2 text-sm text-gray-600">管理者権限を付与する</span>
                                </label>
                                <p class="text-sm text-gray-500 ml-6">管理者権限を付与すると、従業員情報の登録・編集・削除、ユーザー管理が可能になります。</p>
                                
                                @if($user->id === auth()->id())
                                    <div class="mt-2 p-3 bg-yellow-50 border border-yellow-200 rounded">
                                        <p class="text-sm text-yellow-700">
                                            ⚠️ 注意: 自分自身の管理者権限を削除すると、ユーザー管理や従業員管理ができなくなります。
                                        </p>
                                    </div>
                                @endif
                            </div>
                        </div>
                        
                        <div class="mb-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">システム情報</h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="bg-gray-50 p-4 rounded">
                                    <label class="block text-sm font-medium text-gray-700">ユーザーID</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $user->id }}</p>
                                </div>
                                
                                <div class="bg-gray-50 p-4 rounded">
                                    <label class="block text-sm font-medium text-gray-700">登録日</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $user->created_at->format('Y年m月d日 H:i') }}</p>
                                </div>
                                
                                <div class="bg-gray-50 p-4 rounded">
                                    <label class="block text-sm font-medium text-gray-700">最終更新日</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $user->updated_at->format('Y年m月d日 H:i') }}</p>
                                </div>
                                
                                <div class="bg-gray-50 p-4 rounded">
                                    <label class="block text-sm font-medium text-gray-700">メール認証状態</label>
                                    <p class="mt-1 text-sm text-gray-900">
                                        @if($user->email_verified_at)
                                            <span class="text-green-600">認証済み ({{ $user->email_verified_at->format('Y/m/d') }})</span>
                                        @else
                                            <span class="text-yellow-600">未認証</span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('users.show', $user) }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded mr-2">
                                キャンセル
                            </a>
                            <x-primary-button>
                                {{ __('ユーザー情報更新') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>