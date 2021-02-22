<?php


return [
	
	'modules' => [
		'my-module' => \modules\Module::class,
	],

	'components' => [

		'mutex' => function() {

			$config = craft\helpers\App::mutexConfig();
			$config['isWindows'] = true;

			return Craft::createObject($config);
			
		},
		
	],

];
