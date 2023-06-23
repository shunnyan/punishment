<?php

declare(strict_types=1);

namespace punishment;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\server\CommandEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerPreLoginEvent;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use punishment\form\punish_control;
use punishment\type\Ban;
use punishment\type\Mute;
use punishment\util\playerData;
use punishment\util\time;

class Main extends PluginBase implements Listener{
	public static $scheduler;
	public static $config_datafolder;

	public function onEnable() : void{
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		date_default_timezone_set('Asia/Tokyo');
		if(!file_exists($this->getDataFolder())){
			mkdir($this->getDataFolder(), 0744, true);
		}
		self::$scheduler = $this->getScheduler();
		self::$config_datafolder = $this->getDataFolder();
		Ban::init();
		Mute::init();
		playerData::init();
	}

	public function Login(PlayerJoinEvent $event){
		$player = $event->getPlayer();
		playerData::set($player);
		$bandata = Ban::isBanned($player->getName());
		if($bandata != []){
			$player->kick($bandata["text"]);
		}
	}

	public function PreLogin(PlayerPreLoginEvent $event){
		$player = $event->getPlayerInfo();
		$name = $player->getUsername();
		$xuid = playerData::getxuid($name);
		if($xuid != null){
			$bandata = Ban::isBanned($name,$xuid);
			if($bandata != []){
				$event->setKickFlag(0,$bandata["text"]);
			}
		}
	}

	public function Chat(PlayerChatEvent $event){
		$player = $event->getPlayer();
		$xuid = playerData::getxuid(strtolower($player->getName()));
		if($xuid != null){
			$mutedata = Mute::isMuted(strtolower($player->getName()),$xuid);
			if($mutedata != []){
				$player->sendMessage($mutedata["text"]);
				$event->cancel();
			}
		}
	}
	public function Command(CommandEvent $event){
		$player = $event->getSender();
		$xuid = playerData::getxuid(strtolower($player->getName()));
		$command = $event->getCommand();
		$args = explode(" ", $command);
		if($args[0] === "me" or $args[0] === "msg" or $args[0] === "w" or $args[0] === "tell"){
			if($xuid != null){
				$mutedata = Mute::isMuted(strtolower($player->getName()),$xuid);
				if($mutedata != []){
					$player->sendMessage($mutedata["text"]);
					$event->cancel();
				}
			}
		}
	}

	public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool {
		if ($command->getName() === "punish") {
			$howtouse = "使用方法: /punish <playerID> <ban|mute> <理由> [期限(1h=1時間)]";
			if (count($args) < 3) {
				$sender->sendMessage("§c引数が不足しています $howtouse");
				return false;
			}
			$target = $this->parseQuotedArgument(array_shift($args)); // プレイヤー名
			$punishment = strtolower(array_shift($args)); // banまたはmute
			$reason = $this->parseQuotedArgument(array_shift($args)); // 理由
			if($punishment !== "mute" && $punishment !== "ban"){
				$sender->sendMessage("§c引数が異常です $howtouse");
				return false;
			}
			if($args != null){
				$duration = array_shift($args);
				$duration = strtolower($duration);
				$time = time::parseTime($duration);
			}else{
				$time = "infinity";
			}

			if ($time === false) {
				$sender->sendMessage("§c無効な時間形式です $howtouse");
				return false;
			}
			if($punishment == "ban"){
				if(Ban::Ban($target,$reason,$time)){
					$sender->sendMessage("§f$target §aをBANしました。(理由:§f $reason §a)");
				}else{
					$sender->sendMessage("§c無効な形式です $howtouse");
					return false;
				}
			}
			if($punishment == "mute"){
				if(Mute::Mute($target,$reason,$time)){
					$sender->sendMessage("§f$target §aをMuteしました。(理由:§f $reason §a)");
				}else{
					$sender->sendMessage("§c無効な形式です $howtouse");
					return false;
				}
			}
			return true;
		}
		if ($command->getName() === "unpunish") {
			$howtouse = "使用方法: /unpunish <playerID> <ban|mute>";
			if (count($args) < 2) {
				$sender->sendMessage("§c引数が不足しています $howtouse");
				return false;
			}
			$target = $this->parseQuotedArgument(array_shift($args)); // プレイヤー名
			$punishment = strtolower(array_shift($args)); // banまたはmute
			if($punishment !== "mute" && $punishment !== "ban"){
				$sender->sendMessage("§c引数が異常です $howtouse");
				return false;
			}
			if($punishment == "ban"){
				if(Ban::unBan($target)){
					$sender->sendMessage("§f$target §aの処罰(Ban)を撤回しました");
				}else{
					$sender->sendMessage("§cエラーが発生しました");
					return false;
				}
			}
			if($punishment == "mute"){
				if(Mute::unMute($target)){
					$sender->sendMessage("§f$target §aの処罰(Mute)を撤回しました");
				}else{
					$sender->sendMessage("§cエラーが発生しました");
					return false;
				}
			}
			return true;
		}
		if ($command->getName() === "punishform") {
			if (!$sender instanceof Player) {
				$sender->sendMessage("§cこのコマンドはプレイヤーのみ使用できます");
				return true;
			}
			$sender->sendForm(new punish_control($sender));
			return true;
		}
		return false;
	}

	private function parseQuotedArgument(string $argument): string {
		if (str_starts_with($argument, '"') && str_ends_with($argument, '"')) {
			return substr($argument, 1, -1);
		}
		return $argument;
	}

}
