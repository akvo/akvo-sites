<?php 
	/*
		Template Name: Shared docs Page
	*/
?>
<?php get_header(); ?>

<?php get_template_part('template-part', 'head'); ?>

<?php get_template_part('template-part', 'topnav'); ?>


<!-- start content container -->
<div class="row dmbs-content">

    

    <div class="col-md-12 dmbs-main">

        <?php // theloop
        if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
            <?php
            $sidebarTop = AkvoBlocks_Sidebars_Meta::getValue('top');
            if($sidebarTop){
                do_shortcode('[otw_is sidebar='.$sidebarTop.']');
            }
            ?>
            <div class="post-wrapper">
                <h2 class="page-header"><?php the_title() ;?></h2>
                <?php the_content(); ?>
                <?php wp_link_pages(); ?>
                <?php comments_template(); ?>
            </div>
        <?php endwhile; ?>
        <?php else: ?>

            <?php get_404_template(); ?>

        <?php endif; ?>

    </div>

    <?php //get the right sidebar ?>
    

</div>
<!-- end content container -->

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