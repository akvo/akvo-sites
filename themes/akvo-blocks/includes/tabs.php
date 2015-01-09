<?php
       $sPageLink = get_the_permalink();                        
global $tabOptions;
global $sitepress;
$current_lang = $sitepress->get_current_language();
$sitepress->switch_lang('en', true);
     
        //query all categories
        $aExclude=get_category_ids(array('vacancies','thematic-partner','alliance-members','none','Blog'));
        if($tabOptions['categories']=='all'){
            $aCategories = get_categories(array('exclude'=>join(',',$aExclude)));
            //var_dump($aCategories);
        }elseif(isset($tabOptions['categories'])){
            $aCategories=array();
            if(!is_array($tabOptions['categories']))$tabOptions['categories']=array();
            $iNone = array_search(1,$tabOptions['categories']);
            if($iNone!==false)unset($tabOptions['categories'][$iNone]);
            if(isset($tabOptions['country'])){
                $aInclude = get_category_ids(array('blog'));
                $tabOptions['categories']=array_merge($tabOptions['categories'],$aInclude);
            }
            $tabOptions['categories']=array_unique($tabOptions['categories']);
            
            if(count($tabOptions['categories'])>0){
                $aCategories=get_categories(array('exclude'=>join(',',$aExclude),'include'=>join(',',$tabOptions['categories'])));
            }
            //foreach($tabOptions['categories'] AS $iCatID)$aCategories[]=get_category($iCatID);
        }
	if(count($aCategories)>0 || $tabOptions['showUpdates']){
        $aPosts = array('all'=>'');

        $aCategoryIDs = array();
        $aCategoryNames = array();
        
        ?>
            
        <?php
        $keys = array('financial','institutional','environmental','technical','social');
         foreach($aCategories AS $aCategory){
             
            $aCategoryIDs[$aCategory->name]=$aCategory->cat_ID;
            $aCategoryNames[array_search($aCategory->slug,$keys)]=$aCategory->name;
            
           
        }
         ksort($aCategoryNames);
        $aPosts=array();
        $aUpdates=array();
        global $wp_query;
        if(isset($tabOptions['page'])){
            $page = ($tabOptions['page']) ? $tabOptions['page'] : 1;
        }else{
            $page = (get_query_var('paged')) ? get_query_var('paged') : 1;
        }
        
        $temp = $wp_query;
        //if($tabOptions['doquery']==true){
            $wp_query = null;
            $wp_query = new WP_Query();
            if(
                     (!isset($tabOptions['onlyupdates']) || $tabOptions['onlyupdates']==0) && (count($aCategoryIDs)>0 || isset($tabOptions['country']))){
                $args=array();
                if(isset($tabOptions['categories'])){
                    $args = array(
                        'category__in'=>$aCategoryIDs,
                        'posts_per_page' => -1,
                        'nopaging'=>true
                    );
                    
                }
                if(isset($tabOptions['country'])){
                    
                    $aTagIDs = get_tag_ids(array($tabOptions['country']));
                    
                    if(count($aTagIDs)>0)$args['tag__in']=join(',',$aTagIDs);
                }
    
                //get posts for category
                if(count($args)>0)$aPosts = $wp_query->query($args);
//                var_dump($aTagIDs);
            }
//            akvo_debug_dump($tabOptions['showUpdates']);
            if($tabOptions['showUpdates']){
 
                    $args= array(
                        'post_type'=>'project_update',
                        'posts_per_page' => -1
                        );
                   
                    if(isset($tabOptions['country'])){
                        $oAPC = new AkvoPartnerCommunication();
                        $aProjectUpdates = $oAPC->readProjectUpdatesFromDbByCountry($tabOptions['country']);
                        // var_dump($aProjectUpdates);
                        $tabOptions['updateIDs']=$aProjectUpdates;
                        $args['post__in']=$tabOptions['updateIDs'];
                    }
                    
                    if(
                            !isset($tabOptions['updateIDs']) || 
                            (isset($tabOptions['updateIDs']) && count($tabOptions['updateIDs'])>0)
                        ){
                        //$args['paged']=$page;
                        $aUpdates = $wp_query->query($args);
                        
                    }


            }
            $aAllPosts = array_merge($aPosts,$aUpdates);

            usort($aAllPosts,'order_combined_posts');
            $itemsPP = 9;
            $totalPages = ceil(count($aAllPosts)/$itemsPP);
            $offset = ($page - 1) * $itemsPP;
            $aAllPosts = array_slice($aAllPosts, $offset, $itemsPP);
            $prevpage = ($page>1) ? true : false;
            $nextpage = ($totalPages>1 && $page<$totalPages) ? true : false;
        if($tabOptions['showTabs'] && 1===0){ 
        ?>
           <ul class="cUlBlogCats"> 
               <li id="iLiBlogPosts" class="cLiFilterBy" rel="all">
					Filter by:
				</li>
				<li id="iLiBlogPosts" class="cLiBlogCat" rel="all">
					<a>All</a>
				</li>
            <?php
            //if show updates is true, add project updates tab

            if($tabOptions['showUpdates']){

            ?>
				<li id="iLiBlogPosts" class="cLiBlogCat" rel="project updates">
					<a>Project updates</a>
				</li>
            <?php
            }
            if(!isset($tabOptions['showcategories']) && $tabOptions['categories']=='all'){?>
                <li id="iLiBlogPosts" class="cLiBlogCat" rel="blogposts">
                            <a>Blog posts</a>
                        </li>
            <?php
            
            }else{
            ///add category tab per category
                foreach($aCategoryNames AS $iK=>$sCategory){
                    ?>
                        <li id="iLiBlogPosts" class="cLiBlogCat" rel="<?php echo $sCategory; ?>" catid ="<?php echo $aCategoryIDs[$sCategory]; ?>">
                            <a><?php echo $sCategory; ?></a>
                        </li>
                <?php
                }
            }?><br style="clear:both;" />
            </ul>
<?php }
//echo $tabOptions['showTabs'];
//if($tabOptions['showTabs']==0){
?>
            <div id="iDivBlogPosts">
                    <div class="cDivBlogPosts">

                            <?php
                            if(count($aAllPosts)>0) : foreach($aAllPosts AS $post):
//                            if ( have_posts() ) : while ( have_posts() ) : the_post();
                            ?>
                            <?php get_template_part('includes/entry'); ?>
                             
							<?php
							endforeach; endif;
                            
                            ?>
                            <br style="clear:both;" /> 
                            <div class="pagination">
                                <input type="hidden" name="iInputQueryString" id="iInputQueryString" value="<?php echo urldecode(http_build_query($tabOptions)); ?>" />
                                <?php
                                
                                if($prevpage){
                                    
                                    echo '<span><a href="'.$sPageLink.'page/'.($page-1).'"  >&laquo; Newer Entries</a></span>&nbsp;&nbsp;&nbsp;';
                                }
                                if($nextpage){
                                    echo '<span><a href="'.$sPageLink.'page/'.($page+1).'">Older Entries &raquo;</a></span>';
                                }
                                
                                ?>
                            </div>
                    <br style="clear:both;" />
                    
                    
        </div><br style="clear:both;" />
    </div>
<?php 
} 
// switch back to original language before wp_footer
$sitepress->switch_lang($current_lang, true);   
?> 