<?php

namespace punishment\util;

class time{
	public static function getTimeStamp(Int $difSeconds){
		$diffDateTime['seconds'] = $difSeconds % 60;
		$difMinutes = ($difSeconds - ($difSeconds % 60)) / 60;
		$diffDateTime['minutes'] = $difMinutes % 60;
		$difHours = ($difMinutes - ($difMinutes % 60)) / 60;
		$diffDateTime['hours'] = $difHours % 24;
		$difDays = ($difHours - ($difHours % 24)) / 24;
		$diffDateTime['days'] = $difDays;
		$hms = sprintf("%02d日%02d時間%02d分%02d秒",$diffDateTime['days'],$diffDateTime['hours'], $diffDateTime['minutes'], $diffDateTime['seconds']);
		return $hms;
	}

	public static function parseTime(string $duration): int|bool {
		$pattern = '/(\d+)([d|h|m|s])/';
		$matches = [];
		$totalTime = 0;

		if (!preg_match_all($pattern, $duration, $matches, PREG_SET_ORDER)) {
			return false;
		}

		foreach ($matches as $match) {
			$amount = (int)$match[1];
			$unit = $match[2];

			switch ($unit) {
				case 'd':
					$totalTime += $amount * 24 * 60 * 60;
					break;
				case 'h':
					$totalTime += $amount * 60 * 60;
					break;
				case 'm':
					$totalTime += $amount * 60;
					break;
				case 's':
					$totalTime += $amount;
					break;
			}
		}

		return $totalTime;
	}
}