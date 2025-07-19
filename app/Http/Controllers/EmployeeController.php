<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Company;
use App\Http\Requests\StoreEmployeeRequest;
use App\Exports\EmployeesExport;
use App\Imports\EmployeesImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $query = Employee::with(['documents', 'company']);
        
        // 検索・フィルタリング
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('full_name', 'like', '%' . $request->search . '%')
                  ->orWhere('furigana', 'like', '%' . $request->search . '%');
            });
        }
        
        if ($request->filled('job_category')) {
            $query->where('job_category', $request->job_category);
        }
        
        if ($request->filled('nationality')) {
            $query->where('nationality', $request->nationality);
        }
        
        // 所属会社フィルタ
        if ($request->filled('company_id')) {
            $query->where('company_id', $request->company_id);
        }
        
        // 在留カード期限切れ間近フィルター
        if ($request->filled('expiring_residence_cards')) {
            $query->expiringResidenceCards();
        }
        
        // 退職者を含むフィルター
        if ($request->filled('include_retired')) {
            // 退職者を含む場合は全従業員を取得
            $employees = $query->paginate(20);
        } else {
            // 通常は退職者を除外（退職日がnullの従業員のみ）
            $employees = $query->whereNull('retirement_date')->paginate(20);
        }
        
        // 会社一覧を取得
        $companies = Company::active()->orderBy('name')->get();
        
        return view('employees.index', compact('employees', 'companies'));
    }

    public function create()
    {
        // 管理者権限チェック
        if (!auth()->user()->is_admin) {
            abort(403, 'この操作は管理者権限が必要です。');
        }
        
        $companies = Company::active()->orderBy('name')->get();
        
        return view('employees.create', compact('companies'));
    }

    public function store(StoreEmployeeRequest $request)
    {
        $validatedData = $request->validated();
        
        // 顔写真のアップロード処理
        if ($request->hasFile('profile_photo')) {
            $validatedData['profile_photo'] = $this->uploadProfilePhoto($request->file('profile_photo'));
        }
        
        $employee = Employee::create($validatedData);
        
        return redirect()->route('employees.show', $employee)
                        ->with('success', '従業員情報を登録しました。');
    }

    public function show(Employee $employee)
    {
        return view('employees.show', compact('employee'));
    }

    public function edit(Employee $employee)
    {
        // 管理者権限チェック
        if (!auth()->user()->is_admin) {
            abort(403, 'この操作は管理者権限が必要です。');
        }
        
        $companies = Company::active()->orderBy('name')->get();
        
        return view('employees.edit', compact('employee', 'companies'));
    }

    public function update(StoreEmployeeRequest $request, Employee $employee)
    {
        $validatedData = $request->validated();
        
        // 顔写真の更新処理
        if ($request->hasFile('profile_photo')) {
            // 古い写真を削除
            if ($employee->profile_photo) {
                Storage::delete('public/' . $employee->profile_photo);
            }
            $validatedData['profile_photo'] = $this->uploadProfilePhoto($request->file('profile_photo'));
        }
        
        $employee->update($validatedData);
        
        return redirect()->route('employees.show', $employee)
                        ->with('success', '従業員情報を更新しました。');
    }

    public function destroy(Employee $employee)
    {
        // 管理者権限チェック
        if (!auth()->user()->is_admin) {
            abort(403, 'この操作は管理者権限が必要です。');
        }
        
        $employee->delete();
        
        return redirect()->route('employees.index')
                        ->with('success', '従業員情報を削除しました。');
    }

    /**
     * CSVエクスポート
     */
    public function export()
    {
        return Excel::download(new EmployeesExport, '従業員一覧_' . date('Y-m-d') . '.csv');
    }

    /**
     * CSVインポート画面
     */
    public function importForm()
    {
        // 管理者権限チェック
        if (!auth()->user()->is_admin) {
            abort(403, 'この操作は管理者権限が必要です。');
        }

        return view('employees.import');
    }

    /**
     * CSVインポート実行
     */
    public function import(Request $request)
    {
        // 管理者権限チェック
        if (!auth()->user()->is_admin) {
            abort(403, 'この操作は管理者権限が必要です。');
        }

        $request->validate([
            'csv_file' => 'required|mimes:csv,txt|max:2048',
        ]);

        try {
            $import = new EmployeesImport;
            Excel::import($import, $request->file('csv_file'));

            $successCount = $import->getRowCount();
            $failures = $import->failures();
            $errors = $import->errors();

            if ($failures->isNotEmpty() || $errors->isNotEmpty()) {
                $errorMessage = 'インポート中にエラーが発生しました。';
                if ($failures->isNotEmpty()) {
                    $errorMessage .= ' 失敗した行: ' . $failures->count();
                }
                if ($errors->isNotEmpty()) {
                    $errorMessage .= ' エラー: ' . $errors->count();
                }
                
                return redirect()->back()
                    ->with('error', $errorMessage)
                    ->with('failures', $failures)
                    ->with('import_errors', $errors);
            }

            return redirect()->route('employees.index')
                ->with('success', "CSVインポートが完了しました。{$successCount}件のデータを処理しました。");

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'CSVインポートに失敗しました: ' . $e->getMessage());
        }
    }

    /**
     * CSVテンプレートダウンロード
     */
    public function downloadTemplate()
    {
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="従業員インポートテンプレート.csv"',
        ];

        $csvData = [
            ['氏名', 'ふりがな', '職種', '部署名', '役職', '雇入年月日', '生年月日', '国籍', '性別', '配偶者の有無', '在留資格', '在留カード有効期限', '郵便番号', '現住所', '家族住所', '家族名', '家族名ふりがな', '続柄', '家族名2', '家族名ふりがな2', '続柄2', '家族住所2', '家族電話番号2', '電話番号', '家族電話番号', '最近の健康診断日', '血圧', '血液型', '特殊健康診断日', '特殊健康診断種類', '協会けんぽ番号', '厚生年金番号', '雇用保険番号', '雇入･職長特別教育', '技能講習', '免許', '受入教育実施年月日', '建退共手帳所有の有無'],
            ['田中太郎', 'たなかたろう', '現場作業員', '工事部', '主任', '2023-01-15', '1990-05-20', '日本', '男性', 'はい', '', '', '160-0022', '東京都新宿区1-1-1', '東京都新宿区1-1-1', '田中花子', 'たなかはなこ', '配偶者', '田中次郎', 'たなかじろう', '息子', '東京都新宿区1-1-1', '03-9876-5432', '03-1234-5678', '03-1234-5678', '2023-06-01', '120/80', 'A', '', '', 'ABC12345', 'DEF67890', 'GHI11111', '安全教育修了', '玉掛け技能講習', '普通自動車運転免許', '2023-01-10', 'はい']
        ];

        $callback = function() use ($csvData) {
            $file = fopen('php://output', 'w');
            fputs($file, "\xEF\xBB\xBF"); // BOM for UTF-8
            
            foreach ($csvData as $row) {
                fputcsv($file, $row);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    // プライベートメソッド
    private function uploadProfilePhoto($file)
    {
        $filename = 'profile_' . Str::uuid() . '.' . $file->getClientOriginalExtension();
        return $file->storeAs('employee_photos', $filename, 'public');
    }
}
