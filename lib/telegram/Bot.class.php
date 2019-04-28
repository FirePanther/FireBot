<?php
namespace telegram;

class Bot {
	const API_URL_PREFIX = 'https://api.telegram.org/bot';
	
	// telegram bot api method call
	public static function __callStatic($method, $arguments) {
		$botApiMethodUrl = self::API_URL_PREFIX.BOT_TOKEN.'/'.$method;
		
		$ch = curl_init($botApiMethodUrl);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $arguments[0]);
		$result = curl_exec($ch);
		curl_close($ch);
		
		$resultArray = @json_decode($result, true);
		
		\util\File::write(MAIN_DIR.'/log/last-response', [
			$botApiMethodUrl,
			$arguments[0],
			$result
		]);
		
		return $resultArray ?: $result;
	}
}
