<?php

namespace App\Http\Controllers;

use App\Http\Requests\BulkDeleteUserRequest;
use App\Http\Requests\BulkUpdateUserRequest;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('user/index', [
            'users' => User::get()->map(function ($user) {
                return $user->only(['id', 'name', 'email']) + [
                    'roles' => $user->getRoleNames(),
                ];
            }),
            'roles' => Role::pluck('name')
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request)
    {
        $data = $request->validated();
        User::create($data);
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        return Inertia::render('user/show', [
            'user' => $user
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        $data = $request->validated();
        $user->update($data);

        $user->syncRoles($data['roles']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $user->delete();
    }

    public function bulkUpdate(BulkUpdateUserRequest $request)
    {
        $data = $request->validated();
        User::whereIn('id', $data['user_ids'])->update($data);
    }
    
    public function bulkDelete(BulkDeleteUserRequest $request)
    {
        $data = $request->validated();
        User::whereIn('id', $data['user_ids'])->delete();
    }

    public function archived()
    {
        return Inertia::render('user/archived', [
            'users' => User::onlyTrashed()->get(),
        ]);
    }

    public function restore($user)
    {
        $user = User::onlyTrashed()->find($user);
        $user->restore();
    }

    public function forceDelete($user)
    {
        $user = User::onlyTrashed()->find($user);
        $user->forceDelete();
    }
}
