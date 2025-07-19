<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Company::withCount('employees');
        
        // 検索・フィルタリング
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        
        $companies = $query->active()->paginate(20);
        
        return view('companies.index', compact('companies'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // 管理者権限チェック
        if (!auth()->user()->is_admin) {
            abort(403, 'この操作は管理者権限が必要です。');
        }
        
        return view('companies.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // 管理者権限チェック
        if (!auth()->user()->is_admin) {
            abort(403, 'この操作は管理者権限が必要です。');
        }
        
        $validatedData = $request->validate([
            'name' => 'required|string|max:100',
            'representative_name' => 'nullable|string|max:100',
            'address' => 'nullable|string',
            'phone_number' => 'nullable|string|max:20',
            'fax_number' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100',
            'website' => 'nullable|url|max:200',
            'logo_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'boolean',
        ]);
        
        // ロゴ画像のアップロード処理
        if ($request->hasFile('logo_image')) {
            $validatedData['logo_image'] = $this->uploadLogoImage($request->file('logo_image'));
        }
        
        $company = Company::create($validatedData);
        
        return redirect()->route('companies.show', $company)
                        ->with('success', '会社情報を登録しました。');
    }

    /**
     * Display the specified resource.
     */
    public function show(Company $company)
    {
        $company->load('employees');
        return view('companies.show', compact('company'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Company $company)
    {
        // 管理者権限チェック
        if (!auth()->user()->is_admin) {
            abort(403, 'この操作は管理者権限が必要です。');
        }
        
        return view('companies.edit', compact('company'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Company $company)
    {
        // 管理者権限チェック
        if (!auth()->user()->is_admin) {
            abort(403, 'この操作は管理者権限が必要です。');
        }
        
        $validatedData = $request->validate([
            'name' => 'required|string|max:100',
            'representative_name' => 'nullable|string|max:100',
            'address' => 'nullable|string',
            'phone_number' => 'nullable|string|max:20',
            'fax_number' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100',
            'website' => 'nullable|url|max:200',
            'logo_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'boolean',
        ]);
        
        // ロゴ画像の更新処理
        if ($request->hasFile('logo_image')) {
            // 古いロゴ画像を削除
            if ($company->logo_image) {
                Storage::delete('public/' . $company->logo_image);
            }
            $validatedData['logo_image'] = $this->uploadLogoImage($request->file('logo_image'));
        }
        
        $company->update($validatedData);
        
        return redirect()->route('companies.show', $company)
                        ->with('success', '会社情報を更新しました。');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Company $company)
    {
        // 管理者権限チェック
        if (!auth()->user()->is_admin) {
            abort(403, 'この操作は管理者権限が必要です。');
        }
        
        // ロゴ画像の削除
        if ($company->logo_image) {
            Storage::delete('public/' . $company->logo_image);
        }
        
        $company->delete();
        
        return redirect()->route('companies.index')
                        ->with('success', '会社情報を削除しました。');
    }
    
    // プライベートメソッド
    private function uploadLogoImage($file)
    {
        $filename = 'logo_' . Str::uuid() . '.' . $file->getClientOriginalExtension();
        return $file->storeAs('company_logos', $filename, 'public');
    }
}
