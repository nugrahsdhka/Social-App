<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\User;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    // ==========================================
    // BAGIAN 1: FITUR PROFIL INSTAGRAM (BARU)
    // ==========================================
    
    public function show(User $user)
    {
        $currentUser = Auth::user();
        
        // Hitung jumlah follower, following, dan posts
        $user->loadCount(['followers', 'following', 'posts']);

        $isCurrentUserProfile = $currentUser->id === $user->id;
        $isFollowing = $currentUser->isFollowing($user->id);

        return Inertia::render('Profile/Show', [
            'user' => $user,
            'isCurrentUserProfile' => $isCurrentUserProfile,
            'isFollowing' => $isFollowing,
        ]);
    }

    // ==========================================
    // BAGIAN 2: FITUR EDIT PROFIL (BAWAAN BREEZE)
    // ==========================================
    // Error Anda terjadi karena bagian ini hilang
    
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): Response
    {
        return Inertia::render('Profile/Edit', [
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
            'status' => session('status'),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}