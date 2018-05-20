<?php
/*
	This class holds PHP functions for use with databases
*/
class Database {
	/**
	 * Creates a backup of the speciefied MySQL database
	 * @param <string> $host Host computer name where the database is stored
	 * @param <string> $user Username of the database
	 * @param <string> $pass Password of the database
	 * @param <string> $name Database name
	 * @param <array> $tables (optional) Array of tables that neds to be backed up
	 * @return <boolean> TRUE if the database backed up successfully
	 * @category Database
	 * <code>
	 *  $result = Database::backupDbTables('localhost', 'dbuser', 'p@55word', 'myprefix', '*');
	 * </code>
	 */
	function backupDbTables($host, $user, $pass, $name, $pref, $tables = '*') {
		try {
			$link = mysql_connect($host, $user, $pass);
			mysql_select_db($name, $link);
			//get all of the tables
			if ($tables == '*') {
				$tables = array();
				$result = mysql_query('SHOW TABLES');
				while ($row = mysql_fetch_row($result)) {
					$tables[] = $row[0];
				}
			} else {
				$tables = is_array($tables) ? $tables : explode(',', $tables);
			}
			//cycle through
			foreach ($tables as $table) {
				$result = mysql_query('SELECT * FROM ' . $table);
				$numFields = mysql_numFields($result);
				$return.= 'DROP TABLE ' . $table . ';';
				$row2 = mysql_fetch_row(mysql_query('SHOW CREATE TABLE ' . $table));
				$return.= "\n\n" . $row2[1] . ";\n\n";
				for ($i = 0; $i < $numFields; $i++) {
					while ($row = mysql_fetch_row($result)) {
						$return.= 'INSERT INTO ' . $table . ' VALUES(';
						for ($j = 0; $j < $numFields; $j++) {
							$row[$j] = addslashes($row[$j]);
							$row[$j] = ereg_replace("\n", "\\n", $row[$j]);
							if (isset($row[$j])) {
								$return.= '"' . $row[$j] . '"';
							} else {
								$return.= '""';
							}
							if ($j < ($numFields - 1)) {
								$return.= ',';
							}
						}
						$return.= ");\n";
					}
				}
				$return.="\n\n\n";
			}
			//save file
			$handle = fopen($pref . '-' . time() . '-' . (md5(implode(',', $tables))) . '.sql', 'w+');
			fwrite($handle, $return);
			fclose($handle);
			return TRUE;
		} catch (Exception $err) {
			return $err->getMessage();
		}
	}
	/**
	 * Calculates the size of the specified database
	 * @param <string> $database Name of the database to be calculated
	 * @return <string> Size of the database
	 * @category Database
	 * <code>
	 *  $result = Database::calculateDbSize('db_mydb');
	 * </code>
	 */
	function calculateDbSize($database) {
		try {
			$tables = mysql_list_tables($database);
			if (!$tables) {
				return -1;
			}
			$tableCount = mysql_num_rows($tables);
			$size = 0;
			for ($i = 0; $i < $tableCount; $i++) {
				$tname = mysql_tablename($tables, $i);
				$r = mysql_query("SHOW TABLE STATUS FROM " . $database . " LIKE '" . $tname . "'");
				$data = mysql_fetch_array($r);
				$size += ( $data['Index_length'] + $data['Data_length']);
			};
			$units = array(' B', ' KB', ' MB', ' GB', ' TB');
			for ($i = 0; $size > 1024; $i++) {
				$size /= 1024;
			}
			return round($size, 2) . $units[$i];
		} catch (Exception $err) {
			return $err->getMessage();
		}
	}
	/**
	 * This is the function handler for all my data requests
	 * @param <string> $query Prepared statement
	 * @param <array> $params Parameters for the prepare statement
	 * @param <boolean> $command TRUE if it is an insert, update, delete statement. If FALSE then its a select statement
	 * @return Multiple values
	 * @category Database
	 *
	 * NOTE: This function needs the refValues function (below) in the same class
	 * 
	 * <code>
	 * 	$result = Database::runCommand("DELETE FROM [tbl_name] WHERE [column] = ?", array("i", 1), TRUE);
	 * 	$result = Database::runCommand("INSERT INTO [tbl_name] VALUES (?)", array("i", 1), TRUE);
	 * 	$result = Database::runCommand("UPDATE [tbl_name] SET [column_name] = ?", array("i", 1), TRUE);
	 *  $result = Database::runCommand("SELECT * FROM [tbl_Name] WHERE [column] = ?", array("i", 1), FALSE);
	 *
	 * 	echo $result[row_num][$column_name];
	 * 	echo count($result);
	 * </code>
	 */
	function runCommand($query, $params, $command) {
		try {
			//Setting the default result value
			$result = "";
			//Configuring the database settings
			$hostname = "localhost";
			$username = "root";
			$password = "";
			$database = "db_name";
			//Connecting to the database
			$mysqli = new mysqli($hostname, $username, $password, $database);
			//Checking for errors
			if (!mysqli_connect_errno()) {
				$stmt = $mysqli->prepare($query);
				//Call the function inside of the prepare statement and set all the parameters of the string
				if (count($params)) {
					call_user_func_array(array($stmt, 'bind_param'), $this->refValues($params));
				}
				//Executing the statement
				$stmt->execute();
				//If TRUE, it is an UPDATE,INSERT or DELETE
				if ($command) {
					//If successful then show the success message
					if ($mysqli->affected_rows > 0) {
						$result = 'success';
					} else {
						//Show the error message
						$result = " " . $mysqli->error;
					}
				}
				//If FALSE, it is a SELECT statement
				else {
					//Creating columns for the data 
					$meta = $stmt->result_metadata();
					//Setting the parameter values
					while ($field = $meta->fetch_field()) {
						$parameters[] = &$row[$field->name];
					}
					call_user_func_array(array($stmt, 'bind_result'), $this->refValues($parameters));
					//Getting the data, creating a two deminsional array
					while ($stmt->fetch()) {
						$x = array();
						$results;
						foreach ($row as $key => $val) {
							//Creating a key so can get the values
							$x[$key] = $val;
						}
						$results[] = $x;
					}
					if (!empty($results)) {
						$result = $results;
					} else {
						$result = NULL;
					}
				}
				//Closing the database connection
				$mysqli->close();
			} else {
				//Setting the error message on database connection error
				$result = "** Connection to database was unsuccessful";
			}
			//Returning the result
			return $result;
		} catch (Exception $err) {
			return $err->getMessage();
		}
	}
	/**
	 * This is to make sure that only the keys are in the arrange for PHP 5.3 +. Used with the function runCommand (above)
	 * @param <array> $arr Array containing the parameters
	 * @return <array> Returns a single dimensional array (only keys)
	 * @category Database
	 */
	function refValues($arr) {
		try {
			//Reference is required for PHP 5.3+
			if (strnatcmp(phpversion(), '5.3') >= 0) {
				$refs = array();
				foreach ($arr as $key => $value) {
					//Saving only the keys to the array and returning a single array
					$refs[$key] = &$arr[$key];
				}
				return $refs;
			}
			//Return the array
			return $arr;
		} catch (Exception $err) {
			return $err->getMessage();
		}
	}
}
?>