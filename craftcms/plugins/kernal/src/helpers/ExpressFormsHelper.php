<?php

namespace hrotti\kernal\helpers;

use Craft;

trait ExpressFormsHelper {

	use \hrotti\kernal\helpers\SecurityHelper;

	function getFormByHandle($handle) {

		return Craft::$app->plugins->getPlugin('express-forms')->forms->getFormByHandle($handle);

	}

	function getFormPayload($handle, $form = null) {

		$form = $form ?? $this->getFormByHandle($handle);

		$data = [
			'attributes' => $form->getHtmlAttributes()->toArray(),
			'parameters' => $form->getParameters()->toArray(),
		];

		$serialized = \GuzzleHttp\json_encode($data);

		return $this->encrypt($serialized, $form->getUuid());

	}

	function getFormBasics($handle, $form = null) {

		$form = $form ?? $this->getFormByHandle($handle);

		return [
			'uuid' => $form->getUuid(),
			'payload' => $this->getFormPayload($handle, $form)
		];

	}

}