<?php
/*
	This class holds PHP functions for use with images
*/
class Images {
	/**
	 * Function resizes the specified image
	 * @param <string> $imageName Name of the image to resize
	 * @param <string> $tmpName Temporary name for the image
	 * @param <integer> $xmax Maximum width
	 * @param <integer> $ymax Maximum height
	 * @return <image> Resized image
	 * @category Images
	 * <code>
	 *  $result = Images::resizeImage('C:/img.jpg', 'C:/tmpimg.tmp', 100, 200);
	 * </code>
	 */
	function resizeImage($imageName, $tmpName, $xmax, $ymax) {
		$ext = explode(".", $imageName);
		$ext = $ext[count($ext) - 1];
		if ($ext == "jpg" || $ext == "jpeg") {
			$im = imagecreatefromjpeg($tmpName);
		} elseif ($ext == "png") {
			$im = imagecreatefrompng($tmpName);
		} elseif ($ext == "gif") {
			$im = imagecreatefromgif($tmpName);
		}
		$x = imagesx($im);
		$y = imagesy($im);
		if ($x <= $xmax && $y <= $ymax) {
			return $im;
		}
		if ($x >= $y) {
			$newx = $xmax;
			$newy = $newx * $y / $x;
		} else {
			$newy = $ymax;
			$newx = $x / $y * $newy;
		}
		$im2 = imagecreatetruecolor($newx, $newy);
		imagecopyresized($im2, $im, 0, 0, 0, 0, floor($newx), floor($newy), $x, $y);
		return $im2;
	}
}
?>