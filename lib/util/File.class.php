<?php
namespace util;

class File {
	public static function append($filename, $content = '') {
		self::createFilePath($filename);
		$fh = fopen($filename, 'a');
		if (is_array($content)) {
			array_walk($content, function($line) use ($fh) {
				fwrite($fh, $line.PHP_EOL);
			});
		} else fwrite($fh, $content.PHP_EOL);
		fclose($fh);
	}
	
	public static function createFilePath($filename) {
		$dirname = dirname($filename);
		if (!is_dir($dirname)) return mkdir($dirname, 0777, true);
		return true;
	}
	
	public static function temp($prefix = '') {
		return \tempnam(sys_get_temp_dir(), $prefix);
	}
}
