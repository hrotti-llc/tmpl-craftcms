<?php

namespace hrotti\kernal;

use Craft;

use craft\base\Plugin;
use craft\services\Plugins;

use craft\web\UrlManager;
use craft\web\twig\variables\CraftVariable;

use craft\events\PluginEvent;
use craft\events\RegisterUrlRulesEvent;

use yii\base\Event;

use hrotti\kernal\services\Metadata as MetadataService;

class Kernal extends Plugin {

	static public $plugin;
	public $schemaVersion = '1.0.1';

	public function init() {

		self::$plugin = $this;

		$this->bindComponents();
		$this->bindEvents();

		parent::init();

	}

	// protected function createSettingsModel() {

	// 	return new Settings();

	// }

	protected function bindComponents() {

		return $this->setComponents([
			'metadata' => MetadataService::class,
		]);

	}

	protected function bindEvents() {

		$this->_bindUrlRules();
		$this->_bindAfterInstall();

	}

	private function _bindUrlRules() {

		Event::on(
			
			UrlManager::class,
			UrlManager::EVENT_REGISTER_SITE_URL_RULES,
			
			function (RegisterUrlRulesEvent $event) {

				$event->rules['api/kernal/metadata'] = 'kernal/metadata';

			}
	
		);

	}

	private function _bindAfterInstall() {

		Event::on(

			Plugins::class,
			Plugins::EVENT_AFTER_INSTALL_PLUGIN,

			function (PluginEvent $event) {

				if ($event->plugin === $this) {

				}

			}

		);

	}

}