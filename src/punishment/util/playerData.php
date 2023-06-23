<?php

namespace punishment\util;

use pocketmine\player\Player;
use pocketmine\utils\Config;
use punishment\Main;

class playerData{
	public static $PlayerData;

	public static function init(){
		self::$PlayerData = Main::$config_datafolder;
	}
	public static function set(Player $player){
		$config = new Config(self::$PlayerData . "PlayerDataList.json",Config::JSON);
		$xuid = $player->getXuid();
		if(!$config->exists(strtolower($player->getName()))){
			$config->set(strtolower($player->getName()),$xuid);
			$config->save();
		}
	}
	public static function getxuid(String $name) : ?string{
		$config = new Config(self::$PlayerData . "PlayerDataList.json",Config::JSON);
		if($config->exists(strtolower($name))){
			return $config->get(strtolower($name));
		}
		return null;
	}
}