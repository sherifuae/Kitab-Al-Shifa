<?php
App::uses('AppModel', 'Model');
/**
 * BillingsItem Model
 *
 * @property Billings $Billings
 */
class BillingsItem extends AppModel {

	/**
	 * Validation rules
	 *
	 * @var array
	 */
	public $validate = array(
			'service_date' => array(
					'date' => array(
							'rule' => array('date'),
							//'message' => 'Your custom message here',
							//'allowEmpty' => false,
							'required' => false,
							//'last' => false, // Stop validation after this rule
							//'on' => 'create', // Limit validation to 'create' or 'update' operations
					),
			),
	);
	//The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'Billing' => array(
			'className' => 'Billing',
			'foreignKey' => 'billing_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
 
	public function beforeValidate($options = array()) {
		if (!empty($this->data['BillingsItem']['service_date'])) {
			$this->data['BillingsItem']['service_date'] = $this->dateFormatBeforeSave($this->data['BillingsItem']['service_date']);
		}
		return true;
	}
	
	public function dateFormatBeforeSave($dateString) {
		return date('Y-m-d', strtotime($dateString));
	}
}
