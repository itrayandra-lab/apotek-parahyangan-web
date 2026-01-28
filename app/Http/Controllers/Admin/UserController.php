<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserFormRequest;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request): View|\Illuminate\Http\JsonResponse
    {
        $query = User::query();

        if ($request->ajax()) {
            return \Yajra\DataTables\Facades\DataTables::of($query)
                ->addColumn('user_info', function ($user) {
                    return '<div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-full bg-gradient-to-br from-gray-200 to-gray-300 flex items-center justify-center text-gray-600 font-bold font-display text-lg flex-shrink-0">
                                    ' . strtoupper(substr($user->name, 0, 1)) . '
                                </div>
                                <div>
                                    <div class="text-base font-bold text-gray-900 font-display">' . $user->name . '</div>
                                    <div class="text-sm text-gray-500">' . '@' . $user->username . '</div>
                                </div>
                            </div>';
                })
                ->addColumn('role_label', function ($user) {
                    if ($user->role === 'admin') {
                        return '<span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-bold uppercase tracking-wider bg-rose-50 text-rose-600 border border-rose-100">
                                    <span class="h-2 w-2 rounded-full bg-rose-500"></span>
                                    Admin
                                </span>';
                    } elseif ($user->role === 'content_manager') {
                        return '<span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-bold uppercase tracking-wider bg-purple-50 text-purple-600 border border-purple-100">
                                    <span class="h-2 w-2 rounded-full bg-purple-500"></span>
                                    Content Manager
                                </span>';
                    } else {
                        return '<span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-bold uppercase tracking-wider bg-gray-100 text-gray-500 border border-gray-200">
                                    <span class="h-2 w-2 rounded-full bg-gray-400"></span>
                                    User
                                </span>';
                    }
                })
                ->addColumn('joined_at', function ($user) {
                    return '<span class="text-sm text-gray-500">' . $user->created_at->format('M d, Y') . '</span>';
                })
                ->addColumn('actions', function ($user) {
                    $editBtn = '<a href="' . route('admin.users.edit', $user) . '"
                                   class="w-10 h-10 rounded-xl flex items-center justify-center text-gray-400 hover:text-rose-600 hover:bg-rose-50 transition-all"
                                   title="Edit User">
                                   <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                       <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                   </svg>
                               </a>';
                    
                    $deleteBtn = '';
                    if ($user->id !== (int) auth()->id()) {
                        $deleteBtn = '<form action="' . route('admin.users.destroy', $user) . '" method="POST"
                                          onsubmit="return confirm(\'Are you sure you want to delete this user?\');"
                                          class="inline">
                                          ' . csrf_field() . '
                                          ' . method_field('DELETE') . '
                                          <button type="submit"
                                              class="w-10 h-10 rounded-xl flex items-center justify-center text-gray-400 hover:text-red-600 hover:bg-red-50 transition-all"
                                              title="Delete User">
                                              <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                              </svg>
                                          </button>
                                      </form>';
                    }

                    return '<div class="inline-flex items-center gap-1">' . $editBtn . $deleteBtn . '</div>';
                })
                ->rawColumns(['user_info', 'role_label', 'joined_at', 'actions'])
                ->make(true);
        }

        return view('admin.users.index', [
            'users' => null,
            'filters' => [
                'search' => $request->string('search')->toString(),
                'role' => $request->string('role')->toString(),
            ],
        ]);
    }

    public function create(): View
    {
        return view('admin.users.form');
    }

    public function store(UserFormRequest $request): RedirectResponse
    {
        User::create($request->validated());

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User created successfully.');
    }

    public function edit(User $user): View
    {
        return view('admin.users.form', compact('user'));
    }

    public function update(UserFormRequest $request, User $user): RedirectResponse
    {
        $data = $request->validated();

        if (empty($data['password'])) {
            unset($data['password']);
        }

        $user->update($data);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User updated successfully.');
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->id === (int) auth()->id()) {
            return redirect()
                ->route('admin.users.index')
                ->with('error', 'You cannot delete your own account.');
        }

        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User deleted successfully.');
    }
}
