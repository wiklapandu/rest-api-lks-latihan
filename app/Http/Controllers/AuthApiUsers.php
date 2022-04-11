<?php

namespace App\Http\Controllers;

use App\Models\users;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;


class AuthApiUsers extends Controller
{
    //
    protected $messageErr;
    public function __construct()
    {
        $this->messageErr = [
            'required' => ':attribute harus di isi',
            'between' => ':attribute harus lebih dari :min dan kurang dari :max',
            'unique' => ':attribute tidak tersedia',
        ];
    }
    public function login(Request $req, DB $db)
    {
        $validation = Validator::make($req->all(), [
            'Username' => 'required',
            'Password' => 'required',
        ], $this->messageErr);
        if ($validation->fails()) {
            return response()->json([
                'message' => 'invalid field'
            ]);
        }
        $username = $req->post('Username');
        $password = $req->post('Password');
        $user = $db::table('users')->where([
            'username' => $username
        ]);
        if (!$user->exists()) {
            return response()
                ->json([
                    'status' => 'error',
                    'message' => 'akun tidak ditemukan'
                ], 422);
        }
        if (Hash::check($password, $user->get()->first()->password)) {
            $user = $user->get()->first();
            $token = $db::table('login_tokens')->where([
                'user_id' => $user->id,
            ]);

            if ($token->exists()) {
                $token = $token->get()->first()->token;
            } else {
                $db::table('login_tokens')->insert([
                    'user_id' => $user->id,
                    'token' => Str::random(50),
                    'expired_at' => Carbon::tomorrow('Asia/Jakarta'),
                    'created_at' => Carbon::now('Asia/Jakarta'),
                    'updated_at' => Carbon::now('Asia/Jakarta'),
                ]);
                $token = $db::table('login_tokens')
                    ->where([
                        'user_id' => $user->id
                    ])->get()->first()->token;
            };
            $req->session()->push('user_id', $user->id);
            return response()->json([
                'token' => $token,
                'Role' => 'users'
            ], 200);
        }
    }

    public function registrasi(Request $req, DB $db)
    {
        $validation = Validator::make($req->all(), [
            'FirstName' => 'required|between:2,20',
            'LastName' => 'required|between:2,20',
            'Username' => 'required|unique:users,username',
            'Password' => 'required|between:5,12',
        ], $this->messageErr);
        if ($validation->fails()) {
            return response()->json([
                'message' => 'invalid field',
                'errors' => $validation->errors()
            ], 422);
        }
        $firstName = $req->post('FirstName');
        $lastName = $req->post('LastName');
        $username = $req->post('Username');
        $password = $req->post('Password');

        $user = new users;
        $user->first_name = $firstName;
        $user->last_name = $lastName;
        $user->username = $username;
        $user->password = Hash::make($password);
        $user->save();

        // Make token
        $user = $db::table('users')->where([
            'username' => $username
        ])->get()->first();
        $db::table('login_tokens')->insert([
            'user_id' => $user->id,
            'token' => Str::random(50),
            'expired_at' => Carbon::tomorrow('Asia/Jakarta'),
            'created_at' => Carbon::now('Asia/Jakarta'),
            'updated_at' => Carbon::now('Asia/Jakarta'),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'akun berhasil dibuat, silahkan login',
            'token' => $db::table('login_tokens')->where([
                'user_id' => $user->id
            ])->get()->first()->token,
            'role' => 'users'
        ]);
    }
}
