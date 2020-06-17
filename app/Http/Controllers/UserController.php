<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'email' => [ 'required', 'email', 'unique:users,email' ],
            'name' => [ 'required' ],
            'password' => [ 'required' ],
            'public_key' => [ 'required' ],
            'data' => [ 'required' ]
        ]);

        $user = User::create($request->only([ 'email', 'name', 'password', 'public_key', 'data' ]));
        auth()->login($user);
        return response()->json(null);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function authenticate(Request $request)
    {
        $this->validate($request, [
            'email' => [ 'required', 'email', 'exists:users,email' ],
            'password' => [ 'required' ],
        ], [
            'email.exists' => 'Invalid credentials'
        ]);

        $user = User::where('email', $request->get('email'))->first();
        if (is_null($user)) return response()->json([
                'errors' => [ 'email' => [ 'Invalid credentials' ] ]
            ], 422);

        if (!hash_equals($user->password, $request->get('password'))) {
            return response()->json([
                    'errors' => [ 'email' => [ 'Invalid credentials' ] ]
                ], 422);
        }

        auth()->login($user);
        return response()->json([ 'user_data' => $user->data ]);
    }

    public function logout(Request $request)
    {
        auth()->logout();
        if ($request->ajax()) {
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return response()->json([ 'token' => csrf_token() ]);
        } else return redirect()->to('/');
    }

    public function search(Request $request)
    {
        $users = User::where('id', '!=', auth()->id())
            ->when($request->has('q') && $request->filled('q'), function ($query) use ($request) {
                $query->where(function ($query_1) use ($request) {
                    $query_1->where('email', 'like', $request->get('q') . '%')
                        ->orWhere('name', 'like', $request->get('q') . '%');
                });
            })
        ->get();
        return response()->json([ 'users' => $users ]);
    }
}
