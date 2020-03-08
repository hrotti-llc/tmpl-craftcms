<?php

namespace modules;

use Craft;

class Module extends \yii\base\Module {

	public function init() {

		Craft::setAlias('@modules', __DIR__);

		if (Craft::$app->getRequest()->getIsConsoleRequest()) {

			$this->controllerNamespace = 'modules\\console\\controllers';

		} else {

			$this->controllerNamespace = 'modules\\controllers';

		}

		parent::init();

	}

}
