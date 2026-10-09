<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller
{
    public function index(Request $request): Response
    {
        $query = User::with('employee');

        if ($request->filled('role')) {
            $query->where('role', $request->input('role'));
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->latest()->paginate(15)->withQueryString();
        $employees = Employee::select('id', 'nik', 'name')->orderBy('nik')->get();

        return Inertia::render('Users/Index', [
            'users' => $users,
            'employees' => $employees,
            'filters' => $request->only(['role', 'search']),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'role' => 'required|string|in:admin,hse,doctor,employee,manager',
            'employee_id' => 'nullable|exists:employees,id',
            'password' => 'required|string|min:8',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'employee_id' => $validated['employee_id'] ?? null,
            'password' => Hash::make($validated['password']),
        ]);

        if (! empty($validated['employee_id'])) {
            Employee::where('id', $validated['employee_id'])->update(['user_id' => $user->id]);
        }

        ActivityLog::create([
            'user_id' => $request->user()->id,
            'action' => 'CREATE_USER',
            'entity_type' => User::class,
            'entity_id' => $user->id,
            'changes' => ['email' => $user->email, 'role' => $user->role],
        ]);

        return redirect()->back()->with('success', 'User pengguna berhasil ditambahkan.');
    }

    public function update(Request $request, int $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => "required|string|email|max:255|unique:users,email,{$id}",
            'role' => 'required|string|in:admin,hse,doctor,employee,manager',
            'employee_id' => 'nullable|exists:employees,id',
            'password' => 'nullable|string|min:8',
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->role = $validated['role'];
        $user->employee_id = $validated['employee_id'] ?? null;

        if (! empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        if (! empty($validated['employee_id'])) {
            Employee::where('id', $validated['employee_id'])->update(['user_id' => $user->id]);
        }

        ActivityLog::create([
            'user_id' => $request->user()->id,
            'action' => 'UPDATE_USER',
            'entity_type' => User::class,
            'entity_id' => $user->id,
            'changes' => ['email' => $user->email, 'role' => $user->role],
        ]);

        return redirect()->back()->with('success', 'Data user berhasil diperbarui.');
    }
}
