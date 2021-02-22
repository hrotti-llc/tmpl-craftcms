<?php

namespace hrotti\kernal\controllers;

use Craft;

use craft\web\Controller;
use craft\web\UrlManager;
use craft\events\RegisterUrlRulesEvent;

use yii\base\Event;

class MetadataController extends Controller {

	public $plugin;
	public $service;

	public $args = [];

	public function init() {

		$this->allowAnonymous = true;

		parent::init();

		$this->plugin = Craft::$app->plugins->getPlugin('kernal');
		$this->service = $this->plugin->metadata;

	}

	public function actionIndex() {
		

		switch ($this->request->method) {

			case 'GET':

				$result = $this->service->GET($this);
				break;

		}

		return $result;

	}

}
