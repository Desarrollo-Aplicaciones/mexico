<?php
/**
* XMLToArray : Converting XML into Array
*
* @author  :  MA Razzaque Rupom <rupom_315@yahoo.com>, <rupom.bd@gmail.com>
*             Moderator, phpResource (http://groups.yahoo.com/group/phpresource/)
*             URL: http://www.rupom.info  
* @version :  1.0
* @date       06/21/2006
* Purpose  :  Creating Hierarchical Array from XML Data
*/

class XmlToArray
{
	
	private $xml=''; //To use in PHP4, use "var" 
	
	/**
	* Default Constructor
	* @param $xml = xml data
	* @return none
	*/
	
	function XmlToArray($xml)
	{
	   $this->xml = $xml;	
	}
	
	/** 
	* _struct_to_array($values, &$i) 
	* 
	* This adds the contents of the return xml into the array for easier processing. 
	* Recursive, Static
	* 
	* @access    private
	* @param    array  $values this is the xml data in an array 
	* @param    int    $i  this is the current location in the array 
	* @return    Array
	*/ 
	
	function _struct_to_array($values, &$i)
	{
		$child = array(); 
		
		if (isset($values[$i]['value'])) 
		{
		   array_push($child, $values[$i]['value']); 
	  }
	  
		while ($i++ < count($values)) 
		{ 
			 switch ($values[$i]['type']) 
			 { 
			 	  case 'cdata': 
               	array_push($child, $values[$i]['value']); 
			 	  break; 
			 	  
			 	  case 'complete': 
			 	  	  $name = $values[$i]['tag']; 
			 	  	  if(!empty($name))
			 	  	  {
			 	  	     $child[$name]= ($values[$i]['value'])?($values[$i]['value']):''; 
			 	  	     
			 	  	     if(isset($values[$i]['attributes'])) 
			 	  	     {					
			 	  	        $child[$name] = $values[$i]['attributes']; 
			 	  	     } 
			 	      }	
          break; 
			 	  
			 	  case 'open': 
			 	  	$name = $values[$i]['tag']; 
			 	  	$size = isset($child[$name]) ? sizeof($child[$name]) : 0;
			 	  	$child[$name][$size] = $this->_struct_to_array($values, $i); 
			 	  break;
			 	  
			 	  case 'close': 
               	return $child; 
			 	  break; 
			 }
		}
		return $child; 
	
	}//EO _struct_to_array
	
	/**
	* createArray($data) 
	* 
	* This is adds the contents of the return xml into the array for easier processing. 
	* 
	* @access    public 
	* @param    string    $data this is the string of the xml data 
	* @return    Array 
	*/ 
	function createArray()
	{ 
		$xml    = $this->xml;
		$values = array(); 
		$index  = array(); 
		$array  = array(); 
		$parser = xml_parser_create(); 
		xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
		xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
		xml_parse_into_struct($parser, $xml, $values, $index);
		xml_parser_free($parser);
		$i = 0; 
		$name = $values[$i]['tag']; 
		$array[$name] = isset($values[$i]['attributes']) ? $values[$i]['attributes'] : ''; 
		$array[$name] = $this->_struct_to_array($values, $i); 
		return $array; 
	}//createArray
	
	
}//EO Class XmlToArray

?>