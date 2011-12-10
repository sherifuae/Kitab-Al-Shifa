<?php
class User extends AppModel
{
	public $actsAs = array('Containable');
	public $useDbConfig = 'joomla';
	public $useTable = 'users';
	public $hasOne = array(
			'Profile' => array(
				'className' => 'Profile',
				'foreignKey' => 'user_id',
				'conditions' => '',
				'fields' => '',
				'order' => '')
	);
	var $hasMany = array('Shifts');
	var $displayField = 'name';
	public $order = array('block' => 'ASC');

	public $validate = array(
		'username' => array(
			'required' => array(
				'rule' => array('notEmpty'),
				'message' => 'A username is required'
			)
		),
		'password' => array(
			'required' => array(
				'rule' => array('notEmpty'),
				'message' => 'A password is required'
			)
		),
	);
}

?>