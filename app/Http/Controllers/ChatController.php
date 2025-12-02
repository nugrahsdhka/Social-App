<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use App\Events\UserFollowed;

class ChatController extends Controller
{
    public function index()
    {
        $myId = Auth::id();

        // 1. Ambil semua pesan di mana kita terlibat (pengirim/penerima)
        // 2. Kumpulkan ID lawan bicara
        $userIds = Message::where('sender_id', $myId)
                    ->orWhere('receiver_id', $myId)
                    ->latest() // Urutkan dari yang terbaru
                    ->get()
                    ->map(function ($msg) use ($myId) {
                        return $msg->sender_id == $myId ? $msg->receiver_id : $msg->sender_id;
                    })
                    ->unique(); // Hapus duplikat ID

        // 3. Ambil data User berdasarkan ID tersebut
        $recentUsers = User::whereIn('id', $userIds)->get();

        return Inertia::render('Chat/Index', [
            'recentUsers' => $recentUsers // Kirim data ini ke Frontend
        ]);
    }

    // Fitur Cari User berdasarkan Username
    public function search(Request $request)
    {
        $query = $request->input('query');
        if (!$query) return response()->json([]);

        $users = User::where('username', 'like', "%{$query}%")
                     ->where('id', '!=', Auth::id())
                     ->select('id', 'name', 'username', 'is_private', 'bio') // Ambil data profil
                     ->get();

        return response()->json($users);
    }

    // Ambil Profil & Chat (Cek Privacy)
    public function show(User $user)
    {
        $me = Auth::user();
        
        // Cek status follow
        $isFollowing = $me->isFollowing($user->id);
        
        // Logic: Boleh chat jika: (Akun Public) ATAU (Akun Private TAPI sudah difollow)
        $canChat = !$user->is_private || $isFollowing;

        $messages = [];
        if ($canChat) {
            $messages = Message::where(function ($query) use ($user) {
                $query->where('sender_id', Auth::id())->where('receiver_id', $user->id);
            })->orWhere(function ($query) use ($user) {
                $query->where('sender_id', $user->id)->where('receiver_id', Auth::id());
            })->orderBy('created_at', 'asc')->get();
        }

        return response()->json([
            'profile' => $user,
            'is_following' => $isFollowing,
            'can_chat' => $canChat,
            'messages' => $messages
        ]);
    }

    // Fitur Follow
    public function follow(Request $request)
    {
        $targetUser = User::findOrFail($request->user_id);
        $me = Auth::user();

        if (!$me->isFollowing($targetUser->id)) {
            $me->following()->attach($targetUser->id, ['status' => 'accepted']);
            
            // --- TAMBAHAN: Kirim Notifikasi ---
            broadcast(new UserFollowed($me, $targetUser->id)); 
        } else {
            $me->following()->detach($targetUser->id);
        }

        return response()->json(['status' => 'success']);
    }

    // Simpan Pesan (Sama seperti sebelumnya)
    public function store(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'message' => 'required|string',
        ]);

        $message = Message::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $request->receiver_id,
            'message' => $request->message,
        ]);
        
        // Load relasi sender biar namanya kebawa
        $message->load('sender'); 

        broadcast(new MessageSent($message));

        return response()->json($message);
    }
}