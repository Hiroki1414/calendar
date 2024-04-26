<?php

class Controller_Register extends Controller
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
            $password = Auth::hash_password(Input::post('password')); // パスワードのハッシュ化を確実に行う
            $email = Input::post('email');

            // 入力バリデーション
            if ($this->validate_input($username, $password, $email)) {
                // Authを使用して新しいユーザーを登録
                try {
                    // ユーザー作成時に現在のUNIXタイムスタンプを渡す
                    $user_id = Auth::create_user($username, $password, $email);
                    if ($user_id) {
                        Session::set_flash('success', '登録が完了しました。');
                        Response::redirect('calendar/index');
                    } else {
                        throw new Exception('ユーザーの作成に失敗しました。');
                    }
                } catch (Exception $e) {
                    Session::set_flash('error', $e->getMessage());
                }
            } else {
                Session::set_flash('error', '入力データが不完全です。');
            }
            Response::redirect('register/index');
        }
    }

    private function validate_input($username, $password, $email) {
        $isUsernameValid = !empty($username);
        $isPasswordValid = !empty($password) && strlen($password) >= 8;
        $isEmailValid = filter_var($email, FILTER_VALIDATE_EMAIL);
        return $isUsernameValid && $isPasswordValid && $isEmailValid;
    }
}
