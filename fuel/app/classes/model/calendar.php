<?php

class Model_Calendar extends Model
{
    // ユーザーのスケジュールを取得
    public static function get_user_schedule($user_id) {
        return DB::select()
                ->from('schedules')
                ->where('user_id', '=', $user_id)
                ->execute();
    }

    //rest/calendar.phpでのユーザーのスケジュールを取得
    public static function get_schedules_by_user($user_id) {
        return DB::select('*')
                 ->from('schedules')
                 ->where('user_id', '=', $user_id)
                 ->execute()
                 ->as_array();
    }

    public static function add_schedule($data, $user_id) {
        return DB::insert('schedules')
                 ->set(array(
                     'start' => $data['start'],
                     'end' => $data['end'],
                     'title' => $data['title'],
                     'color' => $data['color'],
                     'created_at' => date('Y-m-d H:i:s'),
                     'modified_at' => date('Y-m-d H:i:s'),
                     'user_id' => $user_id
                 ))
                 ->execute();
    }

    public static function update_schedule($data, $schedule_id) {
        return DB::update('schedules')
                 ->set(array(
                     'start' => $data['start'],
                     'end' => $data['end'],
                     'title' => $data['title'],
                     'color' => $data['color'],
                     'modified_at' => date('Y-m-d H:i:s')
                 ))
                 ->where('schedule_id', '=', $schedule_id)
                 ->execute();
    }

    public static function get_schedule($schedule_id) {
        return DB::select('*')
                 ->from('schedules')
                 ->where('schedule_id', '=', $schedule_id)
                 ->execute()
                 ->as_array();
    }

    public static function delete_schedule($schedule_id) {
        return DB::delete('schedules')
                 ->where('schedule_id', '=', $schedule_id)
                 ->execute();
    }
}