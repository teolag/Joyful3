<?php

class CssMagic {

	private static $fileList=array();
	private static $path;
	private static $variablesFile;

	public static function addPattern($pattern) {
		// add multiple files at once
		// ex. all css files in folder
		// 	   "css/*.css"
    }

	public static function addFile($file) {
		self::$fileList[] = $file;
    }

	public static function addVariables($file) {
		self::$variablesFile = $file;
    }

	public static function setPath($path) {
		self::$path = $path;
	}

	public static function printInline($minified=true) {
		$css = self::getCssConcat();
		if($minified===true) {
			$css = self::minifyCss($css);
		}
		echo "<style>";
		echo $css;
		echo "</style>";
	}

	public static function printInclude($minified=true) {
		$last_modified = 0;
		foreach(self::$fileList as $file) {
			if($last_modified < filemtime($file)) $last_modified=filemtime($file);
		}

		if(self::$variablesFile!=null && file_exists(self::$variablesFile)) {
			if($last_modified < filemtime(self::$variablesFile)) $last_modified=filemtime(self::$variablesFile);
			$doReplaceVariables = true;
		} else {
			$doReplaceVariables = false;
		}

		$minifile = "style.mini.".date("ymdHis", $last_modified).".css";

		if(!file_exists($minifile)) {
			//File updated, save new version
			$css = self::getCssConcat();

			if($doReplaceVariables) {
				$css = self::replaceVariables($css);
			}

			if($minified===true) {
				$before = strlen($css);
				$css = self::minifyCss($css);
				$after = strlen($css);
				$precent = 100-round(($after/$before)*100,1);

				//Add compression rate
				$css = "/* " . date("Y-m-d H:i:s") . " | Removed ".($before-$after)." bytes | Compressed " . $precent . "% */\n" . $css;
			}

			//Delete old versions
			foreach (glob("style.mini*.css") as $oldfile) unlink($oldfile);

			//Save new file
			file_put_contents($minifile, $css);
		}


		echo '<link rel="stylesheet" href="/' . $minifile . '" type="text/css" />' . "\n";
	}

	private static function getCssConcat() {
		$css = "";
		foreach(self::$fileList as $file) {
			$css .= file_get_contents($file);
		}
		return $css;
	}

	private static function replaceVariables($css) {
		$json = file_get_contents(self::$variablesFile);
		$variables = json_decode($json, true);
		foreach($variables as $key => $value) {
			$css = str_replace("[[".$key."]]", $value, $css);
		}
		return $css;
	}

	private static function minifyCss($css) {
		// TODO
		// Remove empty selectors    				ex:	div#section.blue{}
		// Remove last value if equal to second		ex:	margin: 10px 100px 10px 100px;
		// Remove second value if equal to first	ex:	margin: 10px 10px;

		$css = preg_replace('|[\n\r\t]*|', "", $css); 					//Remove tabs and line breaks
		$css = preg_replace('|[ ]+|', " ", $css);						//Remove more than one space in a row
		$css = preg_replace('/([ :])0(px|%)/', '${1}0', $css);			//Remove unit after a single zero
		$css = preg_replace('/([, :])0\./', '${1}.', $css);				//Remove zeros before decimal in number less than 1
		$css = preg_replace('|\/\*.*?(\*\/)|', "", $css);				//Remove comments
		$css = preg_replace('|;\s|', ";", $css);						//Remove space between styles
		$css = preg_replace('|:\s|', ":", $css);						//Remove space between key and attribute
		$css = preg_replace('|\s*\{\s*|', "{", $css);					//Remove space around {
		$css = preg_replace('|\s*\}\s*|', "}", $css);					//Remove space around }
		$css = preg_replace('|\s*\,\s*|', ",", $css);					//Remove space around commas
		$css = preg_replace('|;\}|', "}", $css);						//Remove semi-colon before end curly braces
		$css = preg_replace('|([: ])white([ ;])|', '${1}#fff${2}', $css);//Replace white with #fff
		$css = preg_replace('|([: ])black([ ;])|', '${1}#000${2}', $css);//Replace black with #000
		$css = preg_replace_callback('|#([0-9A-Fa-f]{6})([ ;\}])|', 'self::shortenColors', $css);
		$css = trim($css);
		return $css;
	}

	// Converts colors like #aabbcc to #abc
	private static function shortenColors($m) {
		$hex = $m[1];
		if($hex[0]==$hex[1] && $hex[0]==$hex[1] && $hex[0]==$hex[1]) $hex = $hex[0].$hex[2].$hex[4];
		return "#".$hex.$m[2];
	}


}


/*
// Find the last updated file in the css folder
$files = glob("../css/*.css");


$files[] = "../codemirror/lib/codemirror.css";
$files[] = "../codemirror/addon/dialog/dialog.css";
//$files[] = "../codemirror/theme/ambiance.css";
//$files[] = "../codemirror/theme/lesser-dark.css";
//$files[] = "../codemirror/theme/neat.css";

$files[] = "../xioPop/XioPop.css";

$last_modified = 0;
foreach($files as $file) {
	if($last_modified < filemtime($file)) $last_modified=filemtime($file);
}

$tsstring = gmdate('D, d M Y H:i:s ', $last_modified) . 'GMT';
$if_modified_since = isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) ? $_SERVER['HTTP_IF_MODIFIED_SINCE'] : false;
if ($if_modified_since && $if_modified_since == $tsstring) {
    header('HTTP/1.1 304 Not Modified');
    exit();
}


$minifile = "style.mini.".date("ymdHis", $last_modified).".css";

if(file_exists($minifile)) {
	//No updates, use existing file
	$css=file_get_contents($minifile);
}
else {
	//File updated, save new version
	$css="";
	foreach($files as $file) {
		$css .= file_get_contents($file);
	}
	$before = strlen($css);
	$css = cssMinify($css);
	$after = strlen($css);
	$precent = 100-round(($after/$before)*100,1);

	//Add compression rate
	$css = "// " . date("Y-m-d H:i:s", $last_modified) . " | Removed ".($before-$after)." bytes | Compressed " . $precent . "% \n" . $css;

	//Delete old versions
	foreach (glob("style.mini*.css") as $oldfile) unlink($oldfile);

	//Save new file
	file_put_contents($minifile, $css);
}

header("Last-Modified: $tsstring");
header("Content-type: text/css");
echo $css;



//Find and removes unused bytes

*/

?>
