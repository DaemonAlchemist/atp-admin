<?php

namespace ATPAdmin\Controller;

class IndexController extends \ATPCore\Controller\AbstractController
{
	private $_checkLogin = true;

	protected function init($checkLogin = true)
	{
		//Set password salt for user
		\ATPAdmin\Model\User::setPasswordSalt($this->config('admin.auth.password_salt'));
		
		//Check for logged in user
		if($checkLogin && $this->_checkLogin && !\ATPAdmin\Auth::isLoggedIn())
		{
			$this->redirect()->toRoute('admin', array('action' => 'login'));
		}
	
		//Set the admin layout
		$this->layout("atp-admin/layout/admin");
		
		//Get the model information
		$this->models = $this->config('admin.models');
		$this->reports = $this->config('admin.reports');
		
		//Setup the view
		$this->view = new \Zend\View\Model\ViewModel();
		
		//Get the flash messenger
		$this->flash = $this->flashMessenger();
		$this->layout()->addChild(new \ATPCore\View\Widget\FlashWidget($this->flash), 'flash');
		
		//Create the admin menu
		$adminMenu = array();
		
		//Add the models
		foreach($this->models as $model => $modelData)
		{
			if(!isset($adminMenu[$modelData['category']])) $adminMenu[$modelData['category']] = array();
			
			$linkData = array('action' => 'list', 'model' => \ATP\Inflector::underscore($model));
			if(isset($modelData['custom_actions']['list']))
			{
				$linkData['controller'] = $modelData['custom_actions']['list']['controller'];
				$linkData['action'] = $modelData['custom_actions']['list']['action'];
			}
			
			$adminMenu[$modelData['category']][] = array(
				'label' => \ATP\Inflector::pluralize($modelData['displayName']),
				'linkData' => $linkData,
			);
		}		
		
		//Add the reports
		foreach($this->reports as $report => $reportData)
		{
			if(!isset($adminMenu[$reportData['category']])) $adminMenu[$reportData['category']] = array();
			$adminMenu[$reportData['category']][] = array(
				'label' => $reportData['label'],
				'linkData' => array('action' => 'report', 'model' => $report)
			);
		}
		
		$this->layout()->menu = $adminMenu;
		
		//Load the model data if needed
		$this->modelType = $this->params('model');
		if(!empty($this->modelType) && $this->params('action') != 'report')
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
			$this->forward()->dispatch('default-admin', array('action' => 'edit', 'model' => 'atpadmin_user'));
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
		$objects = $obj->loadMultiple(array(
			'orderBy' => $this->modelData['defaultOrder']
		));
		
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
		$object = new $modelClass();
		$object->loadById($this->params('id'));
		
		if(count($_POST) > 0)
		{
			$data = $_POST['modelData'];
			
			if(count($_FILES) > 0) 
			{
				//Reorganize files array
				$files = array();
				foreach($_FILES['modelData'] as $var => $inputs)
				{
					foreach($inputs as $input => $value)
					{
						$files[$input][$var] = $value;
					}
				}
				
				//Copy files into data array
				foreach($files as $name => $fileData)
				{
					$data[$name] = $fileData;
				}
			}

			try {
				$object->setFrom($data);
				$object->save();
				
				if(isset($this->modelData['postSaveListeners']))
				{
					foreach($this->modelData['postSaveListeners'] as $callback)
					{
						$class = $callback['class'];
						$func = $callback['func'];
						$callbackObj = new $class();
						$callbackObj->$func($object);
					}
				}
				
				$this->flash->addSuccessMessage($this->modelData['displayName'] . " " . $object->displayName() . " saved.");
				$this->redirect()->toRoute('admin', array(
					'action' => isset($_POST['save-and-edit']) ? 'edit' : 'list',
					'model' => $this->modelType,
					'id' => isset($_POST['save-and-edit']) ? $object->id : null
				));
			} catch(\Exception $e) {
				$this->flash->addErrorMessage("Error saving " . $this->modelData['displayName'] . " " . $object->displayName() . ": " . $e->getMessage());
				$this->redirect()->toRoute('admin', array(
					'action' => 'edit',
					'model' => $this->modelType,
					'id' => $object->id
				));
			}
		}
		
		//Set default field types
		foreach($object->dataColumns() as $column)
		{
			if(!isset($this->modelData['fields'])) $this->modelData['fields'] = array();
			if(!isset($this->modelData['fields'][$column])) $this->modelData['fields'][$column] = array();
			if(!isset($this->modelData['fields'][$column]['label'])) $this->modelData['fields'][$column]['label'] = ucwords(str_replace("_", " ", $column));
			
			if(!isset($this->modelData['fields'][$column]['type']))
			{
				$def = $object->getDefinition();
				
				$columnType = $def['columns'][$column];
				
				$type = "Text";
				if(strpos($column, 'password') !== false)		$type = "Password";
				elseif(strpos($columnType, "tinyint(1)") === 0)	$type = "Boolean";
				elseif(strpos($column, 'html') !== false)		$type = "Html";
				elseif(strpos($columnType, 'text') !== false)	$type = "Textarea";
				elseif(strpos($column, '_file') !== false)		$type = "File";
				elseif(strpos($columnType, 'date') !== false)	$type = "Date";
				
				$this->modelData['fields'][$column]['type'] = $type;
			}
		}
		
		//Set tab grouping for fields
		if(!isset($this->modelData['tabs']))
		{
			$this->modelData['tabs'] = array(
				'Details' => array_merge(
					array_keys($object->ownerFields()),
					$object->dataColumns()
				),
			);			
		}
		
		//Detect custom tabs
		if(!isset($this->modelData['customTabs'])) $this->modelData['customTabs'] = array();
		
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
		$object = new $modelClass();
		$object->loadById($this->params('id'));
		
		try {
			$object->delete();
			$this->flash->addSuccessMessage($this->modelData['displayName'] . " " . $object->displayName() . " deleted.");
			$this->redirect()->toRoute('admin', array(
				'action' => 'list',
				'model' => $this->modelType,
			));
		} catch(\Exception $e) {
			$this->flash->addErrorMessage("Error deleting " . $this->modelData['displayName'] . " " . $object->displayName() . ": " . $e->getMessage());
			$this->redirect()->toRoute('admin', array(
				'action' => 'list',
				'model' => $this->modelType,
			));
		}
	}

	public function reportAction()
	{
		$this->init();
		
		$report = $this->modelType;
		$reportInfo = $this->config("admin.reports.{$report}");
		
		$reportClass = $reportInfo['class'];
		$report = new $reportClass();
		
		$data = $report->getData();
		
		$this->view->columns = $data['columns'];
		$this->view->data = $data['data'];
		$this->view->label = $reportInfo['label'];
		
		return $this->view;
	}
}
