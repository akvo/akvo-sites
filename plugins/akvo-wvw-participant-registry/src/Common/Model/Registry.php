<?php
namespace Akvo\WvW\ParticipantRegistry\Common\Model;

/**
 * Description of Register
 *
 * @author Jayawi Perera
 */
class Registry {

	/**
	 * CUTOFF_MONTH and CUTOFF_DATE are the month and date in which the "year" should change. 
	 * If client wants to change the year before the cutoff month , then change the CUTOFF_MONTH and CUTOFF_DATE values accordingly.
	 */
//	const CUTOFF_MONTH = 7;
	const CUTOFF_MONTH = 5;
//	const CUTOFF_DATE = 31;
	const CUTOFF_DATE = 10;

	public static function getPastParticipationYears () {

		$oDate = new \DateTime();

		$iCurrentYear = $oDate->format('Y');
		$iCurrentMonth = $oDate->format('n');
		$iCurrentDay = $oDate->format('j');

		$iLastYear = $iCurrentYear;

		if ($iCurrentMonth < self::CUTOFF_MONTH) {

			$iLastYear = $iCurrentYear - 1;

		} else {

			if ($iCurrentMonth == self::CUTOFF_MONTH) {

				if ($iCurrentDay < self::CUTOFF_DATE) {

					$iLastYear = $iCurrentYear - 1;

				}

			}

		}
		return $iLastYear;

	}

}