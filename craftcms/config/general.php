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
		'userSessionDuration' => 0
	],

	'staging' => [
		'allowAdminChanges' => true,
		'userSessionDuration' => 0
	],

	'production' => [
		'allowAdminChanges' => true,
		'userSessionDuration' => 3600
	],
	
];
