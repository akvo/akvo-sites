<?php 
	/*
		Template Name: Shared docs Page
	*/
?>

<?php get_header(); ?>

<div id="container">
    <div id="iDivBreadcrumb">
		<?php the_breadcrumbs();?>
		</div>
	<div id="container2">
		<div id="left-div">
			<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
				<div class="post-wrapper no_sidebar">
					<h1 class="titles">
						<a href="<?php the_permalink() ?>" rel="bookmark" title="<?php printf(esc_attr__('Permanent Link to %s','Quadro'), the_title()) ?>">
							<?php the_title(); ?>
						</a>
					</h1>
					<div style="clear: both;"></div>
					
					<?php if (get_option('quadro_page_thumbnails') == 'on') { ?>
					
						<?php $thumb = ''; 	  

						$width = (int) get_option('quadro_thumbnail_width_pages');
						$height = (int) get_option('quadro_thumbnail_height_pages');
						$classtext = '';
						$titletext = get_the_title();
						
						$thumbnail = get_thumbnail($width,$height,$classtext,$titletext,$titletext);
						$thumb = $thumbnail["thumb"]; ?>
						
						<?php if($thumb <> '') { ?>
							<div style="float: left; margin: 10px 10px 10px 0px;">
								<?php print_thumbnail($thumb, $thumbnail["use_timthumb"], $titletext , $width, $height, $classtext); ?>
							</div>
						<?php }; ?>
							
					<?php }; ?>
					
					<?php the_content(); ?>
					<div style="clear: both;"></div>
				
					<?php wp_link_pages(array('before' => '<p><strong>'.esc_html__('Pages','Quadro').':</strong> ', 'after' => '</p>', 'next_or_number' => 'number')); ?>
					<?php edit_post_link(esc_html__('Edit this page','Quadro')); ?>
					
					<?php if (get_option('quadro_show_pagescomments') == 'on') { ?>
						<!--Begin Comments Template-->
						<div class="recentposts">
							<?php comments_template('', true); ?>
						</div>
						<!--End Comments Template-->
					<?php }; ?>
				</div> <!-- end .post-wrapper -->
<!--                <div class="post-wrapper no_sidebar">
                    <div id="shared_docs_container">
                        <h2>Library</h2>
                        <div class="filter">
                            <select id="contenttypes" rel="contenttype"></select>
                            <select id="themes" rel="theme"></select>
                            <select id="regions" rel="region"></select>
                            <select id="languages" rel="language"></select>
                            <input id="filesearch" type="text" name="filesearch" />
                            <button id="filterDocs" value="filter">filter</button>
                        </div>
                        <div id="api_results">
                            
                        </div>
                    </div>
                </div>-->
			<?php
                $aCategories = wp_get_post_categories($post->ID);
                
                ?>
			<?php endwhile; endif;
                if(count($aCategories)>0){
            ?>
                <div class="fullwidth_blogs" >
                    <?php 
                    $tabOptions['showTabs']=true;
                        $tabOptions['showUpdates']=false;
                        sort($aCategories,SORT_NUMERIC);
                        $tabOptions['categories']=$aCategories;
                        get_template_part('includes/tabs');
                        ?>
                </div>
                <?php } ?>
		</div> <!-- end #left-div -->
	</div> <!-- end #container2 -->
	
	</div> <!-- end #container -->
<?php get_footer(); ?>
<script>
  //<!--
    var orderBy = 'title';
    (function($, document) {
		$(document).ready(function () {
                var fileApi = {
                    doRequest : function(section,params,onresult){
                        var baseUrl = 'http://stage.plainspace.com/ifad_shareddocs/';
                        var baseUrl = 'http://api.rain4food.net/';
                        var requestURL = '';
                        if(section.indexOf('http')==0){
                            requestURL = section;
                        }else{
                            requestURL = baseUrl+section+'/';
                        }
                        $.get(requestURL, params, function(response){
                                if(onresult){
                                    onresult(response);
                                }
                          }, 'json');
                    },
                    getFiles : function(query){
                        
                        
                        this.doRequest('files',query,fileApi.createTable);
                        
                       
                       //this.doRequest('files');
                    },
                    createTable:function(response){
                        var sTable = '<table>';
                        sTable += '<tr>';
                        sTable += '<th><a href="#" rel="title">Filename</a></th>';
                        sTable += '<th><a href="#" rel="author">Author</a></th>';
                        sTable += '<th><a href="#" rel="contenttype">Type</a></th>';
                        sTable += '<th><a href="#" rel="theme">Theme</a></th>';
                        sTable += '<th><a href="#" rel="region">Region</a></th>';
                        sTable += '<th><a href="#" rel="language">Language</a></th>';
                        sTable += '<th><a href="#" rel="uploaded">Uploaded</a></th>';
                        sTable += '</tr>';
                    
                        if(response.count>0){
                            var results=response.results;
                            var colored = 'colored';
                            for(i=0;i<results.length;i++){
                                var file=results[i];
                                sTable += '<tr class="'+colored+'">';
                                sTable += '<td><a href="'+file.filepath+'" target="_blank">'+file.title+'</a></td>';
                                sTable += '<td>'+file.author+'</td>';
                                sTable += '<td>'+file.contenttype+'</td>';
                                sTable += '<td>'+file.theme+'</td>';
                                sTable += '<td>'+file.region+'</td>';
                                sTable += '<td>'+file.language+'</td>';
                                sTable += '<td>'+file.uploaded+'</td>';
                                sTable += '</tr>';
                                colored = (colored=='') ? 'colored' : '';
                            }
                            sTable += '<tr>';
                            sTable += '<td>';
                            if(response.previous){
                                sTable += '<a href="#" class="paging" rel="'+response.previous+'">< previous</a>'
                            }
                            sTable += '</td>';
                            sTable += '<td colspan="5"><center>'+response.count+' documents found</center></td>';
                            sTable += '<td>';
                            if(response.next){
                                sTable += '<a href="#" class="paging" rel="'+response.next+'">next ></a>'
                            }
                            sTable += '</td>';
                            sTable += '</tr>';

                        }else{
                            sTable += '<tr>';
                            sTable += '<td colspan="7">0 documents found</td>';
                            sTable += '</tr>';
                        }
                        sTable += '</table>';
                         $('#api_results').html(sTable);
                         fileApi.bindTableEvents();
                                 
                    },
                    getDropDownValues : function(){
                        $('.filter select').each(function(i){
                            var section = $(this).attr('id');
                            var type = $(this).attr('rel');
                            var sOptions = '<option value="0">select a '+type+'</option>';
                            fileApi.doRequest(section,{},function(response){
                                if(response.count>0){
                                    
                                    var results=response.results;
                                    
                                    for(i=0;i<results.length;i++){
                                        sOptions += '<option value="'+results[i].name+'">'+results[i].short_desc+'</option>';
                                    }
                                    
                                }
                                
                                $('#'+section).html(sOptions);
                            }); 
                            
                        });
                       
                    },
                    
                    getUsers : function(){
                        
                    },
                    doSearch: function(){
                        var params = {ordering:orderBy};
                        if($('#filesearch').val()!='')params.filepath = $('#filesearch').val();
                        $('.filter select').each(function(i){
                           if($(this).val()!=0){
                               params[$(this).attr('rel')]=$(this).val();
                           } 
                        });
                        fileApi.getFiles(params);
                    },
                    bindTableEvents:function(){
                        $('#api_results th a').click(function(e){
                            e.preventDefault();
                           var order = $(this).attr('rel');
                           if(orderBy==order){
                               orderBy = '-'+order;
                           }else{
                               orderBy = order;
                           }
                            fileApi.doSearch();
                        });
                        $('#api_results a.paging').click(function(e){
                            e.preventDefault();
                           var url = $(this).attr('rel');
                           
                            fileApi.doRequest(url,{},fileApi.createTable);
                        });
                    }
                }
                
                fileApi.getDropDownValues();
                //fileApi.doLogin();
                $('.filter select').change(function(e){
                    fileApi.doSearch();
                });
                $('#filterDocs').click(function(e){
                    fileApi.doSearch();
                });
                
                //fileApi.doSearch();
		});
    })(jQuery, document);
  //-->
</script>
</body>
</html>
