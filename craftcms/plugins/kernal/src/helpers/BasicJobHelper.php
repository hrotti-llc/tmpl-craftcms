<?php

namespace hrotti\kernal\helpers;

use Craft;

trait BasicJobHelper {

	public $result = true;

	public $entries;

	private $queue;
	private $plugin;

	public function execute($queue) {

		$this->queue = $queue;

		$this->bootstrap($queue);
		$this->doJob();

		return $this->result;

	}

	public function bootstrap($queue) {

		$this->queue = $queue;
		$this->plugin = Craft::$app->plugins->getPlugin('kernal');

		$this->entries = $this->findEntries();

	}

	public function log(
		$message, 
		$level = "info"
	) {

		Craft::$level($message);
		Craft::getLogger()->flush(true);

	}

	public function warn(
		$message
	) {

		$this->log($message, 'warning');

	}

	public function getCfg() {

		return $this->plugin->settings;

	}

	protected function doJob() {

	}

	protected function findEntries() {

	}

}