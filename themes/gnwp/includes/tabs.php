<?php
                               
global $tabOptions;
        //var_dump($tabOptions);
        //query all categories
        $aExclude=get_category_ids(array('vacancies','thematic-partner','alliance-members','none'));
//var_dump($aExclude);
        if($tabOptions['categories']=='all'){
            $aCategories = get_categories(array('exclude'=>join(',',$aExclude)));
            //var_dump($aCategories);
        }else{
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
//var_dump($aCategoryIDs);
         ksort($aCategoryNames);
//ksort($aCategoryIDs);
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
            if((!isset($tabOptions['onlyupdates']) || $tabOptions['onlyupdates']==0) && (count($aCategoryIDs)>0 || isset($tabOptions['country']))){
                $args=array();
                if(count($aCategoryIDs)>0){
                    $args = array(
                        'category__in'=>$aCategoryIDs,
                        'posts_per_page' => -1,
                        'nopaging'=>true
                    );
                }
                if(isset($tabOptions['country'])){
                    $aTagIDs = get_tag_ids(array($tabOptions['country']));
                    $args['tag__in']=join(',',$aTagIDs);
                }
    //var_dump($args);
                //get posts for category
                $aPosts = $wp_query->query($args);
            }
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
                       // var_dump($wp_query->query);
                    }


            }
            $aAllPosts = array_merge($aPosts,$aUpdates);
            usort($aAllPosts,'order_combined_posts');
        //array_unique($aAllPosts);
            $aAllPostIDs = wp_list_pluck($aAllPosts, 'ID');
            if(count($aAllPostIDs)>0){
                $aPostTypes = array('post','project_update') ;
                $args = array(
                    'post__in'=>$aAllPostIDs,
                    'post_type'=>$aPostTypes,
                    'posts_per_page' => (isset($tabOptions['numposts'])) ? $tabOptions['numposts'] : 9,
                    'posts_per_archive_page' => (isset($tabOptions['numposts'])) ? $tabOptions['numposts'] : 9,
                    'paged' => $page,
                    'orderby'=>'post__in'
                    );
                //var_dump($args);
                $aPosts = $wp_query->query($args);
                //var_dump($wp_query->query);
                //die();
            }
        //}
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
                        <ul class="cUlBlogPosts">

                            <?php
                            if ( have_posts() ) : while ( have_posts() ) : the_post();
                                ?>
                            <?php get_template_part('includes/entry'); ?>
                             
							<?php
							endwhile; endif;
                            
                            ?>
                            <br style="clear:both;" /> 
                            <div class="pagination">
                                <input type="hidden" name="iInputQueryString" id="iInputQueryString" value="<?php echo urldecode(http_build_query($tabOptions)); ?>" />
                                <?php
                                $prevpage = ($page-1);
                                $nextpage = ($page+1);
                                if($prevpage){
                                    $tabOptions['page']=$prevpage;
                                    $prevPaginationlink = urldecode(http_build_query($tabOptions));
                                    echo '<span><a href="#" onclick="getEntries(this);" rel="'.$prevPaginationlink.'">&laquo; Older Entries</a></span>&nbsp;&nbsp;&nbsp;';
                                }
                                if(get_next_posts_link()){
                                    $tabOptions['page']=$nextpage;
                                    $nextPaginationlink = urldecode(http_build_query($tabOptions));
                                    echo '<span><a href="#" onclick="getEntries(this);" rel="'.$nextPaginationlink.'">Next Entries &raquo;</a></span>';
                                }
                                
                                ?>
                            </div>
                    </ul><br style="clear:both;" />
                    
                    
        </div><br style="clear:both;" />
    </div>
<?php 
} ?> 