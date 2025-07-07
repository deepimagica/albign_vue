<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AdminAuthController extends Controller
{
    public function getLoginPage()
    {
        return Inertia::render('Admin/LoginForm');
    }

    public function index(Request $request)
    {
        if ($request->isMethod('post')) {

            $validator = Validator::make($request->all(), [
                'email' => 'required|email|max:37',
                'password' => 'required|max:30'
            ]);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $email = $request->input('email');
            $password = md5($request->input('password'));

            $globalSettings = DB::table('settings')
                ->whereIn('setting_name', [
                    'admin_email',
                    'admin_password',
                    'strategy_email',
                    'strategy_password'
                ])
                ->pluck('setting_value', 'setting_name');

            if (($globalSettings['admin_email'] ?? null) === $email && ($globalSettings['admin_password'] ?? null) === $password) {
                $this->savePasswordIfNotExist($email, $password, 'super_admin');
                $this->logActivity('login_success', 'admin', $email);
                return redirect()->route('admin.dashboard');
            }

            if (($globalSettings['strategy_email'] ?? null) === $email && ($globalSettings['strategy_password'] ?? null) === $password) {
                $this->savePasswordIfNotExist($email, $password, 'super_admin');
                $this->logActivity('login_success', 'strategy', $email);

                return Inertia::location(url('/admin/strategy/view'));
            }

            $division = DB::table('divisions')
                ->where('email', $email)
                ->where('password', $password)
                ->first();

            if ($division) {
                $nextDueDate = date('Y-m-d', strtotime($division->last_change_password_date . ' +30 days'));

                if ($nextDueDate <= date('Y-m-d')) {
                    $this->logActivity('password_expired', 'division', $email);

                    return redirect()->back()->with([
                        'error' => 'Password expired. Please change your password.'
                    ]);
                }

                $typeMap = [
                    0 => 'medical',
                    1 => 'marketing',
                    2 => 'seat',
                    3 => 'marketing_pmt',
                    4 => 'headfinance',
                    5 => 'review'
                ];

                $userType = $typeMap[$division->type] ?? 'unknown';
                $this->savePasswordIfNotExist($division->division_id, $password, 'division_admin');
                $this->logActivity('login_success', $userType, $email);
                return redirect()->route('admin.dashboard');
            }
            $this->logActivity('login_try', 'admin', $email);

            return redirect()->back()->with([
                'error' => 'Wrong email or password.'
            ]);
        }

        return Inertia::render('Admin/Login');
    }

    private function savePasswordIfNotExist($userId, $password, $type)
    {
        $exists = DB::table('passwords')
            ->where('user_id', $userId)
            ->where('type', $type)
            ->exists();

        if (!$exists) {
            DB::table('passwords')->insert([
                'user_id' => $userId,
                'passwords' => $password,
                'type' => $type,
                'created_at' => now()
            ]);
        }
    }

    private function logActivity($action, $module, $email)
    {
        DB::table('activity_logs')->insert([
            'type' => $action,
            'module' => $module,
            'value' => $email,
            'created_at' => now()
        ]);
    }
}
