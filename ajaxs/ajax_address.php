<?php

include(dirname(__FILE__) . '/../config/config.inc.php');
include(dirname(__FILE__) . '/../init.php');

class ajax_states extends FrontController {

    public function get_countryes() {

        $query = "select country.id_country, country.`name` FROM
ps_country_active active INNER JOIN ps_country_lang country
ON( active.id_country = country.id_country);";

        if ($results = Db::getInstance()->ExecuteS($query)) {
            
            $str_countryes='';
            if (count($results) > 0) {
                $str_countryes.='<option value="">Seleccionar</option>';
                foreach ($results as $value) {
                    $str_countryes .= '<option value="'. $value['id_country'] .'">'. $value['name'] . '</option>';
                }
                return json_encode(array('results' =>$str_countryes));
            }
        }

        return '!';
    }

    public function get_states($id_country) {

        $query = "select  state.id_state,state.`name` FROM
ps_country country
INNER JOIN ps_state state ON (country.id_country= state.id_country)
WHERE country.id_country=" . (int) $id_country . ";";

        if ($results = Db::getInstance()->ExecuteS($query)) {
            
            $str_states='';
            if (count($results) > 0) {
                $str_states.='<option value="">Seleccionar</option>';
                foreach ($results as $value) {
                    $str_states .= '<option value="'. $value['id_state'] .'">'. $value['name'] . '</option>';
                }
                return json_encode(array('results' =>$str_states));
            }
        }
         return '!';
    }

    public function get_cityes($id_state) {
        $query = "select city.id_city, city.city_name FROM
ps_state state INNER JOIN ps_cities_col city ON(state.id_state=city.id_state)
WHERE state.id_state=" . (int) $id_state . ";";

        if ($results = Db::getInstance()->ExecuteS($query)) {
             $str_cityes='';
            if (count($results) > 0) {
                $str_cityes.='<option value="">Seleccionar</option>';
                foreach ($results as $value) {
                    $str_cityes .= '<option value="'. $value['id_city'] .'">'. $value['city_name'] . '</option>';
                }
                return json_encode(array('results' =>$str_cityes));
            }
        }
        return '!';
    }

}


if(isset($_POST) && !empty($_POST) && isset($_POST['action']) && !empty($_POST['action']) && isset($_POST['value']) && !empty($_POST['value'])){
   
    $ajax=new ajax_states();
    
   switch ($_POST['action']) {
    case 'country':
  echo $ajax->get_countryes();
   break;
    case 'sate':
      echo  $ajax->get_states($_POST['value']);
        break;
    case 'city':
      echo $ajax->get_cityes($_POST['value']); 
      break;
  default :
      echo '!';
      break;  

   }
    
}else{
     echo '!';
}
