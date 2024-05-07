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
            $events = \Model_Calendar::get_schedules_by_user($user_id);
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
        $data = $_POST;

        list(, $user_id) = Auth::instance()->get_user_id();

        try {
            $event_id = \Model_Calendar::add_schedule($data, $user_id);

            if ($event_id) {
                return $this->response(array(
                    'status' => 'success',
                    'message' => 'Schedule added successfully.',
                    'event' => $$event_id
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
        if (!$id) {
            Response::redirect('welcome/404');
        }

        $json = file_get_contents('php://input');
        $data = json_decode($json, true);

        $update = \Model_Calendar::update_schedule($data, $id);

        if ($update) {
            $updatedEvent = \Model_Calendar::get_schedule($id);
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
            return $this->response(array(
                'status' => 'error',
                'message' => 'Invalid event ID.'
            ), 400);
        }

        $delete = \Model_Calendar::delete_schedule($id);

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
