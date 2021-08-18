<?php

namespace hrotti\kernal\helpers;

use Craft;

use Solspace\ExpressForms\decorators\Forms\Extras\PreventDuplicateSubmissionsDecorator;

trait ExpressFormsHelper {

	use \hrotti\kernal\helpers\SecurityHelper;

	function getFormByHandle(
		$handle
	) {

		return Craft::$app->plugins->getPlugin('express-forms')->forms->getFormByHandle($handle);

	}

	function getFormHash(
		$updateSession = true
	) {

		$prefix = PreventDuplicateSubmissionsDecorator::PREFIX;
		$hash = Craft::$app->plugins->getPlugin('express-forms')->container->get('Solspace\ExpressForms\providers\Security\HashingInterface')->getUuid4();

		if ($updateSession) $this->appendToSession($prefix . $hash);

		return $prefix . $hash;

	}

	public function appendToSession(
		string $value
	) {

		$sortedByTime = [];

		if (isset($_SESSION)) {

			foreach ($_SESSION as $key => $ttl) {

				if ($this->isHashedToken($key)) $sortedByTime[$ttl] = $key;

			}

		}

		ksort($sortedByTime, \SORT_DESC);

		if (\count($sortedByTime) > 40) {

			while (\count($sortedByTime) > 40) {

				$key = array_pop($sortedByTime);

				Craft::$app->getSession()->remove($key);

			}

		}

		Craft::$app->getSession()->set($value, time());

	}

	private function isHashedToken(
		$key
	) {
		
		$prefix = PreventDuplicateSubmissionsDecorator::PREFIX;

		return preg_match("/^$prefix/", $key);
	
	}

	function getFormPayload(
		$handle, 
		$form = null
	) {

		$form = $form ?? $this->getFormByHandle($handle);

		$data = [
			'attributes' => $form->getHtmlAttributes()->toArray(),
			'parameters' => $form->getParameters()->toArray(),
		];

		$serialized = \GuzzleHttp\json_encode($data);

		return $this->encrypt($serialized, $form->getUuid());

	}

	function getFormBasics(
		$handle,
		$form = null
	) {

		$form = $form ?? $this->getFormByHandle($handle);

		return [
			'uuid' => $form->getUuid(),
			'payload' => $this->getFormPayload($handle, $form),
			'hash' => $this->getFormHash()
		];

	}

}