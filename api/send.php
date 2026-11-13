<?php
// Надсилає заявки з форми сайту в Telegram. Потрібен PHP з увімкненим cURL (є на більшості хостингів).
// === НАЛАШТУВАННЯ: вставте токен бота (від @BotFather) і ID чату ===
const BOT_TOKEN = 'ВСТАВТЕ_ТОКЕН_БОТА';
const CHAT_ID   = 'ВСТАВТЕ_CHAT_ID';
header('Content-Type: application/json; charset=utf-8');
function out($code,$ok){http_response_code($code);echo json_encode(['ok'=>$ok]);exit;}
if($_SERVER['REQUEST_METHOD']!=='POST')out(405,false);
$d=json_decode(file_get_contents('php://input'),true);if(!is_array($d))out(400,false);
if(!empty($d['website']))out(200,true); // пастка для ботів
$c=function($k)use($d){return trim(strip_tags((string)($d[$k]??'')));};
$name=$c('name');$phone=$c('phone');$svc=$c('service');$page=$c('page');
if(mb_strlen($name)<2||mb_strlen($name)>80||strlen(preg_replace('/\D/','',$phone))<10)out(422,false);
$f=sys_get_temp_dir().'/ed_'.md5($_SERVER['REMOTE_ADDR']??'');
if(file_exists($f)&&time()-filemtime($f)<30)out(429,false); // не частіше 1 заявки на 30 с
touch($f);
$text="🆕 Нова заявка з сайту\n👤 $name\n📞 $phone\n💆 $svc\n🔗 $page";
$ch=curl_init('https://api.telegram.org/bot'.BOT_TOKEN.'/sendMessage');
curl_setopt_array($ch,[CURLOPT_POST=>true,CURLOPT_POSTFIELDS=>['chat_id'=>CHAT_ID,'text'=>$text],CURLOPT_RETURNTRANSFER=>true,CURLOPT_TIMEOUT=>10]);
$j=json_decode((string)curl_exec($ch),true);
out(!empty($j['ok'])?200:502,!empty($j['ok']));
