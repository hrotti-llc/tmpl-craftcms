<?php

namespace hrotti\kernal\helpers;

use Craft;

trait CommonModelHelper {

	static public $section;
	static public $plugin;

	public function __construct(
		$data = null, 
		$normalize = false
	) {

		self::strap($this->section);

		if ($data !== null && $normalize) $this->normalizeAndSetAttributes($data, false);

		parent::__construct();

	}

	static public function strap(
		$sectionHandle = null
	) {

		self::$plugin = self::$plugin ?? Craft::$app->plugins->getPlugin('kernal');

		if ($sectionHandle != null) self::$section = self::$section ?? Craft::$app->sections->getSectionByHandle($sectionHandle);

	}

	static public function normalizeEntries(
		&$entries
	) {

		$results = [];

		foreach ($entries as $entry) $results[] = self::normalizeEntry($entry);

		return $results;

	}

	static public function normalizeAsset(
		$field
	) {

		$result = null;

		if ($field != null) {

			$result = [
				'url' => $field[0]['url']
			];

		}

		return $result;

	}

	static public function normalizePhone(
		$field
	) {

		$result = null;

		if ($field != null) {

			$result = [
				'national' => $field->getNational(),
				'international' => $field->getInternational()
			];

		}

		return $result;

	}

	static public function normalizeAddress(
		$field
	) {

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

	static public function normalizeDate($timestamp, $tz = null) {

		$times = [
			'datestamp' => null,
			'utc' => ['date' => null],
			'local' => ['date' => null],
		];

		if ($timestamp != null) {

			$datetime = (new \DateTime('@' . $timestamp));

			$times['utc']['date'] = $datetime->format('Y-m-d');
			$times['utc']['datestamp'] = intval($datetime->setTime(0, 0)->getTimestamp());

			if ($tz != null) {

				$datetime = $datetime->setTimezone(new \DateTimeZone($tz));

				$times['local']['date'] = $datetime->format('Y-m-d');
				$times['local']['datestamp'] = intval($datetime->setTime(0, 0)->getTimestamp());

			}

			$times['datestamp'] = $times['utc']['datestamp'];

		}

		return $times;

	}

	static public function normalizeTime($timestamp, $tz = null) {

		$times = [
			'timestamp' => null,
			'datestamp' => null,
			'utc' => ['date' => null, 'time' => null, 'seconds' => null, 'datetime' => null],
			'local' => ['date' => null, 'time' => null, 'seconds' => null, 'datetime' => null],
		];

		if ($timestamp != null) {

			$datetime = (new \DateTime('@' . $timestamp, new \DateTimeZone('ETC/UTC')));
			[$h, $m, $s] = explode(':', $datetime->format('H:i:s.u'));

			$times['utc']['date'] = $datetime->format('Y-m-d');
			$times['utc']['time'] = $datetime->format('H:i:s');
			$times['utc']['datetime'] = $datetime->format('Y-m-d H:i:s');
			$times['utc']['seconds'] = ($h * 3600) + ($m * 60) + $s;
			$times['utc']['datestamp'] = intval($datetime->setTime(0, 0)->getTimestamp());

			if ($tz != null) {

				$datetime = (new \DateTime('@' . $timestamp, new \DateTimeZone('ETC/UTC')))->setTimezone(new \DateTimeZone($tz));
				[$h, $m, $s] = explode(':', $datetime->format('H:i:s.u'));

				$times['local']['date'] = $datetime->format('Y-m-d');
				$times['local']['time'] = $datetime->format('H:i:s');
				$times['local']['datetime'] = $datetime->format('Y-m-d H:i:s');
				$times['local']['seconds'] = ($h * 3600) + ($m * 60) + $s;
				$times['local']['datestamp'] = intval($datetime->setTime(0, 0)->getTimestamp());

			}

			$times['timestamp'] = intval($timestamp);
			$times['datestamp'] = $times['utc']['datestamp'];

		}

		return $times;

	}

}