<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Mail\UserCreated;
use Illuminate\Support\Facades\Mail;


class UsersController extends ApiController
{
    /**
     * Instantiate a new UsersController instance.
     */
    public function __construct()
    {
        //$this->middleware('jwt.auth')->except(['store']);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::all();
        return $this->showAll($users);;
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

        $carbonDate = User::formatDateToCarbon($request->date_birthday);
        if (User::verifyDate($carbonDate) < 18) {
            return $this->errorResponse('Unauthorized action.', 403);
        }
         
        $request['date_birthday'] = User::formatDateToSql($carbonDate);
        $request['password'] = bcrypt($request->password);
        $request['verified'] = User::NO_VERIFICADO;
        $request['verification_token'] = str_random(56);

        $user = User::create($request->all());
        return $this->showOne($user, 201);
    }

    public function verify($token){
        $user = User::where('verification_token', $token)->firstOrFail();
        $user->verified = User::VERIFICADO;
        $user->verification_token = null;
        $user->save();
        return $this->showMessage('Verified User', 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        return $this->showOne($user);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        $validator = $this->validator($request, $user->id);
        if ($validator->fails()) {
            return $this->errorResponse('Request failed validation.', 422);
        }

        $carbonDate = User::formatDateToCarbon($request->date_birthday);
        if (User::verifyDate($carbonDate) < 18) {
            return $this->errorResponse('Unauthorized action.', 403);
        }
         
        $request['date_birthday'] = User::formatDateToSql($carbonDate);
        if ($user->email != $request->email) {
            $request['verified'] = User::NO_VERIFICADO;
            $request['verification_token'] = str_random(56);
        }
        $request['password'] = bcrypt($request['password']);
        $user->update($request->all());
        return $this->showOne($user);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        $user->delete();
        return $this->showOne($user);
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
            $validateUpdate = ',email,'.$id;
        }
        return Validator::make($data, [
            'name' => 'required|string|max:191',
            'lastname' => 'required|string|max:191',
            'country' => 'string|max:191',
            'date_birthday' => 'date_format:"d/m/Y"|required|before:today',
            'email' => 'required|string|email|max:191|unique:users'.$validateUpdate,
            'password' => 'required|string|min:6|confirmed',
            'verified' => 'boolean'
        ]);
    }

    public function resend(User $user){

        if ($user->verified()) {
            return $this->errorResponse('This user has already been verified', 409);
        }
        retry(5, function() use ($user){ 
            Mail::to($user)->send(new UserCreated($user));
        }, 100);

        return $this->showMessage('Verification mail has been sent',200);
    }
}
