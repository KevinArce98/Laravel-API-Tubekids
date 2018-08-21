<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;

class UserVideoController extends ApiController
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
    public function index(User $user)
    {
        return $this->showAll($user->videos);
    }
}
