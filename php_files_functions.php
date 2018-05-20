<?php
/*
	This class holds PHP functions for use with files
*/
class Files {
	/**
	 * Converts from a size in bytes to a user-friendly size for display
	 * @param <integer> $size Amount of bytes to convert
	 * @param <integer> $round (optional) Number of decimal points in resulting size
	 * @return <string> Converted size
	 * @category Files
	 * <code>
	 *  $result = Files::formatFilesize(12382224, 2);
	 * </code>
	 */
	function formatFilesize($size, $round = 0) {
		try {
			$sizes = array('B', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
			for ($i = 0; $size > 1024 && isset($sizes[$i + 1]); $i++) {
				$size /= 1024;
			}
			return round($size, $round) . $sizes[$i];
		} catch (Exception $err) {
			return $err->getMessage();
		}
	}
	/**
	 * Retrieve all files in a specified directory
	 * @param <string> $folder Directory path to search for files
	 * @param <boolean> $includeSubs (optional) TRUE if sub directories should be included
	 * @return <array> List of files retrieved
	 * @category Files
	 * <code>
	 *  $result = Files::getFiles('C:/xampp/', TRUE);
	 * </code>
	 */
	function getFiles($folder, $includeSubs = FALSE) {
		try {
			// Remove any trailing slash
			if (substr($folder, -1) == '/') {
				$folder = substr($folder, 0, -1);
			}
			// Make sure a valid folder was passed
			if (!file_exists($folder) || !is_dir($folder) || !is_readable($folder)) {
				return FALSE;
				exit();
			}
			// Grab a file handle
			$allFiles = FALSE;
			if ($handle = opendir($folder)) {
				$allFiles = array();
				// Start looping through a folder contents
				while ($file = @readdir($handle)) {
					// Set the full path
					$path = $folder . '/' . $file;
					// Filter out this and parent folder
					if ($file != '.' && $file != '..') {
						// Test for a file or a folder
						if (is_file($path)) {
							$allFiles[] = $path;
						} elseif (is_dir($path) && $includeSubs) {
							// Get the subfolder files
							$subfolderFiles = get_files($path, TRUE);
							// Anything returned
							if ($subfolderFiles) {
								$allFiles = array_merge($allFiles, $subfolderFiles);
							}
						}
					}
				}
				// Cleanup
				closedir($handle);
			}
			// Return the file array
			@sort($allFiles);
			return $allFiles;
		} catch (Exception $err) {
			return $err->getMessage();
		}
	}
	/**
	 * Retrieve all files with a specfic extension
	 * @param <string> $path Directory to search for files
	 * @param <string> $ext Extension name to search for
	 * @return <array> List of files retrieved
	 * @category Files
	 * <code>
	 *  $result = Files::getFilesByExtension('C:/', 'txt');
	 * </code>
	 */
	function getFilesByExtension($path, $ext) {
		try {
			$files = array();
			if (is_dir($path)) {
				$handle = opendir($path);
				while ($file = readdir($handle)) {
					if ($file[0] == '.') {
						continue;
					}
					if (is_file($path . $file) and preg_match('/\.' . $ext . '$/', $file)) {
						$files[] = $file;
					}
				}
				closedir($handle);
				sort($files);
			}
			return $files;
		} catch (Exception $err) {
			return $err->getMessage();
		}
	}
	/**
	 * Retrieves a random file from a specified folder
	 * @param <string> $folder Directory to search for file
	 * @param <string> $extensions (optional) Extension to search for. Leave blank for all extensions
	 * @return <string> Random file name
	 * @category Files
	 * <code>
	 *  $result = Files::randomFile('C:/', 'txt');
	 * </code>
	 */
	function randomFile($folder = NULL, $extensions = '.*') {
		try {
			// fix path:
			$folder = trim($folder);
			$folder = ($folder == '') ? './' : $folder;
			// check folder:
			if (!is_dir($folder)) {
				die('invalid folder given!');
			}
			// create files array
			$files = array();
			// open directory
			if ($dir = @opendir($folder)) {
				// go trough all files:
				while ($file = readdir($dir)) {
					if (!preg_match('/^\.+$/', $file) and
							preg_match('/\.(' . $extensions . ')$/', $file)) {
						// feed the array:
						$files[] = $file;
					}
				}
				// close directory
				closedir($dir);
			} else {
				die('Could not open the folder "' . $folder . '"');
			}
			if (count($files) == 0) {
				die('No files where found');
			}
			// seed random function:
			mt_srand((double) microtime() * 1000000);
			// get an random index:
			$rand = mt_rand(0, count($files) - 1);
			// check again:
			if (!isset($files[$rand])) {
				die('Array index was not found! very strange!');
			}
			// return the random file:
			return $folder . $files[$rand];
		} catch (Exception $err) {
			return $err->getMessage();
		}
	}
	/**
	 * Appends text to a specified file
	 * @param <string> $fileName File name to be written to
	 * @param <string> $text Text to be appended to the file
	 * @param <string> $writeMode (optional) Write mode of the file
	 * @return <boolean> TRUE if write is successful
	 * @category Files
	 * <code>
	 *  $result = Files::writeToFile('C:/test.txt', 'Test Text');
	 * </code>
	 */
	function writeToFile($fileName, $text, $writeMode = 'ab') {
		try {
			//Checking if the file opens successfully
			if ($fh = fopen($fileName, $writeMode)) {
				//Locking the file for writing
				flock($fh, LOCK_EX);
				//Writing content to the file
				fwrite($fh, $text);
				//Unlocking the file
				flock($fh, LOCK_UN);
				//Closing the file
				fclose($fh);
				return TRUE;
			} else {
				return FALSE;
			}
		} catch (Exception $err) {
			return $err->getMessage();
		}
	}
	/**
	 * Reads text one line at a time from a specified file
	 * @param <string> $fileName File to be read
	 * @param <string> $readMode (optional) Read mode of the file
	 * @return <string> Text read from the file
	 * @category Files
	 * <code>
	 *  $result = Files::readFromFile('C:/test.txt');
	 * </code>
	 */
	function readFromFile($fileName, $readMode = 'rb') {
		try {
			//Checking if the file opens successfully
			if ($fh = fopen($fileName, $readMode)) {
				//Locking the file for reading
				flock($fh, LOCK_SH);
				//Reading from the file
				while (!feof($fh)) {
					//Appending the text to a variable
					$line .= fgets($fh, 999) . "\n";
				}
				//Unlocking the file
				flock($fh, LOCK_UN);
				//Closing the file
				fclose($fh);
				//Returning the contents of the file
				return $line;
			} else {
				return FALSE;
			}
		} catch (Exception $err) {
			return $err->getMessage();
		}
	}
	/**
	 * Destroys or deletes a directory
	 * @param <string> $dir Directory to be destroyed
	 * @param <boolean> $virtual (optional) TRUE if it is a virtual directory
	 * @return <boolean> TRUE if the directory is destroyed successfully
	 * @category Files
	 * <code>
	 *  $result = Files::destroyDirectory('C:/test');
	 * </code>
	 */
	function destroyDirectory($dir, $virtual = FALSE) {
		try {
			$ds = DIRECTORY_SEPARATOR;
			$dir = $virtual ? realpath($dir) : $dir;
			$dir = substr($dir, -1) == $ds ? substr($dir, 0, -1) : $dir;
			if (is_dir($dir) && $handle = opendir($dir)) {
				while ($file = readdir($handle)) {
					if ($file == '.' || $file == '..') {
						continue;
					} elseif (is_dir($dir . $ds . $file)) {
						destroyDir($dir . $ds . $file);
					} else {
						unlink($dir . $ds . $file);
					}
				}
				closedir($handle);
				rmdir($dir);
				return TRUE;
			} else {
				return FALSE;
			}
		} catch (Exception $err) {
			return $err->getMessage();
		}
	}
	/**
	 * Reads a csv file and saved it into an array
	 * @param <string> $csvFile Name of the csv file to be read
	 * @category Files
	 * <code>
	 *  echo Files::readCsv('C:/Book1.csv');
	 * </code>
	 */
	function readCsv($csvFile) {
		try {
			// Let's make sure the file is there
			if (($handle = fopen($csvFile, "r")) !== FALSE) {
				// This reads the the file line for line
				while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
					// the data variable holds the whole line
					echo '<pre>';
					print_r($data);
					echo '</pre>';
				}
			}
		} catch (Exception $err) {
			return $err->getMessage();
		}
	}
	/**
	 * Retrieves the last argument in a path name
	 * @param <string> $path Path to be checked
	 * @return <string> Last argument in the specified path
	 * @category Files
	 * <code>
	 *  $result = Files::getLastArgument('I:\bootstrap v3.1.1\dist\css');
	 * </code>
	 */
	function getLastArgument($path) {
		try {
			$path = str_replace('\\', '/', $path);
			$path = preg_replace('/\/+$/', '', $path);
			$path = explode('/', $path);
			$l = count($path) - 1;
			return isset($path[$l]) ? $path[$l] : '';
		} catch (Exception $err) {
			return $err->getMessage();
		}
	}
	/**
	 * Creates a zip file of the specified array of files
	 * @param <array> $files Array of files to merge into the zip file
	 * @param <string> $destination Destination for where the zip file will be created
	 * @param <boolean> $overwrite (optional) Whether the zip file needs to be overwritten or not
	 * @return <boolean> Whether the zip file has been created successfully
	 * @category Files
	 * <code>
	 *  $result = Files::zipFile(array('C:/file1.fle'), 'C:/file1.zip', TRUE);
	 * </code>
	 */
	function zipFile($files = array(), $destination, $overwrite = FALSE) {
		try {
			//if the zip file already exists and overwrite is FALSE, return FALSE  
			if (file_exists($destination) && !$overwrite) {
				return FALSE;
			}
			//vars  
			$valid_files = array();
			//if files were passed in...  
			if (is_array($files)) {
				//cycle through each file  
				foreach ($files as $file) {
					//make sure the file exists  
					if (file_exists($file)) {
						$valid_files[] = $file;
					}
				}
			}
			//if we have good files...  
			if (count($valid_files)) {
				//create the archive  
				$zip = new ZipArchive();
				if ($zip->open($destination, $overwrite ? ZIPARCHIVE::OVERWRITE : ZIPARCHIVE::CREATE) !== TRUE) {
					return FALSE;
				}
				//add the files  
				foreach ($valid_files as $file) {
					$zip->addFile($file, $file);
				}
				//debug  
				//echo 'The zip archive contains ',$zip->numFiles,' files with a status of ',$zip->status;  
				//close the zip -- done!  
				$zip->close();
				//check to make sure the file exists  
				return file_exists($destination);
			} else {
				return FALSE;
			}
		} catch (Exception $err) {
			return $err->getMessage();
		}
	}
	/**
	 * Unzips a zip file to the specified directory
	 * @param <string> $file Zip file to unzip
	 * @param <string> $destination Directory to there the unzipped file will be saved
	 * @return <boolean> Whether the zip file has been unzipped successfully
	 * @category Files
	 * <code>
	 *  $result = Files::unzipFile('C:/file.zip', 'D:/file', TRUE);
	 * </code>
	 */
	function unzipFile($file, $destination) {
		try {
			// create object  
			$zip = new ZipArchive();
			// open archive  
			if ($zip->open($file) !== TRUE) {
				return FALSE;
			}
			// extract contents to destination directory  
			$zip->extractTo($destination);
			// close archive  
			$zip->close();
			return TRUE;
		} catch (Exception $err) {
			return $err->getMessage();
		}
	}
	/**
	 * Converts a file's contents to base 64 and returning it as string
	 * @param <string> $file File to be converted
	 * @param <string> $mime Data that should be attached to the return string
	 * @return <string> Base 64 encoded file contents
	 * @category Files
	 * <code>
	 *  $result = Files::dataUri('newEmptyPHP.php', '9898');
	 * </code>
	 */
	function dataUri($file, $mime) {
		try {
			$contents = file_get_contents($file);
			$base64 = base64_encode($contents);
			return "data:$mime;base64,$base64";
		} catch (Exception $err) {
			return $err->getMessage();
		}
	}
	/**
	 * Checks if the specified node exist in the xml file
	 * @param <string> $xmlFile XML file to be checked
	 * @param <string> $searchNode Node to search for
	 * @return <boolean> Whether the node exist in the xml file or not
	 * @category Files
	 * <code>
	 *  $result = Files::nodeExist('./newxml.xml', 'mynode');
	 * </code>
	 */
	function nodeExist($xmlFile, $searchNode) {
		try {
			$dom = new DOMDocument();
			$dom->load($xmlFile);
			$errorNodes = $dom->getElementsByTagName($searchNode);
			if ($errorNodes->length == 0) {
				return FALSE;
			}
			return TRUE;
		} catch (Exception $err) {
			return $err->getMessage();
		}
	}
	/**
	 * Converts a binary file toa PHP file
	 * @param <string> $inputFile Binary file to be converted to a PHP file
	 * @param <string> $outputFile The new PHP file that will be the output file
	 * @return <boolean> Whether the file was successfully converted or not
	 * @category Files
	 * <code>
	 *  $result = Files::binaryToPhp('./newbin.bin', './newphp.php');
	 * </code>
	 */
	function binaryToPhp($inputFile, $outputFile) {
		try {
			$i = file_get_contents($inputFile);
			$b = array();
			$x = 0;
			$y = 0;
			for ($c = 0; $c < strlen($i); $c++) {
				$no = bin2hex($i[$c]);
				$b[$x] = isset($b[$x]) ? $b[$x] . '\\x' . $no : '\\x' . $no;
				if ($y >= 10) {
					$x++;
					$y = 0;
				}
				$y++;
			}
			$output = "<" . "?php\n";
			$output .= "\$f=\"";
			$output .= implode("\";\r\n\$f.=\"", $b);
			$output .= "\";\nprint \$f;";
			$output .= "\n?>";
			$fp = fopen($outputFile, 'w+');
			fwrite($fp, $output);
			fclose($fp);
			return TRUE;
		} catch (Exception $err) {
			return $err->getMessage();
		}
	}
	/**
	 * Converts a CSV file to an array
	 * @param <string> $filePath Path of the CSV file to convert
	 * @param <string> $delimiter (optional) Delimeter that will be used for separation
	 * @param <array> $header (optional) Array containing header values of the CSV file
	 * @param <integer> $skipLines (optional) Number of lines to skip when reading the CSV file
	 * @return <array> Array that was converted from the CSV file
	 * @category Files
	 * <code>
	 *  $result = Files::csvToArray('/var/www/mydemo/file.csv', '\t', array('column1', 'column2', 'column3'), 1);
	 * </code>
	 */
	function csvToArray($filePath, $delimiter = '|', $header = NULL, $skipLines = -1) {
		try {
			$lineNumber = 0;
			$dataList = array();
			if (($handle = fopen($filePath, 'r')) != FALSE) {
				while (($row = fgets($handle, 4096)) !== FALSE) {
					if ($lineNumber > $skipLines) {
						$items = explode($delimiter, $row);
						$record = array();
						for ($index = 0, $m = count($header); $index < $m; $index++) {
							//If column exist then and then added in data with header name
							if (isset($items[$index])) {
								$record[$header[$index]] = trim($items[$index]);
							}
						}
						$dataList[] = $record;
					} else {
						$lineNumber++;
					}
				}
				fclose($handle);
			}
			return $dataList;
		} catch (Exception $err) {
			return $err->getMessage();
		}
	}
	
	/**
	 * Using glob to search for files
	 * @param <string> $globSearchText Glob search text to search for files
	 * @return <array> Array containing glob search matched files
	 * @category Files
	 * <code>
	 *  $result = Files::getFilesByGlob('../images/a*.jpg');
	 * </code>
	 */
	function getFilesByGlob($globSearchText) {
		try {
			// using glob to search for the files
			$files = glob($globSearchText);
			// applies the function to each array element
			$files = array_map('realpath', $files);
			return $files;
		} catch (Exception $err) {
			return $err->getMessage();
		}
	}
}
?>