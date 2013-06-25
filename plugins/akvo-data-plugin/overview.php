<?php 
$options = get_option('akvodata_opts'); 

?>
 
<h1>Akvo data dashboard</h1>
<a href="?page=akvodata-add">Add new</a>
<input name="ajaxurl" id="iInputUrl" type="hidden" value="<?php echo plugins_url('update.php', __FILE__) ?>" />
<table class="wp-list-table widefat fixed akvodata-table" cellspacing="0">
    <thead>
    <tr>
        <tr>
            <th id="columnname" class="manage-column column-columnname" scope="col">Year</th>
            <th id="columnname" class="manage-column column-columnname" scope="col">Month</th> 
            <th id="columnname" class="manage-column column-columnname" scope="col">Yield with fertilizer</th> 
            <th id="columnname" class="manage-column column-columnname" scope="col">Yield without fertilizer</th> 
            <th id="columnname" class="manage-column column-columnname" scope="col">Income with fertilizer</th> 
            <th id="columnname" class="manage-column column-columnname" scope="col">Income without fertilizer</th> 
        </tr>
    </tr>
    </thead>

    <tfoot>
    <tr>
        <tr>
            <th class="manage-column column-columnname" scope="col"></th>
            <th class="manage-column column-columnname" scope="col"></th>
            <th class="manage-column column-columnname" scope="col"></th>
            <th class="manage-column column-columnname" scope="col"></th>
            <th class="manage-column column-columnname" scope="col"></th>
            <th class="manage-column column-columnname" scope="col"></th>
        </tr>
    </tr>
    </tfoot>

    <tbody>
        <?php foreach($options AS $id=>$data){
            $trclass = ($i%2) ? 'alternate' : '';
        ?>
        <tr class="<?php echo $trclass; ?>" rel="<?php echo $id;?>">
            <td class="column-columnname">
                <?php echo $data['year'];?>
                <div class="row-actions">
                    <span><a href="?page=akvodata-add&id=<?php echo $id;?>">Edit</a> |</span>
                    <span><a href="#" class="cAdelAkvoData" rel="<?php echo $id;?>">Delete</a></span>
                </div>
            </td>
            <td class="column-columnname"><?php echo $data['month'];?></td>
            <td class="column-columnname"><?php echo $data['yield-with-fertilizer'];?></td>
            <td class="column-columnname"><?php echo $data['yield-without-fertilizer'];?></td>
            <td class="column-columnname"><?php echo $data['income-with-fertilizer'];?></td>
            <td class="column-columnname"><?php echo $data['income-without-fertilizer'];?></td>
        </tr>
        <?php
        }
        ?>
        
        
    </tbody>
</table>
