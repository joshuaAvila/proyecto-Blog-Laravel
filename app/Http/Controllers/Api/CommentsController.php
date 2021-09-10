<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentsController extends Controller
{
    public function create(Request $request){
        $comment =new Comment();
        $comment->user_id = Auth::user()->id;
        $comment->post_id = $request->id;
        $comment->comentario = $request->comentario;
        $comment->save();
        $comment->user;

        return response()->json([
            'success' => true,
            'comment' => $comment,
            'message' => 'Comentario Agregado'
        ]);
    }

    public function update (Request $request){
        $comment = Comment::find($request->id);

        if($comment->id != Auth::user()->id){
            return response()->json([
                'success' => false,
                'message' => 'Acceso denegado'
            ]);
        }

        $comment->comentario = $request->comentario;
        $comment->update();

        return response()->json([
            'success' => true,
            'message' => 'Comentario actualizado'
        ]);
    }

    public function delete (Request $request){
        $comment = Comment::find($request->id);
        //Condicioin para validar si el usuario esta editando su propio comentario
        if($comment->user_id != Auth::user()->id){
            return response()->json([
                'success' => false,
                'message' => 'Acceso denegado'
            ]);
        }

        $comment->delete();

        return response()->json([
            'success' => true,
            'message' => 'Comentario eliminado'
        ]);
    }

    public function index(Request $request){
  
        $comments = Comment::where('post_id',$request->id)->get();
       
        // Mostrar los comentario de cada usuario
        foreach($comments as $comment){
        
            $comment->user;

          
        }
        return response()->json([
            'success' => true,
            'comentario' => $comments
        ]);
        
    }
}
  