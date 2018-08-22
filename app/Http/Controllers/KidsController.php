<?php

namespace App\Http\Controllers;

use App\Kid;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class KidsController extends ApiController
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
        $kids = Kid::all();
        return $this->showAll($kids);;
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

        $request['pin'] = bcrypt($request->pin);
        $kid = Kid::create($request->all());

        return $this->showOne($kid, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Kid  $kid
     * @return \Illuminate\Http\Response
     */
    public function show(Kid $kid)
    {
        return $this->showOne($kid);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Kid  $kid
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Kid $kid)
    {
        $validator = $this->validator($request, $kid->id);
        if ($validator->fails()) {
            return $this->errorResponse('Request failed validation.', 422);
        }
        $request['pin'] = bcrypt($request['pin']);
        $kid->update($request->all());

        return $this->showOne($kid);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Kid  $kid
     * @return \Illuminate\Http\Response
     */
    public function destroy(Kid $kid)
    {
        $kid->delete();
        return $this->showOne($kid);
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function validator(Request $request , $id =null)
    {
        $data = $request->all();
        $validateUpdate = '';
        if ($request->method() == 'PATCH' || $request->method() == 'PUT') {
            $validateUpdate = ',username,'.$id;
        }

        return Validator::make($data, [
            'fullname' => 'required|string|max:191',
            'username' => 'required|string|max:191|unique:kids'.$validateUpdate,
            'age' => 'integer|required',
            'pin' => 'required|string|min:4',
            'user_id' => 'required|integer|exists:users,id',
        ]);
    }
}
