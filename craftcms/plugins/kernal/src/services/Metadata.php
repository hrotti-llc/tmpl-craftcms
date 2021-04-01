<?php

namespace hrotti\kernal\services;

use Craft;

use craft\base\Component;
use craft\web\Request;

use craft\elements\Entry;
use craft\elements\MatrixBlock;

use craft\db\Query;

class Metadata extends Component {

	use \hrotti\kernal\helpers\CommonServiceHelper;

	public $section = null;

	// Magic Methods
	// =========================================================================

	public function __construct() {

		$this->sectionExtraFields = [];

		$this->outlineFields();

		parent::__construct();

	}

	// Public Methods
	// =========================================================================

	public function metadata($limit = 20, $page=1, $status = ['live'], $filters = null) {

		$query = Entry::find();

		return $query;

	}

	protected function resolveJSONGet($request, $id) {

		$result = null;

		switch ($id) {

			default:

				$result = $this->resolveMetadataJSONGet($request);
				break;

		}

		return $result;

	}
	
	protected function resolveMetadataJSONGet($request, $entries = null) {

		$entries = $entries ?? $this->metadata()->all();

		$fields = [];

		return $this->entriesToJSON($entries, $fields);

	}

}
