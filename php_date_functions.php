<?php
/*
	This class holds PHP functions for use with dates
*/
class Dates {
	/**
	 * Displays a dropdown list containing the days 1 - 31 with the current day selected
	 * @category Dates
	 * <code>
	 *  echo Dates::dropdownDay();
	 * </code>
	 */
	function dropdownDay() {
		try {
			$currentDay = date("d");
			echo "<select name='day'>";
			for ($day = 1; $day <= 31; $day++) {
				if ($day == $currentDay) {
					echo "<option selected='selected'>" . $day . "</option>" . "\n";
				} else {
					echo "<option>" . $day . "</option>" . "\n";
				}
			}
			echo "</select>";
		} catch (Exception $err) {
			return $err->getMessage();
		}
	}
	/**
	 * Displays a dropdown list containing the months 1 - 12 with the current month selected
	 * @category Dates
	 * <code>
	 *  echo Dates::dropdownMonth();
	 * </code>
	 */
	function dropdownMonth() {
		try {
			$currentMonth = date("m");
			echo "<select name='month'>";
			for ($month = 1; $month <= 12; $month++) {
				if ($month == $currentMonth) {
					echo "<option selected='selected'>" . $month . "</option>" . "\n";
				} else {
					echo "<option>" . $month . "</option>" . "\n";
				}
			}
			echo "</select>";
		} catch (Exception $err) {
			return $err->getMessage();
		}
	}
	/**
	 * Displays a dropdown list containing the starting and ending year that is specified
	 * @category Dates
	 * <code>
	 *  echo Dates::dropdownYear(2000, 2014);
	 * </code>
	 */
	function dropdownYear($startYear, $endYear) {
		try {
			$startYear = ($startYear) ? $startYear - 1 : date('Y') - 10;
			$endYear = ($endYear) ? $endYear : date('Y');
			echo "<select name='year'>";
			for ($i = $endYear; $i > $startYear; $i -= 1) {
				echo "<option>{$i}</option>";
			}
			echo "</select>";
		} catch (Exception $err) {
			return $err->getMessage();
		}
	}
	/**
	 * Gets the last  day of the month specified
	 * @param <integer> $monthNum (optional) Month number to check (01 - 12). Leave blank for current month
	 * @param <integer> $year (optional) Year to check e.g. 2012. Leave blank for current year
	 * @return <string> In format yyyy-mm-dd
	 * @category Dates
	 * <code>
	 *  $result = Dates::getMonthLastday(1, 2014);
	 * </code>
	 */
	function getMonthLastday($monthNum = NULL, $year = NULL) {
		try {
			$monthNum = ($monthNum) ? $monthNum : date('m');
			$year = ($year) ? $year : date('Y');
			return date('Y-m-d', strtotime('-1 second', strtotime('+1 month', strtotime($monthNum . '/01/' . $year . ' 00:00:00'))));
		} catch (Exception $err) {
			return $err->getMessage();
		}
	}
	/**
	 * Calculates the difference between two dates in days, months and years
	 * @param <string> $startDate Starting date in format yyyy/mm/dd
	 * @param <string> $endDate Ending date in format yyyy/mm/dd
	 * @return <string> Specifying the days, months and years in difference
	 * @category Dates
	 * <code>
	 *  $result = Dates::getDateDifference('2014/02/01', '2014/02/02');
	 * </code>
	 */
	function getDateDifference($startDate, $endDate) {
		try {
			$diff = abs(strtotime($endDate) - strtotime($startDate));
			$years = floor($diff / (365 * 60 * 60 * 24));
			$months = floor(($diff - $years * 365 * 60 * 60 * 24) / (30 * 60 * 60 * 24));
			$days = floor(($diff - $years * 365 * 60 * 60 * 24 - $months * 30 * 60 * 60 * 24) / (60 * 60 * 24));
			return "{$days} days, {$months} months, {$years} years";
		} catch (Exception $err) {
			return $err->getMessage();
		}
	}
	/**
	 * Calculates the amount of time in words
	 * @param <integer> $seconds Total amount of seconds to convert to words
	 * @return <string> The amount of time in words
	 * @category Dates
	 * <code>
	 *  $result = Dates::secondsToWords(123456789);
	 * </code>
	 */
	function secondsToWords($seconds) {
		try {
			$ret = "";
			// calculating the days
			$days = intval(intval($seconds) / (3600 * 24));
			if ($days > 0) {
				$ret .= "$days days ";
			}
			// calulating the hours
			$hours = (intval($seconds) / 3600) % 24;
			if ($hours > 0) {
				$ret .= "$hours hours ";
			}
			// calulating the minutes
			$minutes = (intval($seconds) / 60) % 60;
			if ($minutes > 0) {
				$ret .= "$minutes minutes ";
			}
			// calulating the seconds
			$seconds = intval($seconds) % 60;
			if ($seconds > 0) {
				$ret .= "$seconds seconds";
			}
			// returning the formatted string
			return $ret;
		} catch (Exception $err) {
			return $err->getMessage();
		}
	}
	/**
	 * Auto updates the copyright dates depending on tha start and current year
	 * @param <integer> $startYear Year to start calculate from
	 * @return <string> Copyright date
	 * @category Dates
	 * <code>
	 *  $result = Dates::autoUpdateCopyright(2010);
	 * </code>
	 */
	function autoUpdateCopyright($startYear) {
		try {
			// given start year (e.g. 2004)
			$startYear = intval($startYear);
			// current year (e.g. 2007)
			$year = intval(date('Y'));
			// is the current year greater than the
			// given start year?
			if ($year > $startYear) {
				return $startYear . ' - ' . $year;
			} else {
				return $startYear;
			}
		} catch (Exception $err) {
			return $err->getMessage();
		}
	}
	/**
	 * Converts the given seconds into an array in the different tme formats
	 * @param <integer> $time Seconds to calculate
	 * @return <array> Array containing time elements
	 * @category Dates
	 * <code>
	 *  $result = Dates::secondsToTimeArray(699999);
	 * </code>
	 */
	function secondsToTimeArray($time) {
		try {
			if (is_numeric($time)) {
				$value = array(
					"years" => 0, "days" => 0, "hours" => 0,
					"minu"
					. "tes" => 0, "seconds" => 0,
				);
				if ($time >= 31556926) {
					$value["years"] = floor($time / 31556926);
					$time = ($time % 31556926);
				}
				if ($time >= 86400) {
					$value["days"] = floor($time / 86400);
					$time = ($time % 86400);
				}
				if ($time >= 3600) {
					$value["hours"] = floor($time / 3600);
					$time = ($time % 3600);
				}
				if ($time >= 60) {
					$value["minutes"] = floor($time / 60);
					$time = ($time % 60);
				}
				$value["seconds"] = floor($time);
				return $value;
			} else {
				return FALSE;
			}
		} catch (Exception $err) {
			return $err->getMessage();
		}
	}
}
?>