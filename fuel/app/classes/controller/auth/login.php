<?php

namespace Controller\Auth;

use Fuel\Core\Controller;
use Fuel\Core\View;
use Fuel\Core\Response;
use Fuel\Core\Input;
use Fuel\Core\Session;
use Auth\Auth;
use Fuel\Core\Security;

class Login extends Controller
{
    public function before()
    {
        parent::before();
        session::start();
    }

    public function action_index()
    {
        // ログインフォームのビューを生成
        $message = Session::get_flash('success');
        $view = View::forge('login/index');
        $view->set('message', $message, false); 
        return Response::forge($view);
    }

    public function action_login()
    {
        if (Input::method() == 'POST') {

            \Log::debug('CSRF Token from POST: ' . Input::post('fuel_csrf_token'));
            \Log::debug('CSRF Token from Session: ' . Session::get('fuel_csrf_token'));
    
            if (!Security::check_token()) {
                \Log::error('CSRF token validation failed.');
                Session::set_flash('error', 'CSRF検証に失敗しました。もう一度試してください。');
                Response::redirect('login');
            }

            $email = Input::post('email');
            $password = Input::post('password');

            // ユーザー認証
            if (Auth::login($email, $password)) {
                // 認証成功：ユーザーIDをセッションに保存し、リダイレクト
                $username = Auth::get_screen_name();
                Session::set('user_id', Auth::get_user_id()[1]);
                Session::set_flash('success', 'ログインしました');
                Response::redirect('calendar/index');
            } else {
                Session::set_flash('error', 'ログインに失敗しました。メールアドレスまたはパスワードが正しくありません。');
                Response::redirect('login');
            }
        } else {
            // POSTリクエストでなければログインページへリダイレクト
            Response::redirect('login');
        }
    }
}

?>
