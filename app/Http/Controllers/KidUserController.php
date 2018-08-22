<?php

namespace App\Http\Controllers;

use App\Kid;
use Illuminate\Http\Request;

class KidUserController extends ApiController
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
    public function index(Kid $kid)
    {
        return $this->showOne($kid->user);
    }
}
