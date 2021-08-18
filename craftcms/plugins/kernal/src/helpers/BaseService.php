<?php

namespace hrotti\kernal\helpers;

use Craft;

use craft\base\Component;
use craft\web\Request;

use craft\web\UrlManager;
use craft\events\RegisterUrlRulesEvent;

use yii\base\Event;

trait BaseServiceTrait {

	use \hrotti\kernal\helpers\BaseHelper;

	// Public Methods
	// =========================================================================

	public function strap(
		object $plugin
	) {	

		$this->plugin = $plugin;

		$this->setupModels();

	}

	public function setupModels(
		$models = null
	) {

		$models = $models ?? $this->models;

		foreach ($models as $model) $model::$plugin = $this->plugin;

	}

	public function GET(
		$controller,
		$id = null
	) {	

		$result = null;

		if ($this->secureRequest($controller->request)) {

			$result = ($controller->request->getIsGraphql()) ? $this->resolveGQLGet($controller->request, $id) : $this->resolveJSONGet($controller->request, $id);

		} else {

			$result = $this->auth->errors;

		}

		return $this->respond($controller, $result);

	}

	public function POST(
		$controller,
		$id = null
	) {

		$result = null;

		if ($this->secureRequest($controller->request)) {

			$result = ($controller->request->getIsGraphql()) ? $this->resolveGQLPost($controller->request, $id) : $this->resolveJSONPost($controller->request, $id);

		} else {

			$result = $this->auth->errors;

		}

		return $this->respond($controller, $result);

	}

	public function DELETE(
		$controller,
		$id = null
	) {

		$result = null;

		if ($this->secureRequest($controller->request)) {

			$result = ($controller->request->getIsGraphql()) ? $this->resolveGQLDelete($controller->request, $id) : $this->resolveJSONDelete($controller->request, $id);

		} else {

			$result = $this->auth->errors;

		}

		return $this->respond($controller, $result);

	}

	public function getBodyParams() {

		return Craft::$app->request->getBodyParams();

	}

	public function getUser(
		$id = null
	) {

		return ($id !== null) ? null : Craft::$app->getUser()->getIdentity();

	}

	public function getCfg() {

		return $this->plugin->settings;

	}

	// Protected Methods
	// =========================================================================

	protected function resolveGQLGet(
		Request $request
	) {

		return null;

	}

	protected function resolveGQLPost(
		Request $request
	) {

		return null;

	}

	protected function resolveGQLDelete(
		Request $request
	) {

		return null;

	}

	protected function resolveJSONGet(
		object $request, 
		$id
	) {

		$result = null;

		switch ($id) {

			default:

				$result = $this->resolveDefaultJSONGet($request);
				break;

		}

		return $result;

	}

	protected function resolveDefaultJSONGet(
		object $request
	) {

		return null;

	}

	protected function resolveJSONPost($request, $id) {

		$result = null;

		switch ($id) {

			default:

				$result = $this->resolveDefaultJSONPost($request);
				break;

		}

		return $result;

	}

	protected function resolveDefaultJSONPost(
		$request
	) {

		return null;

	}

	protected function resolveJSONDelete(
		$request, 
		$id
	) {

		$result = null;

		switch ($id) {

			default:

				$result = $this->resolveDefaultJSONDelete($request);
				break;

		}

		return $result;

	}

	protected function resolveDefaultJSONDelete(
		$request
	) {

		return null;

	}

	protected function secureRequest(
		Request $request
	) {

		return true;

		//return $this->isAuthorizedAPIRequest($request);

	}

	protected function respond(
		$controller, 
		$payload
	) {	

		$pretty = false;

		if (in_array('pretty', array_keys($controller->request->resolve()[1])) && $controller->request->resolve()[1]['pretty'] = 'true') $pretty = true;

		$this->respondAsJson($controller, $payload, $pretty);

	}

	protected function respondAsJson(
		$controller, 
		$payload,
		$pretty = false
	) {
		
		if ($pretty) $payload = '<pre>'. json_encode($payload, JSON_PRETTY_PRINT) . '</pre>';

		$controller->response->format = ($pretty) ? \yii\web\Response::FORMAT_HTML : \yii\web\Response::FORMAT_JSON;
		$controller->response->data = $payload;

		return $controller->response;
	
	}

	protected function setStatus(
		$code
	) {

		Craft::$app->getResponse()->setStatusCode($code);
		
	}

}

class BaseService extends Component {

	use BaseServiceTrait;

	public $models = [];

}