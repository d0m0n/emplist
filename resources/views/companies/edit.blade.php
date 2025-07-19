<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('会社編集') }} - {{ $company->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('companies.update', $company) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <!-- 基本情報 -->
                        <div class="mb-8">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">基本情報</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <x-input-label for="name" :value="__('会社名 *')" />
                                    <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name', $company->name)" required autofocus />
                                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                                </div>
                                
                                <div>
                                    <x-input-label for="representative_name" :value="__('代表者名')" />
                                    <x-text-input id="representative_name" class="block mt-1 w-full" type="text" name="representative_name" :value="old('representative_name', $company->representative_name)" />
                                    <x-input-error :messages="$errors->get('representative_name')" class="mt-2" />
                                </div>
                                
                                <div class="md:col-span-2">
                                    <x-input-label for="address" :value="__('住所')" />
                                    <textarea id="address" name="address" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" rows="3">{{ old('address', $company->address) }}</textarea>
                                    <x-input-error :messages="$errors->get('address')" class="mt-2" />
                                </div>
                                
                                <div>
                                    <x-input-label for="phone_number" :value="__('電話番号')" />
                                    <x-text-input id="phone_number" class="block mt-1 w-full" type="tel" name="phone_number" :value="old('phone_number', $company->phone_number)" />
                                    <x-input-error :messages="$errors->get('phone_number')" class="mt-2" />
                                </div>
                                
                                <div>
                                    <x-input-label for="fax_number" :value="__('FAX番号')" />
                                    <x-text-input id="fax_number" class="block mt-1 w-full" type="tel" name="fax_number" :value="old('fax_number', $company->fax_number)" />
                                    <x-input-error :messages="$errors->get('fax_number')" class="mt-2" />
                                </div>
                                
                                <div>
                                    <x-input-label for="email" :value="__('Eメールアドレス')" />
                                    <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email', $company->email)" />
                                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                                </div>
                                
                                <div>
                                    <x-input-label for="website" :value="__('ホームページアドレス')" />
                                    <x-text-input id="website" class="block mt-1 w-full" type="url" name="website" :value="old('website', $company->website)" placeholder="https://example.com" />
                                    <x-input-error :messages="$errors->get('website')" class="mt-2" />
                                </div>
                                
                                <div>
                                    <x-input-label for="logo_image" :value="__('ロゴ画像')" />
                                    @if($company->logo_image_url)
                                        <div class="mt-2 mb-2">
                                            <img src="{{ $company->logo_image_url }}" alt="{{ $company->name }}" class="w-20 h-20 rounded object-cover">
                                            <p class="text-sm text-gray-600 mt-1">現在のロゴ</p>
                                        </div>
                                    @endif
                                    <input id="logo_image" class="block mt-1 w-full" type="file" name="logo_image" accept="image/*" />
                                    <x-input-error :messages="$errors->get('logo_image')" class="mt-2" />
                                    <p class="mt-1 text-sm text-gray-500">画像ファイル（JPEG、PNG、JPG、GIF）、最大2MB</p>
                                </div>
                                
                                <div>
                                    <label for="is_active" class="inline-flex items-center">
                                        <input id="is_active" type="checkbox" name="is_active" value="1" {{ old('is_active', $company->is_active) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                        <span class="ml-2 text-sm text-gray-600">有効</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('companies.show', $company) }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded mr-2">
                                キャンセル
                            </a>
                            <x-primary-button>
                                {{ __('更新') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>