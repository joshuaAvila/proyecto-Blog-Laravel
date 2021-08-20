<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    public function create(Request $request){

        
            $post = new Post();
            $post->user_id = Auth::user()->id;
            $post->description = $request->description;
    
            if($request->foto != ''){
    
                $foto = time().'jpg';
    
                file_put_contents('storage/posts/'.$foto,base64_decode($request->foto));
    
                $post->foto = $foto;
            }
    
            $post->save();
            $post->user;
            return response()->json([
                'success' => 'true',
                'message' => 'posteado',
                'post' => $post
            ]);
        
    

    }

    public function update(Request $request){

        $post = Post::find($request->id);

        if(Auth::user()->id != $request->id){
            return response()->json([
                'success' => false,
                'message' => 'Acceso denegado'
            ]);
        }
        $post->description = $request->description;
        $post->update();
        return response()->json([
            'success' => true,
            'message' => 'Post editado'
        ]);

    }

    public function delete(Request $request){

        $post = Post::find($request->id);
        //Ccondicion para chequear si el usuario esta editando su propio post
        if(Auth::user()->id != $request->id){
            return response()->json([
                'success' => false,
                'message' => 'Acceso denegado'
            ]);
        }
       //condición para chequear si el post tiene foto 
        if($post->foto != ''){
            Storage::delete('public/posts/'.$post->foto);
        }

        $post->delete();
        return response()->json([
            'success' => true,
            'message' => 'Post eliminado'
        ]);

    }

    public function index(){
        $posts = Post::orderBy('id','desc')->get();
        foreach($posts as $post){

            $post->user;

            $post['commentsCount'] = count($post->comments);

            $post['likesCount'] = count($post->likes);

            $post['selfLike'] = false;
            foreach($post->likes as $like){
                if($like->user_id == Auth::user()->id){
                    $post['selfLike'] = true;
                }
            }
        }

        return response()->json([
            'success' => true,
            'posts' => $posts 
        ]);

    }
}
