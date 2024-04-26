<?php

class Controller_Login extends Controller
{
    public function action_index()
    {
        // 既にログインしている場合はリダイレクト
        if (Auth::check()) {
            Response::redirect('calendar/index');
        }

        // ログインフォームのビューを生成
        $message = Session::get_flash('success');
        $view = View::forge('login/index');
        $view->set('message', $message, false); 
        return Response::forge($view);
    }

    public function action_login()
    {
        if (Input::method() == 'POST') {
            $email = Input::post('email');
            $password = Input::post('password');
            // ユーザー認証
            if (Auth::login($email, $password)) {
                // 認証成功：ユーザーIDをセッションに保存し、リダイレクト
                Session::set('user_id', Auth::get_user_id()[1]);
                Response::redirect('calendar/index');
            } else {
                // 認証失敗：エラーメッセージをセットし、リダイレクト
                Session::set_flash('error', 'ログインに失敗しました。メールアドレスまたはパスワードが正しくありません。');
                Response::redirect('login/index');
            }
        } else {
            // POSTリクエストでなければログインページへリダイレクト
            Response::redirect('login/index');
        }
    }
}

?>
