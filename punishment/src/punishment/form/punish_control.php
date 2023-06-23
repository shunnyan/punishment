<?php

namespace punishment\form;

use pocketmine\form\Form;
use pocketmine\player\Player;
use punishment\type\Ban;
use punishment\type\Mute;
use punishment\util\playerData;

class punish_control implements Form{
	public $player;
	public function __construct(Player $players){
		$this->player = $players;
	}
	public function handleResponse(Player $player, $data) : void{
		if ($data === null) {
			return;
		}
		if($data == 0){
			$player->sendForm(new banlist($player));
		}
		if($data == 1){
			$player->sendForm(new mutelist($player));
		}
	}

	public function jsonSerialize() : array{
		return [
			'type' => 'form',
			'title' => '§l【 処罰リスト 】',
			'content' => "処罰項目を選択してください",
			'buttons' => [["text" => "Ban者リスト"],["text" => "Mute者リスト"]]
		];
	}
}
class banlist implements Form{
	public $player;
	public $list;
	public function __construct(Player $players){
		$this->player = $players;
	}
	public function handleResponse(Player $player, $data) : void{
		if ($data === null) {
			return;
		}
		if($this->list[$data] == "リストに存在しません"){
			return;
		}
		$player->sendForm(new opendata($player,$this->list[$data],"ban"));
	}

	public function jsonSerialize() : array{
		$this->list = Ban::BanList();
		if($this->list == []){
			$this->list[] = "リストに存在しません";
		}
		$array = [];
		foreach($this->list as $key){
			$array[] = ["text" => $key];
		}
		return [
			'type' => 'form',
			'title' => '§l【 Ban処罰リスト 】',
			'content' => "Banされた人一覧",
			'buttons' => $array
		];
	}
}
class mutelist implements Form{
	public $player;
	public $list;
	public function __construct(Player $players){
		$this->player = $players;
	}
	public function handleResponse(Player $player, $data) : void{
		if ($data === null) {
			return;
		}
		if($this->list[$data] == "リストに存在しません"){
			return;
		}
		$player->sendForm(new opendata($player,$this->list[$data],"mute"));
	}

	public function jsonSerialize() : array{
		$this->list = Mute::MuteList();
		if($this->list == []){
			$this->list[] = "リストに存在しません";
		}
		$array = [];
		foreach($this->list as $key){
			$array[] = ["text" => $key];
		}
		return [
			'type' => 'form',
			'title' => '§l【 Mute処罰リスト 】',
			'content' => "Muteされた人一覧",
			'buttons' => $array
		];
	}
}
class opendata implements Form{
	public $player;
	public $name;
	public $punish;
	public function __construct(Player $players,$name,$punish){
		$this->player = $players;
		$this->name = $name;
		$this->punish = $punish;
	}
	public function handleResponse(Player $player, $data) : void{
		if ($data === null) {
			return;
		}
		if ($data === 1) {
			return;
		}
		if($this->punish == "ban"){
			if(Ban::unBan($this->name)){
				$player->sendMessage("$this->name §aの処罰(Ban)を撤回しました");
			}else{
				$player->sendMessage("§cエラーが発生しました");
			}
		}
		if($this->punish == "mute"){
			if(Mute::unMute($this->name)){
				$player->sendMessage("$this->name §aの処罰(Mute)を撤回しました");
			}else{
				$player->sendMessage("§cエラーが発生しました");
			}
		}
	}

	public function jsonSerialize() : array{
		$text = "\n---< プレイヤー情報 >---\nプレイヤーID: $this->name(Xuid: " . (playerData::getxuid($this->name) ?? "不明") . ")\n処罰方法: ";
		if($this->punish == "ban"){

			$data = Ban::isBanned(playerData::getxuid($this->name) ?? $this->name);
			$time = $data["time"];
			$reason = $data["Reason"];
			$text .= "§4BAN\n§e期限(残り):§f $time\n理由:§f $reason";
		}
		if($this->punish == "mute"){
			$data = Mute::isMuted(playerData::getxuid($this->name) ?? $this->name);
			$time = $data["time"];
			$reason = $data["Reason"];
			$text .= "§cMute\n§e期限(残り):§f $time\n理由:§f $reason";
		}
		$text .= "\n";
		return [
			'type' => 'form',
			'title' => '§l【 詳細データ 】',
			'content' => "$text",
			'buttons' => [["text" => "§4処罰を撤回"],["text" => "閉じる"]]
		];
	}
}