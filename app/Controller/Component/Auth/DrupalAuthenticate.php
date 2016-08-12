<?php 
App::uses('BaseAuthenticate', 'Controller/Component/Auth');
App::uses('HttpSocket', 'Network/Http');

class DrupalAuthenticate extends BaseAuthenticate {
	
	/**
	 * Drupal authentication method to authenticate a user based on the request information.
	 *
	 * @param CakeRequest $request Request to get authentication information from.
	 * @param CakeResponse $response A response object that can have headers added.
	 * @return mixed Either false on failure, or an array of user data on success.
	 */
	
    public function authenticate(CakeRequest $request, CakeResponse $response) {
    	$HttpSocket = new HttpSocket();
    	
    	// Set data
    	$data = array(
    			'username' => $request['User']['username'],
    			'password' => $request['User']['password']
    	);

    	
    	// JSON encode data
    	$data = json_encode($data);
    	
    	// Set request options
    	$request = array(
    			'header' => array(
    					'Content-Type' => 'application/json'
    			)
    	);
    	$results = $HttpSocket->post(Configure::read('rest_login'), $data, $request);
debug($results);
    	$results = json_decode($results);
debug($results);    	
    	
    	if ($results !== false) { return $results; }
    }
}