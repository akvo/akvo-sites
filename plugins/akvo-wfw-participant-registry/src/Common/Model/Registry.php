<?php
namespace Akvo\WfW\ParticipantRegistry\Common\Model;

/**
 * Description of Register
 *
 * @author Jayawi Perera
 */
class Registry {

	const CUTOFF_MONTH = 7;
	const CUTOFF_DATE = 31;

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

					$iLastYear = $iCurrentYear;

				}

			}

		}
		return $iLastYear;

	}

}