<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use App\Models\Polyclinic;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    /**
     * Display a listing of staff accounts.
     */
    public function index(): View
    {
        $users = User::with('roles')->latest()->paginate(15);

        return view('admin.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new staff account.
     */
    public function create(): View
    {
        $roles = Role::all();
        $polyclinics = Polyclinic::where('is_active', true)->get();

        return view('admin.users.create', compact('roles', 'polyclinics'));
    }

    /**
     * Store a newly created staff account.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'role' => ['required', 'exists:roles,name'],
            'password' => ['nullable', 'string', 'min:6'],
            // Conditional Doctor fields
            'polyclinic_id' => ['required_if:role,doctor', 'nullable', 'exists:polyclinics,id'],
            'sip_number' => ['required_if:role,doctor', 'nullable', 'string', 'max:50'],
            'specialization' => ['required_if:role,doctor', 'nullable', 'string', 'max:100'],
            'consultation_fee' => ['required_if:role,doctor', 'nullable', 'numeric', 'min:0'],
        ]);

        $password = ! empty($validated['password'])
            ? Hash::make($validated['password'])
            : Hash::make('password'); // default password fallback

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $password,
            'is_active' => true,
        ]);

        $user->assignRole($validated['role']);

        // If user is a Doctor, create doctor profile record
        if ($validated['role'] === 'doctor') {
            Doctor::create([
                'user_id' => $user->id,
                'polyclinic_id' => $validated['polyclinic_id'],
                'sip_number' => $validated['sip_number'],
                'specialization' => $validated['specialization'],
                'consultation_fee' => $validated['consultation_fee'] ?? 50000.00,
                'is_active' => true,
            ]);
        }

        return redirect()->route('admin.users.index')->with(
            'status',
            "Staf {$user->name} ({$user->email}) dengan Role [{$validated['role']}] berhasil didaftarkan! Staf ini kini dapat login menggunakan Email/Password maupun Google SSO."
        );
    }

    /**
     * Toggle active/inactive status of a staff member.
     */
    public function toggleStatus(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return back()->withErrors(['status' => 'Anda tidak dapat menonaktifkan akun Anda sendiri.']);
        }

        $user->update(['is_active' => ! $user->is_active]);

        $statusText = $user->is_active ? 'diaktifkan kembali' : 'dinonaktifkan';

        return back()->with('status', "Akun staf {$user->name} berhasil {$statusText}.");
    }

    /**
     * Delete a staff user account.
     */
    public function destroy(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return back()->withErrors(['status' => 'Anda tidak dapat menghapus akun yang sedang Anda gunakan saat ini.']);
        }

        $userName = $user->name;
        $userEmail = $user->email;

        // If user has doctor profile, delete it first
        \App\Models\Doctor::where('user_id', $user->id)->delete();
        $user->delete();

        return back()->with('status', "Akun staf {$userName} ({$userEmail}) berhasil dihapus permanen dari database.");
    }
}
