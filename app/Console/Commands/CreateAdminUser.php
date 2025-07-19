<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class CreateAdminUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:create';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '管理者ユーザーを作成します';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('管理者ユーザーを作成します。');
        
        // 名前の入力
        $name = $this->ask('名前を入力してください');
        
        // メールアドレスの入力（重複チェック付き）
        do {
            $email = $this->ask('メールアドレスを入力してください');
            
            $validator = Validator::make(['email' => $email], [
                'email' => 'required|email|unique:users,email'
            ]);
            
            if ($validator->fails()) {
                $this->error('無効なメールアドレスか、既に登録されているメールアドレスです。');
                continue;
            }
            break;
        } while (true);
        
        // パスワードの入力
        $password = $this->secret('パスワードを入力してください（8文字以上）');
        
        // パスワードのバリデーション
        $validator = Validator::make(['password' => $password], [
            'password' => 'required|min:8'
        ]);
        
        if ($validator->fails()) {
            $this->error('パスワードは8文字以上で入力してください。');
            return 1;
        }
        
        // パスワード確認
        $passwordConfirmation = $this->secret('パスワードをもう一度入力してください');
        
        if ($password !== $passwordConfirmation) {
            $this->error('パスワードが一致しません。');
            return 1;
        }
        
        // 確認
        $this->info('以下の情報で管理者ユーザーを作成します：');
        $this->table(['項目', '値'], [
            ['名前', $name],
            ['メールアドレス', $email],
            ['権限', '管理者']
        ]);
        
        if (!$this->confirm('この内容で作成しますか？')) {
            $this->info('作成をキャンセルしました。');
            return 0;
        }
        
        // ユーザー作成
        try {
            $user = User::create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make($password),
                'is_admin' => true,
                'email_verified_at' => now(), // 管理者は自動的にメール認証済みにする
            ]);
            
            $this->info('✅ 管理者ユーザーが正常に作成されました！');
            $this->info("ユーザーID: {$user->id}");
            $this->info("名前: {$user->name}");
            $this->info("メールアドレス: {$user->email}");
            $this->info("権限: 管理者");
            
        } catch (\Exception $e) {
            $this->error('❌ ユーザー作成中にエラーが発生しました：' . $e->getMessage());
            return 1;
        }
        
        return 0;
    }
}
