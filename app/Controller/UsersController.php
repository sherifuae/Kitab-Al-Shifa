<?php
App::uses('AppController', 'Controller');
/**
 * Users Controller
 *
 * @property User $User
 */
class UsersController extends AppController {
	var $components = array('RequestHandler', 'Flash');

/**
 * index method
 *
 * @return void
 */
	public function index() {
		$this->User->recursive = 1;
		$this->set('users', $this->paginate());
	}

/**
 * view method
 *
 * @param string $id
 * @return void
 */
	public function view($id = null) {
		$this->User->id = $id;
		if (!$this->User->exists()) {
			throw new NotFoundException(__('Invalid user'));
		}
		$this->set('user', $this->User->read(null, $id));
	}

/**
 * add method
 *
 * @return void
 */
	public function add() {
		if ($this->request->is('post')) {
			$this->User->create();
			if ($this->User->save($this->request->data)) {
				$this->Session->setFlash(__('The user has been saved'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The user could not be saved. Please, try again.'));
			}
		}
	}

/**
 * edit method
 *
 * @param string $id
 * @return void
 */
	public function edit($id = null) {
		$this->User->id = $id;
		if (!$this->User->exists()) {
			throw new NotFoundException(__('Invalid user'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->User->save($this->request->data)) {
				$this->Session->setFlash(__('The user has been saved'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The user could not be saved. Please, try again.'), 'alert');
			}
		} else {
			$this->request->data = $this->User->read(null, $id);
		}
	}

	/**
	 * Preferences method
	 *
	 * @param string $id
	 * @return void
	 */
	public function preferences($id = null) {
		$this->loadModel('Calendar');
		// If user is an administrator, allow the editing of other users' preferences
		if ($this->_isAdmin()) {
			$this->User->id = (isset($this->request->query['id']) ? $this->request->query['id'] : $this->_usersId());
		}
		else {
			$this->User->id = $this->_usersId();
		}
		if (!$this->User->exists()) {
			throw new NotFoundException(__('Invalid user'));
		}
		if (($this->request->is('post') || $this->request->is('put')) && isset($this->request->data['Preference'])) {
			foreach($this->request->data['Preference'] as $key => $value) {
				if ($this->User->Preference->saveSection($this->User->id, array('Preference' => array($key => $value)), 'ShiftLimit')) {
					$this->Flash->success(__('Your preferences have been saved'), 'success');
				} else {
					$this->Flash->alert(__('Your preferences could not be saved. Please, try again. If you still have issues, please report them'), 'alert');
				}
			}
		} else {
			$this->request->data = $this->User->read(null, $id);
		}
		$this->User->recursive = 0;
		$this->set('user', $this->User->read(null, $id));
		$this->set('calendars',
				$this->Calendar->find('list', array(
						'fields' => array('Calendar.id', 'Calendar.name'),
						'order'=>array('Calendar.start_date ASC'),
						'conditions' => array('Calendar.end_date >=' => date('Y-m-d', strtotime('now')))
				)));
		$this->set('preference', $this->User->Preference->getSection($this->User->id, 'ShiftLimit'));
		$this->render();
	}

/**
 * delete method
 *
 * @param string $id
 * @return void
 */
	public function delete($id = null) {
		if (!$this->request->is('post')) {
			throw new MethodNotAllowedException();
		}
		$this->User->id = $id;
		if (!$this->User->exists()) {
			throw new NotFoundException(__('Invalid user'));
		}
		if ($this->User->delete()) {
			$this->Session->setFlash(__('User deleted'));
			$this->redirect(array('action'=>'index'));
		}
		$this->Session->setFlash(__('User was not deleted'), 'alert');
		$this->redirect(array('action' => 'index'));
	}
/**
 * admin_index method
 *
 * @return void
 */
	public function admin_index() {
		$this->User->recursive = 0;
		$this->set('users', $this->paginate());
	}

/**
 * admin_view method
 *
 * @param string $id
 * @return void
 */
	public function admin_view($id = null) {
		$this->User->id = $id;
		if (!$this->User->exists()) {
			throw new NotFoundException(__('Invalid user'));
		}
		$this->set('user', $this->User->read(null, $id));
	}

/**
 * admin_add method
 *
 * @return void
 */
	public function admin_add() {
		if ($this->request->is('post')) {
			$this->User->create();
			if ($this->User->save($this->request->data)) {
				$this->Session->setFlash(__('The user has been saved'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The user could not be saved. Please, try again.'), 'alert');
			}
		}
	}

/**
 * admin_edit method
 *
 * @param string $id
 * @return void
 */
	public function admin_edit($id = null) {
		$this->User->id = $id;
		if (!$this->User->exists()) {
			throw new NotFoundException(__('Invalid user'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->User->save($this->request->data)) {
				$this->Session->setFlash(__('The user has been saved'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The user could not be saved. Please, try again.'), 'alert');
			}
		} else {
			$this->request->data = $this->User->read(null, $id);
		}
	}

/**
 * admin_delete method
 *
 * @param string $id
 * @return void
 */
	public function admin_delete($id = null) {
		if (!$this->request->is('post')) {
			throw new MethodNotAllowedException();
		}
		$this->User->id = $id;
		if (!$this->User->exists()) {
			throw new NotFoundException(__('Invalid user'));
		}
		if ($this->User->delete()) {
			$this->Session->setFlash(__('User deleted'));
			$this->redirect(array('action'=>'index'));
		}
		$this->Session->setFlash(__('User was not deleted'), 'alert');
		$this->redirect(array('action' => 'index'));
	}

	public function login() {
		if ($this->Auth->login()) {
			$this->redirect($this->Auth->redirect());
		} else {
			if ($this->request->isPost()) {
				$this->Session->setFlash(__('Invalid username or password, try again'), 'alert');
			}
			else {
			}

		}
	}

	public function logout() {
		$this->redirect($this->Auth->logout());
	}

	public function listUsers() {
		$userOptions = array();
		$full = null;
		$conditions = array();
		$userList = array();
		$excludeShift = null;

		if (isset($this->request->query['full'])) {
 				$full = true;
		}
		if (isset($this->request->query['term'])) {
			$conditions = array(
				'or' =>
					array('Profile.lastname LIKE' => $this->request->query['term'].'%',
						'Profile.firstname LIKE' => $this->request->query['term'].'%',
						'Profile.cb_displayname LIKE' => $this->request->query['term'].'%')
			);
		}
		if (isset($this->request->query['excludeShift'])) {
			$excludeShift = $this->request->query['excludeShift'];
		}

		if (isset($this->request->query['group'])) {
			$users = $this->User->getActiveUsersForGroup($this->request->query['group'], $full, $conditions, false, $excludeShift);
		}
		else {
			$users = $this->User->getList($conditions, $full);
		}

		foreach ($users as $user) {
			if ($full) {
				$userList[] = array('value' => $user['User']['id'], 'label' => $user['Profile']['firstname'] . ' ' . $user['Profile']['lastname']);
			}
			else {
				$userList[] = array('value' => $user['User']['id'], 'label' => $user['Profile']['cb_displayname']);
			}
		}
		$this->set('userList', $userList);
		$this->set('_serialize', 'userList');
	}
}
