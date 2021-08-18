<?php

namespace hrotti\kernal\helpers;

use Craft;

use craft\elements\Category;
use craft\elements\Tag;
use craft\elements\db\CategoryQuery;
use craft\elements\db\TagQuery;

class CommonService extends \hrotti\kernal\helpers\BaseService {

	public $builtinFields = ['id', 'expiryDate', 'sectionId', 'typeId', 'authorId', 'title', 'slug'];
	public $sectionFields = [];
	public $sectionExtraFields = [];
	public $availableFields;

	public $fields = [];

	// Public Methods
	// =========================================================================

	public function outlineFields() {

		if ($this->section) {

			if (is_string($this->section)) $this->section = Craft::$app->sections->getSectionByHandle($this->section);

			$this->sectionFields = array_map(
				function ($field) { return $field->handle; },
				$this->section->getEntryTypes($this->section->handle)[0]->getFieldLayout()->getFields()
			);

		}

		$this->availableFields = array_merge($this->builtinFields, $this->sectionFields, $this->sectionExtraFields);

	}

	public function getCategories($keywords, $group) {

		if (is_string($group)) $group = Craft::$app->categories->getGroupByHandle($group)->id;

		$query = new CategoryQuery(Category::class);

		$query->groupId = $group;
		$query->title = $keywords;

		return $query;

	}

	public function getCategoryIds($keywords, $group) {

		return array_map(
			function($item) { return $item->id; },
			$this->getCategories($keywords, $group)->all()
		);

	}


	public function getTags($keywords, $group) {

		if (is_string($group)) $group = Craft::$app->tags->getTagGroupByHandle($group)->id;

		$tagQuery = new TagQuery(Tag::class);

		$tagQuery->groupId = $group;
		$tagQuery->title = $keywords;

		return $tagQuery;


	}

	public function getTagIds($keywords, $group) {

		return array_map(
			function($item) { return $item->id; },
			$this->getTags($keywords, $group)->all()
		);

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

	public function resolveFilters($request) {

		$ignore = [];

		$settings = $request->resolve()[1];
		$filters = array_filter(
			$settings, 
			function ($k) use ($ignore) { return !in_array($k, $ignore); },
			ARRAY_FILTER_USE_KEY
		);

		return $filters;

	}

	public function resolveCommonFilters($request, $limit = -1, $page = 1) {

		$ignore = ['limit', 'page'];

		$settings = $request->resolve()[1];
		$filters = array_filter(
			$settings, 
			function ($k) use ($ignore) { return !in_array($k, $ignore); },
			ARRAY_FILTER_USE_KEY
		);

		$filters['limit'] = array_key_exists('limit', $settings)
			? $settings['limit'] : $limit;

		$filters['page'] = array_key_exists('page', $settings)
			? $settings['page'] : $page;

		return $filters;

	}
	
	public function normalizeSortingCriteria($criteria) {
		
		return array_column(array_map(function ($value) {

			$order = explode(',', str_replace(' ', '', $value));

			if (count($order) == 1) $order[] = 'asc';

			$order[1] = strtoupper($order[1]);

			return $order;

		}, $criteria), 1, 0);

	}
	
}