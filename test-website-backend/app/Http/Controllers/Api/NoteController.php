<?php

namespace App\Http\Controllers\Api;

use id;
use App\Models\Note;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class NoteController extends Controller
{
    
    public function index(Request $request)
    {
        $notes = Note::where('user_id', $request->user()->id)
        ->orderBy('created_at', 'desc')
        ->get();

        return response()->json($notes);
 
    }

    public function show(Note $note)
    {
        return response()->json($note);
    }

   
    public function store(Request $request)
    {
        $note = Note::create([
            'content' => $request->content,
            'user_id' => $request->user()->id
        ]);

        return response()->json($note, 201);
    }


   
    public function update(Request $request, Note $note)
    {

    try{
       if($note->user_id !== $request->user()->id){
         return response()->json(['error' => 'Unauthorized', 403]);
       }

       $note->update(['content' => $request->content]);

        return response()->json($note);
    }catch(\Exception $e){
        Log::error('Error updating note: ' . $e->getMessage());
        return response()->json(['message'=> 'Note failed to update'], 500);
    }
        
    }

   
    public function destroy(Request $request, Note $note)
    {
        try {
            
            if ($note->user_id !== $request->user()->id) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            $note->delete();
            return response()->json(['message' => 'Note deleted successfully'], 200);
        } catch (\Exception $e) {
            Log::error('Error deleting note: ' . $e->getMessage());
            return response()->json(['error' => 'Note not found'], 404);
        }
    }
}
