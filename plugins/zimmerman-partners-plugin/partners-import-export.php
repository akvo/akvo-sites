<?php


add_action('admin_menu' , 'zz_partners_enable_pages');

function zz_partners_enable_pages() {
	add_submenu_page('edit.php?post_type=partners', 'Import/export partners', 'Import/export partners', 'edit_posts', basename(__FILE__), 'import_export_partners');
}


function import_export_partners(){

 
  ?>

  <div id="partners-export-block">
    
    <div style="display: block;">
      <b class="partners-export-header"> Upload partner XLS </b>
      <input type="file" name="Partner XLS">
      <b>Example upload format: <a target="_blank" href="<?php echo dirname( __FILE__ ) . '/example_partners.xlsx';?>">download</a></b>
    </div>

    <div style="display: block;">
      <b class="partners-export-header"> Export partner XLS </b>
      <button id="partners-export-button">Export</button>
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