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
	
	// validate requester by ip
	// https://core.telegram.org/bots/webhooks#the-short-version
	public static function validate(&$ip = null) {
		if ($ip === null) {
			$ip = THROUGH_CLOUDFLARE && isset($_SERVER['HTTP_CF_CONNECTING_IP'])
				? $_SERVER['HTTP_CF_CONNECTING_IP']
				: $_SERVER['REMOTE_ADDR'];
		}
		$floatIp = self::ipToFloat($ip);
		
		$ipRanges = [
			[
				'from' => '149.154.160.0',
				'to' => '149.154.175.255'
			], [
				'from' => '91.108.4.0',
				'to' => '91.108.7.255'
			]
		];

		foreach ($ipRanges as $ipRange) {
			$floatIpRangeFrom = self::ipToFloat($ipRange['from']);
			$floatIpRangeTo = self::ipToFloat($ipRange['to']);
			if ($floatIp >= $floatIpRangeFrom && $floatIp <= $floatIpRangeTo) {
				// the ip address is in a valid range
				return true;
			}
		}
		return false;
	}
	
	// help function, convert ip address to a number
	private static function ipToFloat($ip) {
		return (float)sprintf('%u', ip2long($ip));
	}
}
