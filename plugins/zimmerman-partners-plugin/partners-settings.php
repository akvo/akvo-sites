<?php


add_action('admin_menu' , 'zz_partners_enable_pages');

function zz_partners_enable_pages() {
        
	add_submenu_page('edit.php?post_type=partner', 'Upload Partner CSV', 'Upload Partner CSV', 'edit_posts', basename(__FILE__), 'partner_settings');
}


function partner_settings(){
?>

  <div id="partners-export-block">
    
    <div style="display: block;">
      <b class="partners-export-header"> Upload partner CSV file </b>
      <a href='admin.php?import=csv'>Click here to upload the partner CSV file</a>
<!--      <input type="file" name="Partner XLS">
      <b>Example upload format: <a target="_blank" href="<?php echo dirname( __FILE__ ) . '/example_partners.xlsx';?>">download</a></b>-->
    </div>

   


    
  </div>

  <style>

  #partners-export-block{
    clear:both; 
    width: 100%; 
    display: block; 
    margin-top: 40px;
    margin-left:10px;
  }

    .partners-export-header{
        display: block;
        font-size: 16px;
        margin-bottom: 1em;
        margin-top: 2em;
    }

    #partners-export-button{
      border-radius: 4px; 
      border: 0; 
      color: #fff; 
      background-color: #66a64f;
      padding: 0.5em 1em; 
      font-size: 16px;
    }
  </style>

  <?php
}









?>