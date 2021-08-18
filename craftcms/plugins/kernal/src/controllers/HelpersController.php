<?php

namespace hrotti\kernal\controllers;

use Craft;

use craft\web\Controller;
use craft\web\UrlManager;
use craft\events\RegisterUrlRulesEvent;

use yii\base\Event;

class HelpersController extends \hrotti\kernal\helpers\BaseController {

	public function actionTags() {
		
		switch ($this->request->method) {

			case 'GET':

				$result = $this->service->GET($this, 'tags');
				break;

		}

		return $result;

	}

	public function actionCategories() {
		
		switch ($this->request->method) {

			case 'GET':

				$result = $this->service->GET($this, 'categories');
				break;

		}

		return $result;

	}

}
