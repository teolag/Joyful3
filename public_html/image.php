<?php

$imageSrc = $_SERVER['DOCUMENT_ROOT'] . urldecode($_REQUEST['uri']);



$imageType = $_REQUEST['type'];

switch($imageType) {

	case "productThumb":
		$size = 120;
		$thumbSrc = $_SERVER['DOCUMENT_ROOT'] . "/thumbs/" . md5($size.$imageSrc) . ".png";
		if(!is_file($thumbSrc)) {
			//$command = "convert '{$imageSrc}' -resize {$size}x{$size}\> '{$thumbSrc}'";
			$command = "convert '{$imageSrc}' -resize {$size}x{$size}^ -gravity center -extent {$size}x{$size} '{$thumbSrc}'";
			exec($command, $output, $return);

			die($command);
		}
		$image = file_get_contents($thumbSrc);
		header("Content-type: image/png");
		echo $image;
	break;

}



?>