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
}
