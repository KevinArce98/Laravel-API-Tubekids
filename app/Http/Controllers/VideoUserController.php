<?php

namespace App\Http\Controllers;

use App\Video;
use Illuminate\Http\Request;

class VideoUserController extends ApiController
{
	public function __construct()
    {
        $this->middleware('jwt.auth');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Video $video)
    {
        return $this->showOne($video->user);
    }
}