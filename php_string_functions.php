<?php
/*
	This class holds PHP functions for use with strings
*/
class Strings {
	/**
	 * Take XML content and convert it to a PHP array
	 * @param <string> $xml Raw XML data
	 * @param <string> $mainHeading (optional) If there is a primary heading within the XML that you only want the array for
	 * @return <array> XML data in array format
	 * @category Strings
	 * <code>
	 *  $result = Strings::xmlToArray('<root><name>Name</name><surname>Surname</surname></root>');
	 * </code>
	 */
	function xmlToArray($xml, $mainHeading = '') {
		try {
			$deXml = simplexml_load_string($xml);
			$deJson = json_encode($deXml);
			$xml_array = json_decode($deJson, TRUE);
			if (!empty($mainHeading)) {
				$returned = $xml_array[$mainHeading];
				return $returned;
			} else {
				return $xml_array;
			}
		} catch (Exception $err) {
			return $err->getMessage();
		}
	}
	/**
	 * Adds an post-fix to the number as if it is a ranked number
	 * @param <integer> $rank Number to rank
	 * @return <string> Ranked string
	 * @category Strings
	 * <code>
	 *  $result = Strings::makeRanked(6);
	 * </code>
	 */
	function makeRanked($rank) {
		try {
			$last = substr($rank, -1);
			$seclast = substr($rank, -2, -1);
			if ($last > 3 || $last == 0) {
				$ext = 'th';
			} else if ($last == 3) {
				$ext = 'rd';
			} else if ($last == 2) {
				$ext = 'nd';
			} else {
				$ext = 'st';
			}
			if ($last == 1 && $seclast == 1) {
				$ext = 'th';
			}
			if ($last == 2 && $seclast == 1) {
				$ext = 'th';
			}
			if ($last == 3 && $seclast == 1) {
				$ext = 'th';
			}
			return $rank . $ext;
		} catch (Exception $err) {
			return $err->getMessage();
		}
	}
	/**
	 * Creates a tag cloud based on the array values
	 * @param <array> $data Array to create the tag from
	 * @param <integer> $minFontSize (optional) Minimum font size
	 * @param <integer> $maxFontSize (optional) Maximum font size
	 * @return <array> Array containing the sized tag values
	 * @category Strings
	 * <code>
	 *  $result = Strings::getCloud(array('I'=>39, 'Am'=>18, 'Hiding'=>2, 'From'=>29, 'You'=>22));
	 * </code>
	 */
	function getCloud($data = array(), $minFontSize = 12, $maxFontSize = 30) {
		try {
			$minimumCount = min($data);
			$maximumCount = max($data);
			$spread = $maximumCount - $minimumCount;
			$cloudHTML = '';
			$cloudTags = array();
			$spread == 0 && $spread = 1;
			foreach ($data as $tag => $count) {
				$size = $minFontSize + ( $count - $minimumCount ) * ( $maxFontSize - $minFontSize ) / $spread;
				$cloudTags[] = '<a style="font-size: ' . floor($size) . 'px'
						. '" class="tag_cloud" href="#" title="\'' . $tag .
						'\' returned a count of ' . $count . '">'
						. htmlspecialchars(stripslashes($tag)) . '</a>';
			}
			return join("\n", $cloudTags) . "\n";
		} catch (Exception $err) {
			return $err->getMessage();
		}
	}
	/**
	 * Function adds an ending slash to the end of the given url
	 * @param <string> $path Path to add an ending slash to
	 * @return <string> Path ending with a slash
	 * @category Strings
	 * <code>
	 *  $result = Strings::addEndingSlash('C:/one/two');
	 * </code>
	 */
	function addEndingSlash($path) {
		try {
			$slash_type = (strpos($path, '\\') === 0) ? 'win' : 'unix';
			$last_char = substr($path, strlen($path) - 1, 1);
			if ($last_char != '/' and $last_char != '\\') {
				// no slash:
				$path .= ( $slash_type == 'win') ? '\\' : '/';
			}
			return $path;
		} catch (Exception $err) {
			return $err->getMessage();
		}
	}
	/**
	 * Replaces special charactes with safe characters
	 * @param <string> $string String to be checked
	 * @return <string> Replaced string
	 * @category Strings
	 * <code>
	 *  $result = String::formatAsKey('test: *&^%$#@!test');
	 * </code>
	 */
	function formatAsKey($string) {
		try {
			$string = strtolower($string);
			// Fix german special chars
			$string = preg_replace('/[��]/', 'ae', $string);
			$string = preg_replace('/[��]/', 'ue', $string);
			$string = preg_replace('/[��]/', 'oe', $string);
			$string = preg_replace('/[�]/', 'ss', $string);
			// Replace other special chars
			$specialChars = array(
				'sharp' => '#', 'dot' => '.', 'plus' => '+',
				'and' => '&', 'percent' => '%', 'dollar' => '$',
				'equals' => '=',
			);
			while (list($replacement, $char) = each($specialChars))
				$string = str_replace($char, '-' . $replacement . '-', $string);
			$string = strtr(
					$string, "�����������������������������������������������������", "AAAAAAaaaaaaOOOOOOooooooEEEEeeeeCcIIIIiiiiUUUUuuuuyNn"
			);
			// Remove all remaining other unknown characters        
			$string = preg_replace('/[^a-z0-9\-]/', '-', $string);
			$string = preg_replace('/^[\-]+/', '', $string);
			$string = preg_replace('/[\-]+$/', '', $string);
			$string = preg_replace('/[\-]{2,}/', '-', $string);
			return $string;
		} catch (Exception $err) {
			return $err->getMessage();
		}
	}
	/**
	 * Converts sizes like kb, mb, gb, tb to bytes
	 * @param <string> $size Size to convert in words
	 * @return <string> byte equivalent of the size given
	 * @category Strings
	 * <code>
	 * 	$result = Strings::stringSizeToBytes('3gb');
	 * </code>
	 */
	function stringSizeToBytes($size) {
		try {
			$Unit = strtolower($Size);
			$Unit = preg_replace('/[^a-z]/', '', $Unit);
			$Value = intval(preg_replace('/[^0-9]/', '', $size));
			$Units = array('b' => 0, 'kb' => 1, 'mb' => 2, 'gb' => 3, 'tb' => 4);
			$Exponent = isset($Units[$Unit]) ? $Units[$Unit] : 0;
			return ($Value * pow(1024, $Exponent)) . ' bytes';
		} catch (Exception $err) {
			return $err->getMessage();
		}
	}
	/**
	 * Function builds a email link with the given parameters
	 * @param <string> $email Email address to use in the link
	 * @param <string> $linkText Link text to be shown
	 * @param <string> $attrs (optional) Any class attributes to be applied to the link
	 * @return <string> Email link with email and text
	 * @category Strings
	 * <code>
	 *  $result = Strings::encodeEmailLink('me@email.com', 'Go');
	 * </code>
	 */
	function encodeEmailLink($email, $linkText, $attrs = '') {
		try {
			$email = str_replace('@', '&#64;', $email);
			$email = str_replace('.', '&#46;', $email);
			$email = str_split($email, 5);
			$linkText = str_replace('@', '&#64;', $linkText);
			$linkText = str_replace('.', '&#46;', $linkText);
			$linkText = str_split($linkText, 5);
			$part1 = '<a href="ma';
			$part2 = 'ilto&#58;';
			$part3 = '" ' . $attrs . ' >';
			$part4 = '</a>';
			$encoded = '<script type="text/javascript">';
			$encoded .= "document.write('$part1');";
			$encoded .= "document.write('$part2');";
			foreach ($email as $e) {
				$encoded .= "document.write('$e');";
			}
			$encoded .= "document.write('$part3');";
			foreach ($linkText as $l) {
				$encoded .= "document.write('$l');";
			}
			$encoded .= "document.write('$part4');";
			$encoded .= '</script>';
			return $encoded;
		} catch (Exception $err) {
			return $err->getMessage();
		}
	}
	/**
	 * Function creates a clickable url link with the specified url
	 * @param <string> $url Url to create the link from
	 * @return <string> Clickable url sing the given url
	 * @category Strings
	 * <code>
	 *  $result = Strings::makeClickableUrl('http://www.go.com');
	 * </code>
	 */
	function makeClickableUrl($url) {
		try {
			return preg_replace_callback(
					'#\bhttps?://[^\s()<>]+(?:\([\w\d]+\)|([^[:punct:]\s]|/))#', create_function(
							'$matches', 'return "<a href=\'{$matches[0]}\'>{$matches[0]}</a>";'
					), $url
			);
		} catch (Exception $err) {
			return $err->getMessage();
		}
	}
	/**
	 * Function creates a link whether it is a normal url or emil link
	 * @param <string> $url Url to create a link from
	 * @param <boolean> $isEmail Adds the 'mailto:' in front of the link
	 * @param <string> $urlText Text to use for he url
	 * @param <string> $cssClass (optional) Css class to be applied to the link
	 * @return <string> Clickable url or email link
	 * @category Strings
	 * <code>
	 *  $result = Strings::createanchorTag('http://www.google.com', TRUE, 'Google');
	 * </code>
	 */
	function createanchorTag($url, $isEmail, $urlText, $cssClass = '') {
		try {
			$anchorTag = "";
			// Prepare the css class if requested
			if (strlen($cssClass) > 0) {
				$cssClass = "class=\"" . $cssClass . "\" ";
			}
			// Clean URL text
			if (!strlen($urlText) > 0) {
				$urlText = $url;
			}
			// If a URL was passed create an anchor tag
			if (strlen($url) > 0) {
				if ($isEmail == TRUE) {
					$anchorTag = "<a " . $cssClass . "href=\"mailto:" .
							$url . "\">" . $urlText . "</a>";
				} else {
					$anchorTag = "<a " . $cssClass . "href=\"" .
							$url . "\">" . $urlText . "</a>";
				}
			}
			return $anchorTag;
		} catch (Exception $err) {
			return $err->getMessage();
		}
	}
	/**
	 * Generates a random readable string. Ideal for password generation
	 * @param <integer> $length Length of the string
	 * @return <string> Random readable string
	 * @category Strings
	 * <code>
	 *  $result = Strings::readableRandomString(5);
	 * </code>
	 */
	function readableRandomString($length) {
		try {
			$conso = array("b", "c", "d", "f", "g", "h", "j", "k", "l",
				"m", "n", "p", "r", "s", "t", "v", "w", "x", "y", "z");
			$vocal = array("a", "e", "i", "o", "u", "0");
			$numbers = array("1", "2", "3", "4", "5", "6", "7", "8", "9");
			$password = "";
			srand((double) microtime() * 1000000);
			$max = $length / 2;
			for ($i = 1; $i <= $max; $i++) {
				$password.=$conso[rand(0, 19)];
				$password.=$vocal[rand(0, 4)];
				$password.=$numbers[rand(0, 9)];
			}
			return $password;
		} catch (Exception $err) {
			return $err->getMessage();
		}
	}
	/**
	 * Truncates a string at a specific place after a specific number of characters and appending the string with specified characters
	 * @param <string> $string String to be truncated
	 * @param <integer> $limit Amount of characters to display
	 * @param <string> $break (optional) Character where the string should be truncated
	 * @param <string> $pad (optional) Postfix that should be added to the end of the truncated string
	 * @return <string> Truncated string with postfix added
	 * @category Strings
	 * <code>
	 *  $result = Strings::teaserTruncate('jdhskjhdj kadjasjdhas', 5);
	 * </code>
	 */
	function teaserTruncate($string, $limit, $break = ' ', $pad = '...') {
		try {
			// return with no change if string is shorter than $limit  
			if (strlen($string) <= $limit)
				return $string;
			// is $break present between $limit and the end of the string?  
			if (FALSE !== ($breakpoint = strpos($string, $break, $limit))) {
				if ($breakpoint < strlen($string) - 1) {
					$string = substr($string, 0, $breakpoint) . $pad;
				}
			}
			return $string;
		} catch (Exception $err) {
			return $err->getMessage();
		}
	}
	/**
	 * Create a web friendly URL slug from a string
	 *
	 * Although supported, transliteration is discouraged because
	 * 1) most web browsers support UTF-8 characters in URLs
	 * 2) transliteration causes a loss of information
	 *
	 * @param <string> $str String to create slug from
	 * @param <array> $options (optional) Array with options
	 * @return <string> Slug formatted string
	 * @category Strings
	 * <code>
	 *  $result = Strings::createSlug('jdhskjhdj kadjasjdh asdsa as');
	 * </code>
	 */
	function createSlug($str, $options = array()) {
		try {
			// Make sure string is in UTF-8 and strip invalid UTF-8 characters
			$str = mb_convert_encoding((string) $str, 'UTF-8', mb_list_encodings());
			$defaults = array(
				'delimiter' => '-',
				'limit' => NULL,
				'lowercase' => TRUE,
				'replacements' => array(),
				'transliterate' => FALSE,
			);
			// Merge options
			$options = array_merge($defaults, $options);
			$char_map = array(
				// Latin
				'À' => 'A', 'Á' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'A', 'Å' => 'A', 'Æ' => 'AE', 'Ç' => 'C',
				'È' => 'E', 'É' => 'E', 'Ê' => 'E', 'Ë' => 'E', 'Ì' => 'I', 'Í' => 'I', 'Î' => 'I', 'Ï' => 'I',
				'Ð' => 'D', 'Ñ' => 'N', 'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O', 'Õ' => 'O', 'Ö' => 'O', 'Ő' => 'O',
				'Ø' => 'O', 'Ù' => 'U', 'Ú' => 'U', 'Û' => 'U', 'Ü' => 'U', 'Ű' => 'U', 'Ý' => 'Y', 'Þ' => 'TH',
				'ß' => 'ss',
				'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a', 'å' => 'a', 'æ' => 'ae', 'ç' => 'c',
				'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e', 'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i',
				'ð' => 'd', 'ñ' => 'n', 'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o', 'ö' => 'o', 'ő' => 'o',
				'ø' => 'o', 'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ü' => 'u', 'ű' => 'u', 'ý' => 'y', 'þ' => 'th',
				'ÿ' => 'y',
				// Latin symbols
				'©' => '(c)',
				// Greek
				'Α' => 'A', 'Β' => 'B', 'Γ' => 'G', 'Δ' => 'D', 'Ε' => 'E', 'Ζ' => 'Z', 'Η' => 'H', 'Θ' => '8',
				'Ι' => 'I', 'Κ' => 'K', 'Λ' => 'L', 'Μ' => 'M', 'Ν' => 'N', 'Ξ' => '3', 'Ο' => 'O', 'Π' => 'P',
				'Ρ' => 'R', 'Σ' => 'S', 'Τ' => 'T', 'Υ' => 'Y', 'Φ' => 'F', 'Χ' => 'X', 'Ψ' => 'PS', 'Ω' => 'W',
				'Ά' => 'A', 'Έ' => 'E', 'Ί' => 'I', 'Ό' => 'O', 'Ύ' => 'Y', 'Ή' => 'H', 'Ώ' => 'W', 'Ϊ' => 'I',
				'Ϋ' => 'Y',
				'α' => 'a', 'β' => 'b', 'γ' => 'g', 'δ' => 'd', 'ε' => 'e', 'ζ' => 'z', 'η' => 'h', 'θ' => '8',
				'ι' => 'i', 'κ' => 'k', 'λ' => 'l', 'μ' => 'm', 'ν' => 'n', 'ξ' => '3', 'ο' => 'o', 'π' => 'p',
				'ρ' => 'r', 'σ' => 's', 'τ' => 't', 'υ' => 'y', 'φ' => 'f', 'χ' => 'x', 'ψ' => 'ps', 'ω' => 'w',
				'ά' => 'a', 'έ' => 'e', 'ί' => 'i', 'ό' => 'o', 'ύ' => 'y', 'ή' => 'h', 'ώ' => 'w', 'ς' => 's',
				'ϊ' => 'i', 'ΰ' => 'y', 'ϋ' => 'y', 'ΐ' => 'i',
				// Turkish
				'Ş' => 'S', 'İ' => 'I', 'Ç' => 'C', 'Ü' => 'U', 'Ö' => 'O', 'Ğ' => 'G',
				'ş' => 's', 'ı' => 'i', 'ç' => 'c', 'ü' => 'u', 'ö' => 'o', 'ğ' => 'g',
				// Russian
				'А' => 'A', 'Б' => 'B', 'В' => 'V', 'Г' => 'G', 'Д' => 'D', 'Е' => 'E', 'Ё' => 'Yo', 'Ж' => 'Zh',
				'З' => 'Z', 'И' => 'I', 'Й' => 'J', 'К' => 'K', 'Л' => 'L', 'М' => 'M', 'Н' => 'N', 'О' => 'O',
				'П' => 'P', 'Р' => 'R', 'С' => 'S', 'Т' => 'T', 'У' => 'U', 'Ф' => 'F', 'Х' => 'H', 'Ц' => 'C',
				'Ч' => 'Ch', 'Ш' => 'Sh', 'Щ' => 'Sh', 'Ъ' => '', 'Ы' => 'Y', 'Ь' => '', 'Э' => 'E', 'Ю' => 'Yu',
				'Я' => 'Ya',
				'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd', 'е' => 'e', 'ё' => 'yo', 'ж' => 'zh',
				'з' => 'z', 'и' => 'i', 'й' => 'j', 'к' => 'k', 'л' => 'l', 'м' => 'm', 'н' => 'n', 'о' => 'o',
				'п' => 'p', 'р' => 'r', 'с' => 's', 'т' => 't', 'у' => 'u', 'ф' => 'f', 'х' => 'h', 'ц' => 'c',
				'ч' => 'ch', 'ш' => 'sh', 'щ' => 'sh', 'ъ' => '', 'ы' => 'y', 'ь' => '', 'э' => 'e', 'ю' => 'yu',
				'я' => 'ya',
				// Ukrainian
				'Є' => 'Ye', 'І' => 'I', 'Ї' => 'Yi', 'Ґ' => 'G',
				'є' => 'ye', 'і' => 'i', 'ї' => 'yi', 'ґ' => 'g',
				// Czech
				'Č' => 'C', 'Ď' => 'D', 'Ě' => 'E', 'Ň' => 'N', 'Ř' => 'R', 'Š' => 'S', 'Ť' => 'T', 'Ů' => 'U',
				'Ž' => 'Z',
				'č' => 'c', 'ď' => 'd', 'ě' => 'e', 'ň' => 'n', 'ř' => 'r', 'š' => 's', 'ť' => 't', 'ů' => 'u',
				'ž' => 'z',
				// Polish
				'Ą' => 'A', 'Ć' => 'C', 'Ę' => 'e', 'Ł' => 'L', 'Ń' => 'N', 'Ó' => 'o', 'Ś' => 'S', 'Ź' => 'Z',
				'Ż' => 'Z',
				'ą' => 'a', 'ć' => 'c', 'ę' => 'e', 'ł' => 'l', 'ń' => 'n', 'ó' => 'o', 'ś' => 's', 'ź' => 'z',
				'ż' => 'z',
				// Latvian
				'Ā' => 'A', 'Č' => 'C', 'Ē' => 'E', 'Ģ' => 'G', 'Ī' => 'i', 'Ķ' => 'k', 'Ļ' => 'L', 'Ņ' => 'N',
				'Š' => 'S', 'Ū' => 'u', 'Ž' => 'Z',
				'ā' => 'a', 'č' => 'c', 'ē' => 'e', 'ģ' => 'g', 'ī' => 'i', 'ķ' => 'k', 'ļ' => 'l', 'ņ' => 'n',
				'š' => 's', 'ū' => 'u', 'ž' => 'z'
			);
			// Make custom replacements
			$str = preg_replace(array_keys($options['replacements']), $options['replacements'], $str);
			// Transliterate characters to ASCII
			if ($options['transliterate']) {
				$str = str_replace(array_keys($char_map), $char_map, $str);
			}
			// Replace non-alphanumeric characters with our delimiter
			$str = preg_replace('/[^\p{L}\p{Nd}]+/u', $options['delimiter'], $str);
			// Remove duplicate delimiters
			$str = preg_replace('/(' . preg_quote($options['delimiter'], '/') . '){2,}/', '$1', $str);
			// Truncate slug to max. characters
			$str = mb_substr($str, 0, ($options['limit'] ? $options['limit'] : mb_strlen($str, 'UTF-8')), 'UTF-8');
			// Remove delimiter from ends
			$str = trim($str, $options['delimiter']);
			return $options['lowercase'] ? mb_strtolower($str, 'UTF-8') : $str;
		} catch (Exception $err) {
			return $err->getMessage();
		}
	}
	/**
	 * Highlights and bolds specific words in the text
	 * @param <string> $sString String to check
	 * @param <array> $aWords Words to highlight and make bold in the text
	 * @return <string> String with highlighted words
	 * @category Strings
	 * <code>
	 *  $result = Strings::highlightText('<root>was this young root', array('root', 'this'));
	 * </code>
	 */
	function highlightText($sString, $aWords) {
		try {
			if (!is_array($aWords) || empty($aWords) || !is_string($sString)) {
				return FALSE;
			}
			$sWords = implode('|', $aWords);
			return preg_replace('@\b(' . $sWords . ')\b@si', '<strong style="background-color:yellow">$1</strong>', $sString);
		} catch (Exception $err) {
			return $err->getMessage();
		}
	}
	/**
	 * Parses html syntax and handles specified code as html code
	 * @param <string> $code Code to parse as html
	 * @return <string> Code parsed as html
	 * @category Strings
	 * <code>
	 *  $result = Strings::parseSyntax('<h1>jkj</h1>hello<b>lkl</b>');
	 * </code>
	 */
	function parseSyntax($code) {
		try {
			// this matches --> "foobar" <--
			$code = preg_replace(
					'/"(.*?)"/U', '&quot;<span style="color: #007F00">$1</span>&quot;', $code
			);
			// hightlight functions and other structures like --> function foobar() <--- 
			$code = preg_replace(
					'/(\s)\b(.*?)((\b|\s)\()/U', '$1<span style="color: #0000ff">$2</span>$3', $code
			);
			// Match comments (like /* */): 
			$code = preg_replace(
					'/(\/\/)(.+)\s/', '<span style="color: #660066; background-color: #FFFCB1;"><i>$0</i></span>', $code
			);
			$code = preg_replace(
					'/(\/\*.*?\*\/)/s', '<span style="color: #660066; background-color: #FFFCB1;"><i>$0</i></span>', $code
			);
			// hightlight braces:
			$code = preg_replace('/(\(|\[|\{|\}|\]|\)|\->)/', '<strong>$1</strong>', $code);
			// hightlight variables $foobar
			$code = preg_replace(
					'/(\$[a-zA-Z0-9_]+)/', '<span style="color: #0000B3">$1</span>', $code
			);
			/* The \b in the pattern indicates a word boundary, so only the distinct
			 * * word "web" is matched, and not a word partial like "webbing" or "cobweb" 
			 */
			// special words and functions
			$code = preg_replace(
					'/\b(print|echo|new|function)\b/', '<span style="color: #7F007F">$1</span>', $code
			);
			return $code;
		} catch (Exception $err) {
			return $err->getMessage();
		}
	}
	/**
	 * Used when debugging variable values
	 * @param <string> $val Variable to be degugged
	 * @param <boolean> $print (optional) TRUE if the value should be printed out
	 * @param <string> $method (optional) Method used to debug variable
	 * @return <string> Debugged value if $print is TRUE
	 * @category Strings
	 * <code>
	 *  $result = Strings::debugValue('1 + 1', TRUE, 'var_dump');
	 * </code>
	 */
	function debugValue($val, $print = TRUE, $method = 'var_export') {
		try {
			if ($method == 'var_export') {
				$r = var_export($val, TRUE);
			} else {
				$r = print_r($val, TRUE);
			}
			if ($print) {
				print "<pre>[[" . htmlspecialchars($r) . "]]</pre>";
			} else {
				return "<pre>[[" . htmlspecialchars($r) . "]]</pre>";
			}
		} catch (Exception $err) {
			return $err->getMessage();
		}
	}
	/**
	 * Splits a string between a starting and ending point
	 * @param <integer> $min Starting point
	 * @param <integer> $max Ending point
	 * @param <string> $str String to split
	 * @return <array> Array of split strings
	 * @category Strings
	 * <code>
	 *  $result = Strings::randomSplit(3, 10, 'hello my name is ..');
	 * </code>
	 */
	function randomSplit($min, $max, $str) {
		try {
			$a = array();
			while ($str != '') {
				$p = rand($min, $max);
				$p = ($p > strlen($str)) ? strlen($str) : $p;
				$buffer = substr($str, 0, $p);
				$str = substr($str, $p, strlen($str) - $p);
				$a[] = $buffer;
			}
			return $a;
		} catch (Exception $err) {
			return $err->getMessage();
		}
	}
	
	//TEST
	function strToBin3($input)
	{
	    if (!is_string($input))
		return false;
	    $input = unpack('H*', $input);
	    $chunks = str_split($input[1], 2);
	    $ret = '';
	    foreach ($chunks as $chunk)
	    {
		$temp = base_convert($chunk, 16, 2);
		$ret .= str_repeat("0", 8 - strlen($temp)) . $temp;
	    }
	    return $ret;
	}
	
	FUNCTION bin2text($bin_str) 
{ 
    $text_str = ''; 
    $chars = EXPLODE("\n", CHUNK_SPLIT(STR_REPLACE("\n", '', $bin_str), 8)); 
    $_I = COUNT($chars); 
    FOR($i = 0; $i < $_I; $text_str .= CHR(BINDEC($chars[$i])), $i  ); 
    RETURN $text_str; 
} 
 
FUNCTION text2bin($txt_str) 
{ 
    $len = STRLEN($txt_str); 
    $bin = ''; 
    FOR($i = 0; $i < $len; $i  ) 
    { 
        $bin .= STRLEN(DECBIN(ORD($txt_str[$i]))) < 8 ? STR_PAD(DECBIN(ORD($txt_str[$i])), 8, 0, STR_PAD_LEFT) : DECBIN(ORD($txt_str[$i])); 
    } 
    RETURN $bin; 
} 
PRINT text2bin('Isn't this cool?');
}
?>