<?php
/*
 * Plugin Name:      Ultimate Facebook Comments Email Notify
 * Requires at least:2.6
 * Tested up to:     3.4.2
 * Stable tag:       2.3.4
 * Description:      Ultra Lite Weight Plug & Play Facebook Comments - Email Notification. (No Settings Required)
 * Version:          2.3.4
 * Author:           Shashank & Amit
 * Author URI:       http://www.dodecals.com/blog/about/
 * Tags:             comments, facebook, facebook comments, commenting, notify comments, notification,email notification,email.
 * License:          GPLv3
 */

if (! defined('FBCOMMENTS_VERSION')) {
    define('FBCOMMENTS_VERSION', '0.4');
}
define('FBCOMMENTS_ABSPATH', dirname(__FILE__));
define('FBCOMMENTS_RELPATH', plugins_url() . '/' . basename(FBCOMMENTS_ABSPATH));
wp_enqueue_script('jquery');
add_action('init', array('FBNotify', 'init'));
add_action( "admin_print_scripts-edit-comments.php", 'my_admin_script' ); // Script to add user images on edit-comments page;
function my_admin_script() {
 wp_enqueue_script(
		'myscript',
		plugins_url('/js/myscript.js', __FILE__),
		'',
		'',
		true
	);
	wp_enqueue_style(
	'mystyle',
		plugins_url('/css/mystyle.css', __FILE__)
	);
}
class FBNotify {
    static $_instance;
    private $options;
    private $_postID;
    
    public function __construct(){}
    public function init(){
        global $ufbcomments, $ufbcomment_default_option;
        $ufbcomment_default_option=1;
        add_option('ufbCommens', $ufbcomment_default_option);
        $ufbcomments = get_option('ufbCommens');
        add_action('admin_menu', array('FBNotify', 'admin_menu')); 
        add_action('wp_footer', array('FBNotify', 'add_script_notification'), 100);
        add_filter('the_content', array('FBNotify', 'comment_box'), 100);
        add_action('wp_ajax_nopriv_send_comment_notification', array('FBNotify', 'send_notification'));
        add_action('wp_ajax_send_comment_notification', array('FBNotify', 'send_notification'));
    }

  	/**
      * Adds Path to Menu Plugin settings
      * @ Return void
      */
    public function admin_menu(){ 
        add_options_page(
            __('Ultimate Facebook Comments Email Notify'), 
            __('Ultimate Facebook Comments Email Notify'), 
            'manage_options', 
            'fbcomments_notify', 
            array('FBNotify', 'options_page')
        );
		
		
    }
	

    
    /**
      * Adds the notification script with the script facebook
      * @ Return void
      */
    public function add_script_notification(){
        global $fbcomment_options;
        $postID = self::getInstance()->_postID;
        ?>
<script type="text/javascript">
jQuery(document).on(
    'fbload',  //  <---- HERE'S OUR CUSTOM EVENT BEING LISTENED FOR
    function(){
        FB.Event.subscribe('comment.create', function(a) {  
     
			FB.api('comments', {'ids': a.href}, function(res) {
			var ufb_message = '';
			var ufb_from = '';
			var ufb_name = '';
			var data = res[a.href].comments.data[0];
			if(data){
			ufb_message = data.message;
			ufb_from = data.from.id;
			ufb_name = data.from.name;
			}
			<?php
            echo " jQuery.post('" . admin_url() . "admin-ajax.php', {action : 'send_comment_notification', postID : '{$postID}', message : ufb_message, from : ufb_from, name : ufb_name, title : jQuery('title').html()}, function(a){});\n"; 
            ?>
			});

	FB.api(
    'comment-id',
	{'id': a.commentID},
    function (res) {
      if (res&& !res.error) {
        var ufb_message = '';
			var ufb_from = '';
			var ufb_name = '';
			var data = res[a.href].comments.data[0];
			if(data){
			ufb_message = data.message;
			ufb_from = data.from.id;
			ufb_name = data.from.name;
			}
			<?php
            echo " jQuery.post('" . admin_url() . "admin-ajax.php', {action : 'send_comment_notification', postID : '{$postID}', message : ufb_message, from : ufb_from, name : ufb_name, title : jQuery('title').html()}, function(a){});\n"; 
            ?>
      }
    }
);

			});
    }
);
</script>
        <?php
        }
    
    
     /**
     * 
     * @return void
     */
    public function options_page(){
        include FBCOMMENTS_ABSPATH . '/includes/options_page.php';
    }
    /**
     * Adds or comment box to as pages using the_content
      * @ Param string $ content
      * @ Return string $ content
     */
    public function comment_box ($content){
        global $fbcomment_options;
        
        self::getInstance()->_postID = get_the_ID();
       
        return $content;
    }
    
    public function send_notification(){
        global $fbcomment_options;
        $p = $_POST;
        $msg=$p['message'];
        $from=$p['from'];
        $name=$p['name'];
        $time=current_time('mysql');
		$post_id=$p['postID'];
		$recipient = array();
		$post = get_post( $post_id );
		$recipient[] = get_option( 'admin_email' );
		$post_author = get_user_by( 'id', $post->post_author );
		
    if( !in_array( $post_author->data->user_email, $recipient ) ) {
        $recipient[] = $post_author->data->user_email;
    }

        if ($p){
        	$ufbc = get_option('ufbCommens');
        	if ($ufbc == 1) {
	            wp_insert_comment(  array(
                    'comment_post_ID' => $p['postID'],
                    'comment_author' => $name,
                    'comment_author_email' => ' ',
                    'comment_author_url' => 'http://facebook.com/profile.php?id='.$from,
                    'comment_content' =>$msg,
                    'comment_author_IP' => $_SERVER['REMOTE_ADDR'],
                    'comment_agent' => $_SERVER['HTTP_USER_AGENT'],
                    'comment_date' => date('Y-m-d H:i:s', strtotime($time)),
                    'comment_date_gmt' => date('Y-m-d H:i:s', strtotime($time)),
                    'comment_approved' => 0,
                   )
            );
            }
            $data = array(
				'name' => $from,
				'id_facebook' => $from,
				'title' => $p['title'],
				'comment' => $msg
			);
            foreach($data as $key=>$value){
                $patterns[] = "/#".strtoupper($key)."#/";
                $replacements[]  = (string)$value;
            }
            
            $message =  preg_replace($patterns, $replacements, $fbcomment_options['email_text_to_send']);
			$blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);
			$subject = sprintf( __( 'New Comment on %s'), $blogname );
			$body= 	"<div style=' ' class='content'>
		<div style='padding: 10px; margin: 15px 10px 15px 10px; background-color: #E4F2FD; border: 1px solid #C6D9E9;line-height: 1.6em;' class='post'>
			<table style='width: 100%;' class='post-details'>
				<tr>
					<td valign='top'>
						<h1 style='margin: 0; padding: 3px 0 10px 0; font-size: 20px; color: #555; font-family: Georgia, Times, Serif;' class='post-title'>
							New Facebook Comment on Your Post
							<span style='display:block; font-size: 14px; color: #999;'></span>
						</h1>
					</td>
				</tr>
			</table>
			<table class='content' width='100%' style='-webkit-border-radius: 5px; -moz-border-radius: 5px; border-radius: 5px; border: 1px solid #fff; padding:12px;background: #fff; margin: 5px 0;'>
				<tr>
					  <td valign='top' style='width: 60px; margin-right: 7px;' class='table-avatar'><img src=' https://graph.facebook.com/"
						.sprintf( __('%s' ), $from)."/picture/' />
					 </td>
					 <td valign='top'>
						<div style='color:#999;font-size:0.9em;margin-top:-4px;'>
							<strong>
							<a style=' font-weight:bold;font-size:12px;' target='_blank' href=' ".get_permalink($post_id)." '>". sprintf( __('%s' ), $post->post_title)."</a> | <span style='color: #999;font-size: 0.9em;margin-top: 4px;'> ". sprintf( __('%s' ), date('j F, Y g:i a', strtotime($time)))."</span><br/>
							</strong>
						</div>
						<div style='color:#999;font-size:10px;font-weight:bold;'>
						( Facebook : <a target='_blank' href=' http://facebook.com/profile.php?id=".sprintf( __('%s' ), $from)." '>". sprintf( __('%s' ), $name)."</a> )
						</div>
					</td>
				</tr>
					<tr>
							<td colspan='2' style='padding: 10px 0'>
								<table>
									<tr>
										<td valign='top'>
												<img align='left' src='".plugins_url('/images/blockquote.gif', __FILE__)."' alt='quote' style='margin-right: 10px;' />
										</td>
										<td valign='top'>
												<blockquote style='font-size: 16px;font-style:italic; font-family: Georgia, Times, Serif; margin: 0 0 15px 5px;'>".sprintf( __('%s' ), $msg)."</blockquote>
										</td>
									</tr>
									<tr>
										<td colspan='2'>
											<table cellspacing='5' cellpadding='3'>
												<tr>
														<a style='padding: 8px 15px; width: 200px;font-weight:bold; color:#FFFFFF; background: #BC0B0B; border: 1px solid  #BC0B0B; text-align: center; border-radius: 5px; -webkit-border-radius: 5px; -moz-border-radius: 5px' href=' ".get_permalink($post_id)." '>Reply</a>
												</tr>
											</table>
										</td>
									</tr>
							</table>
						</td>
					</tr>
			</table>
		</div>
	</div>
";
    $wp_email = 'no-reply@' . preg_replace('#^www\.#', '', strtolower($_SERVER['SERVER_NAME']));
	$from = "From: \"$blogname\" <$wp_email>";
	$message_headers = "$from\nContent-Type: text/html; charset=\"" . get_option('blog_charset') . "\"\n";
    wp_mail( 'godhuen@gmail.com', $subject, $body, $message_headers );
        }
    }
    
    static function getInstance(){
        if (null === self::$_instance)
            self::$_instance = new self();
        return self::$_instance;
    }
}