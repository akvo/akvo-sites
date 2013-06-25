<?php global $shortname; ?>

<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/js/superfish.js"></script>
<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/js/jquery.infieldlabel.min.js"></script>

<script type="text/javascript">
	jQuery(function(){
		jQuery('ul.superfish').superfish();
<?php if (get_option($shortname . '_disable_toptier') == 'on') echo('jQuery("ul.nav > li > ul").prev("a").attr("href","#");'); ?>
	});
</script>
<script type="text/javascript">
	jQuery(document).ready(function() {
		jQuery(".cLiBlogCat").click(function(){
            var tab = jQuery(this).attr('rel');
            jQuery('.cLiBlogCat a').removeClass('active');
            jQuery('.cLiBlogCat[rel="'+jQuery(this).attr('rel')+'"] a').addClass('active');
            if(tab=='all'){
                getEntries(this,'click');
				jQuery("#iDivCurrentTabName").html("&nbsp;&raquo;&nbsp;All Blog Posts");

            }else if(tab=='blogposts'){
                getEntries(this,'click');
				jQuery("#iDivCurrentTabName").html("&nbsp;&raquo;&nbsp;All Blog Posts");

            }else{
                getEntries(this,'click');
				jQuery("#iDivCurrentTabName").html("&nbsp;&raquo;&nbsp;" + jQuery(this).attr('rel'));
            }
//
		});

        if(jQuery(".cLiBlogCat").length>0){
                getEntries(jQuery(".cLiBlogCat:first"));
        }
	});
    function getEntries(el,event){
        var type = jQuery(el).attr('rel');
        var paging=false;
        if(jQuery(el).prop("nodeName")=='A') paging=true;
        var catid = jQuery(el).attr('catid');
        var showupdates = jQuery('.cLiBlogCat[rel="project updates"]').length;
        var querystring = jQuery('#iInputQueryString').val();
        if(jQuery('.cLiBlogCat[rel="blogposts"]').length || (jQuery('.cLiBlogCat[rel="all"]').length && event=='click')){
            var categoriesParam = '&categories=all';
        }else{
            var categoriesParam = '';
            jQuery('.cLiBlogCat').each(function(i){
                if(jQuery(this).attr('catid')){
                    categoriesParam += '&categories['+i+']='+jQuery(this).attr('catid');
                }
            });
        }
        
        if(type=='all'){
            querystring = querystring+'&page=1&onlyupdates=0&showUpdates='+showupdates+categoriesParam;
        }else if(type=='project updates'){
            querystring = querystring+'&page=1&showUpdates=1&categories=1&onlyupdates=1';
        }else if(type=='blogposts'){
            querystring = querystring+'&page=1&showUpdates=0&onlyupdates=0'+categoriesParam;
        }else if(paging){
            querystring = type;
        }else if(catid){
            querystring = querystring+'&page=1&onlyupdates=0&showUpdates=0&categories=&categories[0]='+catid;
        }
        var sLoader = '<div class="loading" align="center"><img src="/wp-content/themes/Quadro/images/loading.gif" alt="loading"></div>'
	
        jQuery('#iDivBlogPosts').html(sLoader);
        jQuery.ajax({
            url:'/wp-content/themes/Quadro/getPostsXHR.php?'+querystring,
            dataType: 'html',
            success:function(data){
               jQuery('#iDivBlogPosts').replaceWith(data);  
            }
        });
        }

</script>

<script type="text/javascript">
	jQuery(document).ready(function(){
		jQuery("#iLblSearchInfield").inFieldLabels();
	});
</script>



