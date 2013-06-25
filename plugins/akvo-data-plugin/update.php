<html><?php
 
require_once(dirname( __FILE__ ) . '../../../../wp-blog-header.php');
 
/* We are assuming that if a "search" value is being posted all the data is
   being posted. */
$action = $_GET['action'];
$formdata=$_POST;
if(isset($action)){
    $akvodata = get_option('akvodata_opts');
    switch($action) {
        case "add":
            $id = $formdata['id'];
            unset($formdata['id']);
            if($id!=''){
                $akvodata[$id]=$formdata;
            }else{
                $akvodata[]=$formdata;
            }
        
        break;
        
        case "delete":
            $id = $formdata['id'];
            unset($akvodata[$id]);
        break;
    }
    uasort($akvodata,'cmp');
    var_dump($akvodata);
    update_option('akvodata_opts', $akvodata);
    akvodata_setchartdata();
    echo '1';

}


function cmp($a, $b) {
    if ($a['year'] == $b['year'] && $a['month'] == $b['month']) {
        return 0;
    }
    elseif ($a['year'] == $b['year'] && $a['month'] < $b['month']) {
        return -1;
    }
    elseif ($a['year'] == $b['year'] && $a['month'] > $b['month']) {
        return 1;
    }
    elseif ($a['year'] < $b['year']) {
        return -1;
    }else{
        return 1;
    }
    //return ($a < $b) ? -1 : 1;
}
?>
</html>