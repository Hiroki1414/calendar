<?php

class Controller_Rest_Calendar extends \Controller_Rest
{
    public function before()
    {
        parent::before(); // 必ず親クラスのbeforeメソッドを呼び出す

        // CORSヘッダーを設定
        header('Access-Control-Allow-Origin: http://localhost:3000');
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');
        
        // preflightリクエストへの対応
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            // OPTIONSリクエストはここで終了させる
            exit();
        }
    }

    // スケジュールの一覧を取得
    public function get_list()
    {
        try {
            $events = DB::select('*')->from('schedules')->execute()->as_array();
            return $this->response(array(
                'status' => 'success',
                'data' => $events
            ));
        } catch (Exception $e) {
            return $this->response(array(
                'status' => 'error',
                'message' => $e->getMessage()
            ), 500);
        }
    }


    // 新しいスケジュールを作成
    public function post_add()
    {
        $insert = \DB::insert('schedules')
        ->set(array(
            'schedule_id' => Input::json('schedule_id'),
            'start_datetime' => Input::json('start_datetime'),
            'end_datetime' => Input::json('end_datetime'),
            'task' => Input::json('task'),
            'color' => Input::json('color'),
            'created_at' => date('Y-m-d H:i:s'), // 現在の日時を設定
            'modified_at' => date('Y-m-d H:i:s'), // 現在の日時を設定
        ))
        ->execute();

        if ($insert) {
            return $this->response(array(
                'status' => 'success',
                'message' => 'Event created successfully.',
                'event' => $insert
            ));
        } else {
            return $this->response(array(
                'status' => 'error',
                'message' => 'Failed to create event.'
            ), 500);
        }
    }

    // スケジュールを更新
    public function put_update($id = null)
    {
        is_null($id) and Response::redirect('404');

        $event = Model_Calendar::find($id);
        if (!$event) {
            return $this->response(array(
                'status' => 'error',
                'message' => 'Event not found.'
            ), 404);
        }

        $event->set(array(
            'start_datetime' => Input::put('start_datetime'),
            'end_datetime' => Input::put('end_datetime'),
            'task' => Input::put('task'),
            'color' => Input::put('color'),
            'modified_at' => date('Y-m-d H:i:s'), // 更新日時を現在の日時に設定
        ));

        if ($event->save()) {
            return $this->response(array(
                'status' => 'success',
                'message' => 'Event updated successfully.'
            ));
        } else {
            return $this->response(array(
                'status' => 'error',
                'message' => 'Failed to update event.'
            ), 500);
        }
    }

    // スケジュールを削除
    public function delete_delete($id = null)
    {
        is_null($id) and Response::redirect('404');

        $event = Model_Calendar::find($id);
        if (!$event) {
            return $this->response(array(
                'status' => 'error',
                'message' => 'Event not found.'
            ), 404);
        }

        if ($event->delete()) {
            return $this->response(array(
                'status' => 'success',
                'message' => 'Event deleted successfully.'
            ));
        } else {
            return $this->response(array(
                'status' => 'error',
                'message' => 'Failed to delete event.'
            ), 500);
        }
    }
}
