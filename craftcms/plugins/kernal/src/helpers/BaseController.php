<?php

namespace hrotti\kernal\helpers;

use Craft;

use craft\web\Controller;
use craft\web\UrlManager;
use craft\events\RegisterUrlRulesEvent;

use yii\base\Event;

trait BaseControllerTrait {

	use \hrotti\kernal\helpers\BaseHelper;

	private $_service;

	/**
	 * getService
	 *
	 * @return \craft\base\Component
	 */
	public function getService() : \craft\base\Component {

		if (!$this->_service && $this->service_name) {
			
			$name = $this->service_name;

			$this->_service = $this->plugin->$name;

		}

		return $this->_service;

	}

	/**
	 * setService
	 *
	 * @param \craft\base\Component $service 
	 *
	 * @return void
	 */
	public function setService(
		\craft\base\Component $service
	) {

		$this->_service = $service;

	}

	/**
	 * actionIndex
	 *
	 * @return mixed
	 */
	public function actionIndex() : mixed {
		
		switch ($this->request->method) {

			case "GET":

				$result = $this->service->GET($this);
				break;

			case "DELETE":

				$result = $this->service->DELETE($this);
				break;

			case "POST":

				$result = $this->service->POST($this);
				break;

		}

		return $result;

	}

}

class BaseController extends Controller {

	public $service_name = "";
	public $allowAnonymous = true;

	public $args = [];
	public $paths = [];

	use BaseControllerTrait;

}


