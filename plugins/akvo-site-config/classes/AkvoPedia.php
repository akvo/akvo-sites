<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class AkvoPedia{
    // [akvoflickr user="" width="" height="" api_key=""]
    public static function displaywiki($atts){
        extract( shortcode_atts( array(
            'portal' => 'Sanitation'
            ), $atts ) );
        ob_start();

		?>

        <h1 id="akvopedia-title"></h1>
        <a href="#" id="akvopedia-home-link">Back to Portal</a>
        <div id="embedded-akvopedia"><noscript><iframe style="position: absolute; top: 4em; right:1em; left:1em; bottom:1em; height:90%; width:97%;" src="http://www.akvopedia.org/wiki/<?php echo $portal;?>"></iframe></noscript></div>

        <script src="http://akvopedia.org/resources/akvopedia-gadget/akvopedia-gadget.js"></script>
        <script>
          //<!--
            (function($, document) {
                $(document).ready(function () {
                            $('#embedded-akvopedia').akvopedia({page: '<?php echo $portal;?>', addBackAndForwardButtons: false,scrollToElement: $($('#embedded-akvopedia').get(0).parentNode)});
                            $('#embedded-akvopedia').on('akvopedia:title-updated', function(event, title) {
                                $('#akvopedia-title').html(title);
                                $('#akvopedia-home-link').click(function(e) {
                                    e.preventDefault();
                                    $('#embedded-akvopedia').akvopedia('page', '<?php echo $portal;?>');
                                });

                    });
                });
            })(jQuery, document);
          //-->
        </script>
        <?php
        $output_string=ob_get_contents();;
        ob_end_clean();

        return $output_string;
    }
}
?>
