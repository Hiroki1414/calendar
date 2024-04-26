<?php

class Controller_Rest_Calendar extends \Controller_Rest
{
    public function before()
    {
        parent::before();

        date_default_timezone_set('Asia/Tokyo');

        // CORSヘッダーを設定
        header('Access-Control-Allow-Origin: http://localhost:3000');
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');
        
        // preflightリクエストへの対応
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit();
        }

        if (!Auth::check()) {
            return $this->response([
                'status' => 'error',
                'message' => 'Unauthorized'
            ], 403); 
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

    // configから色の情報を取得
    public function get_colors()
    {
        $colors = Config::load('calendar/config', 'colors', true); 
        return $this->response([
            'status' => 'success',
            'data' => $colors['colors']
        ]);
    }

    // 新しいスケジュールを作成
    public function post_add()
    {
        $currentTime = date('Y-m-d H:i:s');

        list(, $user_id) = Auth::instance()->get_user_id();

        try {
            $insert = \DB::insert('schedules')
            ->set(array(
                'start' => $_POST['start'],
                'end' => $_POST['end'],
                'title' => $_POST['title'],
                'color' => $_POST['color'],
                'created_at' => $currentTime,
                'modified_at' => $currentTime,
                'user_id' => $user_id
            ))
            ->execute();

            error_log('Insert result: ' . $insert);

            if ($insert) {
                return $this->response(array(
                    'status' => 'success',
                    'message' => 'Schedule added successfully.',
                    'event' => $insert
                ));
            }
        } catch (Exception $e) {
            return $this->response(array(
                'status' => 'error',
                'message' => 'Database error: ' . $e->getMessage()
            ));
        }
    }    

    // スケジュールを更新
    public function post_update($id)
    {
        $currentTime = date('Y-m-d H:i:s');

        if (!$id) {
            Response::redirect('welcome/404');
        }

        $json = file_get_contents('php://input');
        $data = json_decode($json, true);

        $update = \DB::update('schedules')
            ->set(array(
                'start' => $data['start'],
                'end' => $data['end'],
                'title' => $data['title'],
                'color' => $data['color'],
                'modified_at' => $currentTime 
            ))
            ->where('schedule_id', '=', $id)
            ->execute();

        if ($update) {
            $updatedEvent = DB::select('*')->from('schedules')->where('schedule_id', '=', $id)->execute()->as_array();
            return $this->response(array(
                'status' => 'success',
                'message' => 'Event updated successfully.',
                'data' => $updatedEvent
            ));
        } else {
            return $this->response(array(
                'status' => 'error',
                'message' => 'Failed to update event.'
            ), 500);
        }
    }

    // スケジュールを削除
    public function delete_delete($id)
    {
        if (!$id) {
            Response::redirect('404');
        }

        $delete = \DB::delete('schedules')
            ->where('schedule_id', '=', $id)
            ->execute();

        if ($delete) {
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
