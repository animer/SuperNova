<?php
/**
 chat_add.php
   AJAX-called code to post message to chat

 Changelog:
   2.0 copyright (c) 2009-2001 by Gorlum for http://supernova.ws
     [+] Rewrote to remove unnecessary code dupes
   1.5 copyright (c) 2009-2001 by Gorlum for http://supernova.ws
     [~] More DDoS-realted fixes
   1.4 copyright (c) 2009-2001 by Gorlum for http://supernova.ws
     [~] DDoS-realted fixes
   1.3 copyright (c) 2009-2001 by Gorlum for http://supernova.ws
     [~] Security checks for SQL-injection
   1.2 by Ihor
   1.0 copyright 2008 by e-Zobar for XNova
*/

$skip_fleet_update = true;

include('common.' . substr(strrchr(__FILE__, '.'), 1));

if (($config->_MODE != CACHER_NO_CACHE) && ($config->chat_timeout) && ($config->array_get('users', $user['id'], 'chat_lastUpdate') + $config->chat_timeout < $time_now))
{
  die();
}

$msg = SYS_mysqlSmartEscape ($_POST["msg"]);
$chat_type = SYS_mysqlSmartEscape($_GET['chat_type'] ? $_GET['chat_type'] : $_POST['chat_type']);
$ally_id = $user['ally_id'];

// On récupère les informations du message et de l'envoyeur
if ($msg && $user['username']) {
   $nick = trim(strip_tags($user['username']));
   if($ally_id && $chat_type != 'ally'){
     $tag = doquery("SELECT ally_tag FROM {{alliance}} WHERE id = {$user['ally_id']}", '', true);
     $nick .= '(' . trim(strip_tags($tag['ally_tag'])) . ')';
   };
   if ($user['authlevel'] == 3) {
     $nick = preg_replace("#(.+)#", $config->chat_admin_highlight, $nick); //isU
   }
   $nick = addslashes ($nick);
   $msg = iconv('UTF-8', 'CP1251', $msg); // CHANGE IT !!!!!!!!!!!
} else {
   $msg="";
   $nick="";
}

if ($msg && $nick) {
  if($chat_type!="ally" || !$ally_id)
    $ally_id = 0;

  $query = doquery("INSERT INTO {{chat}} (user, ally_id, message, timestamp) VALUES ('{$nick}','{$ally_id}','{$msg}', '{$time_now}');");
  $config->array_set('users', $user['id'], 'chat_lastUpdate', $time_now);
}

?>
