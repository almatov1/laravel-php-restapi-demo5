<?php

namespace App\Http\Controllers\Api\Post;
use App\Models\Post;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use Validator;

class InfoController extends Controller
{
    public function upload(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|between:2,100',
            'about' => 'required|string|min:100',
            'media' => 'required|file',
        ]);

        if($validator->fails()){
            return response()->json(['error' => true, $validator->errors()], 400);
        }

        $media = $request->file('media');
        $fileName = Str::random(40).'.'.$media->getClientOriginalExtension();
        $media->move(public_path('/uploads/media'), $fileName);

        $post = Post::create(array_merge(
            $validator->validated(),
            ['user_id' => auth('api')->user()->id],
            ['media' => $fileName]
        ));

        return response()->json([
            'error' => false,
            'message' => 'Post successfully uploaded',
            'post' => $post
        ], 201);
    }

    public function getMyData() {
        return response()->json(Post::where('user_id', auth('api')->user()->id)->get(), 200);
    }

    public function getData() {
        $users = QueryBuilder::for(Post::class)
        ->allowedFilters([
            AllowedFilter::exact('name'),
            AllowedFilter::exact('user_id'),
            AllowedFilter::scope('between_date'),
        ])
        ->paginate(2);

        return response()->json($users, 200);
    }

    public function deleteData($id) {
        $post = Post::where('id', $id)->first();

        if (!$post) {
            return response()->json([
                'error' => true,
                'message' => 'Post not found'
            ], 400);
        }

        unlink(public_path('/uploads/media').'/'.$post->media);
        $post->delete();
        return response()->json('', 204);
    }
}
