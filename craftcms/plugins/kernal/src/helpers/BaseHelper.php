<?php

namespace hrotti\kernal\helpers;

use Craft;

trait BaseHelper {

	private $_plugin;

	/**
	 * getPlugin
	 *
	 * @return \craft\base\Plugin
	 */
	public function getPlugin() : \craft\base\Plugin {

		if (!$this->_plugin) $this->_plugin = Craft::$app->plugins->getPlugin('kernal');

		return $this->_plugin;

	}

	/**
	 * setPlugin
	 *
	 * @param \craft\base\Plugin $plugin 
	 *
	 * @return void
	 */
	public function setPlugin(
		\craft\base\Plugin $plugin
	) {

		$this->_plugin = $plugin;

	}

}