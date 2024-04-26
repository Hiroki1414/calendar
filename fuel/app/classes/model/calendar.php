<?php

use Orm\Model;

class Model_Calendar extends \Orm\Model
{
    protected static $_table_name = 'schedules';
    protected static $_primary_key = array('schedule_id');

    protected static $_properties = array(
        'schedule_id',
        'start_datetime',
        'end_datetime',
        'task',
        'color',
        'created_at',
        'modified_at',
        'user_id',
    );

    /**
     * 予定を日付で取得する
     * @param string $date 日付 (YYYY-MM-DD)
     * @return array 予定の配列
     */
    public static function getSchedulesByDate($date)
    {
        return static::find('all', array(
            'where' => array(
                array('start_datetime', 'LIKE', $date . '%'),
            ),
            'order_by' => array('start_datetime' => 'asc'),
        ));
    }

    /**
     * 予定を追加する
     * @param array $data 追加する予定のデータ
     * @return bool 成功した場合はtrue、失敗した場合はfalse
     */
    public static function add_schedule($data)
    {
        $schedule = static::forge($data);
        return $schedule->save();
    }

    /**
     * 予定を更新する
     * @param int $id 予定のID
     * @param array $data 更新する予定のデータ
     * @return bool 成功した場合はtrue、失敗した場合はfalse
     */
    public static function update_schedule($id, $data)
    {
        $schedule = static::find($id);
        if ($schedule) {
            $schedule->set($data);
            return $schedule->save();
        }
        return false;
    }

    /**
     * 予定を削除する
     * @param int $id 予定のID
     * @return bool 成功した場合はtrue、失敗した場合はfalse
     */
    public static function delete_schedule($id)
    {
        $schedule = static::find($id);
        if ($schedule) {
            return $schedule->delete();
        }
        return false;
    }

    // 文字列をエスケープ
    public static function h($string)
    {
        return htmlspecialchars($string, ENT_QUOTES);
    }

}

