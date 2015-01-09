<?php

global $tabOptions;
        //var_dump($tabOptions);
        //query all categories
        $aExclude=get_category_ids(array('vacancies','thematic-partner','alliance-members','none'));
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
            //var_dump($tabOptions['categories']);
            if(count($tabOptions['categories'])>0){
                $aCategories=get_categories(array('exclude'=>join(',',$aExclude),'include'=>join(',',$tabOptions['categories'])));
            }
            //foreach($tabOptions['categories'] AS $iCatID)$aCategories[]=get_category($iCatID);
        }
        $aPosts = array('all'=>'');

        $aCategoryIDs = array();
        $aCategoryNames = array();

        ?>

        <?php
        $keys = array('economic-development','education','health');
         foreach($aCategories AS $aCategory){

            $aCategoryIDs[]=$aCategory->cat_ID;
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
            //die();
        }

        if($tabOptions['showTabs'] && count($aPosts)>0){
        ?>
           <ul class="cUlBlogCats">
			   <div id="iDivTabType">Filter by:</div>
				<li id="iLiBlogPosts" class="cLiBlogCat" rel="all">
					<a>All</a>
				</li>
            <?php
            //if show updates is true, add project updates tab

            if($tabOptions['showUpdates']){

            ?>
				<li id="iLiBlogPosts" class="cLiBlogCat" rel="project updates">
					<a>Updates</a>
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
                        <li id="iLiBlogPosts" class="cLiBlogCat" rel="<?php echo $sCategory; ?>" catid ="<?php echo $aCategoryIDs[$iK]; ?>">
                            <a><?php echo $sCategory; ?></a>
                        </li>
                <?php
                }
            }?><br style="clear:both;" />
            </ul>
<?php }?>
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
                                    echo '<a href="#" onclick="getEntries(this);" rel="'.$prevPaginationlink.'">&laquo; Older Entries</a>';
                                }
                                if(get_next_posts_link()){
                                    $tabOptions['page']=$nextpage;
                                    $nextPaginationlink = urldecode(http_build_query($tabOptions));
                                    echo '<a href="#" onclick="getEntries(this);" rel="'.$nextPaginationlink.'">Next Entries &raquo;</a>';
                                }

                                ?>
                            </div>
                    </ul><br style="clear:both;" />


        </div><br style="clear:both;" />
    </div>

