<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LoginController extends Controller
{
    public function reg()
    {
        return view('reg');
    }
    public  function login(){
        return view('login');
    }

    public function logreg(Request $request)
    {
        $u = $request->input('user');
        $p = $request->input('pwd');
        $user = usermodel::where(['name' => $u])->first();
        if ($user) {
            if ($u == 'admin' && $p == 'admin') {
                $uid=$user->uid;
                $token=$this->getlogintoken($uid);
                setcookie('token', $token, time() + 200, '/', 'api.com', false, true);
                setcookie('uid', $uid, time() + 200, '/', 'api.com', false, true);
                $redis_token_key = 'login_token:uid:' . $user->uid;
                Redis::set($redis_token_key, $token);
                Redis::expire($redis_token_key, 60480);
                $response = [
                    'errno' => 0,
                    'msg' => 'ok',
                    'data' => [
                        'token' => $token
                    ]
                ];
                echo '登录成功';
                header('refresh:3;url=http://www.api.com');
            } else {
                echo '登录失败';
                header('refresh:3;url=http://passport.api.com/login');
            }


        } else {
            $response = [
                'errno' => 50004,
                'msg' => '用户不存在'
            ];
            die(json_encode($response, JSON_UNESCAPED_UNICODE));
        }
    }
    protected function getlogintoken($uid){
        $token=substr(md5($uid.time().Str::random(10)),5,15);
        return $token;
    }
}
