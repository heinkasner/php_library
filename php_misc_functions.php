<?php
/*
	This class holds any non-specific functioons which can be widely used with any project
*/
class Extra {
	/**
	 * Detects which browser is being used
	 * @return <string> Browser being used
	 * @category Extra
	 * <code>
	 *  $result = Extra::detectBrowser();
	 * </code>
	 */
	function detectBrowser() {
		try {
			$useragent = $_SERVER ['HTTP_USER_AGENT'];
			return $useragent;
		} catch (Exception $err) {
			return $err->getMessage();
		}
	}
	/**
	 * Calculates percentage using the specified numbers
	 * @param <integer> $counter Actual value
	 * @param <integer> $maximum Maximum value
	 * @param <integer> $dec (optional) Number of decimal numbers to be shown
	 * @return <integer/double> Calculated percentage
	 * @category Extra
	 * <code>
	 *  $result = Extra::calculatePercentage(15, 33, 1);
	 * </code>
	 */
	function calculatePercentage($counter, $maximum, $dec = 0) {
		try {
			$percentage = ($counter * 100) / $maximum;
			return round($percentage, $dec, PHP_ROUND_HALF_UP) . '%';
		} catch (Exception $err) {
			return $err->getMessage();
		}
	}
	/**
	 * Prevents a web page from caching
	 * @category Extra
	 * <code>
	 *  Extra::preventPageCache();
	 * </code>
	 */
	function preventPageCache() {
		try {
			header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
			header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
			header('Cache-Control: no-store, no-cache, must-revalidate');
			header('Cache-Control: post-check=0, pre-check=0', FALSE);
			header('Pragma: no-cache');
		} catch (Exception $err) {
			return $err->getMessage();
		}
	}
	/**
	 * Performs a safe redirect to another url
	 * @param <string> $url Url to redirect to
	 * @param <boolean> $exit (optional) Exit the operation or not
	 * @category Extra
	 * <code>
	 *  $result = Extra::safeRedirect('http://www.google.co.za');
	 * </code>
	 */
	function safeRedirect($url, $exit = TRUE) {
		try {
			// Only use the header redirection if headers are not already sent
			if (!headers_sent()) {
				header('HTTP/1.1 301 Moved Permanently');
				header('Location: ' . $url);
				// Optional workaround for an IE bug (thanks Olav)
				header("Connection: close");
			}
			// HTML/JS Fallback:
			// If the header redirection did not work, try to use various methods other methods
			print '<html>';
			print '<head><title>Redirecting you...</title>';
			print '<meta http-equiv="Refresh" content="0;url=' . $url . '" />';
			print '</head>';
			print '<body onload="location.replace(\'' . $url . '\')">';
			// If the javascript and meta redirect did not work, 
			// the user can still click this link
			print 'You should be redirected to this URL:<br />';
			print "<a href='$url'>$url</a><br /><br />";
			print 'If you are not, please click on the link above.<br />';
			print '</body>';
			print '</html>';
			// Stop the script here (optional)
			if ($exit) {
				exit;
			}
		} catch (Exception $err) {
			return $err->getMessage();
		}
	}
	/**
	 * Function forces a redirect to the url specified within a given number of seconds
	 * @param <string> $url Url to redirect to
	 * @param <integer> $delay (optional) Delay in seconds that the url should redirect
	 * @category Extra
	 * <code>
	 *  $result = Extra::forceRedirect('http://www.go.com', 1);
	 * </code>
	 */
	function forceRedirect($url, $delay = 0) {
		try {
			if (!headers_sent() && $delay == 0) {
				ob_end_clean();
				header("Location: " . $url);
			}
			// Performs a redirect once headers have been sent
			echo "<meta http-equiv=\"Refresh\" content=\"" . $delay . "; URL=" . $url . "\">";
			exit();
		} catch (Exception $err) {
			return $err->getMessage();
		}
	}
	/**
	 * Function creates a session using the given key and value
	 * @param <string> $key Unique key to identify the session
	 * @param <string> $value Value of the session
	 * @return <boolean> Whether the session key was set successfully
	 * @category Extra
	 * <code>
	 *  Extra::setSession('email', 'info@email.com');
	 * </code>
	 */
	function setSession($key, $value) {
		try {
			//define('APP_ID', 'abc_corp_ecommerce');
			$k = APP_ID . '.' . $key;
			$_SESSION[$k] = $value;
			return TRUE;
		} catch (Exception $err) {
			return $err->getMessage();
		}
	}
	/**
	 * Function gets a session value based on the key
	 * @param <string> $key Unique key of the session
	 * @return <string> Value of the session
	 * @category Extra
	 * <code>
	 *  $result = Extra::getSession('email');
	 * </code>
	 */
	function getSession($key) {
		try {
			//define('APP_ID', 'abc_corp_ecommerce');
			$k = APP_ID . '.' . $key;
			if (isset($_SESSION[$k])) {
				return $_SESSION[$k];
			}
			return FALSE;
		} catch (Exception $err) {
			return $err->getMessage();
		}
	}
	/**
	 * Function used for debugging values
	 * @param <object> $object Object to print out and debug
	 * @category Extra
	 * <code>
	 *  $result = Extra::debugThis(array('hello', 'you', 'lot', array('oh', 'my')));
	 * </code>
	 */
	function debugThis($object) {
		try {
			echo "<pre>";
			print_r($object);
			echo "</pre>";
		} catch (Exception $err) {
			return $err->getMessage();
		}
	}
	/**
	 * Returns an HTML string for a select element containing titles as options
	 * @param <string> $name Name of the selection box
	 * @param <array> $values Values that the select box should contain
	 * @param <string> $selected (optional) Option that should be selected
	 * @param <boolean> $emptyFirst (optional) If the first item should be blank or not
	 * @param <array> $attributes (optional) Any additional attributes that should be applied to the select box
	 * @return <string> HTML string containing the dropdown list
	 * @category Extra
	 * <code>
	 *  $result = Extra::htmlSelect('selector', array('one', 'two', 'three'), 'two', FALSE);
	 * </code>
	 */
	public function htmlSelect($name, $values = array(), $selected = NULL, $emptyFirst = TRUE, $attributes = array()) {
		try {
// load attributes 
			$attrOthers = '';
			foreach ($attributes as $att => $val) {
				$attrOthers .= ' ' . $att . '="' . $val . '"';
			}
			$s = '<select name="' . $name . '"' . $attrOthers . '>';
			// load empty option if set
			if ($emptyFirst) {
				$s .= '<option></option>';
			}
			foreach ($values as $key => $val) {
				if ($selected == $val) {
					$s .= '<option value="' . $key . '" selected="selected">' . $val . '</option>';
				} else {
					$s .= '<option value="' . $key . '">' . $val . '</option>';
				}
			}
			$s .= '</select>';
			return $s;
		} catch (Exception $err) {
			return $err->getMessage();
		}
	}
	/**
	 * Calculates the distance between longatudes and latitudes in a specifiec measurement unit
	 * @param <integer> $lat1 Lattitude 1
	 * @param <integer> $lon1 Longatude 1
	 * @param <integer> $lat2 Lattitude 2
	 * @param <integer> $lon2 Longatude 2
	 * @param <string> $unit (optional) Unit of measurement - K, M, N
	 * @return <integer> Distance between the two points
	 * @category Extra
	 * <code>
	 *  $result = Extra::calculateDistance(38.898556, -77.037852, 38.897147, -77.043934, 'M');
	 * </code>
	 */
	function calculateDistance($lat1, $lon1, $lat2, $lon2, $unit = 'K') {
		try {
			$theta = $lon1 - $lon2;
			$dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
			$dist = acos($dist);
			$dist = rad2deg($dist);
			$miles = $dist * 60 * 1.1515;
			$unit = strtoupper($unit);
			if ($unit == "K") { // kilometers
				return ($miles * 1.609344);
			} else if ($unit == "N") {
				return ($miles * 0.8684);
			} else {
				return $miles; // miles
			}
		} catch (Exception $err) {
			return $err->getMessage();
		}
	}
	/**
	 * Function reads the registry value of the specified path
	 * @param <string> $registryPath Path of the registry value to be read
	 * @return <string> Registry value
	 * @category Extra
	 * <code>
	 *  $result = Extra::readRegistry('registry\\path');
	 * </code>
	 */
	function readRegistry($registryPath) {
		try {
			$Wshshell = new COM('WScript.Shell');
			$data = $Wshshell->regRead($registryPath);
			return $data;
		} catch (Exception $err) {
			return $err->getMessage();
		}
	}
	/**
	 * Function writes a value to the registry
	 * @param <string> $folder Folder to save the registry value in
	 * @param <string> $key Key to write to the registry
	 * @param <string> $value Value to write to the registry
	 * @param <string> $type (optional) Type of registry that is being used
	 * @return <boolean> Whether registry was successfully written to or not
	 * @category Extra
	 * <code>
	 *  $result = Extra::writeRegistry('registry\\path', 'key', 'value');
	 * </code>
	 */
	function writeRegistry($folder, $key, $value, $type = 'REG_SZ') {
		try {
			$WshShell = new COM("WScript.Shell");
			$registry = "HKEY_LOCAL_MACHINE\\SOFTWARE\\" . $folder . "\\" . $key;
			$result = $WshShell->RegWrite($registry, $value, $type);
			echo "Entry is Successfully written at : " . $registry;
			return($result);
		} catch (Exception $err) {
			return $err->getMessage();
		}
		return FALSE;
	}
	/**
	 * Function deletes a value from the registry
	 * @param <string> $folder Folder of the registry value
	 * @param <string> $key Key of the registry
	 * @return <boolean> Whether registry was successfully deleted to or not
	 * @category Extra
	 * <code>
	 *  $result = Extra::deleteRegistry('registry\\path', 'key');
	 * </code>
	 */
	function deleteRegistry($folder, $key) {
		try {
			$WshShell = new COM("Wscript.shell");
			$registry = "HKEY_LOCAL_MACHINE\\SOFTWARE\\" . $folder . "\\" . $key;
			$result = $WshShell->RegDelete($registry);
			echo $key . " is successfully deleted from HKEY_LOCAL_MACHINE\\SOFTWARE\\" . $folder;
			return($result);
		} catch (Exception $err) {
			return $err->getMessage();
		}
		return FALSE;
	}
	/**
	 * Retrieves and displays all tweets from a specific hashtag
	 * @param <string> $hashTag Hash tag tweets that should be displayed
	 * @return <boolean> Whether tweets was successfully retrieved or not
	 * @category Extra
	 * <code>
	 *  $result = Extra::getTweets('#phphelp');
	 * </code>
	 */
	function getTweets($hashTag) {
		try {
			$url = 'http://search.twitter.com/search.atom?q=' . urlencode($hashTag);
			echo "<p>Connecting to <strong>$url</strong> ...</p>";
			$ch = curl_init($url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
			$xml = curl_exec($ch);
			curl_close($ch);
			//If you want to see the response from Twitter, uncomment this next part out:
			echo "<p>Response:</p>";
			echo "<pre>" . htmlspecialchars($xml) . "</pre>";
			$affected = 0;
			$twelement = new SimpleXMLElement($xml);
			foreach ($twelement->entry as $entry) {
				$text = trim($entry->title);
				$author = trim($entry->author->name);
				$time = strtotime($entry->published);
				$id = $entry->id;
				echo "<p>Tweet from " . $author . ": <strong>" . $text . "</strong>  <em>Posted " . date('n/j/y g:i a', $time) . "</em></p>";
			}
			return TRUE;
		} catch (Exception $err) {
			return $err->getMessage();
		}
	}
	/**
	 * Detect the browser's language
	 * @param <array> $availableLanguages Available languages to check for
	 * @param <string> $default (optional) Default language
	 * @return <string> Language of the client pc
	 * @category Extra
	 * <code>
	 *  $result = Extra::getClientLanguage(array('en'));
	 * </code>
	 */
	function getClientLanguage($availableLanguages, $default = 'en') {
		try {
			if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
				$langs = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
				foreach ($langs as $value) {
					$choice = substr($value, 0, 2);
					if (in_array($choice, $availableLanguages)) {
						return $choice;
					}
				}
			}
			return $default;
		} catch (Exception $err) {
			return $err->getMessage();
		}
	}
	/**
	 * Sends an email with specified settings. Needs to use the PHPMailer package to function
	 * @param <string> $toAddress Email address to send the email to
	 * @param <string> $toName Name of the recipient
	 * @param <string> $fromAddress Email address from where the email was sent from
	 * @param <string> $fromName Name of the sender
	 * @param <string> $replyAddress Email address to reply to
	 * @param <string> $replyName Name of the entity to reply to
	 * @param <string> $subject Subject of the email
	 * @param <string> $htmlBody HTML body of the email
	 * @param <string> $plainBody Plaintext body of the email
	 * @param <string> $host Host name of the email server
	 * @param <string> $username Username of the email user
	 * @param <string> $password Password of the email user
	 * @return <boolean> Whether the email was successfully sent or not
	 * @category Extra
	 * <code>
	 *  $result = Extra::sendEmail('to@email.com', 'To', 'from@email.com', 'From', 'reply@email.com', 'Reply', 'subject', '<p>htmlBody</p>', 'plaintext', 'yourdomain.com', 'myusername', 'mypassword');
	 * </code>
	 */
	function sendEmail($toAddress, $toName, $fromAddress, $fromName, $replyAddress, $replyName, $subject, $htmlBody, $plainBody, $host, $username, $password) {
		try {
			include("./PHPMailer/class.phpmailer.php");
			$mail = new PHPMailer();
			$mail->IsSMTP(); // set mailer to use SMTP
			$mail->Host = $host;  // specify main and backup server domain
			$mail->SMTPAuth = TRUE;  // turn on SMTP authentication
			$mail->Username = $username;  // SMTP username
			$mail->Password = $password; // SMTP password
			$mail->SetFrom($fromAddress, $fromName);
			$mail->AddAddress($toAddress, $toName);  // name is optional
			$mail->AddReplyTo($replyAddress, $replyName);
			$mail->WordWrap = 50;   // set word wrap to 50 characters
			//$mail->AddAttachment("/tmp/image.jpg", "new.jpg");    // optional name
			$mail->IsHTML(TRUE); // set email format to HTML
			$mail->Subject = $subject;
			$mail->Body = $htmlBody;
			$mail->AltBody = $plainBody;
			if ($mail->Send()) {
				return TRUE;
			} else {
				return FALSE;
			}
		} catch (Exception $err) {
			return $err->getMessage();
		}
	}
	
	/**
	 * Function retrieves the IP address of the client
	 * @return <string> IP address of the client
	 * @category Extra
	 * <code>
	 *  $result = Extra::getClientIP();
	 * </code>
	 */
	function getClientIP() {
		try {
			if (!empty($_SERVER['HTTP_CLIENT_IP'])) {   //check ip from share internet
				$ip = $_SERVER['HTTP_CLIENT_IP'];
			} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {   //to check ip is pass from proxy
				$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
			} else {
				$ip = $_SERVER['REMOTE_ADDR'];
			}
			
			return $ip;
		} catch (Exception $err) {
			return $err->getMessage();
		}
	}
	
	/**
	 * Function unregisters global settings
	 * @return <boolean> Whether settings were unregistered successfully or not
	 * @category Extra
	 * <code>
	 *  $result = Extra::unregisterGlobals();
	 * </code>
	 */
	function unregisterGlobals() {
		try {
			if (ini_get('register_globals')) {
				$array = array('_SESSION', '_POST', '_GET', '_COOKIE', '_REQUEST', '_SERVER', '_ENV', '_FILES');
				foreach ($array as $value) {
					foreach ($GLOBALS[$value] as $key => $var) {
						if ($var === $GLOBALS[$key]) {
							unset($GLOBALS[$key]);
						}
					}
				}
			}
	
			return true;
		} catch (Exception $err) {
			return $err->getMessage();
		}
	}
	
	/**
	 * Function returns the location of the IP given
	 * @param <string> $ip IP address that is being searched for
	 * @return <string> Location of the IP address. If no location is found, 'UNKNOWN' is returned
	 * @category Extra
	 * <code>
	 *  $result = Extra::detectCity('127.0.0.1');
	 * </code>
	 */
	function detectCity($ip) {
		try {
			$default = 'UNKNOWN';
	
			if (!is_string($ip) || strlen($ip) < 1 || $ip == '127.0.0.1' || $ip == 'localhost')
				$ip = '8.8.8.8';
	
			$curlopt_useragent = 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.9.2) Gecko/20100115 Firefox/3.6 (.NET CLR 3.5.30729)';
	
			$url = 'http://ipinfodb.com/ip_locator.php?ip=' . urlencode($ip);
			$ch = curl_init();
	
			$curl_opt = array(
				CURLOPT_FOLLOWLOCATION => 1,
				CURLOPT_HEADER => 0,
				CURLOPT_RETURNTRANSFER => 1,
				CURLOPT_USERAGENT => $curlopt_useragent,
				CURLOPT_URL => $url,
				CURLOPT_TIMEOUT => 1,
				CURLOPT_REFERER => 'http://' . $_SERVER['HTTP_HOST'],
			);
	
			curl_setopt_array($ch, $curl_opt);
	
			$content = curl_exec($ch);
	
			if (!is_null($curl_info)) {
				$curl_info = curl_getinfo($ch);
			}
	
			curl_close($ch);
	
			if (preg_match('{<li>City : ([^<]*)</li>}i', $content, $regs)) {
				$city = $regs[1];
			}
			if (preg_match('{<li>State/Province : ([^<]*)</li>}i', $content, $regs)) {
				$state = $regs[1];
			}
	
			if ($city != '' && $state != '') {
				$location = $city . ', ' . $state;
				return $location;
			} else {
				return $default;
			}
		} catch (Exception $err) {
			return $err->getMessage();
		}
	}
}
?>