<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * Display a listing of users.
     */
    public function index(): View
    {
        $users = User::with('homePlanet')
            ->latest()
            ->paginate(20);

        return view('admin.users.index', [
            'users' => $users,
        ]);
    }

    /**
     * Display the specified user.
     */
    public function show(User $user): View
    {
        $user->load('homePlanet');

        return view('admin.users.show', [
            'user' => $user,
        ]);
    }
}
