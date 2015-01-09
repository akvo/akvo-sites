<?php

class RS_CSV_Helper {
	
	const DELIMITER = ";";
        public $top_header_array;
        public $top_second_header_array;
        
	
	// File utility functions
	public function fopen($filename, $mode='r') {
		return fopen($filename, $mode);
	}

	public function fgetcsv($handle, $length = 0) {
		return fgetcsv($handle, $length, self::DELIMITER);
	}

	public function fclose($fp) {
		return fclose($fp);
 	}
	
	public function parse_columns(&$obj, $array) {
		if (!is_array($array) || count($array) == 0)
			return false;
		
		$keys = array_keys($array);
		$values = array_values($array);
		
		$obj->column_indexes = array_combine($values, $keys);
		$obj->column_keys = array_combine($keys, $values);
                $this->top_second_header_array = $obj->column_keys;
	}
        
        public function parse_header_columns(&$obj, $array) {
		if (!is_array($array) || count($array) == 0)
			return false;
		
		$keys = array_keys($array);
		$values = array_values($array);
//		print_r($keys);
//                print_r($values);
                $first = true;
                $prev_find_item = '';
                foreach ($values as $key => $value) {
                    if ($first != true){
                        
                        if (strlen($values[$key])>0){
                            
                            $prev_find_item = $values[$key];
                            
                        }else{
                            $values[$key] = $prev_find_item;
                            
                        }
                            
                    }else{
                        $first = false;
                    }
                    
                    
                }
                
		$obj->column_header_indexes = array_combine($values, $keys);
		$this->top_header_array = array_combine($keys, $values);
                
//                print_r($obj->column_header_keys);
	}
        
        public function slugify($text)
{ 
  // replace non letter or digits by -
  $text = preg_replace('~[^\\pL\d]+~u', '-', $text);

  // trim
  $text = trim($text, '-');

  // transliterate
  $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

  // lowercase
  $text = strtolower($text);

  // remove unwanted characters
  $text = preg_replace('~[^-\w]+~', '', $text);

  if (empty($text))
  {
    return 'n-a';
  }

  return $text;
}
	
	public function get_data($obj, &$array, $key) {
		if (!isset($obj->column_indexes) || !is_array($array) || count($array) == 0)
			return false;
		
		if (isset($obj->column_indexes[$key])) {
			$index = $obj->column_indexes[$key];
			if (isset($array[$index]) && !empty($array[$index])) {
				$value = $array[$index];
				unset($array[$index]);
				return $value;
			} elseif (isset($array[$index])) {
				unset($array[$index]);
			}
		}
		
		return false;
	}
	
}