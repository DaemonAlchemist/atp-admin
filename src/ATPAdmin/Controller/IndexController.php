<?php

namespace ATPAdmin\Controller;

class IndexController extends \ATPCore\Controller\AbstractController
{
	private function init()
	{
		//Set the admin layout
		$this->layout("atp-admin/layout/admin");
		
		//Get the model information
		$this->models = $this->config('admin.models');
		
		//Get the flash messenger
		$this->flash = $this->flashMessenger();
		
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

	public function indexAction()
	{
		$this->init();
	
		//echo "<pre>";print_r($this->config('admin.models'));die();
		return new \Zend\View\Model\ViewModel(array(
			'flash' => $this->flash
		));
	}
	
	public function listAction()
	{
		$this->init();		
		
		//Load the objects
		$modelClass = $this->modelData['class'];
		$obj = new $modelClass();
		$objects = $obj->loadMultiple(null, array(), array(), $this->modelData['defaultOrder']);
		
		return new \Zend\View\Model\ViewModel(array(
			'flash' => $this->flash,
			'model' => $this->modelType,
			'modelData' => $this->modelData,
			'objects' => $objects
		));
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
				$this->flash->addMessage($this->modelType . " " . $object->identity() . " saved.");
				$this->redirect()->toRoute('admin', array(
					'action' => 'edit',
					'model' => $this->modelType,
					'id' => $object->id
				));
			} catch(\Exception $e) {
				$this->flash->addMessage("Error saving " . $this->modelType . " " . $object->identity . ": " . $e->getMessage());
				$this->redirect()->toRoute('admin', array(
					'action' => 'edit',
					'model' => $this->modelType,
					'id' => $object->id
				));
			}
		}
		
		return new \Zend\View\Model\ViewModel(array(
			'flash' => $this->flash,
			'model' => $this->modelType,
			'modelData' => $this->modelData,
			'object' => $object
		));		
	}
	
	public function deleteAction()
	{
		$this->init();
		
		//Load the object
		$modelClass = $this->modelData['class'];
		$object = new $modelClass($this->params('id'));
		
		try {
			$object->delete();
			$this->flash->addMessage($this->modelType . " " . $object->identity() . " deleted.");
			$this->redirect()->toRoute('admin', array(
				'action' => 'list',
				'model' => $this->modelType,
			));
		} catch(\Exception $e) {
			$this->flash->addMessage("Error deleting " . $this->modelType . " " . $object->identity() . ": " . $e->getMessage());
			$this->redirect()->toRoute('admin', array(
				'action' => 'list',
				'model' => $this->modelType,
			));
		}
	}
}