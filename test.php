<?php
include './SlackAPI.php';

$slack = new SlackAPI('[トークン]');
// ユーザーとして投稿
$slack->chatPotMessage('[チャンネル]', '[メッセージ]');

// BOTとして投稿
$slack->chatBotMessage('[チャンネル]', '[メッセージ]');


