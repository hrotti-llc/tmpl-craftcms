<?php

namespace hrotti\poleconnect\helpers;

use Craft;

trait CommonModelHelper {

	static public $section;
	static public $plugin;

	public function __construct($data = null, $normalize = false) {

		self::strap($this->section);

		if ($data !== null && $normalize) $this->normalizeAndSetAttributes($data, false);

		parent::__construct();

	}

	static public function strap($sectionHandle = null) {

		self::$plugin = self::$plugin ?? Craft::$app->plugins->getPlugin('poleconnect');

		if ($sectionHandle != null) self::$section = self::$section ?? Craft::$app->sections->getSectionByHandle($sectionHandle);

	}

	static public function normalizeEntries(&$entries) {

		$results = [];

		foreach ($entries as $entry) $results[] = self::normalizeEntry($entry);

		return $results;

	}

	static public function normalizeAsset($field) {

		$result = null;

		if ($field != null) {

			$result = [
				'url' => $field[0]['url']
			];

		}

		return $result;

	}

	static public function normalizePrice($amount, $from = "USD", $to = null) {

		$price = null;

		if ($amount) {
			
			$prices = self::$plugin->currencies->convert($from ?? "USD", Craft::$app->request->get('currency') ?? 'USD', $amount);;
			$price = $prices[$to ?? Craft::$app->request->get('currency') ?? "USD"];

		}

		return $price;

	}

	static public function normalizePrices($amount, $from = "USD", $to = null) {

		$prices = null;

		if ($amount) {
			
			$prices = self::$plugin->currencies->convert($from ?? "USD", $to ?? Craft::$app->request->get('currency') ?? 'USD', $amount);

		}

		return $prices;

	}

	static public function normalizePhone($field) {

		$result = null;

		if ($field != null) {

			$result = [
				'national' => $field->getNational(),
				'international' => $field->getInternational()
			];

		}

		return $result;

	}

	static public function normalizeAddress($field) {

		$result = null;

		if ($field != null) {

			$result = [
				'address1' => $field['address1'],
				'address2' => $field['address2'],
				'administrativeArea' => $field['administrativeArea'],
				'administrativeAreaCode' => $field['administrativeAreaCode'],
				'locality' => $field['locality'],
				'dependentLocality' => $field['dependentLocality'],
				'postalCode' => $field['postalCode'],
				'sortingCode' => $field['sortingCode'],
				'country' => $field['country'],
				'countryCode' => $field['countryCode'],
				'locale' => $field['locale'],
			];

		}

		return $result;

	}

	static public function normalizeCurrency($field) {

		$result = null;

		if ($field != null) {

			return $field['currencyCode'];

		}

		return $result;

	}

	static public function normalizeWebPresence($field) {

		$result = null;

		if ($field != null) {

			$result = [];

			foreach ($field as $media) {

				switch ($media->getGqlTypeName()) {

					case 'webPresence_btFacebook_BlockType':
						$result['facebook'] = $media->mediaURL;
						break;

					case 'webPresence_btInstagram_BlockType':
						$result['instagram'] = $media->mediaHandle;
						break;

					case 'webPresence_btYoutube_BlockType':
						$result['youtube'] = $media->mediaURL;
						break;

					case 'webPresence_btWebsite_BlockType':
						$result['website'] = $media->mediaURL;
						break;

					case 'webPresence_btAffiliate_BlockType':
						$result['affiliate'] = [
							'url' => $media->affiliateURL,
							'code' => $media->affiliateCode
						];
						break;

				}

			}

		}

		return $result;

	}

}