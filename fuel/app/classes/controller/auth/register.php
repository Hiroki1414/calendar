<?php

namespace Controller\Auth;

use Fuel\Core\Controller;
use Fuel\Core\View;
use Fuel\Core\Response;
use Fuel\Core\Input;
use Fuel\Core\Session;
use Auth;

class Register extends Controller
{
    public function action_index() {
        // 既にログインしている場合はリダイレクト
        if (Auth::check()) {
            Response::redirect('calendar/index');
        }

        $view = View::forge('register/index');
        return Response::forge($view);
    }

    public function action_register() {
        if (Input::method() == 'POST') {
            $username = Input::post('username');
            $password = Input::post('password');
            $email = Input::post('email');

            if (!preg_match('/^[a-zA-Z0-9]+$/', $password)) {
                Session::set_flash('error', 'パスワードは半角英数字のみで入力してください。');
                Response::redirect('register');
                return;
            }

            try {
                if ($this->validate_input($username, $password, $email)) {
                    $user_id = Auth::create_user($username, $password, $email);
                    if ($user_id) {
                        Session::set_flash('success', '新規登録が完了しました');
                        Response::redirect('login');
                    } else {
                        Session::set_flash('error', 'ユーザーの作成に失敗しました。');
                        Response::redirect('register');
                    }
                } else {
                    Session::set_flash('error', '入力データが不完全です。');
                    Response::redirect('register');
                }
            } catch (\SimpleUserUpdateException $e) {
                // エラーコードに基づいて異なるエラーメッセージを設定
                if ($e->getCode() == 2) {
                    Session::set_flash('error', 'このメールアドレスはすでに存在します。');
                } else if ($e->getCode() == 3) {
                    Session::set_flash('error', 'このユーザー名はすでに存在します。');
                } else {
                    Session::set_flash('error', '不明なエラーが発生しました。');
                }
                Response::redirect('register');
            }
        }
    }

    private function validate_input($username, $password, $email) {
        $isUsernameValid = !empty($username);
        $isPasswordValid = !empty($password) && strlen($password) >= 8;
        $isEmailValid = filter_var($email, FILTER_VALIDATE_EMAIL);
        return $isUsernameValid && $isPasswordValid && $isEmailValid;
    }
}

?>