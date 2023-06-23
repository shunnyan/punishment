<?php

namespace punishment\type;

use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\utils\Config;
use punishment\Main;
use punishment\util\playerData;
use punishment\util\time;

class Ban{
	public static $BanData;

	public static function init(){
	self::$BanData = Main::$config_datafolder;
	}

	public static function Ban(String $player,$reason = "",$time = "infinity") :bool{
		$Data = new Config(self::$BanData . "BanList.json",Config::JSON);
		$PL = Server::getInstance()->getPlayerByPrefix($player);
		if($time !== "infinity"){
			if(is_numeric($time)){
				$time = time() + (int)$time;
			}else{
				return false;
			}
		}
		if($PL !== null){
			$xuid = $PL->getXuid();
			$Data->set($xuid,["name" => strtolower($PL->getName()),"Reason" => $reason,"time" => $time]);
			$Data->save();
			$PL->kick("サーバーから切断されました");
		}else{
			$id = playerData::getxuid($player);
			if($id == null){
				$id = strtolower($player);
			}
			$Data->set($id,["name" => strtolower($player),"Reason" => $reason,"time" => $time]);
			$Data->save();
		}
		return true;
	}
	public static function unBan(String $name) :bool{
		$Data = new Config(self::$BanData . "BanList.json",Config::JSON);
		$name = strtolower($name);
		foreach($Data->getAll(true) as $key){
			$data = $Data->get($key);
			if($data["name"] == $name){
				$Data->remove($key);
				$Data->save();
				return true;
			}
		}
		return false;
	}

	public static function isBanned(String $name,$xuid = null) :array{
		$Data = new Config(self::$BanData . "BanList.json",Config::JSON);
		$name = strtolower($name);
		if($xuid == null){
			$xuid = playerData::getxuid($name);
		}
		if($Data->exists($name) && $xuid != null){
			$oldData = $Data->get($name);
			$Data->set($xuid,["name" => strtolower($name),"Reason" => $oldData["Reason"],"time" => $oldData["time"]]);
			$Data->remove($name);
			$Data->save();
		}
		$key = ($xuid ?? $name);
			if($Data->exists($key)){
				$getData = $Data->get($key);
				if($getData["time"] != "infinity"){
					if((int)$getData["time"] - time() <= 0){
						$Data->remove($key);
						$Data->save();
						return [];
					}else{
						return ["text" => "§cあなたはこのサーバーからBANされています。\n§6理由: §f" . $getData["Reason"] . "\n§e期限: §f" . time::getTimeStamp((int)$getData["time"] - time()),"Reason" => $getData["Reason"],"time" => time::getTimeStamp((int)$getData["time"] - time())];
					}
				}else{
					return ["text" => "§cあなたはこのサーバーからBANされています。\n§6理由: §f" . $getData["Reason"],"Reason" => $getData["Reason"],"time" => "infinity"];
				}
			}
		return [];
	}
	public static function BanList() :array{
		$Data = new Config(self::$BanData . "BanList.json",Config::JSON);
		$array = [];
		foreach($Data->getAll(true) as $key){
			$data = $Data->get($key);
			$array[] = $data["name"];
		}
		return $array;
	}
}