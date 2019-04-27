<?php
namespace util;

class File {
	// append a file by content (string for a single, or array for multiple lines)
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
	
	// create the path (recursive) for a file
	public static function createFilePath($filename) {
		$dirname = dirname($filename);
		if (!is_dir($dirname)) return mkdir($dirname, 0777, true);
		return true;
	}
	
	// create a temporary file in the systems /tmp dir
	public static function temp($prefix = '') {
		return \tempnam(sys_get_temp_dir(), $prefix);
	}
}
