<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Like;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LikesController extends Controller
{
    public function like(Request $request){
        $like = Like::where('post_id',$request->id)->where('user_id', Auth::user()->id)->get();
        //Validar si el post tiene like o no
        
        if(count($like) > 0){
            //se puede tener mas de un like
            $like[0]->delete();
            return response()->json([
                'success' => true,
                'posts' => 'unliked' 
            ]);
        }

        $like = new Like();
        $like->user_id = Auth::user()->id;
        $like->post_id = $request->id;
        $like->save();

        return response()->json([
            'success' => true,
            'posts' => 'liked' 
        ]);

    
    }
}
