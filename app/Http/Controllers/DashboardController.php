<?php

namespace App\Http\Controllers;

use App\Models\ChatGroup;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $authUserId = (int) Auth::id();

        $users = User::where('id', '!=', $authUserId)
            ->orderBy('name')
            ->get();

        $chatGroups = ChatGroup::whereHas('users', function ($query) use ($authUserId) {
                $query->where('users.id', $authUserId);
            })
            ->orderBy('name')
            ->get();

        return view('dashboard', compact('users', 'chatGroups'));
    }
}