<?php 
$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
$public_base_directory = $_SERVER['DOCUMENT_ROOT']."/".$privat_base_directory;
$env = $public_base_directory.'/env.php';
if (!file_exists($env)) {
	echo 'Please put env file.';
	exit;
}
require_once ($env);

$document_root 	= $_SERVER['DOCUMENT_ROOT'].'/'.getenv('APP_NAME');
require_once ($document_root."/libraries/helper/excelwriter/Xlsxwriter.php");

class XLSXWriterModif extends XLSXWriter{
	private $column_index = 0;

	public function __construct(){
		parent::__construct();
	}
	
	public function setColumnIndex($column=0){ 
		$this->column_index = $column; 
	}

	public function getColumnIndex(){ 
		return $this->column_index; 
	}

	public function writeToStdOutExt(){
		$temp_file = $this->tempFilename();
		$this->writeToFileExt($temp_file);
		readfile($temp_file);
	}

	public function writeToFileExt($filename){
		foreach($this->sheets as $sheet_name => $sheet) {
			self::finalizeSheet($sheet_name);//making sure all footers have been written
		}

		if ( file_exists( $filename ) ) {
			if ( is_writable( $filename ) ) {
				@unlink( $filename ); //if the zip already exists, remove it
			} else {
				self::log( "Error in " . __CLASS__ . "::" . __FUNCTION__ . ", file is not writeable." );
				return;
			}
		}
		$zip = new ZipArchive();
		if (empty($this->sheets))                       { self::log("Error in ".__CLASS__."::".__FUNCTION__.", no worksheets defined."); return; }
		if (!$zip->open($filename, ZipArchive::CREATE)) { self::log("Error in ".__CLASS__."::".__FUNCTION__.", unable to create zip."); return; }

		$zip->addEmptyDir("docProps/");
		$zip->addFromString("docProps/app.xml" , self::buildAppXML() );
		$zip->addFromString("docProps/core.xml", self::buildCoreXML());

		$zip->addEmptyDir("_rels/");
		$zip->addFromString("_rels/.rels", self::buildRelationshipsXML());

		$zip->addEmptyDir("xl/worksheets/");
		foreach($this->sheets as $sheet) {
			$zip->addFile($sheet->filename, "xl/worksheets/".$sheet->xmlname );
		}
		$zip->addFromString("xl/workbook.xml"         , self::buildWorkbookXML() );
		$zip->addFile($this->writeStylesXMLExt(), "xl/styles.xml" );  //$zip->addFromString("xl/styles.xml"           , self::buildStylesXML() );
		$zip->addFromString("[Content_Types].xml"     , self::buildContentTypesXML() );

		$zip->addEmptyDir("xl/_rels/");
		$zip->addFromString("xl/_rels/workbook.xml.rels", self::buildWorkbookRelsXML() );
		$zip->close();
	}

	private function styleFontIndexesExt(){
		static $border_allowed = array('left','right','top','bottom');
		static $border_style_allowed = array('thin','medium','thick','dashDot','dashDotDot','dashed','dotted','double','hair','mediumDashDot','mediumDashDotDot','mediumDashed','slantDashDot');
		static $horizontal_allowed = array('general','left','right','justify','center');
		static $vertical_allowed = array('bottom','center','distributed','top');
		$default_font = array('size'=>'10','name'=>'Arial','family'=>'2');
		$fills = array('','');
		$fonts = array('','','','');
		$borders = array('');
		$style_indexes = array();

		foreach($this->cell_styles as $i=>$cell_style_string){
			$semi_colon_pos = strpos($cell_style_string,";");
			$number_format_idx = substr($cell_style_string, 0, $semi_colon_pos);
			$style_json_string = substr($cell_style_string, $semi_colon_pos+1);
			$style = @json_decode($style_json_string, $as_assoc=true);

			$style_indexes[$i] = array('num_fmt_idx'=>$number_format_idx);
			if (isset($style['border']) && is_string($style['border'])){
				$border_value['side'] = array_intersect(explode(",", $style['border']), $border_allowed);
				if(count($border_value['side']) == 0){
					$arrBorder 	= explode(",", $style['border']);
					$arrTemp1 	= array();
					$arrTemp2 	= array();
					foreach($arrBorder as $val){
						list($sidenya, $stylenya) = explode(":", $val);
						$arrTemp1[] = $sidenya;
						if(in_array($stylenya, $border_style_allowed)) $arrTemp2[$sidenya] = $stylenya;
					}
					$border_value['side']  = array_intersect($arrTemp1, $border_allowed);
					$border_value['style'] = $arrTemp2;
				}
				if (isset($style['border-style']) && in_array($style['border-style'],$border_style_allowed)){
					$border_value['style'] = $style['border-style'];
				}
				if (isset($style['border-color']) && is_string($style['border-color']) && $style['border-color'][0]=='#'){
					$v = substr($style['border-color'],1,6);
					$v = strlen($v)==3 ? $v[0].$v[0].$v[1].$v[1].$v[2].$v[2] : $v;
					$border_value['color'] = "FF".strtoupper($v);
				}
				$style_indexes[$i]['border_idx'] = self::add_to_list_get_index($borders, json_encode($border_value));
			}
			if (isset($style['fill']) && is_string($style['fill']) && $style['fill'][0]=='#'){
				$v = substr($style['fill'],1,6);
				$v = strlen($v)==3 ? $v[0].$v[0].$v[1].$v[1].$v[2].$v[2] : $v;
				$style_indexes[$i]['fill_idx'] = self::add_to_list_get_index($fills, "FF".strtoupper($v) );
			}
			if (isset($style['halign']) && in_array($style['halign'],$horizontal_allowed)){
				$style_indexes[$i]['alignment'] = true;
				$style_indexes[$i]['halign'] = $style['halign'];
			}
			if (isset($style['valign']) && in_array($style['valign'],$vertical_allowed)){
				$style_indexes[$i]['alignment'] = true;
				$style_indexes[$i]['valign'] = $style['valign'];
			}
			if (isset($style['wrap_text'])){
				$style_indexes[$i]['alignment'] = true;
				$style_indexes[$i]['wrap_text'] = (bool)$style['wrap_text'];
			}

			$font = $default_font;
			if (isset($style['font-size'])){
				$font['size'] = floatval($style['font-size']);
			}
			if (isset($style['font']) && is_string($style['font'])){
				if ($style['font']=='Comic Sans MS') { $font['family']=4; }
				if ($style['font']=='Times New Roman') { $font['family']=1; }
				if ($style['font']=='Courier New') { $font['family']=3; }
				$font['name'] = strval($style['font']);
			}
			if (isset($style['font-style']) && is_string($style['font-style'])){
				if (strpos($style['font-style'], 'bold')!==false) { $font['bold'] = true; }
				if (strpos($style['font-style'], 'italic')!==false) { $font['italic'] = true; }
				if (strpos($style['font-style'], 'strike')!==false) { $font['strike'] = true; }
				if (strpos($style['font-style'], 'underline')!==false) { $font['underline'] = true; }
			}
			if (isset($style['color']) && is_string($style['color']) && $style['color'][0]=='#'){
				$v = substr($style['color'],1,6);
				$v = strlen($v)==3 ? $v[0].$v[0].$v[1].$v[1].$v[2].$v[2] : $v;
				$font['color'] = "FF".strtoupper($v);
			}
			if ($font!=$default_font){
				$style_indexes[$i]['font_idx'] = self::add_to_list_get_index($fonts, json_encode($font));
			}
		}
		return array('fills'=>$fills,'fonts'=>$fonts,'borders'=>$borders,'styles'=>$style_indexes );
	}

	private function writeStylesXMLExt(){
		$r = $this->styleFontIndexesExt();
		$fills = $r['fills'];
		$fonts = $r['fonts'];
		$borders = $r['borders'];
		$style_indexes = $r['styles'];

		$temporary_filename = $this->tempFilename();
		$file = new XLSXWriter_BuffererWriter($temporary_filename);
		$file->write('<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'."\n");
		$file->write('<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">');
		$file->write('<numFmts count="'.count($this->number_formats).'">');
		foreach($this->number_formats as $i=>$v) {
			$file->write('<numFmt numFmtId="'.(164+$i).'" formatCode="'.self::xmlspecialchars($v).'" />');
		}
		//$file->write(		'<numFmt formatCode="GENERAL" numFmtId="164"/>');
		//$file->write(		'<numFmt formatCode="[$$-1009]#,##0.00;[RED]\-[$$-1009]#,##0.00" numFmtId="165"/>');
		//$file->write(		'<numFmt formatCode="YYYY-MM-DD\ HH:MM:SS" numFmtId="166"/>');
		//$file->write(		'<numFmt formatCode="YYYY-MM-DD" numFmtId="167"/>');
		$file->write('</numFmts>');

		$file->write('<fonts count="'.(count($fonts)).'">');
		$file->write(		'<font><name val="Arial"/><charset val="1"/><family val="2"/><sz val="10"/></font>');
		$file->write(		'<font><name val="Arial"/><family val="0"/><sz val="10"/></font>');
		$file->write(		'<font><name val="Arial"/><family val="0"/><sz val="10"/></font>');
		$file->write(		'<font><name val="Arial"/><family val="0"/><sz val="10"/></font>');

		foreach($fonts as $font) {
			if (!empty($font)) { //fonts have 4 empty placeholders in array to offset the 4 static xml entries above
				$f = json_decode($font,true);
				$file->write('<font>');
				$file->write(	'<name val="'.htmlspecialchars($f['name']).'"/><charset val="1"/><family val="'.intval($f['family']).'"/>');
				$file->write(	'<sz val="'.intval($f['size']).'"/>');
				if (!empty($f['color'])) { $file->write('<color rgb="'.strval($f['color']).'"/>'); }
				if (!empty($f['bold'])) { $file->write('<b val="true"/>'); }
				if (!empty($f['italic'])) { $file->write('<i val="true"/>'); }
				if (!empty($f['underline'])) { $file->write('<u val="single"/>'); }
				if (!empty($f['strike'])) { $file->write('<strike val="true"/>'); }
				$file->write('</font>');
			}
		}
		$file->write('</fonts>');

		$file->write('<fills count="'.(count($fills)).'">');
		$file->write(	'<fill><patternFill patternType="none"/></fill>');
		$file->write(	'<fill><patternFill patternType="gray125"/></fill>');
		foreach($fills as $fill) {
			if (!empty($fill)) { //fills have 2 empty placeholders in array to offset the 2 static xml entries above
				$file->write('<fill><patternFill patternType="solid"><fgColor rgb="'.strval($fill).'"/><bgColor indexed="64"/></patternFill></fill>');
			}
		}
		$file->write('</fills>');

		$file->write('<borders count="'.(count($borders)).'">');
        $file->write(    '<border diagonalDown="false" diagonalUp="false"><left/><right/><top/><bottom/><diagonal/></border>');
		foreach($borders as $border) {
			if (!empty($border)) { //fonts have an empty placeholder in the array to offset the static xml entry above
				$pieces = json_decode($border,true);
				$border_color = !empty($pieces['color']) ? '<color rgb="'.strval($pieces['color']).'"/>' : '';
				$file->write('<border diagonalDown="false" diagonalUp="false">');
				foreach (array('left', 'right', 'top', 'bottom') as $side){
                    $show_side = in_array($side,$pieces['side']) ? true : false;
					if(is_string($pieces['style'])){
						$border_style = !empty($pieces['style']) ? $pieces['style'] : 'hair';
					} else if(is_array($pieces['style'])){
						$border_style = !empty($pieces['style'][$side]) ? $pieces['style'][$side] : 'hair';
					}
					$file->write($show_side ? '<'.$side.' style="'.$border_style.'">'.$border_color.'</'.$side.'>' : '<'.$side.'/>');
				}
				$file->write(  '<diagonal/>');
				$file->write('</border>');
			}
		}
		$file->write('</borders>');

		$file->write('<cellStyleXfs count="20">');
		$file->write(		'<xf applyAlignment="true" applyBorder="true" applyFont="true" applyProtection="true" borderId="0" fillId="0" fontId="0" numFmtId="164">');
		$file->write(		'<alignment horizontal="general" indent="0" shrinkToFit="false" textRotation="0" vertical="bottom" wrapText="false"/>');
		$file->write(		'<protection hidden="false" locked="true"/>');
		$file->write(		'</xf>');
		$file->write(		'<xf applyAlignment="false" applyBorder="false" applyFont="true" applyProtection="false" borderId="0" fillId="0" fontId="1" numFmtId="0"/>');
		$file->write(		'<xf applyAlignment="false" applyBorder="false" applyFont="true" applyProtection="false" borderId="0" fillId="0" fontId="1" numFmtId="0"/>');
		$file->write(		'<xf applyAlignment="false" applyBorder="false" applyFont="true" applyProtection="false" borderId="0" fillId="0" fontId="2" numFmtId="0"/>');
		$file->write(		'<xf applyAlignment="false" applyBorder="false" applyFont="true" applyProtection="false" borderId="0" fillId="0" fontId="2" numFmtId="0"/>');
		$file->write(		'<xf applyAlignment="false" applyBorder="false" applyFont="true" applyProtection="false" borderId="0" fillId="0" fontId="0" numFmtId="0"/>');
		$file->write(		'<xf applyAlignment="false" applyBorder="false" applyFont="true" applyProtection="false" borderId="0" fillId="0" fontId="0" numFmtId="0"/>');
		$file->write(		'<xf applyAlignment="false" applyBorder="false" applyFont="true" applyProtection="false" borderId="0" fillId="0" fontId="0" numFmtId="0"/>');
		$file->write(		'<xf applyAlignment="false" applyBorder="false" applyFont="true" applyProtection="false" borderId="0" fillId="0" fontId="0" numFmtId="0"/>');
		$file->write(		'<xf applyAlignment="false" applyBorder="false" applyFont="true" applyProtection="false" borderId="0" fillId="0" fontId="0" numFmtId="0"/>');
		$file->write(		'<xf applyAlignment="false" applyBorder="false" applyFont="true" applyProtection="false" borderId="0" fillId="0" fontId="0" numFmtId="0"/>');
		$file->write(		'<xf applyAlignment="false" applyBorder="false" applyFont="true" applyProtection="false" borderId="0" fillId="0" fontId="0" numFmtId="0"/>');
		$file->write(		'<xf applyAlignment="false" applyBorder="false" applyFont="true" applyProtection="false" borderId="0" fillId="0" fontId="0" numFmtId="0"/>');
		$file->write(		'<xf applyAlignment="false" applyBorder="false" applyFont="true" applyProtection="false" borderId="0" fillId="0" fontId="0" numFmtId="0"/>');
		$file->write(		'<xf applyAlignment="false" applyBorder="false" applyFont="true" applyProtection="false" borderId="0" fillId="0" fontId="0" numFmtId="0"/>');
		$file->write(		'<xf applyAlignment="false" applyBorder="false" applyFont="true" applyProtection="false" borderId="0" fillId="0" fontId="1" numFmtId="43"/>');
		$file->write(		'<xf applyAlignment="false" applyBorder="false" applyFont="true" applyProtection="false" borderId="0" fillId="0" fontId="1" numFmtId="41"/>');
		$file->write(		'<xf applyAlignment="false" applyBorder="false" applyFont="true" applyProtection="false" borderId="0" fillId="0" fontId="1" numFmtId="44"/>');
		$file->write(		'<xf applyAlignment="false" applyBorder="false" applyFont="true" applyProtection="false" borderId="0" fillId="0" fontId="1" numFmtId="42"/>');
		$file->write(		'<xf applyAlignment="false" applyBorder="false" applyFont="true" applyProtection="false" borderId="0" fillId="0" fontId="1" numFmtId="9"/>');
		$file->write('</cellStyleXfs>');

		$file->write('<cellXfs count="'.(count($style_indexes)).'">');
		//$file->write(		'<xf applyAlignment="false" applyBorder="false" applyFont="false" applyProtection="false" borderId="0" fillId="0" fontId="0" numFmtId="164" xfId="0"/>');
		//$file->write(		'<xf applyAlignment="false" applyBorder="false" applyFont="false" applyProtection="false" borderId="0" fillId="0" fontId="0" numFmtId="165" xfId="0"/>');
		//$file->write(		'<xf applyAlignment="false" applyBorder="false" applyFont="false" applyProtection="false" borderId="0" fillId="0" fontId="0" numFmtId="166" xfId="0"/>');
		//$file->write(		'<xf applyAlignment="false" applyBorder="false" applyFont="false" applyProtection="false" borderId="0" fillId="0" fontId="0" numFmtId="167" xfId="0"/>');
		foreach($style_indexes as $v)
		{
			$applyAlignment = isset($v['alignment']) ? 'true' : 'false';
			$wrapText = !empty($v['wrap_text']) ? 'true' : 'false';
			$horizAlignment = isset($v['halign']) ? $v['halign'] : 'general';
			$vertAlignment = isset($v['valign']) ? $v['valign'] : 'bottom';
			$applyBorder = isset($v['border_idx']) ? 'true' : 'false';
			$applyFont = 'true';
			$borderIdx = isset($v['border_idx']) ? intval($v['border_idx']) : 0;
			$fillIdx = isset($v['fill_idx']) ? intval($v['fill_idx']) : 0;
			$fontIdx = isset($v['font_idx']) ? intval($v['font_idx']) : 0;
			//$file->write('<xf applyAlignment="'.$applyAlignment.'" applyBorder="'.$applyBorder.'" applyFont="'.$applyFont.'" applyProtection="false" borderId="'.($borderIdx).'" fillId="'.($fillIdx).'" fontId="'.($fontIdx).'" numFmtId="'.(164+$v['num_fmt_idx']).'" xfId="0"/>');
			$file->write('<xf applyAlignment="'.$applyAlignment.'" applyBorder="'.$applyBorder.'" applyFont="'.$applyFont.'" applyProtection="false" borderId="'.($borderIdx).'" fillId="'.($fillIdx).'" fontId="'.($fontIdx).'" numFmtId="'.(164+$v['num_fmt_idx']).'" xfId="0">');
			$file->write('	<alignment horizontal="'.$horizAlignment.'" vertical="'.$vertAlignment.'" textRotation="0" wrapText="'.$wrapText.'" indent="0" shrinkToFit="false"/>');
			$file->write('	<protection locked="true" hidden="false"/>');
			$file->write('</xf>');
		}
		$file->write('</cellXfs>');
		$file->write(	'<cellStyles count="6">');
		$file->write(		'<cellStyle builtinId="0" customBuiltin="false" name="Normal" xfId="0"/>');
		$file->write(		'<cellStyle builtinId="3" customBuiltin="false" name="Comma" xfId="15"/>');
		$file->write(		'<cellStyle builtinId="6" customBuiltin="false" name="Comma [0]" xfId="16"/>');
		$file->write(		'<cellStyle builtinId="4" customBuiltin="false" name="Currency" xfId="17"/>');
		$file->write(		'<cellStyle builtinId="7" customBuiltin="false" name="Currency [0]" xfId="18"/>');
		$file->write(		'<cellStyle builtinId="5" customBuiltin="false" name="Percent" xfId="19"/>');
		$file->write(	'</cellStyles>');
		$file->write('</styleSheet>');
		$file->close();
		return $temporary_filename;
	}

	/** Setting nama sheet, width, autofilter, freeze row dan freeze column **/
	/** Penggunaannya lihat di dokumentasinya **/
	public function setSheetAndColOption($sheet_name, $col_options=null){
		if (empty($sheet_name)) return;

		$suppress_row = isset($col_options['suppress_row']) ? intval($col_options['suppress_row']) : false;
		if (is_bool($col_options)){
			self::log( "Warning! passing $suppress_row=false|true to writeSheetHeader() is deprecated, this will be removed in a future version." );
			$suppress_row = intval($col_options);
		}
		$style = &$col_options;

		$col_widths 	= isset($col_options['widths']) ? (array)$col_options['widths'] : array();
		$auto_filter 	= isset($col_options['auto_filter']) ? intval($col_options['auto_filter']) : false;
		$freeze_rows 	= isset($col_options['freeze_rows']) ? intval($col_options['freeze_rows']) : false;
		$freeze_columns = isset($col_options['freeze_columns']) ? intval($col_options['freeze_columns']) : false;
		self::initializeSheet($sheet_name, $col_widths, $auto_filter, $freeze_rows, $freeze_columns);
		$this->current_sheet = $sheet_name;
	}

	public function writeSheetHeaderAndRow($sheet_name, array $row, $row_options=null, $header_types=array()){
		if (empty($sheet_name)) return;

		$this->initializeSheet($sheet_name);
		$sheet = &$this->sheets[$sheet_name];
		if(count($sheet->columns) < count($row)){
			$default_column_types 	= $this->initializeColumnTypesExtend( array_fill($from=0, $until=count($row), 'GENERAL') );
			$this->column_index 	= count($sheet->columns);
			$sheet->columns 		= array_merge((array)$sheet->columns, $default_column_types);
		}
		if(count($header_types) > 0){
			$default_column_types 	= $this->initializeColumnTypesExtend($header_types);
			$this->column_index 	= count($sheet->columns);
			$sheet->columns 		= array_merge((array)$sheet->columns, $default_column_types);
		}

		if (!empty($row_options)){
			$ht 		= isset($row_options['height']) ? floatval($row_options['height']) : 12.1;
			$customHt 	= isset($row_options['height']) ? true : false;
			$hidden 	= isset($row_options['hidden']) ? (bool)($row_options['hidden']) : false;
			$collapsed 	= isset($row_options['collapsed']) ? (bool)($row_options['collapsed']) : false;
			$rowCount 	= ($sheet->row_count + 1);
			$sheet->file_writer->write('<row collapsed="'.($collapsed).'" customFormat="false" customHeight="'.($customHt).'" hidden="'.($hidden).'" ht="'.($ht).'" outlineLevel="0" r="'.$rowCount.'">');
		} else{
			$sheet->file_writer->write('<row collapsed="false" customFormat="false" customHeight="false" hidden="false" ht="12.1" outlineLevel="0" r="'.($sheet->row_count + 1).'">');
		}

		$style = &$row_options;
		$column_count = 0;
		$column_index = $this->column_index;
		foreach ($row as $v) {
			$number_format = $sheet->columns[$column_index]['number_format'];
			$number_format_type = $sheet->columns[$column_index]['number_format_type'];
			
			if(empty($style)){
				$cell_style_idx = $sheet->columns[$column_index]['default_cell_style'];
			} else{
				if(isset($style[0]) || isset($style['default'])){
					$arrStyle = isset($style[$column_count]) ? $style[$column_count] : (isset($style['default']) ? $style['default'] : array());
				} else{
					$arrStyle = $style;
				}				
				$cell_style_idx = $this->addCellStyleExtend($number_format, json_encode($arrStyle));
			}
			//$cell_style_idx = empty($style) ? $sheet->columns[$column_index]['default_cell_style'] : 
			//$this->addCellStyleExtend( $number_format, json_encode(isset($style[0])?$style[$column_count]:$style));
			//var_dump(isset($style[0]) || isset($style['default'])); echo '<br>';

			$this->writeCell($sheet->file_writer, $sheet->row_count, $column_count, $v, $number_format_type, $cell_style_idx);
			$column_count++;
			$column_index++;
		}
		$sheet->file_writer->write('</row>');
		$sheet->row_count++;
		$this->current_sheet = $sheet_name;
	}

	public function newMergeCell($sheet_name, $startCell, $endCell){
		if (empty($sheet_name) || $this->sheets[$sheet_name]->finalized)
			return;

		self::initializeSheet($sheet_name);
		$sheet = &$this->sheets[$sheet_name];
		$sheet->merge_cells[] = $startCell . ":" . $endCell;
	}

	public function initializeColumnTypesExtend($header_types){
		$column_types = array();
		foreach($header_types as $v){
			$number_format = self::numberFormatStandardizedExtend($v);
			$number_format_type = self::determineNumberFormatTypeExtend($number_format);
			$cell_style_idx = $this->addCellStyleExtend($number_format, $style_string=null);
			$column_types[] = array('number_format'=>$number_format, 'number_format_type'=>$number_format_type, 'default_cell_style'=>$cell_style_idx);
		}
		return $column_types;
	}

	public static function numberFormatStandardizedExtend($num_format){
		if ($num_format=='money') { $num_format='dollar'; }
		if ($num_format=='number') { $num_format='integer'; }

		if      ($num_format=='string')   $num_format='@';
		else if ($num_format=='integer')  $num_format='0';
		else if ($num_format=='date')     $num_format='YYYY-MM-DD';
		else if ($num_format=='date_ind') $num_format='DD/MM/YYYY';
		else if ($num_format=='datetime_ind_second') $num_format='DD/MM/YYYY HH:MM:SS';
		else if ($num_format=='datetime_ind_nosecond') $num_format='DD/MM/YYYY  HH:MM';
		else if ($num_format=='datetime') $num_format='YYYY-MM-DD HH:MM:SS';
        else if ($num_format=='time')     $num_format='HH:MM:SS';
		else if ($num_format=='price_no_dec') $num_format='#,##0';
		else if ($num_format=='price')    $num_format='#,##0.00';
		else if ($num_format=='persen')   $num_format='0.00%';
		else if ($num_format=='dollar')   $num_format='[$$-1009]#,##0.00;[RED]-[$$-1009]#,##0.00';
		else if ($num_format=='euro')     $num_format='#,##0.00 [$€-407];[RED]-#,##0.00 [$€-407]';
		$ignore_until='';
		$escaped = '';
		for($i=0,$ix=strlen($num_format); $i<$ix; $i++){
			$c = $num_format[$i];
			if ($ignore_until=='' && $c=='[')
				$ignore_until=']';
			else if ($ignore_until=='' && $c=='"')
				$ignore_until='"';
			else if ($ignore_until==$c)
				$ignore_until='';
			if ($ignore_until=='' && ($c==' ' || $c=='-'  || $c=='('  || $c==')') && ($i==0 || $num_format[$i-1]!='_'))
				$escaped.= "\\".$c;
			else
				$escaped.= $c;
		}
		return $escaped;
	}

	public static function determineNumberFormatTypeExtend($num_format){
		$num_format = preg_replace("/\[(Black|Blue|Cyan|Green|Magenta|Red|White|Yellow)\]/i", "", $num_format);
		if ($num_format=='GENERAL') return 'n_auto';
		if ($num_format=='@') return 'n_string';
		if ($num_format=='0') return 'n_numeric';
		if (preg_match('/[H]{1,2}:[M]{1,2}(?![^"]*+")/i', $num_format)) return 'n_datetime';
		if (preg_match('/[M]{1,2}:[S]{1,2}(?![^"]*+")/i', $num_format)) return 'n_datetime';
		if (preg_match('/[Y]{2,4}(?![^"]*+")/i', $num_format)) return 'n_date';
		if (preg_match('/[D]{1,2}(?![^"]*+")/i', $num_format)) return 'n_date';
		if (preg_match('/[M]{1,2}(?![^"]*+")/i', $num_format)) return 'n_date';
		if (preg_match('/$(?![^"]*+")/', $num_format)) return 'n_numeric';
		if (preg_match('/%(?![^"]*+")/', $num_format)) return 'n_numeric';
		if (preg_match('/0(?![^"]*+")/', $num_format)) return 'n_numeric';
		return 'n_auto';
	}

	public function addCellStyleExtend($number_format, $cell_style_string){
		$number_format_idx 	= self::add_to_list_get_index($this->number_formats, $number_format);
		$lookup_string 		= $number_format_idx.";".$cell_style_string;
		$cell_style_idx 	= self::add_to_list_get_index($this->cell_styles, $lookup_string);
		return $cell_style_idx;
	}

}

