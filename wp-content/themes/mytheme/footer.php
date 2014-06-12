<?php

// Exit if accessed directly
if ( !defined('ABSPATH')) exit;

/**
 * Footer Template
 *
 *
 * @file           footer.php
 * @package        Responsive 
 * @author         Emil Uzelac 
 * @copyright      2003 - 2013 ThemeID
 * @license        license.txt
 * @version        Release: 1.2
 * @filesource     wp-content/themes/responsive/footer.php
 * @link           http://codex.wordpress.org/Theme_Development#Footer_.28footer.php.29
 * @since          available since Release 1.0
 */
?>
</div>
<!-- end of #wrapper -->
<?php responsive_wrapper_end(); // after wrapper hook ?>
</div>
<!-- end of #container -->
<?php responsive_container_end(); // after container hook ?>

<div id="footer" class="clearfix">
  <div id="footer-wrapper">
    <div class="grid col-940">
      <div class="grid col-540">
        <?php if (has_nav_menu('footer-menu', 'responsive')) { ?>
        <?php wp_nav_menu(array(
				    'container'       => '',
					'fallback_cb'	  =>  false,
					'menu_class'      => 'footer-menu',
					'theme_location'  => 'footer-menu')
					); 
				?>
        <?php } ?>
      </div>
      <!-- end of col-540 -->
      
      <div class="grid col-380 fit">
        <?php $options = get_option('responsive_theme_options');
					
            // First let's check if any of this was set
		
                echo '<ul class="social-icons">';
					
                if (!empty($options['twitter_uid'])) echo '<li class="twitter-icon"><a href="' . $options['twitter_uid'] . '">'
                    .'<img src="' . get_stylesheet_directory_uri() . '/icons/twitter-icon.png" width="24" height="24" alt="Twitter">'
                    .'</a></li>';

                if (!empty($options['facebook_uid'])) echo '<li class="facebook-icon"><a href="' . $options['facebook_uid'] . '">'
                    .'<img src="' . get_stylesheet_directory_uri() . '/icons/facebook-icon.png" width="24" height="24" alt="Facebook">'
                    .'</a></li>';
  
                if (!empty($options['linkedin_uid'])) echo '<li class="linkedin-icon"><a href="' . $options['linkedin_uid'] . '">'
                    .'<img src="' . get_stylesheet_directory_uri() . '/icons/linkedin-icon.png" width="24" height="24" alt="LinkedIn">'
                    .'</a></li>';
					
                if (!empty($options['youtube_uid'])) echo '<li class="youtube-icon"><a href="' . $options['youtube_uid'] . '">'
                    .'<img src="' . get_stylesheet_directory_uri() . '/icons/youtube-icon.png" width="24" height="24" alt="YouTube">'
                    .'</a></li>';
					
                if (!empty($options['stumble_uid'])) echo '<li class="stumble-upon-icon"><a href="' . $options['stumble_uid'] . '">'
                    .'<img src="' . get_stylesheet_directory_uri() . '/icons/stumble-upon-icon.png" width="24" height="24" alt="StumbleUpon">'
                    .'</a></li>';
					
                if (!empty($options['rss_uid'])) echo '<li class="rss-feed-icon"><a href="' . $options['rss_uid'] . '">'
                    .'<img src="' . get_stylesheet_directory_uri() . '/icons/rss-feed-icon.png" width="24" height="24" alt="RSS Feed">'
                    .'</a></li>';
       
                if (!empty($options['google_plus_uid'])) echo '<li class="google-plus-icon"><a href="' . $options['google_plus_uid'] . '">'
                    .'<img src="' . get_stylesheet_directory_uri() . '/icons/googleplus-icon.png" width="24" height="24" alt="Google Plus">'
                    .'</a></li>';
					
                if (!empty($options['instagram_uid'])) echo '<li class="instagram-icon"><a href="' . $options['instagram_uid'] . '">'
                    .'<img src="' . get_stylesheet_directory_uri() . '/icons/instagram-icon.png" width="24" height="24" alt="Instagram">'
                    .'</a></li>';
					
                if (!empty($options['pinterest_uid'])) echo '<li class="pinterest-icon"><a href="' . $options['pinterest_uid'] . '">'
                    .'<img src="' . get_stylesheet_directory_uri() . '/icons/pinterest-icon.png" width="24" height="24" alt="Pinterest">'
                    .'</a></li>';
					
                if (!empty($options['yelp_uid'])) echo '<li class="yelp-icon"><a href="' . $options['yelp_uid'] . '">'
                    .'<img src="' . get_stylesheet_directory_uri() . '/icons/yelp-icon.png" width="24" height="24" alt="Yelp!">'
                    .'</a></li>';
					
                if (!empty($options['vimeo_uid'])) echo '<li class="vimeo-icon"><a href="' . $options['vimeo_uid'] . '">'
                    .'<img src="' . get_stylesheet_directory_uri() . '/icons/vimeo-icon.png" width="24" height="24" alt="Vimeo">'
                    .'</a></li>';
					
                if (!empty($options['foursquare_uid'])) echo '<li class="foursquare-icon"><a href="' . $options['foursquare_uid'] . '">'
                    .'<img src="' . get_stylesheet_directory_uri() . '/icons/foursquare-icon.png" width="24" height="24" alt="foursquare">'
                    .'</a></li>';
             
                echo '</ul><!-- end of .social-icons -->';
         ?>
      </div>
      <!-- end of col-380 fit --> 
      
    </div>
    <!-- end of col-940 -->
    <?php get_sidebar('colophon'); ?>
    <div class="grid col-300 copyright">
      <?php esc_attr_e('&copy;', 'responsive'); ?>
      Copyright
      <?php _e(date('Y')); ?>
      <a href="<?php echo home_url('/') ?>" title="<?php echo esc_attr(get_bloginfo('name', 'display')); ?>">
      <?php //bloginfo('name'); ?>
      Dialoquad</a>. All Rights Reserved. </div>
    <!-- end of .copyright -->
    
    <div class="grid col-620 fit powered">
      <div id="footer-responsive" class="fit"> <em>Theme adapted from</em> <a href="<?php echo esc_url(__('http://themeid.com/responsive-theme/','responsive')); ?>" title="<?php esc_attr_e('Responsive Theme', 'responsive'); ?>"> <?php printf('Responsive Theme'); ?></a></div>
      <div id="footer-wordpress"> <em>Powered by</em> <a href="<?php echo esc_url(__('http://wordpress.org/','responsive')); ?>" title="<?php esc_attr_e('WordPress', 'responsive'); ?>"> <?php printf('WordPress'); ?></a> </div>
      <div id="footer-justfont"><em>Font from</em> <a href="http://www.justfont.com/" title="Just Font" >Just Font</a> </div>
      <div id="footer-openshift"><em>Web hosting by</em> <a href="https://openshift.redhat.com/app/" title="Openshift by Red Hat" ><em>OpenShift</em></a></div>
    </div>
    <!-- end .powered --> 
    
  </div>
  <!-- end #footer-wrapper --> 
  
</div>
<!-- end #footer -->

<?php wp_footer(); ?>

<!--justfont script below DO NOT REMOVE-->
<script type="text/javascript">window.jfAsyncInit=function(){jf.main({'appId':'086f88d9VxjMD59pN8yHbN0ssBMY8uQQ5kOegP8zvjUNQ0elpJy7Mik3bDOFw9HBhDtIfKq6z-tebOLImgyNW8PYsHmaoQARrIOq0BXYKEmM4BIRcx0dk-HyJ32nnZ85i9k-YgQBKB7o8zd5kPkBF_LV6Ysd4qUWcUS0P2-fmyNPQFs3EPc=','p':'1845','font':{'wt005':{'css':{'0':'.ct3'}},'wt011':{'css':{'0':'.ct5'}},'wt064':{'css':{'0':'.ct4'}},'WCL-02':{'css':{'0':'.ct8'}},'wts43':{'css':{'0':'.ct9'}},'xingothic-tc-w4':{'css':{'0':'.ct7'},'alias':'xinheiw4'},'xingothic-tc-w8':{'css':{'0':'.ct2'},'alias':'xinheiw8'},'xingothic-tc-w6':{'css':{'0':'.ct6'},'alias':'xinheiw6'},'xingothic-tc-w3':{'css':{'0':'.ct1'},'alias':'xinheiw3'},'wts55':{'css':{'0':'.ct10'}},'datf5':{'css':{'0':'.datf5'},'alias':'datf5'},'datz5':{'css':{'0':'.datz5'},'alias':'dialoslab','english':'rockwell'},'dats4':{'css':{'0':'.dats4'},'alias':'dialosung','english':'baskerville'},'dass3':{'css':{'0':'.dass3'},'alias':'dass3'},'daty9':{'css':{'0':'.daty9'},'alias':'dialoroundultrabold','english':'Varela Round'}}})};(function(){var c={'scriptTimeout':3000};var h=document.getElementsByTagName("html")[0];h.className+=" jf-loading";var e=setTimeout(function(){h.className=h.className.replace(/(\s|^)jf-loading(\s|$)/g," ");h.className+=" jf-inactive"},c.scriptTimeout);var jfscript=document.createElement("script");var d=false;jfscript.type="text/javascript";jfscript.async=true;jfscript.src='http://ds.justfont.com/js/stable/v/1.13/';jfscript.onload=jfscript.onreadystatechange=function(){var a=this.readyState;if(d||a&&a!="complete"&&a!="loaded"){return}d=true;clearTimeout(e)};var s=document.getElementsByTagName("script")[0];s.parentNode.insertBefore(jfscript,s)})();</script>
<?php if(is_home()): ?>
<script type="text/javascript">
   jQuery(function() {
		jQuery('#cn-slideshow').slideshow();
		
		/*var refreshIntervalId;
		refreshIntervalId =  setInterval(function() { 
		
			jQuery('#cn-slideshow a.cn-nav-next').trigger("click"); 
		} , 4000);
		
		jQuery('div.cn-images').bind("mouseenter", (function(){ 
			clearInterval(refreshIntervalId); 
		}));
		
		jQuery('div.cn-images').bind("mouseleave", (function(){ 
			
			refreshIntervalId =  setInterval(function() { 
				jQuery('#cn-slideshow a.cn-nav-next').trigger("click"); 
			} , 4000);
		}));
		
		jQuery("div.cn-bar").live("mouseenter", function() {
			clearInterval(refreshIntervalId); 
		});
		
		jQuery("div.cn-bar").live("mouseleave", function() {
			refreshIntervalId =  setInterval(function() { 
				jQuery('#cn-slideshow a.cn-nav-next').trigger("click"); 
			} , 4000);
		});*/
	
	});
</script> 
<?php endif;?>
</body></html>