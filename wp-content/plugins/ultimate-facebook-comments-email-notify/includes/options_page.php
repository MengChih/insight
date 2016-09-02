<style type="text/css">
#options h3 {
    padding: 7px;
    padding-top: 10px;
    margin: 0px;
    cursor: auto
}

#options p {
    clear: both;
    padding: 0 10px 10px;
}

#options .postbox {
    margin: 0px 0px 10px 0px;
    padding: 0px;
}
</style>

<div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/en_US/all.js#xfbml=1&appId=406127512778079";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>

<div class="wrap">
<?php 

if(isset($_REQUEST['submit'])&&isset($_REQUEST['ufbcomments'])) {
update_option('ufbCommens', $_REQUEST['ufbcomments']);
}
else if(isset($_REQUEST['submit'])){
update_option('ufbCommens', 0);
}

?>
	
    <div id="icon-plugins" class="icon32">
        <br>
    </div>
    <h2>Ultimate Facebook Comments Email Notify</h2>
        <form method="post" action="" id="options">
        <?php wp_nonce_field('update-options')?>
        <?php
        $ufbc = get_option('ufbCommens');
        ?>
        <label for="ufbcomments"> 
        <input name="ufbcomments" type="checkbox" id="<?php echo "hhh".$ufbc ?>" value="1" <?php if($ufbc==1){ echo 'checked="checked"'; } ?> />
        <?php _e('Un Check this if you dont want Facebook Comments to be added in your dashboard Comments>>All Comments page.')?>
        <?php _e('<br/> I have kept it checked deliberately so that you can get some SEO benefits from your Facebook Comments also.')?>
        </label>
        <div class="metabox-holder">
                <?php submit_button(); ?>
            </div>
        </form>
        <div class="fb-like" data-href="http://facebook.com/doDecals" data-send="false" data-width="450" data-show-faces="false"></div>
        
    <div id="options">
        <div class="postbox-container" style="width: 100%;">
            <div class="metabox-holder">
                <div class="postbox">
                
                    <h3 class="hndle"><?php _e('Information About Ultimate Facebook Comments Email Notify') ?></h3>
                    <table class="form-table">
                        <tr>
                            <td>
                                <p><?php _e('The plugin will automatically send beautifully formatted email notification to the admin and the post author')?><br/><?php _e(' when someone has commented using Facebook social comment box')?></p>
							 	<img src="../wp-content/plugins/ultimate-facebook-comments-email-notify/images/all-comments.png"/>
                                <p><?php _e('You can also see all the comments in the Comments > All Comment Page.')?></p>
                                <img src="../wp-content/plugins/ultimate-facebook-comments-email-notify/images/gmail-screenshot.png"/>
							 	<p><?php _e('Email notification will be triggered when someone comments')?></p>
							 	<img src="../wp-content/plugins/ultimate-facebook-comments-email-notify//images/facebook-comment-box.png"/>
							 	<p><strong><?php _e('This plugin is only for those people who have already installed Facebook Social Comments on their blog ')?></strong>
							 	<br/><a href="http://www.decalsdesign.com/Ultimate-facebook-comments-notify.html">http://www.decalsdesign.com/Ultimate-facebook-comments-notify.html</a>
							 	</p>
							 	

                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

        <div class="fb-like" data-href="http://facebook.com/doDecals" data-send="false" data-width="450" data-show-faces="false"></div>
