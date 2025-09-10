<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\Log;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AdminController extends Controller
{
    public function getAllUsers(Request $request){

        if($request->user()->role!=='admin' && $request->user()->role!=='moderator'){
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        try{
            $perPage = $request->get('per_page', 10);
            $page = $request->get('page', 1);

            $users = User::select('id', 'name', 'email', 'role', 'created_at')
            ->orderByRaw("FIELD(role, 'admin', 'moderator', 'user')")
            ->orderBy('created_at', 'desc')
            ->paginate($perPage, ["*"], 'page', $page);

            return response()->json([
                'data' => $users->items(),
                'current_page' => $users->currentPage(),
                'last_page' => $users->lastPage(),
                'per_page' => $users->perPage(),
                'total' => $users->total(),
                'from' => $users->firstItem(),
                'to' => $users->lastItem()
            ]);
            
            // return response()->json($users);
        }catch(\Exception $e){
            Log::error('Error fetching users: ' . $e->getMessage());
            return response()->json(['error' => 'Server error'], 500);
        }
 
    }

    public function deleteUser(Request $request, User $id){
        try {
            
            if ($request->user()->role !== 'admin') {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            $id->delete();
            return response()->json(['message' => 'User deleted successfully'], 200);
        } catch (\Exception $e) {
            Log::error('Error deleting note: ' . $e->getMessage());
            return response()->json(['error' => 'Note not found'], 404);
        }
    }

    public function changeUserRole(Request $request, $id){
        try {

            if($request->user()->role !== 'admin'){
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            $request->validate([
                'role' => 'required|in:admin,moderator,user'
            ]);


            $user = User::findOrFail($id);
            $user->role = $request->role;
            $user->save();

            return response()->json([
                'message' => 'User role updated successfully', 
                'user' => $user
            ]);

        } catch (\Exception $e) {
            Log::error('Error changing user role: ' . $e->getMessage());
            return response()->json(['error' => 'Note not found'], 404);
        }
    }
}
