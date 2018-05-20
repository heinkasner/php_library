<?php
/*
	This class holds PHP functions for use with arrays
*/
class Arrays {
	/**
	 * Retrieves a random element from a specified array
	 * @param <array> $a Array to retrieve random element from
	 * @return <string> Random element from array
	 * @category Arrays
	 * <code>
	 *  $result = Arrays::getRandomElement(array('1', '2', '3'));
	 * </code>
	 */
	function getRandomElement($a) {
		try {
			mt_srand((double) microtime() * 1000000);
			// get all array keys:
			$k = array_keys($a);
			// find a random array key:
			$r = mt_rand(0, count($k) - 1);
			$rk = $k[$r];
			// return the random key (if exists):
			return isset($a[$rk]) ? $a[$rk] : '';
		} catch (Exception $err) {
			return $err->getMessage();
		}
	}
	/**
	 * Shuffles the associative array keeping the keys and values in place
	 * @param <array> $array Array to shuffle
	 * @return <array> Shuffled array
	 * @category Arrays
	 * <code>
	 *  $result = Arrays::shuffleAssoc(array('1'=>1, '2'=>2, '3'=>3));
	 * </code>
	 */
	function shuffleAssoc($array) {
		try {
			$keys = array_keys($array);
			shuffle($keys);
			$result = array();
			foreach ($keys as $k) {
				$result[$k] = $array[$k];
			}
			return $result;
		} catch (Exception $err) {
			return $err->getMessage();
		}
	}
	/**
	 * Removes any duplicate values from the array
	 * @param <array> $arr Array to check for duplicate values
	 * @return <array> Array without duplicate values
	 * @category Arrays
	 * <code>
	 *  $result = Arrays::removeDuplicateValues(array('3', '2', '3', '1', '3'));
	 * </code>
	 */
	function removeDuplicateValues($arr) {
		try {
			$_a = array();
			while (list($key, $val) = each($arr)) {
				$_a[$val] = 1;
			}
			return array_keys($_a);
		} catch (Exception $err) {
			return $err->getMessage();
		}
	}
	/**
	 * Removes any empty or NULL elements from an array
	 * @param <array> $arr Array to remove empty values from
	 * @return <array> Array without empty values
	 * @category Arrays
	 * <code>
	 *  $result = Arrays::removeEmpty(array('3', '2', NULL, '1', '3', ''));
	 * </code>
	 */
	function removeEmpty($arr) {
		try {
			$narr = array();
			while (list($key, $val) = each($arr)) {
				if (is_array($val)) {
					$val = array_remove_empty($val);
					// does the result array contain anything?
					if (count($val) != 0) {
						// yes :-)
						$narr[$key] = $val;
					}
				} else {
					if (trim($val) != "") {
						$narr[$key] = $val;
					}
				}
			}
			unset($arr);
			return $narr;
		} catch (Exception $err) {
			return $err->getMessage();
		}
	}
	/**
	 * Prints the items in a specific array
	 * @param <array> $arr Array ob objects to print out
	 * @category Arrays
	 * <code>
	 *  Arrays::printArray(array('3', '2', NULL, '1', '', '3'));
	 * </code>
	 */
	function printArray($arr) {
		try {
			foreach ($arr as $object) {
				echo $object, "<br />";
			}
		} catch (Exception $err) {
			return $err->getMessage();
		}
	}
}
?>