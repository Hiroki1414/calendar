<?php

class Controller_Calendar extends \Controller
{
    public function action_index()
    {
        $title = Config::get('site_name');

        $data = [];
        $data['title'] = 'Calendar';
        $data['today'] = date('Y-m-d');
        $data['ym'] = Input::get('ym', date('Y-m'));
        $data['weeks'] = [];
        $data['week'] = '';
        
        $result = DB::select()->from('schedules')->execute();
    
        $view = \View::forge('calendar/index');
        $view->set('title', $title);
        $view->set_global('data', $data);
        $view->set('schedules', $result);
        
        return $view;
    }

    public function post_add()
    {
        try {
            // フォームからのデータを受け取る
            $start_datetime = Input::post('start_datetime');
            $end_datetime = Input::post('end_datetime');
            $task = Input::post('task');
            $color = Input::post('color');

            // バリデーション
            $val = Validation::forge();
            $val->add_field('task', 'Task', 'required|max_length[255]');
            $val->add_field('start_datetime', 'Start datetime', 'required');
            $val->add_field('end_datetime', 'End datetime', 'required');
            $val->add_field('color', 'Color', 'required');

            if ($val->run()) {
                // バリデーションが通ればデータベースに保存
                $schedule = Model_Schedule::forge(array(
                    'start_datetime' => $start_datetime,
                    'end_datetime' => $end_datetime,
                    'task' => $task,
                    'color' => $color,
                ));
                $schedule->save();

                Session::set_flash('success', 'Schedule added successfully.');
                Response::redirect('calendar');
            } else {
                // バリデーションエラー
                Session::set_flash('error', $val->error());
            }
        } catch (Exception $e) {
            // エラーハンドリング
            Log::error($e->getMessage());
            Session::set_flash('error', 'An error occurred. Please try again.');
        }

        // POSTリクエストがあったページにリダイレクトする
        Response::redirect_back();
    }

    public function action_add()
    {
        $title = '予定の追加 | ' . SITE_NAME;

        // 変数を初期化
        $start_datetime = '';
        $end_datetime = '';
        $task = '';
        $color = '';
        $err = [];

        if (Input::method() == 'POST') {

            // フォームから送られてきたデータを取得
            $start_datetime = Input::post('start_datetime');
            $end_datetime = Input::post('end_datetime');
            $task = Input::post('task');
            $color = Input::post('color');

            // 入力チェック
            if (empty($start_datetime)) {
                $err['start_datetime'] = '開始日時を入力して下さい。';
            }

            if (empty($end_datetime)) {
                $err['end_datetime'] = '終了日時を入力して下さい。';
            }

            if (empty($task)) {
                $err['task'] = '予定を入力してください。';
            } elseif (mb_strlen($task) > 32) {
                $err['task'] = '32文字以内で入力してください。';
            }

            if (empty($color)) {
                $err['color'] = 'カラーを選択してください。';
            }

            // エラーがなければデータベースに保存
            if (empty($err)) {

                // データベースへの追加処理
                $sql = 'INSERT INTO schedules(start_datetime, end_datetime, task, color, created_at, modified_at)
                        VALUES(:start_datetime, :end_datetime, :task, :color, now(), now())';
                $stmt = $pdo->prepare($sql);
                $stmt->bindValue(':start_datetime', $start_datetime, PDO::PARAM_STR);
                $stmt->bindValue(':end_datetime', $end_datetime, PDO::PARAM_STR);
                $stmt->bindValue(':task', $task, PDO::PARAM_STR);
                $stmt->bindValue(':color', $color, PDO::PARAM_STR);
                $stmt->execute();

                // リダイレクト
                Response::redirect('calendar/index');
            }
        }
    
        $view = View::forge('calendar/add');
        $view->set('title', $title);
        $view->set('start_datetime', $start_datetime);
        $view->set('end_datetime', $end_datetime);
        $view->set('task', $task);
        $view->set('color', $color);
        $view->set('err', $err);

        return $this->response($view);
    }

    public function action_list() {
        return Response::forge(View::forge('calendar/list'));
    }

    public function action_edit()
    {
        $title = '予定の編集 | ' . SITE_NAME;

        // 変数を初期化
        $schedule_id = Input::get('id');
        $err = [];

        // データベースから予定データを取得
        $pdo = connectDB();
        $sql = 'SELECT * FROM schedules WHERE schedule_id = :schedule_id LIMIT 1';
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':schedule_id', $schedule_id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        // データが見つからなかった場合はリダイレクト
        if (empty($row) || $row === false) {
            Response::redirect('calendar/index');
        }

        // データをフォームにセット
        $start_datetime = $row['start_datetime'];
        $end_datetime = $row['end_datetime'];
        $task = $row['task'];
        $color = $row['color'];

        if (Input::method() == 'POST') {
            
            // フォームから送られてきたデータを取得
            $start_datetime = Input::post('start_datetime');
            $end_datetime = Input::post('end_datetime');
            $task = Input::post('task');
            $color = Input::post('color');

            // 入力チェック
            if (empty($start_datetime)) {
                $err['start_datetime'] = '開始日時を入力して下さい。';
            }

            if (empty($end_datetime)) {
                $err['end_datetime'] = '終了日時を入力して下さい。';
            }

            if (empty($task)) {
                $err['task'] = '予定を入力してください。';
            } elseif (mb_strlen($task) > 32) {
                $err['task'] = '32文字以内で入力してください。';
            }

            if (empty($color)) {
                $err['color'] = 'カラーを選択してください。';
            }

            // エラーがなければデータベースを更新
            if (empty($err)) {

                // データベースの更新処理
                $sql = 'UPDATE schedules 
                        SET start_datetime = :start_datetime, end_datetime = :end_datetime, task = :task, color = :color, modified_at = now() 
                        WHERE schedule_id = :schedule_id';
                $stmt = $pdo->prepare($sql);
                $stmt->bindValue(':start_datetime', $start_datetime, PDO::PARAM_STR);
                $stmt->bindValue(':end_datetime', $end_datetime, PDO::PARAM_STR);
                $stmt->bindValue(':task', $task, PDO::PARAM_STR);
                $stmt->bindValue(':color', $color, PDO::PARAM_STR);
                $stmt->bindValue(':schedule_id', $schedule_id, PDO::PARAM_INT);
                $stmt->execute();

                // リダイレクト
                Response::redirect('calendar/index');
            }
        }

        $view = View::forge('calendar/edit');
        $view->set('title', $title);
        $view->set('start_datetime', $start_datetime);
        $view->set('end_datetime', $end_datetime);
        $view->set('task', $task);
    }

    public function action_delete($id = null)
    {
        // パラメータが存在しない場合や形式が不正な場合は、トップページにリダイレクト
        if (empty($id) || !ctype_digit($id)) {
            Response::redirect('calendar/index');
        }

        // データベースから予定を削除
        $schedule = Model_Schedule::find($id);
        if ($schedule) {
            $schedule->delete();
        }

        // 前の画面にリダイレクト
        Response::redirect(Input::referrer());
    }

    public function action_detail()
    {
        $ymd = Input::get('ymd');
        if (empty($ymd) || !strtotime($ymd)) {
            Response::redirect('index');
        }

        $ymd_formatted = date('Y年n月j日', strtotime($ymd));
        $title = $ymd_formatted . 'の予定 | ' . SITE_NAME;

        $rows = Model_Schedule::getSchedulesByDate($ymd);

        $data = array(
            'title' => $title,
            'ymd_formatted' => $ymd_formatted,
            'rows' => $rows,
        );

        $this->template->title = $title;
        $this->template->content = View::forge('detail/index', $data);
    }
}
