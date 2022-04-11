<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AuthToken
{
    public function handle(Request $request, Closure $next)
    {
        // * get request token from header authentication and get token
        $reqToken = $request->get('token');

        // ? jika tidak ada request token
        if (!$reqToken) {
            return response()
                ->json([
                    'login' => false,
                    'message' => 'token belum dimasukan'
                ], 403);
        }

        // * ambil token yang token dama dengan request token
        $token = DB::table('login_tokens')->where([
            'token' => $reqToken,
            'user_id' => session()->get('user_id')
        ]);

        // ? jika tidak ada
        if (!$token->exists()) {
            return response()->json([
                'status' => 'fails',
                'message' => 'token tidak ditemukan, silahkan login dahulu'
            ]);
        }

        // * ambil data token
        $token = $token->get()->first();

        // ? jika expired token lebih kecil dari waktu sekarang
        // * token berarti expired
        if (strtotime($token->expired_at) < strtotime(Carbon::now('Asia/Jakarta'))) {
            return response()->json([
                'status' => 'fails',
                'message' => 'token telah expired silahkan login kembali'
            ]);
        }
        return $next($request);
    }
}
