<?php

class Model_User extends \Orm\Model
{
    protected static $_properties = [
        'user_id' => [
            'data_type' => 'int',
            'label' => 'User ID',
            'form' => array('type' => 'hidden'),
            'validation' => array('required', 'valid_string' => array('numeric')),
        ],
        'username' => [
            'data_type' => 'varchar',
            'validation' => array('required', 'max_length' => array(255))
        ],
        'password' => [
            'data_type' => 'varchar',
            'validation' => array('required', 'max_length' => array(255))
        ],
        'email' => [
            'data_type' => 'varchar',
            'validation' => array('required', 'valid_email')
        ],
        'created_at' => [
            'data_type' => 'datetime',
            'validation' => array('valid_date')
        ],
        'last_login' => [
            'data_type' => 'datetime',
            'validation' => array('valid_date'),
            'default' => null 
        ]
    ];

    protected static $_primary_key = ['user_id']; 
}