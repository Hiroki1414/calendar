<?php

namespace Controller; 

class Calendar extends \Fuel\Core\Controller
{
    public function before()
    {
        parent::before();

        // ユーザーがログインしているかチェック
        if (!\Auth\Auth::check()) {  // 名前空間に注意
            \Fuel\Core\Session::set_flash('error', 'セッションが切れました。もう一度ログインしてください。');
            \Fuel\Core\Response::redirect('auth/login/index');
        }
    }

    public function action_index()
    {
        $user_id = \Auth\Auth::get_user_id()[1]; // 名前空間に注意
        \Fuel\Core\Log::debug('ログインユーザーID: ' . $user_id);

        $result = \Fuel\Core\DB::select()->from('schedules')->where('user_id', '=', $user_id)->execute();

        \Fuel\Core\Log::debug('取得したスケジュールデータ: ', (array)$result);

        $data = [];
        $data['title'] = 'Calendar';
        $data['today'] = date('Y-m-d');
        $data['ym'] = \Fuel\Core\Input::get('ym', date('Y-m'));
        $data['weeks'] = [];
        $data['week'] = '';

        $view = \View::forge('calendar/index');
        $view->set('title', $data['title']);
        $view->set_global('data', $data);
        $view->set('schedules', $result);

        return $view;
    }

    public function action_list()
    {
        // ログインユーザーのIDを取得
        $user_id = \Auth\Auth::get_user_id()[1];

        // ログインユーザーのスケジュールを取得
        $result = \Fuel\Core\DB::select()->from('schedules')->where('user_id', '=', $user_id)->execute();

        // スケジュールデータをビューに渡す
        $data = [];
        $data['title'] = 'Event List';
        $data['schedules'] = $result;

        // ビューを生成してデータをセットし、返す
        $view = \View::forge('calendar/list');
        $view->set('title', $data['title']);
        $view->set_global('data', $data);

        return $view;
    }
}
