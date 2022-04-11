<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class MemberController extends Controller
{
    //
    public function add($board_id, Request $req)
    {
        $validation = Validator::make($req->all(), [
            'Username' => 'required',
        ]);
        if ($validation->fails()) {
            return response()
                ->json([
                    'message' => 'user did not exist'
                ], 422);
        }

        $hasUser = DB::table('users')->where([
            'username' => $req->post('Username')
        ]);
        $hasBoard = DB::table('boards')->where([
            'id' => $board_id
        ])->exists();

        if (!$hasUser->exists() || !$hasBoard) {
            return response()
                ->json([
                    'message' => 'user not found, please sign up'
                ], 422);
        }

        $user = $hasUser->get()->first();
        $data = [
            'user_id' => $user->id,
            'board_id' => $board_id
        ];

        $board_members = DB::table('board_members');
        if (!$board_members->where($data)->exists()) {
            $board_members
                ->insert($data);
            return response()
                ->json([
                    'message' => 'add member success'
                ], 200);
        }
        return response()
            ->json([
                'message' => 'user sudah menjadi member'
            ]);
    }

    public function delete($board_id, $user_id, DB $db)
    {
        $db::table('board_members')
            ->where([
                'board_id' => $board_id,
                'user_id' => $user_id
            ])
            ->delete();
        return response()
            ->json([
                'message' => 'delete board success'
            ], 200);
    }
}
