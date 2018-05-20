<?php
/*
	This class holds PHP functions for use with validation concepts
*/
class Validation {
	/**
	 * Cleans user input to prevent any scripting to be entered
	 * @param <string> $input Input value to be cleaned
	 * @return <string> Cleansed value
	 * @category Validation
	 * <code>
	 *  $result = Validation::cleanInput('&^^%<h1><sript>alert('adasda');</script>');
	 * </code>
	 */
	function cleanInput($input) {
		try {
			$search = array(
				'@<script[^>]*?>.*?</script>@si', // Strip out javascript
				'@<[\/\!]*?[^<>]*?>@si', // Strip out HTML tags
				'@<style[^>]*?>.*?</style>@siU', // Strip style tags properly
				'@<![\s\S]*?--[ \t\n\r]*>@' // Strip multi-line comments
			);
			$output = preg_replace($search, '', $input);
			return $output;
		} catch (Exception $err) {
			return $err->getMessage();
		}
	}
	/**
	 * Checks to see if the IP is in the valid IP format
	 * @param <string> $ip IP address to be checked
	 * @return <boolean> Is the IP valid or not
	 * @category Validation
	 * <code>
	 *  $result = Validation::isValidIp('12.612.212w.12');
	 * </code>
	 */
	function isValidIp($ip) {
		try {
			if (preg_match('/^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}$/', $ip)) {
				return TRUE;
			}
			return FALSE;
		} catch (Exception $err) {
			return $err->getMessage();
		}
	}
	/**
	 * Check to see if the given email is in the correct format
	 * @param <string> $email Email address to be checked
	 * @param <boolean> $testMx (optional) Should the email be tested for existance or not
	 * @return <boolean> Is the email in the correct format or not
	 * @category Validation
	 * <code>
	 *  $result = Validation::isValidEmail('info@r@email.co.co');
	 * </code>
	 */
	function isValidEmail($email, $testMx = FALSE) {
		try {
			if (eregi("^([_a-z0-9-]+)(\.[_a-z0-9-]+)*@([a-z0-9-]+)(\.[a-z0-9-]+)*(\.[a-z]{2,4})$", $email))
				if ($testMx) {
					list($username, $domain) = split("@", $email);
					return getmxrr($domain, $mxrecords);
				} else {
					return TRUE;
				} else {
				return FALSE;
			}
		} catch (Exception $err) {
			return $err->getMessage();
		}
	}
	/**
	 * Checks if the given date is in the correct format (yyyy-mm-dd hh:mm:ss)
	 * @param <string> $timestamp Timestamp to be checked
	 * @return <boolean> If the timestamp is in the correct format or not
	 * @category Validation
	 * <code>
	 *  $result = Validation::isValidTimestamp('1234-13-13 12:712:12');
	 * </code>
	 */
	function isValidTimestamp($timestamp) {
		try {
			if (preg_match("/^(\d{4})-(\d{2})-(\d{2}) ([01][0-9]|2[0-3]):([0-5][0-9]):([0-5][0-9])$/", $timestamp, $matches)) {
				if (checkdate($matches[2], $matches[3], $matches[1])) {
					return TRUE;
				}
			}
			return FALSE;
		} catch (Exception $err) {
			return $err->getMessage();
		}
	}
	/**
	 * Function cleans data array
	 * @param <array> $data Data array to be cleaned
	 * @return <array> Cleaned array 
	 * @category Validation
	 * <code>
	 *  $result = Validation::cleanData(array('hello', 'bye'));
	 * </code>
	 */
	function cleanData($data) {
		try {
			foreach ($data as $key => $val) {
				if (is_array($val)) {
					$data[$key] = $this->_clean_data_array($val);
				}
			}
			return $data;
		} catch (Exception $err) {
			return $err->getMessage();
		}
	}
	/**
	 * Converts all the html special characters to html entities
	 * @param <string> $input String to convert
	 * @return <string> Converted HTML entities string
	 * @category Strings
	 * <code>
	 *  $result = Strings::convertChars('jdhskjhdj kadjasjdh asdsa <as><p><h1>jhjh</h1>&');
	 * </code>
	 */
	function convertChars($input) {
		try {
			$text = trim($input); //<-- LINE 31
			$text = preg_replace("/(\r\n|\n|\r)/", "\n", $text); // cross-platform newlines
			$text = preg_replace("/\n\n\n\n+/", "\n", $text); // take care of duplicates 
			$text = htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
			$text = stripslashes($text);
			$text = str_replace("\n", " ", $text);
			$text = str_replace("\t", " ", $text);
			return $text;
		} catch (Exception $err) {
			return $err->getMessage();
		}
	}
}
?>