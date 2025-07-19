<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
    public function index()
    {
        // 管理者のみアクセス可能
        if (!auth()->user()->is_admin) {
            abort(403, 'この操作は管理者権限が必要です。');
        }
        
        $users = User::paginate(20);
        
        return view('users.index', compact('users'));
    }

    public function create()
    {
        // 管理者のみアクセス可能
        if (!auth()->user()->is_admin) {
            abort(403, 'この操作は管理者権限が必要です。');
        }
        
        return view('users.create');
    }

    public function store(Request $request)
    {
        // 管理者のみアクセス可能
        if (!auth()->user()->is_admin) {
            abort(403, 'この操作は管理者権限が必要です。');
        }
        
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'is_admin' => ['boolean'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'is_admin' => $request->boolean('is_admin'),
        ]);

        return redirect()->route('users.index')
                        ->with('success', 'ユーザーを作成しました。');
    }

    public function show(User $user)
    {
        // 管理者のみアクセス可能
        if (!auth()->user()->is_admin) {
            abort(403, 'この操作は管理者権限が必要です。');
        }
        
        return view('users.show', compact('user'));
    }

    public function edit(User $user)
    {
        // 管理者のみアクセス可能
        if (!auth()->user()->is_admin) {
            abort(403, 'この操作は管理者権限が必要です。');
        }
        
        return view('users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        // 管理者のみアクセス可能
        if (!auth()->user()->is_admin) {
            abort(403, 'この操作は管理者権限が必要です。');
        }
        
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'is_admin' => ['boolean'],
        ];
        
        // パスワードが入力されている場合のみバリデーション
        if ($request->filled('password')) {
            $rules['password'] = ['confirmed', Rules\Password::defaults()];
        }
        
        $request->validate($rules);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'is_admin' => $request->boolean('is_admin'),
        ]);
        
        // パスワードが入力されている場合のみ更新
        if ($request->filled('password')) {
            $user->update([
                'password' => Hash::make($request->password),
            ]);
        }

        return redirect()->route('users.show', $user)
                        ->with('success', 'ユーザー情報を更新しました。');
    }

    public function destroy(User $user)
    {
        // 管理者のみアクセス可能
        if (!auth()->user()->is_admin) {
            abort(403, 'この操作は管理者権限が必要です。');
        }
        
        // 自分自身は削除できない
        if ($user->id === auth()->id()) {
            return redirect()->route('users.index')
                            ->with('error', '自分自身は削除できません。');
        }
        
        $user->delete();
        
        return redirect()->route('users.index')
                        ->with('success', 'ユーザーを削除しました。');
    }
}
