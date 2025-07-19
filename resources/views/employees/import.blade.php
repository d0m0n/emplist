<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('従業員CSVインポート') }}
            </h2>
            <a href="{{ route('employees.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                一覧に戻る
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if(session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                            {{ session('success') }}
                        </div>
                    @endif
                    
                    @if(session('error'))
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                            {{ session('error') }}
                        </div>
                    @endif

                    @if(session('failures'))
                        <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded mb-4">
                            <p class="font-bold">インポート失敗詳細:</p>
                            <ul class="list-disc pl-5">
                                @foreach(session('failures') as $failure)
                                    <li>行 {{ $failure->row() }}: {{ $failure->errors()[0] }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- 使用方法の説明 -->
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                        <h3 class="text-lg font-semibold text-blue-800 mb-2">CSVインポートの使用方法</h3>
                        <ol class="list-decimal list-inside text-blue-700 space-y-1">
                            <li>下記のテンプレートファイルをダウンロードしてください</li>
                            <li>テンプレートファイルに従業員データを入力してください</li>
                            <li>入力したCSVファイルを選択してアップロードしてください</li>
                        </ol>
                        <div class="mt-4">
                            <a href="{{ route('employees.template.download') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                テンプレートダウンロード
                            </a>
                        </div>
                    </div>

                    <!-- CSV入力フォーマット -->
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 mb-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-2">必須項目</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                            <div>
                                <ul class="list-disc pl-5 space-y-1">
                                    <li>氏名</li>
                                    <li>ふりがな</li>
                                    <li>職種</li>
                                    <li>雇入年月日 (YYYY-MM-DD形式)</li>
                                </ul>
                            </div>
                            <div>
                                <ul class="list-disc pl-5 space-y-1">
                                    <li>生年月日 (YYYY-MM-DD形式)</li>
                                    <li>国籍</li>
                                    <li>現住所</li>
                                    <li>電話番号</li>
                                </ul>
                            </div>
                        </div>
                        <div class="mt-4">
                            <h4 class="font-semibold text-gray-800 mb-2">注意事項</h4>
                            <ul class="list-disc pl-5 space-y-1 text-sm text-gray-600">
                                <li>日付は YYYY-MM-DD 形式で入力してください（例：2023-01-15）</li>
                                <li>血液型は A、B、AB、O のいずれかを入力してください</li>
                                <li>建退共手帳所有の有無は「はい」または「いいえ」で入力してください</li>
                                <li>外国籍の場合は在留資格と在留カード有効期限の入力を推奨します</li>
                            </ul>
                        </div>
                    </div>

                    <!-- ファイルアップロードフォーム -->
                    <form method="POST" action="{{ route('employees.import') }}" enctype="multipart/form-data" class="space-y-6">
                        @csrf
                        
                        <div>
                            <x-input-label for="csv_file" :value="__('CSVファイル')" />
                            <input id="csv_file" name="csv_file" type="file" accept=".csv,.txt" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                            <x-input-error :messages="$errors->get('csv_file')" class="mt-2" />
                            <p class="mt-1 text-sm text-gray-500">
                                CSVファイル（.csv または .txt）を選択してください。最大ファイルサイズ: 2MB
                            </p>
                        </div>

                        <div class="flex items-center justify-end">
                            <x-primary-button class="ml-3">
                                {{ __('CSVインポート実行') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>