<?php

return [

	'*' => [

		'defaultWeekStartDay' => 1,

		'omitScriptNameInUrls' => true,

		'cpTrigger' => 'cp',

		'securityKey' => getenv('SECURITY_KEY'),

		'useProjectConfigFile' => false,

	],

	'dev' => [
		'devMode' => true,
	],

	'staging' => [
		'allowAdminChanges' => true,
	],

	'production' => [
		'allowAdminChanges' => true,
	],
	
];
