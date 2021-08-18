<?php

namespace hrotti\kernal\services;

use Craft;

use craft\base\Component;
use craft\web\Request;

use craft\elements\Entry;
use craft\elements\MatrixBlock;

use craft\db\Query;

use craft\elements\GlobalSet;
use craft\elements\db\GlobalSetQuery;

use craft\elements\Tag;
use craft\elements\db\TagQuery;

use craft\elements\Category;
use craft\elements\db\CategoryQuery;

class Helpers extends \hrotti\kernal\helpers\CommonService {

	use \hrotti\kernal\helpers\SecurityHelper;

	// Public Methods
	// =========================================================================

	public function globals(
		$limit = 20, 
		$page=1, 
		$status = ['live'], 
		$filters = []
	) {

		$query = new GlobalSetQuery(GlobalSet::class);

		$this->normalizeTagsQueryFilters($query, $filters);
		$this->normalizeSortingFilters($query, $filters);

		return $query;

	}

	public function tags($limit = 20, $page=1, $status = ['live'], $filters = null) {

		// if (is_string($group)) $group = Craft::$app->tags->getTagGroupByHandle($group)->id;

		$query = new TagQuery(Tag::class);

		$query->with(['group']);

		$this->normalizeTagsQueryFilters($query, $filters);
		$this->normalizeSortingFilters($query, $filters);

		return $query;

	}

	public function categories(
		$limit = 20, 
		$page=1, 
		$status = ['live'], 
		$filters = null
	) {

		// if (is_string($group)) $group = Craft::$app->tags->getTagGroupByHandle($group)->id;

		$query = new CategoryQuery(Category::class);

		$query->with(['group']);

		$this->normalizeCategoriesQueryFilters($query, $filters);
		$this->normalizeSortingFilters($query, $filters);

		return $query;

	}

	public function csrf() {

		return $this->getCsrfBasics();

	}

	public function resolveFilters(
		$request
	) {

		$ignore = ['limit', 'page'];

		$settings = $request->resolve()[1];
		$filters = array_filter(
			$settings, 
			function ($k) use ($ignore) { return !in_array($k, $ignore); },
			ARRAY_FILTER_USE_KEY
		);

		return [
			array_key_exists('limit', $settings)
				? $settings['limit'] : 20,
			array_key_exists('page', $settings)
				? $settings['page'] : 1,
			['live'],
			$filters
		];

	}


	// Protected Methods
	// =========================================================================

	protected function normalizeTagsQueryFilters(
		$query, 
		$filters
	) {

		foreach($filters as $name => $value) {

			switch ($name) {

				case 'group':

					$query->group($value);
					break;

			}

		}

	}

	protected function normalizeCategoriesQueryFilters(
		$query, 
		$filters
		) {

		foreach($filters as $name => $value) {

			switch ($name) {

				case 'group':

					$query->group($value);
					break;

			}

		}

	}

	protected function normalizeSortingFilters(
		$query, 
		$filters
	) {

		$criteria = $this->normalizeSortingCriteria(
			(array_key_exists('order_by', $filters)) ? explode(';', $filters['order_by']) : []
		);

		foreach($criteria as $name => $direction) {

			switch ($name) {


			}

		}

		if (!count($criteria)) {

			$query->addOrderBy(
				'title ASC'
			);

		}

		return $query;
		
	}

	protected function resolveJSONGet(
		$request, 
		$id
	) {

		$result = null;

		switch ($id) {

			case 'categories':

				$result = $this->resolveCategoriesJSONGet($request);
				break;

			case 'tags':

				$result = $this->resolveTagsJSONGet($request);
				break;

			case 'csrf':

				$result = $this->resolveCSRFGet($request);
				break;

		}

		return $result;

	}

	protected function resolveCSRFGet(
		$request
	) {

		return $this->csrf();

	}

	protected function resolveCategoriesJSONGet(
		$request, 
		$entries = null
	) {

		$entries = $entries ?? $this->categories(...$this->resolveFilters($request))->all();

		$items = [];

		foreach ($entries as $entry) {

			$item = [
				"meta" => [
					"id" => $entry->id,
					"groupId" => $entry->groupId,
					"slug" => $entry->slug,
				],
				"title" => $entry->title,
				"description" => $entry->description,
				"parent" => null
			];

			if ($entry->parent !== null) {
				
				$item['parent'] = [
					"meta" => [
						"id" => $entry->parent->id,
						"groupId" => $entry->parent->groupId,
						"slug" => $entry->parent->slug
					],
					"title" => $entry->title,
					"description" => $entry->description
				];

			}

			$items[$entry->slug] = $item;

		}

		return $items;

	}

	protected function resolveTagsJSONGet(
		$request, 
		$entries = null
	) {

		$entries = $entries ?? $this->tags(...$this->resolveFilters($request))->all();

		$items = [];

		foreach ($entries as $entry) {

			$item = [
				"meta" => [
					"id" => $entry->id,
					"groupId" => $entry->groupId,
					"slug" => $entry->slug,
				],
				"title" => $entry->title,
				"description" => $entry->description
			];

			$items[$entry->slug] = $item;

		}

		return $items;

	}

}
