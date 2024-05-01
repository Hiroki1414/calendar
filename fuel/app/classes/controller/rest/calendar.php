<?php

namespace Controller\Rest;

use Fuel\Core\Controller_Rest;
use Fuel\Core\Response;
use Auth\Auth;
use Fuel\Core\DB;
use Fuel\Core\Config;
use Exception;

class Calendar extends Controller_Rest
{
    public function before()
    {
        parent::before();

        // CORSヘッダーを設定
        header('Access-Control-Allow-Origin: http://localhost:3000');
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');
        header('Access-Control-Allow-Credentials: true');
        
        // preflightリクエストへの対応
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            header('HTTP/1.1 200 OK');
            exit();
        }

        // ユーザーがログインしているかチェック
        if (!Auth::check()) {
            header('HTTP/1.1 401 Unauthorized');
            echo json_encode(array('status' => 'error', 'message' => 'User not authenticated'));
            exit();
        }

        date_default_timezone_set('Asia/Tokyo');
    }

    // スケジュールの一覧を取得
    public function get_list()
    {   
        try {
            $user_id = Auth::instance()->get_user_id()[1];
            $events = DB::select('*')
                        ->from('schedules')
                        ->where('user_id', '=', $user_id)
                        ->execute()
                        ->as_array();
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
            $inserted_rows = 0;

            $inserted_rows = \DB::insert('schedules')
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

            if ($inserted_rows > 0) {
                return $this->response(array(
                    'status' => 'success',
                    'message' => 'Schedule added successfully.',
                    'event' => $inserted_rows
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
