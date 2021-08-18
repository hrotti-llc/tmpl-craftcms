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

	public function getService() {

		if (!$this->_service && $this->service_name) {
			
			$name = $this->service_name;

			$this->_service = $this->plugin->$name;

		}

		return $this->_service;

	}

	public function setService(
		$service
	) {

		$this->_service = $service;

	}

	public function actionIndex() {
		
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


