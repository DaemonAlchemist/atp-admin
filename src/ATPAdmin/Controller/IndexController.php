<?php

namespace ATPAdmin\Controller;

class IndexController extends \ATPCore\Controller\AbstractController
{
	private $_checkLogin = true;

	private function init($checkLogin = true)
	{
		//Check for logged in user
		if($checkLogin && $this->_checkLogin && !\ATPAdmin\Auth::isLoggedIn())
		{
			$this->redirect()->toRoute('admin', array('action' => 'login'));
		}
	
		//Set the admin layout
		$this->layout("atp-admin/layout/admin");
		
		//Get the model information
		$this->models = $this->config('admin.models');
		
		//Setup the view
		$this->view = new \Zend\View\Model\ViewModel();
		
		//Get the flash messenger
		$this->flash = $this->flashMessenger();
		$this->layout()->addChild(new \ATPCore\View\Widget\FlashWidget($this->flash), 'flash');
		
		//Create the admin menu
		$adminMenu = array();
		foreach($this->models as $model => $modelData)
		{
			if(!isset($adminMenu[$modelData['category']])) $adminMenu[$modelData['category']] = array();
			$adminMenu[$modelData['category']][] = array(
				'name' => $modelData['displayName'],
				'model' => $model
			);
		}		
		$this->layout()->menu = $adminMenu;
		
		//Load the model data if needed
		$this->modelType = $this->params('model');
		if(!empty($this->modelType))
		{
			$this->modelData = $this->models[$this->modelType];
		}
	}

	public function loginAction()
	{
		$this->init(false);
		
		$this->layout("atp-admin/layout/blank");
		
		if(!\ATPAdmin\Model\User::hasUsers())
		{
			$this->_checkLogin = false;
			$this->forward()->dispatch('ATPAdmin\Controller\IndexController', array('action' => 'edit', 'model' => 'admin_users'));
		}
		
		if(count($_POST) > 0)
		{
			$user = \ATPAdmin\Auth::authenticatedUser($_POST['username'], $_POST['password']);
			if(!is_null($user))
			{
				\ATPAdmin\Auth::login($user);
				$this->redirect()->toRoute('admin', array('action' => 'index'));
			}
			else
			{
				$this->flash->addErrorMessage("Incorrect login information");
				$this->redirect()->toRoute('admin', array('action' => 'login'));
			}
		}
		
		return $this->view;
	}
	
	public function indexAction()
	{
		$this->init();
	
		return $this->view;
	}
	
	public function listAction()
	{
		$this->init();		
		
		//Load the objects
		$modelClass = $this->modelData['class'];
		$obj = new $modelClass();
		$objects = $obj->loadMultiple(null, array(), array(), $this->modelData['defaultOrder']);
		
		$this->view->model = $this->modelType;
		$this->view->modelData = $this->modelData;
		$this->view->objects = $objects;
		return $this->view;
	}
	
	public function editAction()
	{
		$this->init();
		
		//Load the object
		$modelClass = $this->modelData['class'];
		$object = new $modelClass($this->params('id'));
		
		if(count($_POST) > 0)
		{
			$data = $_POST;
			foreach($_FILES as $name => $fileData)
			{
				$data[$name] = $fileData;
			}
			
			try {
				$object->setFrom($data);
				$object->save();
				$this->flash->addSuccessMessage($this->modelType . " " . $object->identity() . " saved.");
				$this->redirect()->toRoute('admin', array(
					'action' => 'edit',
					'model' => $this->modelType,
					'id' => $object->id
				));
			} catch(\Exception $e) {
				$this->flash->addErrorMessage("Error saving " . $this->modelType . " " . $object->identity . ": " . $e->getMessage());
				$this->redirect()->toRoute('admin', array(
					'action' => 'edit',
					'model' => $this->modelType,
					'id' => $object->id
				));
			}
		}
		
		$this->view->model = $this->modelType;
		$this->view->modelData = $this->modelData;
		$this->view->object = $object;
		return $this->view;
	}
	
	public function deleteAction()
	{
		$this->init();
		
		//Load the object
		$modelClass = $this->modelData['class'];
		$object = new $modelClass($this->params('id'));
		
		try {
			$object->delete();
			$this->flash->addSuccessMessage($this->modelType . " " . $object->identity() . " deleted.");
			$this->redirect()->toRoute('admin', array(
				'action' => 'list',
				'model' => $this->modelType,
			));
		} catch(\Exception $e) {
			$this->flash->addErrorMessage("Error deleting " . $this->modelType . " " . $object->identity() . ": " . $e->getMessage());
			$this->redirect()->toRoute('admin', array(
				'action' => 'list',
				'model' => $this->modelType,
			));
		}
	}
}
