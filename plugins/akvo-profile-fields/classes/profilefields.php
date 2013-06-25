<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class AkvoProfileFields{
    
    public static function add_extra_meta_fields($user){
        $organisationOptions = array("Akvo","AMREF","Both Ends","ETC -RUAF-Foundation","ICCO","IRC","Practica","Programme management","RAIN","Simavi" ,"WASTE","Wateraid","Wetlands International");
        $groupOptions = array("Steering","Programme","Finance","PME","PG+","L-and-A","Country coordinator","Country group","Communication Group","Sustainability Working Group");
        $countryTeams = array("Bangladesh", "Benin", "Ethiopia", "Ghana", "Kenya", "Mali", "Nepal", "Uganda");
        
        $aUserOrganisations = get_the_author_meta( 'organisation', $user->ID );
        $aUserGroups = get_the_author_meta( 'group', $user->ID );
        $aUserCountryTeams = get_the_author_meta( 'country_team', $user->ID );
        //var_dump($aUserCountryTeams);
      ?>
	<h3><?php _e('Extra Profile Information'); ?></h3>
	<table class="form-table">
		<tr>
			<th>
				<label for="phone1"><?php _e('Phone'); ?>
			</label></th>
			<td>
				<input type="text" name="phone1" id="phone1" value="<?php echo esc_attr( get_the_author_meta( 'phone1', $user->ID ) ); ?>" class="regular-text" /><br />
				<span class="description"><?php _e('Please enter your phone number.'); ?></span>
			</td>
        </tr><tr>
			<th>
				<label for="function"><?php _e('Function'); ?>
			</label></th>
			<td>
				<input type="text" name="function" id="function" value="<?php echo esc_attr( get_the_author_meta( 'function', $user->ID ) ); ?>" class="regular-text" /><br />
				<span class="description"><?php _e('Please enter your function.'); ?></span>
			</td>
		 </tr><tr>
			<th>
				<label for="group"><?php _e('Group'); ?>
			</label></th>
			<td>
                <select name="group[]" id="group" multiple>
                    <?php echo AkvoProfileFields::getOptionHTML($groupOptions, $aUserGroups); ?>
                </select><br />
				<span class="description"><?php _e('Groups this user is part of.'); ?></span>
			</td>
			 </tr><tr>
			<th>
				<label for="organisation"><?php _e('Organisation'); ?>
			</label></th>
			<td>
				<select name="organisation[]" id="organisation" multiple>
                    <?php echo AkvoProfileFields::getOptionHTML($organisationOptions, $aUserOrganisations); ?>
                </select><br />
				<span class="description"><?php _e('Please enter your organisation.'); ?></span>
			</td>
			 </tr><tr>
			<th>
				<label for="country_team"><?php _e('Country team'); ?>
			</label></th>
			<td>
				<select name="country_team[]" id="country_team" multiple>
                    <?php echo AkvoProfileFields::getOptionHTML($countryTeams, $aUserCountryTeams); ?>
                </select><br />
				<span class="description"><?php _e('Please enter your country_team.'); ?></span>
			</td>
		</tr>
	</table>
<?php  
    }
    public static function save_extra_meta_fields( $user_id ) {
        if ( !current_user_can( 'edit_user', $user_id ) )
            return FALSE;
        
        update_user_meta( $user_id, 'phone1', $_POST['phone1'] );
        update_user_meta( $user_id, 'function', $_POST['function'] );
        update_user_meta( $user_id, 'group', $_POST['group']) ;
        update_user_meta( $user_id, 'organisation', $_POST['organisation'] );
        update_user_meta( $user_id, 'country_team', $_POST['country_team'] );
//        var_dump($_POST['organisation']);
//        die();
    }
    
    public static function getOptionHTML($aOptions, $aUserMeta){
        $sHTML = '';
       foreach($aOptions AS $option){
           //var_dump(array_search($option, $aUserMeta));
            $sSelected = (is_array($aUserMeta) && array_search($option, $aUserMeta)!==false) ? 'selected' : '';
            $sHTML .= '<option value="'.$option.'" '.$sSelected.'>'.$option.'</option>';
        }
        return $sHTML;
    }
}
?>
