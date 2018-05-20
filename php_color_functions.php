<?php
/*
	This class holds PHP functions for use with colors
*/
class Colors {
	/**
	 * Converts a hexadecimal value to a rgb formatted color value
	 * @param <string> $hexValue Value of the hexadecimal color thatneeds to be converted
	 * @return <array> Contains the values for Red, Green & Blue
	 * @category Colors
	 * <code>
	 *  $result = Colors::hexToRgb('#000FF0');
	 * </code>
	 */
	function hexToRgb($hexValue) {
		try {
			if ($hexValue[0] == '#') {
				$hexValue = substr($hexValue, 1);
			}
			if (strlen($hexValue) == 6) {
				list($r, $g, $b) = array($hexValue[0] . $hexValue[1], $hexValue[2] . $hexValue[3], $hexValue[4] . $hexValue[5]);
			} elseif (strlen($hexValue) == 3) {
				list($r, $g, $b) = array($hexValue[0] . $hexValue[0], $hexValue[1] . $hexValue[1], $hexValue[2] . $hexValue[2]);
			} else {
				return FALSE;
			}
			$r = hexdec($r);
			$g = hexdec($g);
			$b = hexdec($b);
			return array('R' => $r, 'G' => $g, 'B' => $b);
		} catch (Exception $err) {
			return $err->getMessage();
		}
	}
	/**
	 * Converts rgb formatted color value to hexadecimal format
	 * @param <integer> $r Red value
	 * @param <integer> $g Green value
	 * @param <integer> $b Blue value
	 * @return <string> Hexadecimal value of the color
	 * @category Colors
	 * <code>
	 *  $result = Colors::rgbToHex(0, 12, 255);
	 * </code>
	 */
	function rgbToHex($r, $g, $b) {
		try {
			$r = dechex($r);
			$g = dechex($g);
			$b = dechex($b);
			return "#" . $r . $g . $b;
		} catch (Exception $err) {
			return $err->getMessage();
		}
	}
	/**
	 * Inverts the specified color
	 * @param <string> $color Hexadecimal color to be inversed
	 * @return <string> Inverted color in hexadecimal format
	 * @category Colors
	 * <code>
	 *  $result = Colors::inverseColor('#00000');
	 * </code>
	 */
	function inverseColor($color) {
		try {
			$color = str_replace('#', '', $color);
			if (strlen($color) != 6) {
				return '000000';
			}
			$rgb = '';
			for ($x = 0; $x < 3; $x++) {
				$c = 255 - hexdec(substr($color, (2 * $x), 2));
				$c = ($c < 0) ? 0 : dechex($c);
				$rgb .= ( strlen($c) < 2) ? '0' . $c : $c;
			}
			return '#' . $rgb;
		} catch (Exception $err) {
			return $err->getMessage();
		}
	}
	/**
	 * Darkens the specified color
	 * @param <string> $color Hexadecimal color to darken
	 * @param <integer> $diff (optional) Difference of the original anddarkened color
	 * @return <string> Hexadecimal equivalent of the darkened color
	 * @category Colors
	 * <code>
	 *  $result = Colors::darkenColor('#00FF00');
	 * </code>
	 */
	function darkenColor($color, $diff = 20) {
		try {
			$color = str_replace('#', '', $color);
			if (strlen($color) != 6) {
				return '000000';
			}
			$rgb = '';
			for ($x = 0; $x < 3; $x++) {
				$c = hexdec(substr($color, (2 * $x), 2)) - $diff;
				$c = ($c < 0) ? 0 : dechex($c);
				$rgb .= ( strlen($c) < 2) ? '0' . $c : $c;
			}
			return '#' . $rgb;
		} catch (Exception $err) {
			return $err->getMessage();
		}
	}
	/**
	 * Generates a random color in hexadecimal format
	 * @return <string> Random hexadecimal color
	 * @category Colors
	 * <code>
	 *  $result = Colors::randomColor();
	 * </code>
	 */
	function randomColor() {
		try {
			mt_srand((double) microtime() * 1000000);
			$c = '';
			while (strlen($c) < 6) {
				$c .= sprintf("%02X", mt_rand(0, 255));
			}
			return '#' . $c;
		} catch (Exception $err) {
			return $err->getMessage();
		}
	}
}
?>