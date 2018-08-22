<?php

namespace App\Http\Controllers;

use App\Video;
use Illuminate\Http\Request;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Facades\Validator;

class VideosController extends ApiController
{
    public function __construct()
    {
        $this->middleware('jwt.verify');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $videos = Video::all();
        return $this->showAll($videos);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {   
        $validator = $this->validator($request);
        if ($validator->fails()) {
            return $this->errorResponse('Request failed validation.', 422);
        }
        if ($request->type_local == '1' || $request->type_local == 1 || $request->type_local == true) {
            $video = $request->file('video');
            $videoFileName = time() . '.' . $video->getClientOriginalExtension();
            $local = \Storage::disk('local');
            $filePath = 'public/tubekids/' . $videoFileName;
            $e = $local->put($filePath, file_get_contents($video), 'public');
            $request['url'] = 'storage/tubekids/' . $videoFileName;
        }

        $video = Video::create($request->all());
        return $this->showOne($video);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Video  $video
     * @return \Illuminate\Http\Response
     */
    public function show(Video $video)
    {
        return $this->showOne($video);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Video  $video
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Video $video)
    {
        $validator = $this->validator($request);
        if ($validator->fails()) {
            return $this->errorResponse('Request failed validation.', 422);
        }
        if ($request->type_local == '1' || $request->type_local == 1 || $request->type_local == true) {
            if ($request->has('video')) {

                $fileName = explode('storage/tubekids/', $video->url)[1];
                \Storage::delete('public/tubekids/' . $fileName);

                $video_file = $request->file('video');
                $videoFileName = time() . '.' . $video_file->getClientOriginalExtension();
                $local = \Storage::disk('local');
                $filePath = 'public/tubekids/' . $videoFileName;
                $e = $local->put($filePath, file_get_contents($video_file), 'public');
                $request['url'] = 'storage/tubekids/' . $videoFileName;   
            }else{
                $request['url'] = $video->url;
            }
        }else{
            if ($video->type_local == '1') {
                $fileName = explode('storage/tubekids/', $video->url)[1];
                \Storage::delete('public/tubekids/' . $fileName);
            }
        }
        $video->update($request->all());
        return $this->showOne($video);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Video  $video
     * @return \Illuminate\Http\Response
     */
    public function destroy(Video $video)
    {
        if ($video->type_local == 1) {
            $fileName = explode('storage/tubekids/', $video->url)[1];
            \Storage::delete('public/tubekids/' . $fileName);
        }
        $video = $video->delete();
        if (!$video) {
             return $this->errorResponse('Request failed validation.', 422);
        }
        return $this->showMessage('User deleted successfully', 200);
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function validator(Request $request , $id =null)
    {   
        $name = '';
        $validate= '';
        $method = $request->method();
        if ($request->has('type_local')) {
            if ($request->type_local == '1' || $request->type_local == 1 || $request->type_local == true) {
                if (strcmp($method, "PATCH") != 0) {
                    $name ='video';
                    $validate = 'required|file|mimes:mp4,avi,wmv,flv|max:20000';
                }else{
                    $name ='video';
                    $validate = 'file|mimes:mp4,avi,wmv,flv|max:20000';
                }
            }else{
                if (strcmp($method, "PATCH") != 0) {
                    $name ='url';
                    $validate = 'required|string';
                }else{
                    $name ='url';
                    $validate = 'string';
                }
            }
        }
        return Validator::make($request->all(), [
            'name' => 'required|string|max:191',
            'type_local' => 'boolean',
            'user_id' => 'required|integer|exists:users,id',
            $name => $validate
        ]);
    }
}
