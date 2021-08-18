<?php

namespace hrotti\kernal\helpers;

use Craft;

trait BaseHelper {

	private $_plugin;

	public function getPlugin() {

		if (!$this->_plugin) $this->_plugin = Craft::$app->plugins->getPlugin('kernal');

		return $this->_plugin;

	}

	public function setPlugin(
		$plugin
	) {

		$this->_plugin = $plugin;

	}

}