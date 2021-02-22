<?php

namespace hrotti\kernal\helpers;

use Craft;

use craft\elements\Tag;
use craft\elements\db\TagQuery;

use craft\web\Request;

use craft\elements\Entry;

trait CommonServiceHelper {

	public $builtinFields = ['id', 'expiryDate', 'sectionId', 'typeId', 'authorId', 'title', 'slug'];
	public $sectionFields = [];
	public $sectionExtraFields = [];
	public $fields;

	public $plugin;

	// Public Methods
	// =========================================================================

	public function outlineFields() {

		$this->plugin = Craft::$app->plugins->getPlugin('kernal');

		if ($this->section) {

			if (is_string($this->section)) $this->section = Craft::$app->sections->getSectionByHandle($this->section);

			$this->sectionFields = array_map(
				function ($field) { return $field->handle; },
				$this->section->getEntryTypes($this->section->handle)[0]->getFieldLayout()->getFields()
			);

		}

		$this->fields = array_merge($this->builtinFields, $this->sectionFields, $this->sectionExtraFields);

	}

	public function GET(
		$controller,
		$id = null
	) {	

		$result = null;

		if ($this->secureRequest($controller->request)) {

			$result = ($controller->request->getIsGraphql()) ? $this->resolveGQLGet($controller->request, $id) : $this->resolveJSONGet($controller->request, $id);

		}

		//return $result;

		return $this->respond($controller, $result);

	}

	public function entriesToJSON(
		$entries, 
		&$fields
	) {

		$parsed = [];
		
		foreach ($entries as $entry) $parsed[] = $this->entryToJSON($entry, $fields);

		return $parsed;

	}

	public function entryToJSON(
		$entry, 
		&$fields, 
		&$data = null
	) {

		$data = $data ?? [];

		foreach ($fields as $id => $field) {

			$handle = (is_int($id)) ? $field : $id;

			if (is_array($entry[$handle])) {

				[$sub_handle, $sub_fields, $target_key] = [null, [], null];

				if (is_array($field)) {

					if (count($field) == 1) [$sub_handle, $sub_fields] = [$handle, $field[0]];
					if (count($field) == 2) [$sub_handle, $sub_fields] = [$field[0], $field[1]];
					if (count($field) == 3) [$sub_handle, $sub_fields, $target_key] = [$field[0], $field[1], $field[2]];

				} else {

					$sub_handle = $field;

				}

				if (!array_key_exists($sub_handle, $data)) $data[$sub_handle] = [];

				if (
					count($entry[$handle]) && 
					is_object($entry[$handle][0]) && 
					in_array(get_class($entry[$handle][0]), ['craft\elements\MatrixBlock', 'verbb\supertable\elements\SuperTableBlockElement'])
				) {

					if ($target_key) {

						foreach ($entry[$handle] as $item) {
							
							$output = $this->entryToJSON($item, $sub_fields);

							if ($target_key !== true || ($target_key == true && count($sub_fields) > 1)) {

								$data[$sub_handle][$item->$target_key] = ($output) ? $output[array_key_first($sub_fields)] : null;

							} else {

								$data[$sub_handle][] = ($output) ? $output[array_key_first($sub_fields)] : null;

							}

						}

					} else {

						foreach ($entry[$handle] as $item) $data[$sub_handle][] = $this->entryToJSON($item, $sub_fields);

					}

				} else {

					foreach ($entry[$handle] as $item) {

						if ($target_key === true) {

							if (count($sub_fields) === 1) {

								$data[$sub_handle][] = $item[$sub_fields[0]];

							}

						} else {

							$output = [];
							foreach ($sub_fields as $sub_field_handle => $sub_field) $output[$sub_field] = $item[is_int($sub_field_handle) ? $sub_field : $sub_field_handle];
							
							$data[$sub_handle][] = $output;

						}

					}

				}

			} else if (is_object($entry[$handle])) {
				
				if (get_class($entry[$handle]) == 'DateTime') {

					$data[$field] = $entry[$handle]->getTimestamp();

				} else if (get_class($entry[$handle]) == 'barrelstrength\sproutbasefields\models\Address') {

					$data[$field] = [
						'address1' => $entry[$handle]['address1'],
						'address2' => $entry[$handle]['address2'],
						'administrativeArea' => $entry[$handle]['administrativeArea'],
						'administrativeAreaCode' => $entry[$handle]['administrativeAreaCode'],
						'locality' => $entry[$handle]['locality'],
						'dependentLocality' => $entry[$handle]['dependentLocality'],
						'postalCode' => $entry[$handle]['postalCode'],
						'sortingCode' => $entry[$handle]['sortingCode'],
						'country' => $entry[$handle]['country'],
						'countryCode' => $entry[$handle]['countryCode'],
						'currencyCode' => $entry[$handle]['currencyCode'],
						'locale' => $entry[$handle]['locale'],
					];

				} else if (get_class($entry[$handle]) == 'barrelstrength\sproutbasefields\models\Phone') {

					$data[$field] = [
						'national' => $entry[$handle]->getNational(),
						'international' => $entry[$handle]->getInternational()
					];

				} else if (get_class($entry[$handle]) == 'mmikkel\incognitofield\IncognitoField') {

					$data[$field] = $entry[$handle];

				} else {

					$data[$field] = null;
					//$data[$field] = get_class($entry[$handle]);

				}

			} else {

				$data[$field] = $entry[$handle];

			}

		}

		return $data;

	}

	public function createOrGetTag($keyword, $group) {

		if (is_string($group)) $group = Craft::$app->tags->getTagGroupByHandle($group)->id;

		$tagQuery = new TagQuery(Tag::class);

		$tagQuery->groupId = $group;
		$tagQuery->title = $keyword;

		$tag = $tagQuery->one();

		if (!$tag) {

			$tag = new Tag();

			$tag->groupId = $group;
			$tag->title = $keyword;

			Craft::$app->elements->saveElement($tag);

		}

		return $tag;

	}

	// Protected Methods
	// =========================================================================

	protected function resolveGQLGet(
		Request $request
	) {

		return null;

	}

	protected function resolveJSONGet(
		Request $request
	) {

		return null;

	}

	protected function secureRequest(
		Request $request
	) {

		return true;

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

}