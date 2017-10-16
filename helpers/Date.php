<?php
namespace wpf\helpers;

use \DateTime;
use \DateTimeZone;
use \wpf\helpers\Cache;

/**
 * Class Date
 * @package wpf\helpers
 */
class Date extends DateTime {

	use Cache;

	private $_locale;

	/**
	 * Date constructor.
	 *
	 * @param string $time
	 * @param DateTimeZone|NULL $timezone
	 */
	public function __construct( $time = 'now', DateTimeZone $timezone = NULL ) {
		$timezone = $timezone ?? new DateTimeZone( $this->timezone() );
		parent::__construct( $time, $timezone );
		
		global $wp_locale;
		
		$this->_locale = $wp_locale;
	}

	/**
	 * @param string $format
	 *
	 * @return mixed|string
	 */
	public function format( $format ) {
		$out = parent::format( $format ); // TODO: Change the autogenerated stub

		return self::rd( $out );
	}

	/**
	 * @return mixed
	 */
	public function weekday() {
		return $this->_locale->weekday[ parent::format('w') ];
	}

	/**
	 * @return mixed
	 */
	public function weekdayInitial() {
		return $this->_locale->weekday_initial[ $this->weekday() ];
	}

	/**
	 * @return mixed
	 */
	public function weekdayAbbrev() {
		return $this->_locale->weekday_abbrev[ $this->weekday() ];
	}

	/**
	 * @return mixed
	 */
	public function month() {
		return $this->_locale->month[ parent::format('m') ];
	}

	/**
	 * @return mixed
	 */
	public function monthAbbrev() {
		return $this->_locale->month_abbrev[ $this->month() ];
	}

	/**
	 * @return mixed
	 */
	public function monthGenitive() {
		return $this->_locale->month_genitive[ parent::format('m') ];
	}
	
	/**
	 * @return mixed
	 */
	public function timezone() {
		return static::cache( function () {
			return self::timezoneString();
		} );
	}
	
	/**
	 * Returns the timezone string for a site, even if it's set to a UTC offset
	 *
	 * Adapted from http://www.php.net/manual/en/function.timezone-name-from-abbr.php#89155
	 *
	 * @return string valid PHP timezone string
	 */
	public static function timezoneString() {
		// if site timezone string exists, return it
		if ( $timezone = get_option( 'timezone_string' ) ) {
			return $timezone;
		}
		// get UTC offset, if it isn't set then return UTC
		if ( ! $utc_offset = get_option( 'gmt_offset', 0 ) ) {
			return 'UTC';
		}
		// adjust UTC offset from hours to seconds
		$utc_offset *= 3600;
		// attempt to guess the timezone string from the UTC offset
		if ( $timezone = timezone_name_from_abbr( '', $utc_offset, 0 ) ) {
			return $timezone;
		}
		// last try, guess timezone string manually
		$is_dst = date( 'I' );
		foreach ( timezone_abbreviations_list() as $abbr ) {
			foreach ( $abbr as $city ) {
				if ( $city[ 'dst' ] == $is_dst && $city[ 'offset' ] == $utc_offset ) {
					return $city[ 'timezone_id' ];
				}
			}
		}
		
		// fallback to UTC
		return 'UTC';
	}

	/**
	 * @param string $datetime
	 *
	 * @return mixed|string
	 */
	public static function rd( string $datetime ) {
		if ( substr_count( $datetime, '--' ) > 0 ) {
			return str_replace( '--', '', $datetime );
		}
		$rus_date_array = [
			"Январь"    => "января",
			"Февраль"   => "февраля",
			"Март"      => "марта",
			"Апрель"    => "апреля",
			"Май"       => "мая",
			"Июнь"      => "июня",
			"Июль"      => "июля",
			"Август"    => "августа",
			"Сентябрь"  => "сентября",
			"Октябрь"   => "октября",
			"Ноябрь"    => "ноября",
			"Декабрь"   => "декабря",
			"January"   => "января",
			"February"  => "февраля",
			"March"     => "марта",
			"April"     => "апреля",
			"May"       => "мая",
			"June"      => "июня",
			"July"      => "июля",
			"August"    => "августа",
			"September" => "сентября",
			"October"   => "октября",
			"November"  => "ноября",
			"December"  => "декабря",
			"Sunday"    => "воскресенье",
			"Monday"    => "понедельник",
			"Tuesday"   => "вторник",
			"Wednesday" => "среда",
			"Thursday"  => "четверг",
			"Friday"    => "пятница",
			"Saturday"  => "суббота",
			"Sun"       => "Вс",
			"Mon"       => "Пн",
			"Tue"       => "Вт",
			"Wed"       => "Ср",
			"Thu"       => "Чт",
			"Fri"       => "Пт",
			"Sat"       => "Сб",
			"th"        => "",
			"st"        => "",
			"nd"        => "",
			"rd"        => "",
			"Jan"       => "Янв",
			"Feb"       => "Фев",
			"Mar"       => "Мар",
			"Apr"       => "Апр",
			"Jun"       => "Июн",
			"Jul"       => "Июл",
			"Aug"       => "Авг",
			"Sep"       => "Сен",
			"Oct"       => "Окт",
			"Nov"       => "Ноя",
			"Dec"       => "Дек"
		];

		return strtr( $datetime, $rus_date_array );
	}
}