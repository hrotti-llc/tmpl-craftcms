<?php

namespace hrotti\kernal\helpers;

use Craft;

trait SecurityHelper {

	public function getProjectSecretKey(): string {
	
		return Craft::$app->getConfig()->getGeneral()->securityKey;

	}
	
	public function encrypt(string $value, string $salt = null, bool $baseEncode = true) {

		$encrypted = \Craft::$app->getSecurity()->encryptByKey($value, $this->getProjectSecretKey().$salt);

		return $baseEncode ? base64_encode($encrypted) : $encrypted;

	}

}