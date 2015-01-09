<?php
namespace KwgPress\Version;
/**
 * Description of Utility
 *
 * @author Jayawi Perera
 */
class Utility {

	const VERSION_COMPARISON_EQUAL = 'equal';
	const VERSION_COMPARISON_OLDER = 'older';
	const VERSION_COMPARISON_NEWER = 'newer';

	/**
	 * Compares the versions provided and gives the result from the 2nd
	 * version value's perspective
	 *
	 * @param string $sLeftVersion
	 * @param string $sRightVersion
	 * @return type
	 */
	public static function compareVersions ($sLeftVersion, $sRightVersion) {

		$sComparisonResult = null;

		if ($sLeftVersion == $sRightVersion) {

			$sComparisonResult = self::VERSION_COMPARISON_EQUAL;

		} else {

			$aLeftVersionParts = explode('.', $sLeftVersion);
			$aRightVersionParts = explode('.', $sRightVersion);

			$iLeftVersionMajorNumber = $aLeftVersionParts[0];
			$iLeftVersionMinorNumber = $aLeftVersionParts[1];
			$iLeftVersionRevisionNumber = $aLeftVersionParts[2];

			$iRightVersionMajorNumber = $aRightVersionParts[0];
			$iRightVersionMinorNumber = $aRightVersionParts[1];
			$iRightVersionRevisionNumber = $aRightVersionParts[2];

			if ($iLeftVersionMajorNumber == $iRightVersionMajorNumber) {
				// Major Version is the same

				if ($iLeftVersionMinorNumber == $iRightVersionMinorNumber) {
					// Minor Version is the same

					if ($iLeftVersionRevisionNumber > $iRightVersionRevisionNumber) {
						// Left Revision Version is newer

						$sComparisonResult = self::VERSION_COMPARISON_OLDER;

					} else {
						// Left Revision Version is older

						$sComparisonResult = self::VERSION_COMPARISON_NEWER;

					}

				} else {
					// Minor Version is different

					if ($iLeftVersionMinorNumber > $iRightVersionMinorNumber) {
						// Left Minor Version is newer

						$sComparisonResult = self::VERSION_COMPARISON_OLDER;

					} else {
						// Left Minor Version is older

						$sComparisonResult = self::VERSION_COMPARISON_NEWER;

					}
				}

			} else {
				// Major Version is different

				if ($iLeftVersionMajorNumber > $iRightVersionMajorNumber) {
					// Left Major Version is newer

					$sComparisonResult = self::VERSION_COMPARISON_OLDER;

				} else {
					// Left Major Version is older

					$sComparisonResult = self::VERSION_COMPARISON_NEWER;

				}

			}

		}

		return $sComparisonResult;

	}

}