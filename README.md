# 処罰プラグイン
シンプルな処罰を追加します。

# 動作環境
PMMP 5.0

# 特徴
Muteではチャットのミュートだけではなく、tellなどのデフォルトのささやきコマンドも使用不可能にします。     
処罰時間の指定が可能で、期限BAN等をすることができます。    
Banする際には、XuidというIDを使用してプレイヤーをBanするので、    
名前が変わったとしても安心です。    
(サーバーに一度も入ったことがない人をBanする際は、サーバーに入ってきたときにXuidで上書きします。)    
後一応APIもあります(小声)   

# コマンド

|**コマンド**|**説明**|
|------------|--------|
|`/punish <PlayerID> <ban｜mute> <Reason> [time]`|処罰を与えます|
|`/unpunish <PlayerID> <ban｜mute>`|処罰を撤回します|
|`/punishform`|処罰情報を確認できます|

### [time]について
1dと書くと1日だけ処罰が施行されます。細かい調整が可能です。
例: 1d1h1m1s ->一日1時間1分1秒間の処罰
|**指定**|**説明**|
|------------|--------|
|`s`|秒|
|`m`|分|
|`h`|時|
|`d`|日|

※[time]の項目がない場合は永久という判定になります。

### 処罰したプレイヤーの情報を確認する方法
処罰したプレイヤー情報の確認は/punishformで行えます。
また、その時に処罰したプレイヤーをボタン一つで撤回も可能です。

# API
一応APIがあります。
```php
use punishment\api\punishmentAPI;

punishmentAPI::Ban("PlayerID","理由","時効までの秒数");
punishmentAPI::Mute("PlayerID","理由","時効までの秒数");
//時効までの秒数がいらない(永久処罰)にしたい場合は("PlayerID","理由")で大丈夫です。
punishmentAPI::unBan("PlayerID");
punishmentAPI::unMute("PlayerID");
```

# 実例
![BAN例](https://github.com/shunnyan/punishment/assets/80146606/6f366ab9-d62a-45a1-83a2-5863e41ed53c)

# 注意
中身のコードが汚いのは重々承知しています。本当に見にくくてすいません。

# ライセンス
MIT

