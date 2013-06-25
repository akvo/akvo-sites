<?php $options = get_option('akvodata_opts');
if(isset($_GET['id'])){
    $options = $options[$_GET['id']];
}
?>
 
<h1>Add data</h1>
 
<form id="akvodata-settings" action="<?php echo plugins_url('update.php', __FILE__) ?>">
    <input type="hidden" name="id" value="<?php echo $_GET['id'];?>" />
    <label>Month:</label>  
    <select name="month">
        <?php for($i=1;$i<=12;$i++){
        $selected = ($i==$options['month']) ? 'selected' : '' ;       
?>
        <option value="<?php echo $i;?>" <?php echo $selected; ?>><?php echo $i;?></option>
        <?php } ?>
    </select>
    <label>Year:</label> 
    <select name="year">
        <?php for($i=2010;$i<=(date('Y')+2);$i++){
        $selected = ($i==date('Y') || $i==$options['year']) ? 'selected' : '' ;       
?>
        <option value="<?php echo $i;?>" <?php echo $selected; ?>><?php echo $i;?></option>
        <?php } ?>
    </select><br />
    <h2>Yield</h2>
    <label>With fertilizer:</label> <input name="yield-with-fertilizer" value="<?php echo $options['yield-with-fertilizer'] ?>" type="text" /><br />
    <label>Without fertilizer:</label> <input name="yield-without-fertilizer" value="<?php echo $options['yield-without-fertilizer'] ?>" type="text" /><br />
    <h2>Net. income</h2>
    <label>With fertilizer:</label> <input name="income-with-fertilizer" value="<?php echo $options['income-with-fertilizer'] ?>" type="text" /><br />
    <label>Without fertilizer:</label> <input name="income-without-fertilizer" value="<?php echo $options['income-without-fertilizer'] ?>" type="text" /><br />
    <input type="submit" value="Update" /><span class="update-status"></span>
 
</form>

