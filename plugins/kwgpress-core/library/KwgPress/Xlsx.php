<?php
namespace KwgPress;

/**
 * Description of Xslx
 *
 * @author Jayawi Perera
 */
class Xlsx {

	protected $_rFile = null;
	protected $_sFilePath = null;

	public function __construct ($sFilePath) {

		$this->_sFilePath = $sFilePath;

	}

	public function open ($sMode = null) {

		if (is_null($sMode)) {
			$sMode = 'r';
		}

		if (is_file($this->_sFilePath)) {
			$this->_rFile = fopen($this->_sFilePath, 'w');
		}

	}

	public function close () {

		if (!is_null($this->_rFile)) {
			fclose($this->_rFile);
			$this->_rFile = null;
		}

	}


	public function create () {

		$this->close();
		$this->_rFile = fopen($this->_sFilePath, 'w');

	}



	public function startWorkbook () {

		if (!$this->_isFileOpen()) {
			return;
		}

		$sContent = <<<CONTENT
<?xml version="1.0" ?><ss:Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet" xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet" xmlns:html="http://www.w3.org/TR/REC-html40">
CONTENT;

		fwrite($this->_rFile, $sContent);

		return $this;

	}

	public function endWorkbook () {

		if (!$this->_isFileOpen()) {
			return;
		}

		$sContent = <<<CONTENT
</ss:Workbook>
CONTENT;
		fwrite($this->_rFile, $sContent);

		return $this;

	}




	public function startStyles () {

		if (!$this->_isFileOpen()) {
			return;
		}

		$sContent = <<<CONTENT
<ss:Styles>
CONTENT;

		fwrite($this->_rFile, $sContent);

		return $this;

	}

	public function endStyles () {

		if (!$this->_isFileOpen()) {
			return;
		}

		$sContent = <<<CONTENT
</ss:Styles>
CONTENT;

		fwrite($this->_rFile, $sContent);

		return $this;

	}

	public function startStyle ($sId = 'Default', $aOptions = null) {

		if (!$this->_isFileOpen()) {
			return;
		}

		$sName = '';
		$sParent = '';
		if (is_array($aOptions)) {

		}

		$sContent = <<<CONTENT
<ss:Style ss:ID="{$sId}"{$sName}{$sParent}>
CONTENT;

		fwrite($this->_rFile, $sContent);

		return $this;

	}

	public function endStyle () {

		if (!$this->_isFileOpen()) {
			return;
		}

		$sContent = <<<CONTENT
</ss:Style>
CONTENT;

		fwrite($this->_rFile, $sContent);

		return $this;

	}

	public function startAlignment ($aOptions = null) {

		if (!$this->_isFileOpen()) {
			return;
		}

		$sHorizontal = '';
		$sIndent = '';
		$sReadingOrder = '';
		$sRotate = '';
		$sShrinkToFit = '';
		$sVertical = '';
		$sVerticalText = '';
		$sWrapText = '';

		if (is_array($aOptions)) {
			if (isset($aOptions['horizontal'])) {
				$sHorizontal = ' ss:Horizontal="' . $aOptions['horizontal'] . '"';
			}
			if (isset($aOptions['indent'])) {
				$sIndent = ' ss:Indent="' . $aOptions['indent'] . '"';
			}
			if (isset($aOptions['reading_order'])) {
				$sReadingOrder = ' ss:ReadingOrder="' . $aOptions['reading_order'] . '"';
			}
			if (isset($aOptions['rotate'])) {
				$sRotate = ' ss:Rotate="' . $aOptions['rotate'] . '"';
			}
			if (isset($aOptions['shrink_to_fit'])) {
				$sShrinkToFit = ' ss:ShrinkToFit="' . $aOptions['shrink_to_fit'] . '"';
			}
			if (isset($aOptions['vertical'])) {
				$sVertical = ' ss:Vertical="' . $aOptions['vertical'] . '"';
			}
			if (isset($aOptions['vertical_text'])) {
				$sVerticalText = ' ss:VerticalText="' . $aOptions['vertical_text'] . '"';
			}
			if (isset($aOptions['wrap_text'])) {
				$sWrapText = ' ss:WrapText="' . $aOptions['wrap_text'] . '"';
			}
		}

		$sContent = <<<CONTENT
<ss:Alignment{$sHorizontal}{$sIndent}{$sReadingOrder}{$sRotate}{$sShrinkToFit}{$sVertical}{$sVerticalText}{$sWrapText}>
CONTENT;

		fwrite($this->_rFile, $sContent);

		return $this;

	}

	public function endAlignment () {

		if (!$this->_isFileOpen()) {
			return;
		}

		$sContent = <<<CONTENT
</ss:Alignment>
CONTENT;

		fwrite($this->_rFile, $sContent);

		return $this;

	}

	public function startFont ($aOptions = null) {

		if (!$this->_isFileOpen()) {
			return;
		}

		$sBold = '';
		$sColour = '';
		$sFontName = '';
		$sItalic = '';
		$sOutline = '';
		$sShadow = '';
		$sSize = '';
		$sStrikeThrough = '';
		$sUnderline = '';
		$sVerticalAlign = '';
		$sCharSet = '';
		$sFamily = '';
		if (is_array($aOptions)) {
			if (isset($aOptions['bold'])) {
				$sBold = ' ss:Bold="' . $aOptions['bold'] . '"';
			}
			if (isset($aOptions['colour'])) {
				$sColour = ' ss:Color="' . $aOptions['colour'] . '"';
			}
			if (isset($aOptions['font_name'])) {
				$sFontName = ' ss:FontName="' . $aOptions['font_name'] . '"';
			}
			if (isset($aOptions['italic'])) {
				$sItalic = ' ss:Italic="' . $aOptions['italic'] . '"';
			}
			if (isset($aOptions['outline'])) {
				$sOutline = ' ss:Outline="' . $aOptions['outline'] . '"';
			}
			if (isset($aOptions['shadow'])) {
				$sShadow = ' ss:Shadow="' . $aOptions['shadow'] . '"';
			}
			if (isset($aOptions['size'])) {
				$sSize = ' ss:Size="' . $aOptions['size'] . '"';
			}
			if (isset($aOptions['strike_through'])) {
				$sStrikeThrough = ' ss:StrikeThrough="' . $aOptions['strike_through'] . '"';
			}
			if (isset($aOptions['underline'])) {
				$sUnderline = ' ss:Underline="' . $aOptions['underline'] . '"';
			}
			if (isset($aOptions['vertical_align'])) {
				$sVerticalAlign = ' ss:VerticalAlign="' . $aOptions['vertical_align'] . '"';
			}
			if (isset($aOptions['charset'])) {
				$sCharSet = ' ss:CharSet="' . $aOptions['charset'] . '"';
			}
			if (isset($aOptions['family'])) {
				$sFamily = ' ss:Family="' . $aOptions['family'] . '"';
			}
		}

		$sContent = <<<CONTENT
<ss:Font{$sBold}{$sColour}{$sFontName}{$sItalic}{$sOutline}{$sShadow}{$sSize}{$sStrikeThrough}{$sUnderline}{$sVerticalAlign}{$sCharSet}{$sFamily}>
CONTENT;

		fwrite($this->_rFile, $sContent);

		return $this;

	}

	public function endFont () {

		if (!$this->_isFileOpen()) {
			return;
		}

		$sContent = <<<CONTENT
</ss:Font>
CONTENT;

		fwrite($this->_rFile, $sContent);

		return $this;

	}

	public function startInterior ($aOptions = null) {

		if (!$this->_isFileOpen()) {
			return;
		}

		$sColour = '';
		$sPattern = '';
		$sPatternColour = '';

		if (is_array($aOptions)) {
			if (isset($aOptions['colour'])) {
				$sColour = ' ss:Color="' . $aOptions['colour'] . '"';
			}
			if (isset($aOptions['pattern'])) {
				$sPattern = ' ss:Pattern="' . $aOptions['pattern'] . '"';
			}
			if (isset($aOptions['pattern_colour'])) {
				$sPatternColour = ' ss:PatternColor="' . $aOptions['pattern_colour'] . '"';
			}
		}

		$sContent = <<<CONTENT
<ss:Interior{$sColour}{$sPattern}{$sPatternColour}>
CONTENT;

		fwrite($this->_rFile, $sContent);

		return $this;

	}

	public function endInterior () {

		if (!$this->_isFileOpen()) {
			return;
		}

		$sContent = <<<CONTENT
</ss:Interior>
CONTENT;

		fwrite($this->_rFile, $sContent);

		return $this;

	}




	public function startWorksheet ($sSheetName = null) {

		if (!$this->_isFileOpen()) {
			return;
		}

		$sContent = <<<CONTENT
<ss:Worksheet ss:Name="{$sSheetName}">
CONTENT;

		fwrite($this->_rFile, $sContent);

		return $this;

	}

	public function endWorksheet () {

		if (!$this->_isFileOpen()) {
			return;
		}

		$sContent = <<<CONTENT
</ss:Worksheet>
CONTENT;

		fwrite($this->_rFile, $sContent);

		return $this;

	}

	/**
	 *
	 * http://msdn.microsoft.com/en-us/library/office/aa140066(v=office.10).aspx#odc_xmlss_ss:table
	 *
	 * @param type $aOptions
	 * @return type
	 */
	public function startTable ($aOptions = null) {

		if (!$this->_isFileOpen()) {
			return;
		}

		$sDefaultColumnWidth = '';
		$sDefaultRowHeight = '';
		$sExpandedColumnCount = '';
		$sExpandedRowCount = '';
		$sLeftCell = '';
		$sTopCell = '';
		$sStyleId = '';
		$sFullColumns = '';
		$sFullRows = '';
		if (is_array($aOptions)) {

		}

		if ($sFullColumns == '') {
			$sFullColumns = ' x:FullColumns="1"';
		}
		if ($sFullRows == '') {
			$sFullRows = ' x:FullRows="1"';
		}

		$sContent = <<<CONTENT
<ss:Table{$sDefaultColumnWidth}{$sDefaultRowHeight}{$sExpandedColumnCount}{$sExpandedRowCount}{$sLeftCell}{$sTopCell}{$sStyleId}{$sFullColumns}{$sFullRows}>
CONTENT;

		fwrite($this->_rFile, $sContent);

		return $this;

	}

	public function endTable () {

		if (!$this->_isFileOpen()) {
			return;
		}

		$sContent = <<<CONTENT
</ss:Table>
CONTENT;

		fwrite($this->_rFile, $sContent);

		return $this;

	}

	/**
	 *
	 * http://msdn.microsoft.com/en-us/library/office/aa140066(v=office.10).aspx#odc_xmlss_ss:row
	 *
	 * @param type $aOptions
	 * @return type
	 */
	public function startRow ($aOptions = null) {

		if (!$this->_isFileOpen()) {
			return;
		}

		$sCaption = '';
		$sAutoFitHeight = '';
		$sHeight = '';
		$sHidden = '';
		$sIndex = '';
		$sSpan = '';
		$sStyleId = '';

		if (is_array($aOptions)) {

		}

		$sContent = <<<CONTENT
<ss:Row{$sCaption}{$sAutoFitHeight}{$sHeight}{$sHidden}{$sIndex}{$sSpan}{$sStyleId}>
CONTENT;

		fwrite($this->_rFile, $sContent);

		return $this;

	}

	public function endRow () {

		if (!$this->_isFileOpen()) {
			return;
		}

		$sContent = <<<CONTENT
</ss:Row>
CONTENT;

		fwrite($this->_rFile, $sContent);

		return $this;

	}

	public function startColumn ($aOptions = null) {

		if (!$this->_isFileOpen()) {
			return;
		}

		$sCaption = '';
		$sAutoFitWidth = '';
		$sHidden = '';
		$sIndex = '';
		$sSpan = '';
		$sStyleId = '';
		$sWidth = '';

		if (is_array($aOptions)) {

		}

		$sContent = <<<CONTENT
<ss:Column{$sCaption}{$sAutoFitWidth}{$sHidden}{$sIndex}{$sSpan}{$sStyleId}{$sWidth}>
CONTENT;

		fwrite($this->_rFile, $sContent);

		return $this;

	}

	public function endColumn () {

		if (!$this->_isFileOpen()) {
			return;
		}

		$sContent = <<<CONTENT
</ss:Column>
CONTENT;

		fwrite($this->_rFile, $sContent);

		return $this;

	}

	public function startCell ($aOptions = null) {

		if (!$this->_isFileOpen()) {
			return;
		}

		$sPasteFormula = '';
		$sArrayRange = '';
		$sFormula = '';
		$sHRef = '';
		$sIndex = '';
		$sMergeAcross = '';
		$sMergeDown = '';
		$sStyleId = '';
		$sHRefScreenTip = '';

		if (is_array($aOptions)) {

			if (isset($aOptions['style_id'])) {
				$sStyleId = ' ss:StyleID="' . $aOptions['style_id'] . '"';
			}

		}

		$sContent = <<<CONTENT
<ss:Cell{$sPasteFormula}{$sArrayRange}{$sFormula}{$sHRef}{$sIndex}{$sMergeAcross}{$sMergeDown}{$sStyleId}{$sHRefScreenTip}>
CONTENT;

		fwrite($this->_rFile, $sContent);

		return $this;

	}

	public function endCell () {

		if (!$this->_isFileOpen()) {
			return;
		}

		$sContent = <<<CONTENT
</ss:Cell>
CONTENT;

		fwrite($this->_rFile, $sContent);

		return $this;

	}

	public function outputData ($sData, $aOptions = null) {

		if (!$this->_isFileOpen()) {
			return;
		}

		$sType = '';
		$sTicked = '';

		if (is_array($aOptions)) {

			if (isset($aOptions['type'])) {
				$sType = ' ss:Type="' . $aOptions['type'] . '"';
			}

		}

		if ($sType == '') {
			$sType = ' ss:Type="String"';
		}

		$sContent = <<<CONTENT
<ss:Data{$sType}{$sTicked}>{$sData}</ss:Data>
CONTENT;

		fwrite($this->_rFile, $sContent);

		return $this;

	}















	protected function _isFileOpen () {

		return (!is_null($this->_rFile));

	}
}