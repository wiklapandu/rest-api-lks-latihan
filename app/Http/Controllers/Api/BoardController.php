<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class BoardController extends Controller
{
    //
    public function create(Request $req, DB $db)
    {
        $validation = Validator::make($req->all(), [
            'Name' => 'required'
        ]);
        if ($validation->fails()) {
            return response()
                ->json([
                    'message' => 'invalid field'
                ], 422);
        }

        $db::table('boards')->insert([
            'name' => $req->post('Name'),
            'creator_id' => (int) session()->get('user_id'),
            'created_at' => Carbon::now('Asia/Jakarta'),
            'updated_at' => Carbon::now('Asia/Jakarta')
        ]);

        return response()
            ->json([
                'message' => 'invalid field'
            ], 200);
    }

    public function update($board_id, Request $req, DB $db)
    {
        $validation = Validator::make($req->all(), [
            'Name' => 'required'
        ]);

        if ($validation->fails()) {
            return response()
                ->json([
                    'message' => 'invalid field'
                ], 422);
        }
        $db::table('boards')
            ->where([
                'id' => $board_id,
                'creator_id' => session()->get('user_id')
            ])
            ->update([
                'name' => $req->post('Name')
            ]);
        return response()
            ->json([
                'message' => 'update board success'
            ], 200);
    }

    public function delete($board_id, DB $db)
    {
        $db::table('boards')
            ->where([
                'id' => $board_id,
                'creator_id' => session()->get('user_id')
            ])
            ->delete();
        return response()
            ->json([
                'message' => 'delete board success'
            ], 200);
    }

    public function index(DB $db)
    {
        $boards = $db::table('boards')->get();
        return response()
            ->json([$boards], 200);
    }

    public function show($board_id, DB $db)
    {
        $board = $db::table('boards')->where([
            'id' => $board_id
        ])->get(['id', 'creator_id', 'name'])->first();;

        $members =  $db::table('users')
            ->get([
                'id', 'first_name', 'last_name'
            ]);
        $members = $db::table('board_members')
            ->where([
                'board_id' => $board_id
            ])
            ->join('users', 'users.id', '=', 'board_members.user_id')
            ->get(['users.id', 'users.first_name', 'users.last_name']);

        $board->members = $members;
        foreach ($members as $key => $member) {
            $board
                ->members[$key]
                ->initial = (random_int(0, 1) == 0) ? $member->first_name : $member->last_name;
        }

        $board->list = [];
        return response()
            ->json($board, 200);
    }
}
