<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ListsController extends Controller
{
    //
    public function add($board_id, Request $req)
    {
        $validation = Validator::make($req->all(), [
            'Name' => 'required',
        ]);
        if ($validation->fails()) {
            return response()
                ->json([
                    'message' => 'invalid field'
                ], 422);
        }

        $hasTable = DB::table('boards')->where([
            'id' => $board_id
        ])->exists();

        if (!$hasTable) {
            return response()
                ->json([
                    'message' => 'board tidak ditemukan'
                ], 403);
        }
        $order = DB::table('board_lists')->get(['order'])->count();

        DB::table('board_lists')->insert([
            'board_id' => $board_id,
            'name' => $req->post('Name'),
            'order' => $order + 1,
            'created_at' => Carbon::now('Asia/Jakarta')
        ]);

        return response()
            ->json([
                'message' => 'create list success'
            ], 200);
    }

    public function update($board_id, $list_id, Request $req)
    {
        $validation = Validator::make($req->all(), [
            'Name' => 'required',
        ]);
        if ($validation->fails()) {
            return response()
                ->json([
                    'message' => 'invalid field'
                ], 422);
        }

        $hasTable = DB::table('board_lists')->where([
            'id' => $list_id,
            'board_id' => $board_id
        ])->exists();

        if (!$hasTable) {
            return response()
                ->json([
                    'message' => 'List tidak ditemukan'
                ], 403);
        }

        DB::table('board_lists')
            ->where([
                'id' => $list_id,
                'board_id' => $board_id
            ])
            ->update([
                'name' => $req->post('Name'),
                'update_at' => Carbon::now('Asia/Jakarta')
            ]);

        return response()
            ->json([
                'message' => 'create list success'
            ], 200);
    }
}
