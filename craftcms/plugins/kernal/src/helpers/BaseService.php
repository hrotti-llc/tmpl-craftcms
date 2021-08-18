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

	/**
	 * strap
	 *
	 * @param \craft\base\Plugin $plugin 
	 *
	 * @return void
	 */
	function strap(
		\craft\base\Plugin $plugin
	) {	

		$this->plugin = $plugin;

		$this->setupModels();

	}
	
	/**
	 * setupModels
	 *
	 * @param array|null $models 
	 *
	 * @return void
	 */
	public function setupModels(
		array $models = null
	) {

		$models = $models ?? $this->models;

		foreach ($models as $model) $model::$plugin = $this->plugin;

	}

	/**
	 * GET
	 *
	 * @param \craft\web\Controller $controller 
	 * @param string|mixed $slug 
	 *
	 * @return \craft\web\Response
	 */
	public function GET(
		\craft\web\Controller $controller,
		string $slug = null
	) : \craft\web\Response {	

		$result = null;

		if ($this->secureRequest($controller->request)) {

			$result = ($controller->request->getIsGraphql()) ? $this->resolveGQLGet($controller->request, $slug) : $this->resolveJSONGet($controller->request, $slug);

		} else {

			$result = $this->auth->errors;

		}

		return $this->respond($controller, $result);

	}

	/**
	 * POST
	 *
	 * @param \craft\web\Controller $controller 
	 * @param string|mixed $slug 
	 *
	 * @return \craft\web\Response
	 */
	public function POST(
		\craft\web\Controller $controller,
		sting $slug = null
	) : \craft\web\Response {

		$result = null;

		if ($this->secureRequest($controller->request)) {

			$result = ($controller->request->getIsGraphql()) ? $this->resolveGQLPost($controller->request, $slug) : $this->resolveJSONPost($controller->request, $slug);

		} else {

			$result = $this->auth->errors;

		}

		return $this->respond($controller, $result);

	}

	/**
	 * DELETE
	 *
	 * @param \craft\web\Controller $controller 
	 * @param string|null $slug 
	 *
	 * @return \craft\web\Response
	 */
	public function DELETE(
		\craft\web\Controller $controller,
		string $slug = null
	) : \craft\web\Response {

		$result = null;

		if ($this->secureRequest($controller->request)) {

			$result = ($controller->request->getIsGraphql()) ? $this->resolveGQLDelete($controller->request, $slug) : $this->resolveJSONDelete($controller->request, $slug);

		} else {

			$result = $this->auth->errors;

		}

		return $this->respond($controller, $result);

	}

	/**
	 * getBodyParams
	 *
	 * @return array
	 */
	public function getBodyParams() : array {

		return Craft::$app->request->getBodyParams();

	}

	/**
	 * getUser
	 *
	 * @param int|null $id 
	 *
	 * @return \craft\elements\User
	 */
	public function getUser(
		int $id = null
	) : \craft\elements\User {

		// TODO: Is this partially implemented? I should be passing an id.

		return ($int !== null) ? null : Craft::$app->getUser()->getIdentity();

	}

	/**
	 * getCfg
	 *
	 * @return \craft\base\Model
	 */
	public function getCfg() : \craft\base\Model {

		return $this->plugin->settings;

	}





	// Protected Methods
	// =========================================================================

	/**
	 * resolveGQLGet
	 *
	 * @param \craft\web\Request $request 
	 *
	 * @return mixed
	 */
	protected function resolveGQLGet(
		\craft\web\Request $request
	) : mixed {

		return null;

	}

	/**
	 * resolveGQLPost
	 *
	 * @param \craft\web\Request $request 
	 *
	 * @return mixed
	 */
	protected function resolveGQLPost(
		\craft\web\Request $request
	) : mixed {

		return null;

	}

	/**
	 * resolveGQLDelete
	 *
	 * @param \craft\web\Request $request 
	 *
	 * @return mixed
	 */
	protected function resolveGQLDelete(
		\craft\web\Request $request
	) : mixed {

		return null;

	}

	/**
	 * resolveJSONGet
	 *
	 * @param \craft\web\Request $request 
	 * @param string $slug 
	 *
	 * @return mixed
	 */
	protected function resolveJSONGet(
		\craft\web\Request $request, 
		string $slug
	) : mixed {

		$result = null;

		switch ($slug) {

			default:

				$result = $this->resolveDefaultJSONGet($request);
				break;

		}

		return $result;

	}

	/**
	 * resolveDefaultJSONGet
	 *
	 * @param \craft\web\Request $request 
	 *
	 * @return mixed
	 */
	protected function resolveDefaultJSONGet(
		\craft\web\Request $request
	) : mixed {

		return null;

	}

	/**
	 * resolveJSONPost
	 *
	 * @param \craft\web\Request $request 
	 * @param string $slug 
	 *
	 * @return mixed
	 */
	protected function resolveJSONPost(
		\craft\web\Request $request, 
		string $slug
	) : mixed {

		$result = null;

		switch ($slug) {

			default:

				$result = $this->resolveDefaultJSONPost($request);
				break;

		}

		return $result;

	}

	/**
	 * resolveDefaultJSONPost
	 *
	 * @param \craft\web\Request $request 
	 *
	 * @return mixed
	 */
	protected function resolveDefaultJSONPost(
		\craft\web\Request $request
	) : mixed {

		return null;

	}

	/**
	 * resolveJSONDelete
	 *
	 * @param \craft\web\Request $request 
	 * @param string $slug 
	 *
	 * @return mixed
	 */
	protected function resolveJSONDelete(
		\craft\web\Request $request, 
		string $slug
	) : mixed {

		$result = null;

		switch ($slug) {

			default:

				$result = $this->resolveDefaultJSONDelete($request);
				break;

		}

		return $result;

	}

	/**
	 * resolveDefaultJSONDelete
	 *
	 * @param \craft\web\Request $request 
	 *
	 * @return mixed
	 */
	protected function resolveDefaultJSONDelete(
		\craft\web\Request $request
	) : mixed {

		return null;

	}

	/**
	 * secureRequest
	 *
	 * @param \craft\web\Request $request 
	 *
	 * @return bool
	 */
	protected function secureRequest(
		\craft\web\Request $request
	) : bool {

		return true;

		//return $this->isAuthorizedAPIRequest($request);

	}

	/**
	 * respond
	 *
	 * @param \craft\web\Controller $controller 
	 * @param string $payload 
	 *
	 * @return \craft\web\Response
	 */
	protected function respond(
		\craft\web\Controller $controller, 
		string $payload
	) : \craft\web\Response {	

		$pretty = false;

		if (in_array('pretty', array_keys($controller->request->resolve()[1])) && $controller->request->resolve()[1]['pretty'] = 'true') $pretty = true;

		$this->respondAsJson($controller, $payload, $pretty);

	}

	/**
	 * respondAsJson
	 *
	 * @param \craft\web\Controller $controller 
	 * @param string $payload 
	 * @param bool $pretty 
	 *
	 * @return \craft\web\Response
	 */
	protected function respondAsJson(
		\craft\web\Controller $controller, 
		string $payload,
		bool $pretty = false
	) : \craft\web\Response {
		
		if ($pretty) $payload = '<pre>'. json_encode($payload, JSON_PRETTY_PRINT) . '</pre>';

		$controller->response->format = ($pretty) ? \yii\web\Response::FORMAT_HTML : \yii\web\Response::FORMAT_JSON;
		$controller->response->data = $payload;

		return $controller->response;
	
	}

	/**
	 * setStatus
	 *
	 * @param int $code 
	 *
	 * @return void
	 */
	protected function setStatus(
		int $code
	) {

		Craft::$app->getResponse()->setStatusCode($code);
		
	}

}

class BaseService extends Component {

	use BaseServiceTrait;

	public $models = [];

}