<?php

namespace punishment\api;

use punishment\type\Ban;
use punishment\type\Mute;

class punishmentAPI{
	/**
	 * @param String $Target PlayerID
	 * @param String $Reason 理由
	 * @param Int    $Seconds 時効になるまでの秒数
	 */
	public static function Ban(String $Target,String $Reason,Int $Seconds): bool{
		return Ban::Ban($Target,$Reason,$Seconds);
	}
	/**
	 * @param String $Target PlayerID
	 * @param String $Reason 理由
	 * @param Int    $Seconds 時効になるまでの秒数
	 */
	public static function Mute(String $Target,String $Reason,Int $Seconds): bool{
		return Mute::Mute($Target,$Reason,$Seconds);
	}
	/**
	 * @param String $Target PlayerID
	 */
	public static function unBan(String $Target){
		Ban::unBan($Target);
	}
	/**
	 * @param String $Target PlayerID
	 */
	public static function unMute(String $Target): bool{
		return Mute::unMute($Target);
	}

}