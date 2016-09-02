<?php
/*
  Plugin Name: YouTube Advanced Embed
  Plugin URI: http://www.embedplus.com/
  Description: YouTube embed plugin. Customize a YouTube embed with your own annotations, links, and extra controls. Visitors will see YouTube in a unique way!
  Version: 5.5
  Author: EmbedPlus Team
  Author URI: http://www.embedplus.com/
 */

/*
  Copyright (C) 2016 EmbedPlus.com

  This program is free software: you can redistribute it and/or modify
  it under the terms of the GNU General Public License as published by
  the Free Software Foundation, either version 3 of the License, or
  (at your option) any later version.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program. If not, see <http://www.gnu.org/licenses/>.

 */

//define('WP_DEBUG', true);


class EmbedPlusOfficialPlugin
{

    public static $version = '5.5';
    public static $opt_version = 'version';
    public static $optembedwidth = null;
    public static $optembedheight = null;
    public static $defaultheight = null;
    public static $defaultwidth = null;
    public static $opt_enhance_youtube = 'enhance_youtube';
    public static $opt_show_react = 'show_react';
    public static $opt_auto_hd = 'auto_hd';
    public static $opt_show_ann = 'show_ann';
    public static $opt_sweetspot = 'sweetspot';
    public static $opt_emb = 'emb';
    public static $opt_pro = 'pro';
    public static $opt_oldspacing = 'oldspacing';
    public static $opt_schemaorg = 'schemaorg';
    public static $opt_apikey = 'apikey';
    public static $opt_ogvideo = 'ogvideo';
    public static $opt_ssl = 'ssl';
    public static $opt_lean = 'lean';
    public static $opt_alloptions = 'embedplusopt_alloptions';
    public static $alloptions = null;
    public static $scriptsprinted = 0;
    //public static $epbase = 'http://localhost:2346';
    public static $epbase = '//www.embedplus.com';
    public static $badentities = array('&#215;', '×', '&#8211;', '–', '&amp;');
    public static $goodliterals = array('x', 'x', '--', '--', '&');
    ///////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////
    //public static $ytregex = '@^[\r\n]{0,1}[[:blank:]]*https?://(?:www\.)?(?:(?:youtube.com/watch\?)|(?:youtu.be/))([^\s"]+)[[:blank:]]*[\r\n]{0,1}$@im';
    public static $oldytregex = '@^\s*http[vhs]?://(?:www\.)?(?:(?:youtube.com/watch\?)|(?:youtu.be/))([^\s"]+)\s*$@im';
    public static $ytregex = '@^[\r\t ]*http[vhs]?://(?:www\.)?(?:(?:youtube.com/watch\?)|(?:youtu.be/))([^\s"]+)[\r\t ]*$@im';
    public static $justurlregex = '@https?://(?:www\.)?(?:(?:youtube.com/(?:(?:(?:(?:watch)|(?:embed))/{0,1}\?)|(?:v/)))|(?:youtu.be/))([^<\[\s"]+)@i';

    //public static $justurlregex = '@(?:embedplusvideo [^]]+ytid=([^&])+)|(?:https?://(?:www\.)?(?:(?:youtube.com/(?:(?:watch)|(?:embed))/{0,1}\?)|(?:youtu.be/))([^\[\s"]+))@i';
    ///////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////

    public function __construct()
    {
        self::$alloptions = get_option(self::$opt_alloptions);
        if (self::$alloptions == false || version_compare(self::$alloptions[self::$opt_version], self::$version, '<'))
        {
            self::install();
        }

        if (self::$alloptions[self::$opt_oldspacing] == 1)
        {
            self::$ytregex = self::$oldytregex;
        }


        if (self::$optembedwidth == null)
        {
            self::$optembedwidth = intval(get_option('embed_size_w'));
            self::$optembedheight = intval(get_option('embed_size_h'));
        }

        $do_youtube2embedplus = self::$alloptions[self::$opt_enhance_youtube];

        if ($do_youtube2embedplus == 1)
        {
            self::do_yt_ep();
        }

        // add admin menu under settings
        add_action('admin_menu', 'EmbedPlusOfficialPlugin::embedplus_plugin_menu');
        if (!is_admin())
        {
            //tell wordpress to register the shortcode
            add_shortcode("embedplusvideo", "EmbedPlusOfficialPlugin::embedplusvideo_shortcode");

            // allow shortcode in widgets

            add_filter('widget_text', 'do_shortcode', 11);

            add_action('wp_print_scripts', array('EmbedPlusOfficialPlugin', 'cantembedplus'));
            if (self::$alloptions[self::$opt_pro] && strlen(trim(self::$alloptions[self::$opt_pro])) > 0 && self::$alloptions[self::$opt_ogvideo] == 1)
            {
                add_action('wp_head', array('EmbedPlusOfficialPlugin', 'do_ogvideo'));
            }
        }
    }

    static function install()
    {
        $_opt_enhance_youtube = get_option('embedplusopt_enhance_youtube', 1);
        $_opt_show_react = get_option('embedplusopt_show_react', 1);
        $_opt_auto_hd = get_option('embedplusopt_auto_hd', 0);
        $_opt_sweetspot = get_option('embedplusopt_sweetspot', 1);
        $_opt_lean = 0;
        $_opt_emb = 1;
        $_opt_pro = get_option('embedplusopt_pro', '');
        $_opt_oldspacing = 1;
        $_schemaorg = 0;
        $_apikey = '';
        $_ssl = 0;
        $_ogvideo = 0;
        $_opt_show_ann = 1;

        $arroptions = get_option(self::$opt_alloptions);

        if ($arroptions !== false)
        {
            $_opt_enhance_youtube = self::tryget($arroptions, self::$opt_enhance_youtube, 1);
            $_opt_show_react = self::tryget($arroptions, self::$opt_show_react, 1);
            $_opt_auto_hd = self::tryget($arroptions, self::$opt_auto_hd, 0);
            $_opt_sweetspot = self::tryget($arroptions, self::$opt_sweetspot, 0);
            $_opt_lean = self::tryget($arroptions, self::$opt_lean, 0);
            $_opt_emb = self::tryget($arroptions, self::$opt_emb, 1);
            $_opt_pro = self::tryget($arroptions, self::$opt_pro, '');
            $_opt_oldspacing = self::tryget($arroptions, self::$opt_oldspacing, 1);
            $_schemaorg = self::tryget($arroptions, self::$opt_schemaorg, 0);
            $_apikey = self::tryget($arroptions, self::$opt_apikey, '');
            $_ssl = self::tryget($arroptions, self::$opt_ssl, 0);
            $_ogvideo = self::tryget($arroptions, self::$opt_ogvideo, 0);
            $_opt_show_ann = self::tryget($arroptions, self::$opt_show_ann, 1);
        }
        else
        {
            $_opt_oldspacing = 0;
        }

        $all = array(
            self::$opt_version => self::$version,
            self::$opt_enhance_youtube => $_opt_enhance_youtube,
            self::$opt_show_react => $_opt_show_react,
            self::$opt_auto_hd => $_opt_auto_hd,
            self::$opt_show_ann => $_opt_show_ann,
            self::$opt_sweetspot => $_opt_sweetspot,
            self::$opt_lean => $_opt_lean,
            self::$opt_pro => $_opt_pro,
            self::$opt_ssl => $_ssl,
            self::$opt_ogvideo => $_ogvideo,
            self::$opt_emb => $_opt_emb,
            self::$opt_oldspacing => $_opt_oldspacing,
            self::$opt_schemaorg => $_schemaorg,
            self::$opt_apikey => $_apikey
        );

        update_option(self::$opt_alloptions, $all);
        update_option('embed_autourls', 1);
        self::$alloptions = get_option(self::$opt_alloptions);
    }

    public static function tryget($array, $key, $default = null)
    {
        return isset($array[$key]) ? $array[$key] : $default;
    }

    public static function wp_above_version($ver)
    {
        global $wp_version;
        if (version_compare($wp_version, $ver, '>='))
        {
            return true;
        }
        return false;
    }

    public static function do_yt_ep()
    {
        if (!is_admin())
        {
            //add_filter('the_content', 'EmbedPlusOfficialPlugin::youtube2embedplus_non_oembed', 1);
            //wp_embed_register_handler('youtube2embedplus', self::$ytregex, 'EmbedPlusOfficialPlugin::youtube2embedplus_handler', 1);
            add_filter('the_content', 'EmbedPlusOfficialPlugin::youtube2embedplus_filter', 1);
            add_filter('widget_text', 'EmbedPlusOfficialPlugin::youtube2embedplus_filter', 1);
        }
    }

    public static function youtube2embedplus_filter($content)
    {
        $content = preg_replace_callback(self::$ytregex, "EmbedPlusOfficialPlugin::youtube2embedplus_handler", $content);
        return $content;
    }

    public static function init_dimensions($url = null)
    {

        // get default dimensions; try embed size in settings, then try theme's content width, then just 480px
        if (self::$defaultwidth == null)
        {
            global $content_width;
            if (empty($content_width))
            {
                $content_width = $GLOBALS['content_width'];
            }
            self::$defaultwidth = self::$optembedwidth ? self::$optembedwidth : ($content_width ? $content_width : 480);
            self::$defaultheight = self::get_aspect_height($url);
        }
    }

    public static function get_aspect_height($url)
    {

        // attempt to get aspect ratio correct height from oEmbed
        $aspectheight = round((self::$defaultwidth * 9) / 16, 0);
        if ($url)
        {
            require_once( ABSPATH . WPINC . '/class-oembed.php' );
            $oembed = _wp_oembed_get_object();
            $args = array();
            $args['width'] = self::$defaultwidth;
            //$args['height'] = self::$optembedheight;
            $args['discover'] = false;
            $odata = $oembed->fetch('http://www.youtube.com/oembed', $url, $args);

            if ($odata)
            {
                $aspectheight = $odata->height;
            }
        }

        //add 30 for YouTube's own bar
        return $aspectheight + 30;
    }

    public static function youtube2embedplus_handler($m)
    {

        //for future: cache results http://codex.wordpress.org/Class_Reference/WP_Object_Cache
        //$cachekey = '_epembed_' . md5( $url . serialize( $attr ) );

        $url = trim(preg_replace('/&amp;/i', '&', $m[0]));
        $urlqstring = preg_split('/[?]/i', $url);

        self::init_dimensions($url);

        $epreq = array(
            "height" => self::$defaultheight,
            "width" => self::$defaultwidth,
            "vars" => "",
            "standard" => "",
            "id" => "ep" . rand(10000, 99999)
        );

        $ytvars = array();
        if (count($urlqstring) > 1)
        {
            $ytvars = preg_split('/[&]/i', $urlqstring[1]);
        }


        // extract youtube vars (special case for youtube id)
        $ytkvp = array();
        foreach ($ytvars as $k => $v)
        {
            $kvp = preg_split('/=/', $v);
            if (count($kvp) == 2)
            {
                $ytkvp[$kvp[0]] = $kvp[1];
            }
//            else if (count($kvp) == 1 && $k == 0)
//            {
//                $ytkvp['v'] = $kvp[0];
//            }
        }

        if (strpos($url, 'youtu.be') !== false && !isset($ytkvp['v']))
        {
            $vtemp = explode('/', $urlqstring[0]);
            $ytkvp['v'] = array_pop($vtemp);
        }


        // setup variables for creating embed code
        $epreq['vars'] = 'ytid=';
        $epreq['standard'] = '//www.youtube.com/v/';
        if ($ytkvp['v'])
        {
            $epreq['vars'] .= strip_tags($ytkvp['v']) . '&amp;';
            $epreq['standard'] .= strip_tags($ytkvp['v']) . '?fs=1&amp;';
        }
        $realheight = intval(isset($ytkvp['h']) && is_numeric($ytkvp['h']) ? $ytkvp['h'] : $epreq['height']);
        $epreq['vars'] .= 'height=' . $realheight . '&amp;';
        $epreq['height'] = $realheight;

        $realwidth = intval(isset($ytkvp['w']) && is_numeric($ytkvp['w']) ? $ytkvp['w'] : $epreq['width']);
        $epreq['vars'] .= 'width=' . $realwidth . '&amp;';
        $epreq['width'] = $realwidth;


        $realhd = isset(self::$alloptions[self::$opt_auto_hd]) && self::$alloptions[self::$opt_auto_hd] == 1 ? 'hd=1&amp;' : '';
        $realhd = isset($ytkvp['hd']) && $ytkvp['hd'] == 1 ? 'hd=1&amp;' : $realhd;
        $epreq['vars'] .= $realhd;
        $epreq['standard'] .= str_replace('hd=1', 'vq=720', $realhd);

        $realshow_ann = isset(self::$alloptions[self::$opt_show_ann]) && self::$alloptions[self::$opt_show_ann] == 3 ? 'iv_load_policy=3&amp;' : '';
        $realshow_ann = isset($ytkvp['show_ann']) && $ytkvp['show_ann'] == 3 ? 'iv_load_policy=3&amp;' : $realshow_ann;
        $epreq['vars'] .= $realshow_ann;
        $epreq['standard'] .= $realshow_ann;

        $realstart = isset($ytkvp['start']) && is_numeric($ytkvp['start']) ? 'start=' . intval($ytkvp['start']) . '&amp;' : '';
        $epreq['vars'] .= $realstart;
        $epreq['standard'] .= $realstart;

        $realend = isset($ytkvp['end']) && is_numeric($ytkvp['end']) ? 'end=' . intval($ytkvp['end']) . '&amp;' : '';
        $epreq['vars'] .= preg_replace('/end/', 'stop', $realend);
        $epreq['standard'] .= $realend;

        $epreq['vars'] .= 'react=' . intval(self::$alloptions[self::$opt_show_react]) . '&amp;';
        $epreq['vars'] .= 'sweetspot=' . intval(self::$alloptions[self::$opt_sweetspot]) . '&amp;';


        $epreq['vars'] .= self::$alloptions[self::$opt_emb] == '0' ? 'emb=0&amp;' : '';
        $epreq['vars'] .= self::$alloptions[self::$opt_lean] == '1' ? 'lean=1&amp;' : '';

        return self::get_embed_code($epreq);
    }

    public static function embedplusvideo_shortcode($incomingfrompost)
    {

        self::init_dimensions();

        //process incoming attributes assigning defaults if required
        $incomingfrompost = shortcode_atts(array(
            "height" => self::$defaultheight,
            "width" => self::$defaultwidth,
            "vars" => "",
            "standard" => "",
            "id" => "ep" . rand(10000, 99999)
                ), $incomingfrompost);

        if (self::$alloptions[self::$opt_sweetspot] == '0' && strpos($incomingfrompost['vars'], 'sweetspot=') === false)
        {
            $incomingfrompost['vars'] .= '&sweetspot=0';
        }
        if (self::$alloptions[self::$opt_emb] == '0' && strpos($incomingfrompost['vars'], 'emb=') === false)
        {
            $incomingfrompost['vars'] .= '&emb=0';
        }
        if (self::$alloptions[self::$opt_lean] == '1' && strpos($incomingfrompost['vars'], 'lean=') === false)
        {
            $incomingfrompost['vars'] .= '&lean=1';
        }

        $epoutput = EmbedPlusOfficialPlugin::get_embed_code($incomingfrompost);
        //send back text to replace shortcode in post
        return $epoutput;
    }

    public static function get_embed_code($incomingfromhandler)
    {
        $epheight = $incomingfromhandler['height'];
        $epwidth = $incomingfromhandler['width'];
        $epvars = $incomingfromhandler['vars'];
        $epobjid = $incomingfromhandler['id'];
        $epstandard = $incomingfromhandler['standard'];
        $epfullheight = null;
        $schemaorgoutput = '';
        $linkscheme = 'http';


        if (self::$alloptions[self::$opt_pro] && strlen(trim(self::$alloptions[self::$opt_pro])) > 0)
        {
            if (self::$alloptions[self::$opt_schemaorg] == 1)
            {
                $epvarsarr = self::keyvalue($epvars, true);
                $schemaorgoutput = self::getschemaorgoutput($epvarsarr['ytid']);
            }
            if (self::$alloptions[self::$opt_ssl] == 1)
            {
                $linkscheme = 'https';
            }
        }


        $epobjid = htmlspecialchars($epobjid);

        if (is_numeric($epheight))
        {
            $epheight = (int) $epheight;
        }
        else
        {
            $epheight = $this->defaultheight;
        }
        $epfullheight = $epheight + 32;

        if (is_numeric($epwidth))
        {
            $epwidth = (int) $epwidth;
        }
        else
        {
            $epwidth = $this->defaultwidth;
        }




        $epvars = preg_replace('/\s/', '', $epvars);
        $epvars = preg_replace('/¬/', '&not', $epvars);


        if ($epstandard == "")
        {
            $epstandard = "//www.youtube.com/embed/";
            $ytidmatch = array();
            preg_match('/ytid=([^&]+)&/i', $epvars, $ytidmatch);
            $epstandard .= $ytidmatch[1] . '?';
        }

        $epstandard = preg_replace('/\s/', '', $epstandard);


        if (strpos($epvars, 'iv_load_policy') === false)
        {
            $realshow_ann = isset(self::$alloptions[self::$opt_show_ann]) && self::$alloptions[self::$opt_show_ann] == 3 ? '&amp;iv_load_policy=3' : '';
            $epvars .= $realshow_ann;
            $epstandard .= $realshow_ann;
        }


        $epstandard = preg_replace('/youtube.com\/v\//i', 'youtube.com/embed/', $epstandard);
        $epoutputstandard = '<iframe class="cantembedplus" title="YouTube video player" width="~width" height="~height" src="~standard" frameborder="0" allowfullscreen></iframe>';


        //       $epoutput = '<object type="application/x-shockwave-flash" width="~width" height="~fullheight" data="' . self::$epbase . '/_embedplus.swf" id="' . $epobjid . '">' . chr(13) .
        //               '<param value="' . self::$epbase . '/_embedplus.swf" name="movie" />' . chr(13) .
        $epoutput = '<object type="application/x-shockwave-flash" width="~width" height="~fullheight" data="' . $linkscheme . '://getembedplus.com/embedplus.swf" id="' . $epobjid . '">' . chr(13) .
                '<param value="' . $linkscheme . '://getembedplus.com/embedplus.swf" name="movie" />' . chr(13) .
                '<param value="high" name="quality" />' . chr(13) .
                '<param value="transparent" name="wmode" />' . chr(13) .
                '<param value="always" name="allowscriptaccess" />' . chr(13) .
                '<param value="true" name="allowFullScreen" />' . chr(13) .
                '<param name="flashvars" value="~vars&amp;rs=w" />' . chr(13) .
                $epoutputstandard . chr(13) .
                '</object>' . $schemaorgoutput;


        $ua = $_SERVER['HTTP_USER_AGENT'];
        if (strlen($epvars) == 0 ||
                stripos($ua, 'iPhone') !== false ||
                stripos($ua, 'iPad') !== false ||
                stripos($ua, 'iPod') !== false)
        {// if no embedplus vars for some reason, or if iOS
            $epoutput = $epoutputstandard;
        }

        if (function_exists('wp_specialchars_decode'))
        {
            $epvars = wp_specialchars_decode($epvars);
            $epstandard = wp_specialchars_decode($epstandard);
        }
        else
        {
            $epvars = htmlspecialchars_decode($epvars);
            $epstandard = htmlspecialchars_decode($epstandard);
        }
        //strip tags
        $epvars = strip_tags($epvars);
        $epstandard = strip_tags($epstandard);

        $epoutput = str_replace('~height', $epheight, $epoutput);
        $epoutput = str_replace('~fullheight', $epfullheight, $epoutput);
        $epoutput = str_replace('~width', $epwidth, $epoutput);
        $epoutput = str_replace('~standard', $epstandard, $epoutput);
        $epoutput = str_replace('~vars', $epvars, $epoutput);

        // reset static vals for next embed
        self::$defaultheight = null;
        self::$defaultwidth = null;

        //send back text to calling function
        return $epoutput;
    }

    public static function calcblogwidth()
    {
        $blogwidth = null;
        try
        {
            $embed_size_w = intval(get_option('embed_size_w'));

            global $content_width;
            if (empty($content_width))
            {
                $content_width = $GLOBALS['content_width'];
            }
            $blogwidth = $embed_size_w ? $embed_size_w : ($content_width ? $content_width : 450);
        }
        catch (Exception $ex)
        {
            
        }

        return $blogwidth;
    }

    public static function cantembedplus()
    {
        echo '<!--[if lte IE 6]> <style type="text/css">.cantembedplus{display:none;}</style><![endif]-->';
    }

    public static function getschemaorgoutput($vidid)
    {
        $schemaorgcode = '';
        try
        {
            $ytapilink = 'https://www.googleapis.com/youtube/v3/videos?id=' . $vidid . '&part=contentDetails,snippet&key=' . self::$alloptions[self::$opt_apikey];


            $apidata = wp_remote_get($ytapilink);
            if (!is_wp_error($apidata))
            {
                $raw = wp_remote_retrieve_body($apidata);
                if (!empty($raw))
                {
                    $json = json_decode($raw, true);
                    if (is_array($json))
                    {
                        $_name = esc_attr(sanitize_text_field(str_replace("@", "&#64;", $json['items'][0]['snippet']['title'])));
                        $_description = esc_attr(sanitize_text_field(str_replace("@", "&#64;", $json['items'][0]['snippet']['description'])));
                        $_thumbnailUrl = esc_url("http://i.ytimg.com/vi/" . $vidid . "/0.jpg");
                        $_duration = $json['items'][0]['contentDetails']['duration']; // "T0H9M35S" "PT9M35S"
                        $_uploadDate = sanitize_text_field($json['items'][0]['snippet']['publishedAt']); // "2014-10-03T15:30:12.000Z"

                        $schemaorgcode = '<span itemprop="video" itemscope itemtype="http://schema.org/VideoObject">';
                        $schemaorgcode .= '<meta itemprop="embedURL" content="http://www.youtube.com/embed/' . $vidid . '">';
                        $schemaorgcode .= '<meta itemprop="name" content="' . $_name . '">';
                        $schemaorgcode .= '<meta itemprop="description" content="' . $_description . '">';
                        $schemaorgcode .= '<meta itemprop="thumbnailUrl" content="' . $_thumbnailUrl . '">';
                        $schemaorgcode .= '<meta itemprop="duration" content="' . $_duration . '">';
                        $schemaorgcode .= '<meta itemprop="uploadDate" content="' . $_uploadDate . '">';
                        $schemaorgcode .= '</span>';
                    }
                }
            }
        }
        catch (Exception $ex)
        {
            
        }
        return $schemaorgcode;
    }

    public static function secondsToDuration($seconds)
    {
        $remaining = $seconds;
        $parts = array();
        $multipliers = array(
            'hours' => 3600,
            'minutes' => 60,
            'seconds' => 1
        );

        foreach ($multipliers as $type => $m)
        {
            $parts[$type] = (int) ($remaining / $m);
            $remaining -= ($parts[$type] * $m);
        }

        return $parts;
    }

    public static function formatDuration($parts)
    {
        $default = array(
            'hours' => 0,
            'minutes' => 0,
            'seconds' => 0
        );

        extract(array_merge($default, $parts));

        return "T{$hours}H{$minutes}M{$seconds}S";
    }

    public static function do_ogvideo()
    {
        global $wp_query;
        $the_content = $wp_query->post->post_content;
        $matches = Array();
        $ismatch = preg_match_all(self::$justurlregex, $the_content, $matches);

        if ($ismatch)
        {
            $match = $matches[0][0];

            $link = trim(preg_replace('/&amp;/i', '&', $match));
            $link = preg_replace('/\s/', '', $link);
            $link = trim(str_replace(self::$badentities, self::$goodliterals, $link));
            $link = str_replace('/v/', '?v=', $link);

            $linkparamstemp = explode('?', $link);

            $linkparams = array();
            if (count($linkparamstemp) > 1)
            {
                $linkparams = self::keyvalue($linkparamstemp[1], true);
            }
            if (strpos($linkparamstemp[0], 'youtu.be') !== false && !isset($linkparams['v']))
            {
                $vtemp = explode('/', $linkparamstemp[0]);
                $linkparams['v'] = array_pop($vtemp);
            }
            ?>
            <meta property="og:type" content="video">
            <meta property="og:video" content="https://www.youtube.com/v/<?php echo $linkparams['v']; ?>?autohide=1&amp;version=3">
            <meta property="og:video:type" content="application/x-shockwave-flash">
            <meta property="og:video:width" content="480">
            <meta property="og:video:height" content="360">
            <meta property="og:image" content="https://img.youtube.com/vi/<?php echo $linkparams['v']; ?>/0.jpg">
            <?php
        }
    }

    public static function embedplus_plugin_menu()
    {
        if (self::$alloptions[self::$opt_pro] && strlen(trim(self::$alloptions[self::$opt_pro])) > 0)
        {
            add_menu_page('EmbedPlus Global Settings', 'EmbedPlus PRO', 'manage_options', 'embedplus-official-options', 'EmbedPlusOfficialPlugin::embedplus_show_options', plugins_url('images/epicon.png', __FILE__), '10.000592854349');
            //add_menu_page('EmbedPlus Video Analytics Dashboard', 'PRO Analytics', 'manage_options', 'embedplus-video-analytics-dashboard', 'EmbedPlusOfficialPlugin::epstats_show_options', plugins_url('images/epstats16.png', __FILE__), '10.000692884349');
            add_submenu_page('embedplus-official-options', '', '', 'manage_options', 'embedplus-official-options', 'EmbedPlusOfficialPlugin::embedplus_show_options');
            add_submenu_page('embedplus-official-options', 'EmbedPlus Video Analytics Dashboard', 'PRO Analytics', 'manage_options', 'embedplus-video-analytics-dashboard', 'EmbedPlusOfficialPlugin::epstats_show_options');
        }
        else
        {
            add_menu_page('EmbedPlus Global Settings', 'EmbedPlus Free', 'manage_options', 'embedplus-official-options', 'EmbedPlusOfficialPlugin::embedplus_show_options', plugins_url('images/epicon.png', __FILE__), '10.000592854349');
            add_submenu_page('embedplus-official-options', '', '', 'manage_options', 'embedplus-official-options', 'EmbedPlusOfficialPlugin::embedplus_show_options');
            add_submenu_page('embedplus-official-options', 'EmbedPlus PRO', 'EmbedPlus PRO', 'manage_options', 'embedplus-video-analytics-dashboard', 'EmbedPlusOfficialPlugin::epstats_show_options');
        }

        add_options_page('EmbedPlus Global Settings', 'EmbedPlus', 'manage_options', 'embedplus-official-options', 'EmbedPlusOfficialPlugin::embedplus_show_options');
    }

    public static function epstats_show_options()
    {
        if (!current_user_can('manage_options'))
        {
            wp_die(__('You do not have sufficient permissions to access this page.'));
        }
        ?>
        <style type="text/css">
            .epicon { width: 20px; height: 20px; vertical-align: middle; padding-right: 5px;}
            .epindent {padding-left: 25px;}
            iframe.shadow {-webkit-box-shadow: 0px 0px 20px 0px #000000; box-shadow: 0px 0px 20px 0px #000000;}
        </style>
        <?php
        $thishost = (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : "");
        $thiskey = self::$alloptions[self::$opt_pro];

        $dashurl = self::$epbase . '/dashboard/easy-video-analytics-seo.aspx?ref=protab&domain=' . $thishost . '&prokey=' . $thiskey;
        ?>
        <div class="wrap">
            <?php
            if (self::$alloptions[self::$opt_pro] && strlen(trim(self::$alloptions[self::$opt_pro])) > 0)
            {
                //// header
                echo "<h2>" . '<img src="' . plugins_url('images/epicon.png', __FILE__) . '" /> ' . __('YouTube Analytics Dashboard') . "</h2>";
                echo '<p><i>Logging you in below... (You can also <a class="button-primary" target="_blank" href="' . $dashurl . '">click here</a> to launch your dashboard in a new tab)</i></p>';
            }
            else
            {
                //// header
                echo "<h2>" . '<img src="' . plugins_url('images/epicon.png', __FILE__) . '" /> ' . __('EmbedPlus PRO') . "</h2><p class='bold orange'>This tab is here to provide direct access to analytics. Graphs and other data about your site will show below after you activate PRO.</p><br>";
            }


            // settings form
            ?>
            <br>

            <iframe class="shadow" src="<?php echo $dashurl; ?>" width="1030" height="2000" scrolling="auto"></iframe>
        </div>
        <?php
    }

    public static function keyvalue($qry, $includev)
    {
        $ytvars = explode('&', $qry);
        $ytkvp = array();
        foreach ($ytvars as $k => $v)
        {
            $kvp = explode('=', $v);
            if (count($kvp) == 2 && ($includev || strtolower($kvp[0]) != 'v'))
            {
                $ytkvp[$kvp[0]] = $kvp[1];
            }
        }

        return $ytkvp;
    }

    public static function custom_admin_pointers_check()
    {
        $admin_pointers = self::custom_admin_pointers();
        foreach ($admin_pointers as $pointer => $array)
        {
            if ($array['active'])
                return true;
        }
    }

    public static function custom_admin_pointers_footer()
    {
        $admin_pointers = self::custom_admin_pointers();
        ?>
        <script type="text/javascript">
            /* <![CDATA[ */
            (function($) {
        <?php
        foreach ($admin_pointers as $pointer => $array)
        {
            if ($array['active'])
            {
                ?>
                        var wpajaxurl = "<?php echo admin_url('admin-ajax.php') ?>";
                        if (window.location.toString().indexOf('https://') == 0)
                        {
                            wpajaxurl = wpajaxurl.replace("http://", "https://");
                        }
                        $('<?php echo $array['anchor_id']; ?>').pointer({
                            content: '<?php echo $array['content']; ?>',
                            position: {
                                edge: '<?php echo $array['edge']; ?>',
                                align: '<?php echo $array['align']; ?>'
                            },
                            close: function() {
                                $.post(wpajaxurl, {
                                    pointer: '<?php echo $pointer; ?>',
                                    action: 'dismiss-wp-pointer'
                                });
                            }
                        }).pointer('open');
                <?php
            }
        }
        ?>
            })(jQuery);
            /* ]]> */
        </script>
        <?php
    }

    public static function custom_admin_pointers()
    {
        $dismissed = explode(',', (string) get_user_meta(get_current_user_id(), 'dismissed_wp_pointers', true));
        $version = str_replace('.', '_', self::$version); // replace all periods in 1.0 with an underscore
        $prefix = 'custom_admin_pointers' . $version . '_';

        $new_pointer_content = '<h3>' . __('Plugin Update') . '</h3>';
        $new_pointer_content .= '<p>';
            
        $new_pointer_content .= __('<b>IMPORTANT NOTE:</b> Unfortunately, Google has deprecated an important API that allowed us to provide several of the advanced features of this plugin like 3rd-party annotations, chapters, and clickable links. We strongly recommend that you install our other <a target="_blank" href="https://wordpress.org/plugins/youtube-embed-plus/">popular YouTube plugin for embedding mobile-friendly HTML5 videos, galleries, and playlists</a>.  By just checking a box in its settings, you will even <a href="https://www.embedplus.com/migrate-advanced-youtube-embed.aspx" target="_blank">automatically migrate your past embeds to the HTML5 player</a>. (Your video start times and end times will be ported, but your 3rd-party annotations, chapters, and clickable links cannot be migrated however.) If you already have a Pro license key, you can use it to activate the Pro features of the recommended plugin at no extra charge.');

        $new_pointer_content .= '</p>';


        return array(
            $prefix . 'new_items' => array(
                'content' => $new_pointer_content,
                'anchor_id' => 'a.toplevel_page_embedplus-official-options',
                'edge' => 'top',
                'align' => 'left',
                'active' => (!in_array($prefix . 'new_items', $dismissed) )
            ),
        );
    }

    public static function my_embedplus_pro_record()
    {
        $result = array();
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')
        {
            $tmppro = preg_replace('/[^A-Za-z0-9-]/i', '', $_REQUEST[self::$opt_pro]);
            $new_options = array();
            $new_options[self::$opt_pro] = $tmppro;
            $all = get_option(self::$opt_alloptions);
            $all = $new_options + $all;
            update_option(self::$opt_alloptions, $all);

            if (strlen($tmppro) > 0)
            {
                $result['type'] = 'success';
            }
            else
            {
                $result['type'] = 'error';
            }
            echo json_encode($result);
        }
        else
        {
            $result['type'] = 'error';
            header("Location: " . $_SERVER["HTTP_REFERER"]);
        }
        die();
    }

    public static function embedplus_show_options()
    {

        if (!current_user_can('manage_options'))
        {
            wp_die(__('You do not have sufficient permissions to access this page.'));
        }

        // variables for the field and option names 
        $embedplus_submitted = 'embedplus_submitted';
        $pro_submitted = 'pro_submitted';

        $all = get_option(self::$opt_alloptions);

        // See if the user has posted us some information
        // If they did, this hidden field will be set to 'Y'
        if (isset($_POST[$embedplus_submitted]) && $_POST[$embedplus_submitted] == 'Y')
        {
            // Read their posted values
            $new_options = array();

            $new_options[self::$opt_enhance_youtube] = isset($_POST[self::$opt_enhance_youtube]) && $_POST[self::$opt_enhance_youtube] == (true || 'on') ? 1 : 0;
            $new_options[self::$opt_show_react] = isset($_POST[self::$opt_show_react]) && $_POST[self::$opt_show_react] == (true || 'on') ? 1 : 0;
            $new_options[self::$opt_auto_hd] = isset($_POST[self::$opt_auto_hd]) && $_POST[self::$opt_auto_hd] == (true || 'on') ? 1 : 0;
            $new_options[self::$opt_show_ann] = isset($_POST[self::$opt_show_ann]) && $_POST[self::$opt_show_ann] == (true || 'on') ? 1 : 3;
            $new_options[self::$opt_sweetspot] = isset($_POST[self::$opt_sweetspot]) && $_POST[self::$opt_sweetspot] == (true || 'on') ? 1 : 0;
            $new_options[self::$opt_emb] = isset($_POST[self::$opt_emb]) && $_POST[self::$opt_emb] == (true || 'on') ? 0 : 1;
            $new_options[self::$opt_lean] = isset($_POST[self::$opt_lean]) && $_POST[self::$opt_lean] == (true || 'on') ? 1 : 0;
            $new_options[self::$opt_oldspacing] = isset($_POST[self::$opt_oldspacing]) && $_POST[self::$opt_oldspacing] == (true || 'on') ? 1 : 0;
            $new_options[self::$opt_schemaorg] = isset($_POST[self::$opt_schemaorg]) && $_POST[self::$opt_schemaorg] == (true || 'on') ? 1 : 0;
            $new_options[self::$opt_ogvideo] = isset($_POST[self::$opt_ogvideo]) && $_POST[self::$opt_ogvideo] == (true || 'on') ? 1 : 0;
            $new_options[self::$opt_ssl] = isset($_POST[self::$opt_ssl]) && $_POST[self::$opt_ssl] == (true || 'on') ? 1 : 0;

            $_apikey = '';
            try
            {
                $_apikey = trim(str_replace(array(' ', "'", '"'), array('', '', ''), strip_tags($_POST[self::$opt_apikey])));
            }
            catch (Exception $ex)
            {
                $_apikey = '';
            }
            $new_options[self::$opt_apikey] = $_apikey;


            $all = $new_options + $all;

            // Save the posted value in the database
            update_option(self::$opt_alloptions, $all);

            // Put a settings updated message on the screen
            ?>
            <div class="updated"><p><strong><?php _e('Settings saved.'); ?></strong></p></div>
            <?php
        }

        // Now display the settings editing screen
        ?>
        <style type="text/css">
            .epicon { width: 20px; height: 20px; vertical-align: middle; padding-right: 5px;}
            #epform p { line-height: 20px; }
            .epindent {padding-left: 25px;}
            .epsmalltext {font-style: italic;}
            #epform ul li, ul.reglist li {margin-left: 30px; list-style: disc outside none;}
            .orange {color: #f85d00;}
            .bold {font-weight: bold;}
            .grey{color: #888888;}
            iframe.shadow {-webkit-box-shadow: 0px 0px 20px 0px #000000; box-shadow: 0px 0px 20px 0px #000000;}
            .smallnote {font-style: italic; color: #888888; font-size: 11px;}
            #nonprosupport {border-radius: 15px; padding: 5px 10px 10px 10px;  border: 3px solid #ff6655; width: 800px;}
            input[type=checkbox] {border: 1px solid #000000;}
            input[disabled] {border: 1px dotted #666666;}
            a.goprolink {font-weight: bold; color: #f85d00;}

            .ssschema {float: right; width: 350px; height: auto; margin-right: 10px;}
            .ssfb {float: right; width: 300px; height: auto; margin: 0 10px;}
            .hideallnote {float: right; width: 300px; height: auto; margin: 0px 10px;}
            .clearboth {clear: both;}
            #boxschemaorg {font-weight: bold; padding: 7px 0;  <?php echo $all[self::$opt_schemaorg] ? 'display: block;' : 'display: none;' ?>}
            .apikey-msg {display: inline-block; width: 45%; vertical-align: top;}
            .apikey-video{margin-left: 3%; display: inline-block; width: 50%; position: relative; padding-top: 29%}
            .apikey-video iframe{display: block; width: 100%; height: 100%; position: absolute; top: 0; left: 0;}
            .upgchecks { padding: 10px 20px; border-radius: 15px; border: 1px dotted #777777; background-color: #fcfcfc; }
            .orange.smallnote {color: #f85d00;}
            .strike {text-decoration: line-through;}
            .ep-alert {padding: 15px 25px; border-radius: 20px; background-color: #ffdddd;}
        </style>
        <div class="wrap" style="max-width: 1000px;">

            <p class="ep-alert">
                <b>IMPORTANT NOTE:</b> Unfortunately, Google has deprecated an important API that allowed us to provide several of the advanced features of this plugin like 3rd-party annotations, chapters, and clickable links. We strongly recommend that you install our other <a target="_blank" href="https://wordpress.org/plugins/youtube-embed-plus/">popular YouTube plugin for embedding mobile-friendly HTML5 videos, galleries, and playlists</a>.  By just checking a box in its settings, you will even <a href="https://www.embedplus.com/migrate-advanced-youtube-embed.aspx" target="_blank">automatically migrate your past embeds to the HTML5 player</a>. (Your video start times and end times will be ported, but your 3rd-party annotations, chapters, and clickable links cannot be migrated however.) If you already have a Pro license key, you can use it to activate the Pro features of the recommended plugin at no extra charge.                
            </p>
            
            <?php
            $haspro = ($all[self::$opt_pro] && strlen(trim($all[self::$opt_pro])) > 0);

            if ($haspro)
            {
                echo "<h2>" . '<img src="' . plugins_url('images/epicon.png', __FILE__) . '" /> ' . __('Thank you for going PRO.');
                echo ' &nbsp;<input type="submit" name="showkey" class="button-primary" style="vertical-align: 15%;" id="showprokey" value="Show my PRO key" />';
                echo "</h2>";
                ?>
                <?php
            }
            else
            {
                echo "<h2>" . '<img src="' . plugins_url('images/epicon.png', __FILE__) . '" /> ' . __('Go PRO') . "</h2>";
                ?>
                <span class="orange bold">
                    PRO users help keep new features coming and our coffee cups filled. Go PRO and <a class="button-primary" href="<?php echo self::$epbase ?>/dashboard/easy-video-analytics-seo.aspx?ref=protab" target="_blank">get these perks in return &raquo;</a>
                </span>
                <?php
            }
            ?>
            <div class="epindent">


                <form name="form2" method="post" action="" id="epform2">
                    <input type="hidden" name="<?php echo $pro_submitted; ?>" value="Y">

                    <p class="submit submitpro" <?php if ($haspro) echo 'style="display: none;"' ?>>
                        <label for="opt_pro"><?php _e('Enter PRO key:') ?></label>
                        <input style="box-shadow: 0px 0px 5px 0px #1870D5; width: 270px;" name="<?php echo self::$opt_pro; ?>" id="opt_pro" value="<?php echo $all[self::$opt_pro]; ?>" type="text">

                        <input type="submit" name="Submit" class="button-primary" id="prokeysubmit" value="<?php _e('Save Key') ?>" /> &nbsp;
                        <span style="display: none;" id="prokeyloading" class="orange bold">Verifying...</span>
                        <span  class="orange bold" style="display: none;" id="prokeysuccess">Success! Please refresh this page.</span>
                        <span class="orange bold" style="display: none;" id="prokeyfailed">Sorry, that seems to be an invalid key.</span>
                    </p>

                </form>

            </div>

            <?php
            // header

            echo "<h2>" . '<img src="' . plugins_url('images/epicon.png', __FILE__) . '" /> ' . __('EmbedPlus Global Settings') . "</h2>";

            // settings form
            ?>

            <div class="epindent">
                <form name="form1" method="post" action="" id="epform">
                    <input type="hidden" name="<?php echo $embedplus_submitted; ?>" value="Y">
                    <p>
                        <?php
                        _e("This plugin automatically converts YouTube URLs that are on their own line, in plain text, to advanced EmbedPlus video embeds. All you have to do is paste the YouTube URL in the editor (example: <code>http://www.youtube.com/watch?v=YVvn8dpSAt0</code>), and:");
                        ?>
                    <ul class="reglist">
                        <li>Make sure the url is really on its own line by itself</li>
                        <li>Make sure the url is <strong>not</strong> an active hyperlink (i.e., it should just be plain text). Otherwise, highlight the url and click the "unlink" button in your editor: <img src="<?php echo plugins_url('images/unlink.png', __FILE__) ?>"/></li>
                        <li>Make sure you did <strong>not</strong> format or align the url in any way. If your url still appears in your actual post instead of a video, highlight it and click the "remove formatting" button (formatting can be invisible sometimes): <img src="<?php echo plugins_url('images/erase.png', __FILE__) ?>"/></li>
                    </ul>       
                    <?php
                    _e("This plugin can make those \"auto-embeds\" display the enhanced player if you check the first option below. "
                            . (get_option('embed_autourls') ? "" : " <strong>Make sure that <strong><a href=\"/wp-admin/options-media.php\">Settings &raquo; Media &raquo; Embeds &raquo; Auto-embeds</a></strong> is checked too.</strong>"));
                    ?>
                    </p>
                    <p>
                        <input name="<?php echo self::$opt_enhance_youtube; ?>" id="<?php echo self::$opt_enhance_youtube; ?>" <?php checked($all[self::$opt_enhance_youtube], 1); ?> type="checkbox" class="checkbox">
                        <label for="<?php echo self::$opt_enhance_youtube; ?>"><img class="epicon" src="<?php echo WP_PLUGIN_URL; ?>/embedplus-for-wordpress/images/epicon.png"/> <?php _e('Use the customizable player for all YouTube videos') ?></label>
                    </p>
                    <p>
                        <input name="<?php echo self::$opt_oldspacing; ?>" id="<?php echo self::$opt_oldspacing; ?>" <?php checked($all[self::$opt_oldspacing], 1); ?> type="checkbox" class="checkbox">
                        <label for="<?php echo self::$opt_oldspacing; ?>"><img class="epicon" src="<?php echo WP_PLUGIN_URL; ?>/embedplus-for-wordpress/images/oldspacing.jpg"/>
                            Continue the spacing style from version 4.0 and older. Those versions required you to manually add spacing above and below your video. Unchecking this will automatically add the spacing for you.
                            <i>(Ignore this option if you installed this plugin after Jan 2014)</i>
                        </label>
                    </p>
                    <p>
                        <input disabled name="<?php echo self::$opt_auto_hd; ?>" id="<?php echo self::$opt_auto_hd; ?>" <?php checked($all[self::$opt_auto_hd], 1); ?> type="checkbox" class="checkbox">
                        <label for="<?php echo self::$opt_auto_hd; ?>"><img class="epicon" src="<?php echo WP_PLUGIN_URL; ?>/embedplus-for-wordpress/images/hd.jpg"/>
                            <span class="strike"><?php _e('Automatically make all videos HD quality.') ?></span>
                            <b>NOTE:</b> YouTube has deprecated this unofficially supported option. 
                        </label>
                    </p>
                    <p>
                        <input name="<?php echo self::$opt_show_ann; ?>" id="<?php echo self::$opt_show_ann; ?>" <?php checked($all[self::$opt_show_ann], 1); ?> type="checkbox" class="checkbox">
                        <label for="<?php echo self::$opt_show_ann; ?>"><img class="epicon" src="<?php echo WP_PLUGIN_URL; ?>/embedplus-for-wordpress/images/show_ann.png"/> <?php _e("Show the video creator's annotations, if any. (uncheck to hide)") ?></label>
                    </p>
                    <p>
                        <input name="<?php echo self::$opt_sweetspot; ?>" id="<?php echo self::$opt_sweetspot; ?>" <?php checked($all[self::$opt_sweetspot], 1); ?> type="checkbox" class="checkbox">
                        <label for="<?php echo self::$opt_sweetspot; ?>"><img class="epicon" src="<?php echo WP_PLUGIN_URL; ?>/embedplus-for-wordpress/images/ssm.jpg"/> <?php _e('Enable <a href="' . self::$epbase . '/whysearchhere.aspx" target="_blank">Sweetspot Marking</a> for the next/previous buttons') ?></label>            
                    </p>
                    <p>
                        <input name="<?php echo self::$opt_ssl; ?>" id="<?php echo self::$opt_ssl; ?>" <?php checked($all[self::$opt_ssl], '1'); ?> type="checkbox" class="checkbox">
                        <label for="<?php echo self::$opt_ssl; ?>">
                            <img class="epicon" src="<?php echo WP_PLUGIN_URL; ?>/embedplus-for-wordpress/images/ssl.png"/>
                            HTTPS/SSL Player: Use the secure player for all of your visitors and videos you embed. This will go back and also secure your past embeds as they are loaded on their pages.</label>
                    </p>

                    <?php
                    $eadopt = get_option('embedplusopt_enhance_youtube') !== false;
                    $prostuffmsg = '<div class="upgchecks"><p class="orange smallnote bold">Below are some PRO features. <a href="' . self::$epbase . '/dashboard/easy-video-analytics-seo.aspx?ref=protab" target="_blank">Activate them and more here &raquo;</a></p>';


                    if (!$eadopt)
                    {
                        echo $prostuffmsg;
                        if ($haspro)
                        {
                            ?>

                            <p id="chkhideall">
                                <img class="hideallnote" src="<?php echo plugins_url('images/samplehideallnote.jpg', __FILE__) ?>" />
                                <input name="<?php echo self::$opt_lean; ?>" id="<?php echo self::$opt_lean; ?>" <?php checked($all[self::$opt_lean], '1'); ?> type="checkbox" class="checkbox">

                                <label for="<?php echo self::$opt_lean; ?>">
                                    <img class="epicon" src="<?php echo WP_PLUGIN_URL; ?>/embedplus-for-wordpress/images/hideall.png"/>
                                    Hides all extra control buttons. Checking this option will hide the extra buttons which can allow more emphasis on your annotations and clickable links. (View sample on the right)
                                </label>
                            </p>
                            <div class="clearboth"></div>
                            <p class="panshowreact clearboth">
                                <input name="<?php echo self::$opt_show_react; ?>" id="<?php echo self::$opt_show_react; ?>" <?php checked($all[self::$opt_show_react], 1); ?> type="checkbox" class="checkbox">
                                <label for="<?php echo self::$opt_show_react; ?>"><img class="epicon" src="<?php echo WP_PLUGIN_URL; ?>/embedplus-for-wordpress/images/convo.jpg"/> <?php _e('Display Social Media Reactions (This is recommended so your visitors can see web discussions for each video right from your blog)') ?></label>            
                            </p>

                            <?php
                        }
                        else
                        {
                            ?>

                            <p>
                                <img class="hideallnote" src="<?php echo plugins_url('images/samplehideallnote.jpg', __FILE__) ?>" />
                                <input type="checkbox" disabled class="checkbox">
                                <img class="epicon" src="<?php echo WP_PLUGIN_URL; ?>/embedplus-for-wordpress/images/hideall.png"/>
                                Hides all extra control buttons. Checking this option will hide the extra buttons which can allow more emphasis on your annotations and clickable links. (<a class="goprolink"  target="_blank" href="<?php echo self::$epbase ?>/dashboard/easy-video-analytics-seo.aspx?ref=protab" title="">PRO &raquo;</a> View sample on the right)</span>
                            </p>
                            <div class="clearboth"></div>
                            <p>
                                <input type="checkbox" disabled class="checkbox">
                                <img class="epicon" src="<?php echo WP_PLUGIN_URL; ?>/embedplus-for-wordpress/images/convo.jpg"/> Hide Social Media Reactions (This button shows web discussions for each video right from your blog) (<a class="goprolink"  target="_blank" href="<?php echo self::$epbase ?>/dashboard/easy-video-analytics-seo.aspx?ref=protab" title="">PRO &raquo;</a>)
                            </p>
                            <?php
                        }
                    }
                    else
                    {
                        if ($haspro)
                        {
                            ?>
                            <p class="panshowreact">
                                <input name="<?php echo self::$opt_show_react; ?>" id="<?php echo self::$opt_show_react; ?>" <?php checked($all[self::$opt_show_react], 1); ?> type="checkbox" class="checkbox">
                                <label for="<?php echo self::$opt_show_react; ?>"><img class="epicon" src="<?php echo WP_PLUGIN_URL; ?>/embedplus-for-wordpress/images/convo.jpg"/> <?php _e('Display Social Media Reactions (This is recommended so your visitors can see web discussions for each video right from your blog)') ?></label>            
                            </p>
                            <?php echo $prostuffmsg; ?>
                            <p id="chkhideall">
                                <img class="hideallnote" src="<?php echo plugins_url('images/samplehideallnote.jpg', __FILE__) ?>" />
                                <input name="<?php echo self::$opt_lean; ?>" id="<?php echo self::$opt_lean; ?>" <?php checked($all[self::$opt_lean], '1'); ?> type="checkbox" class="checkbox">

                                <label for="<?php echo self::$opt_lean; ?>">
                                    <img class="epicon" src="<?php echo WP_PLUGIN_URL; ?>/embedplus-for-wordpress/images/hideall.png"/>
                                    Hides all extra control buttons. Checking this option will hide the extra buttons which can allow more emphasis on your annotations and clickable links. (View sample on the right)
                                </label>
                            </p>
                            <div class="clearboth"></div>
                            <?php
                        }
                        else
                        {
                            ?>
                            <p class="panshowreact">
                                <input name="<?php echo self::$opt_show_react; ?>" id="<?php echo self::$opt_show_react; ?>" <?php checked($all[self::$opt_show_react], 1); ?> type="checkbox" class="checkbox">
                                <label for="<?php echo self::$opt_show_react; ?>"><img class="epicon" src="<?php echo WP_PLUGIN_URL; ?>/embedplus-for-wordpress/images/convo.jpg"/> <?php _e('Display Social Media Reactions (This is recommended so your visitors can see web discussions for each video right from your blog)') ?></label>            
                            </p>
                            <?php echo $prostuffmsg; ?>
                            <p>
                                <img class="hideallnote" src="<?php echo plugins_url('images/samplehideallnote.jpg', __FILE__) ?>" />
                                <input type="checkbox" disabled class="checkbox">
                                <img class="epicon" src="<?php echo WP_PLUGIN_URL; ?>/embedplus-for-wordpress/images/hideall.png"/>
                                Hides all extra control buttons. Checking this option will hide the extra buttons which can allow more emphasis on your annotations and clickable links. (<a class="goprolink"  target="_blank" href="<?php echo self::$epbase ?>/dashboard/easy-video-analytics-seo.aspx?ref=protab" title="">PRO &raquo;</a> View sample on the right)</span>
                            </p>
                            <div class="clearboth"></div>
                            <?php
                        }
                    }

                    ////////////////////////////////////////////

                    if ($haspro)
                    {
                        ?>

                        <p class="panhideget">
                            <input name="<?php echo self::$opt_emb; ?>" id="<?php echo self::$opt_emb; ?>" <?php checked($all[self::$opt_emb], '0'); ?> type="checkbox" class="checkbox">
                            <label for="<?php echo self::$opt_emb; ?>">
                                <img class="epicon" src="<?php echo WP_PLUGIN_URL; ?>/embedplus-for-wordpress/images/get.jpg"/> <?php _e('Hide GET button') ?></label>
                        </p>
                        <p >
                            <input name="<?php echo self::$opt_schemaorg; ?>" id="<?php echo self::$opt_schemaorg; ?>" <?php checked($all[self::$opt_schemaorg], 1); ?> type="checkbox" class="checkbox">
                            <img class="epicon" src="<?php echo WP_PLUGIN_URL; ?>/embedplus-for-wordpress/images/videoseoicon.png"/> 
                            <label for="<?php echo self::$opt_schemaorg; ?>">
                                Automatically add Google, Bing, and Yahoo friendly markup so that your pages with video embeds can be indexed to have a greater chance of showing up in search engine results for those particular videos, even if you aren't the owner.
                            </label>
                            <span id="boxschemaorg">
                                <span class="apikey-msg">
                                    The video SEO tags include data like the title, description, and thumbnail information of each video you embed.  This plugin automatically extracts this data directly from YouTube using the version 3 API,
                                    which will soon replace the version 2 API. This particular API version requires that you obtain an API key so that YouTube can authenticate the requests.  Don't worry, it's an easy process.  
                                    Just <a href="https://developers.google.com/youtube/registering_an_application" target="_blank">click this link &raquo;</a> and follow the video to the right to get your API key. Then paste it in the box below, and click the "Save Changes" button:
                                    <br>
                                    <span style="vertical-align: middle; display: inline-block;">
                                        YouTube API Key: <input type="text" name="<?php echo self::$opt_apikey; ?>" id="<?php echo self::$opt_apikey; ?>" value="<?php echo trim($all[self::$opt_apikey]); ?>" class="textinput" style="width: 200px;">
                                    </span>
                                </span>
                                <span class="apikey-video">
                                    <iframe width="384" height="216" src="https://www.youtube.com/embed/2vmBACVETf4?rel=0" frameborder="0" allowfullscreen></iframe>
                                </span>
                            </span>
                        </p>
                        <p id="jumpfb">
                            <br>
                            <img class="ssfb" src="<?php echo plugins_url('images/ssfb.jpg', __FILE__) ?>" />
                            <input name="<?php echo self::$opt_ogvideo; ?>" id="<?php echo self::$opt_ogvideo; ?>" <?php checked($all[self::$opt_ogvideo], '1'); ?> type="checkbox" class="checkbox">
                            <label for="<?php echo self::$opt_ogvideo; ?>">
                                <img class="epicon" src="<?php echo WP_PLUGIN_URL; ?>/embedplus-for-wordpress/images/facebook.png"/>
                                Facebook Open Graph Markup: Automatically add Open Graph markup on your pages with YouTube embeds to enhance Facebook sharing and discovery of the pages.  Your shared pages, for example, will also display embedded video thumbnails on Facebook Timelines (See example on the right).                            
                            </label>
                        </p>

                        <?php
                    }
                    else
                    {
                        ?>

                        <p>
                            <input type="checkbox" disabled class="checkbox">
                            <img class="epicon" src="<?php echo WP_PLUGIN_URL; ?>/embedplus-for-wordpress/images/get.jpg"/> Hide GET button (<a class="goprolink"  target="_blank" href="<?php echo self::$epbase ?>/dashboard/easy-video-analytics-seo.aspx?ref=protab" title="">PRO &raquo;</a>)</span>
                        </p>
                        <p>
                            <input type="checkbox" disabled class="checkbox">
                            <img class="epicon" src="<?php echo WP_PLUGIN_URL; ?>/embedplus-for-wordpress/images/videoseoicon.png"/> 
                            Automatically add Google, Bing, and Yahoo friendly markup so that your pages with video embeds can be indexed to have a greater chance of showing up in search engine results for those particular videos, even if you aren't the owner.
                            (<a class="goprolink"  target="_blank" href="<?php echo self::$epbase ?>/dashboard/easy-video-analytics-seo.aspx?ref=protab" title="">PRO &raquo;</a>)
                        </p>
                        <p id="jumpfb">
                            <img class="ssfb" src="<?php echo plugins_url('images/ssfb.jpg', __FILE__) ?>" />
                            <input type="checkbox" disabled class="checkbox">
                            <img class="epicon" src="<?php echo WP_PLUGIN_URL; ?>/embedplus-for-wordpress/images/facebook.png"/>
                            Facebook Open Graph Markup: Automatically add Open Graph markup on your pages with YouTube embeds to enhance Facebook sharing and discovery of the pages.  Your shared pages, for example, will also display embedded video thumbnails on Facebook Timelines (See example on the right).
                            (<a class="goprolink"  target="_blank" href="<?php echo self::$epbase ?>/dashboard/easy-video-analytics-seo.aspx?ref=protab" title="">PRO &raquo;</a>)
                        </p>

                        <div class="clearboth"></div>
                </div>
                <?php
            }
            ?>
            <p class="submit">
                <input type="submit" name="Submit" onclick="return savevalidate()" class="button-primary" value="<?php _e('Save Changes') ?>" />
            </p>
        </div>

        </form>
        <br>
        <?php
        echo "<h2>" . '<img src="' . plugins_url('images/epicon.png', __FILE__) . '" /> ' . __('EmbedPlus Wizard Settings') . "</h2>";
        ?>
        <div class="epindent">
            <?php
            $newtab = self::$epbase . '/wpembedcode.aspx?pluginversion=' . self::$version .
                    '&blogwidth=' . self::calcblogwidth() .
                    '&domain=' . urlencode(isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : "") .
                    '&prokey=' . urlencode(self::$alloptions[self::$opt_pro]) .
                    '&eadopt=' . (get_option('embedplusopt_enhance_youtube') === false ? '0' : '1') .
                    '&external=1' .
                    '&mydefaults=' . urlencode(http_build_query(self::$alloptions));
            ?>
            <p>
                Most of the FREE and PRO settings that this plugin offers are made through its wizard. 
                If your blog's rich-text editor is enabled, you have access to a EmbedPlus wizard button (look for this in your editor: <img class="epicon" src="<?php echo WP_PLUGIN_URL; ?>/embedplus-for-wordpress/images/epicon.png"/>).
                It allows you to override some of the above global defaults. It's also where you create your annotations, chapter markers, and other customizations to a video. If you use the HTML editor instead, you can <a href="<?php echo $newtab ?>" target="_blank">open the wizard in a new tab</a>.
                <br>
                <br>
                <img src="<?php echo plugins_url('images/screenshot-2.jpg', __FILE__) ?>" />
            </p>
        </div>
        <?php echo "<h2>" . '<img src="' . plugins_url('images/epicon.png', __FILE__) . '" />' . " Additional URL Options</h2>" ?>
        <div class="epindent">
            <?php
            _e("<p>If you prefer to override values manually, the following optional values can be added to the YouTube URLs to override default behavior. The URL must be pasted on its own line. Each additional option should begin with '&'</p>");
            _e('<ul>');
            _e("<li><strong>w - Sets the width of your player.</strong> If omitted, the default width will be the width of your theme's content (or your <a href=\"/wp-admin/options-media.php\">WordPress maximum embed size</a>, if set).<em> Example: http://www.youtube.com/watch?v=quwebVjAEJA<strong>&w=500</strong>&h=350</em></li>");
            _e("<li><strong>h - Sets the height of your player.</strong> <em>Example: http://www.youtube.com/watch?v=quwebVjAEJA&w=500<strong>&h=350</strong></em> </li>");
            _e("<li><strong>hd - If set to 1, this makes the video play in HD quality when possible.</strong> <em>Example: http://www.youtube.com/watch?v=quwebVjAEJA&w=500&h=350<strong>&hd=1</strong></em> </li>");
            _e("<li><strong>start - Sets the time (in seconds) to start the video.</strong> <em>Example: http://www.youtube.com/watch?v=quwebVjAEJA&w=500&h=350<strong>&start=20</strong></em> </li>");
            _e("<li><strong>end - Sets the time (in seconds) to end the video.</strong> <em>Example: http://www.youtube.com/watch?v=quwebVjAEJA&w=500&h=350<strong>&end=60</strong></em> </li>");
            _e('</ul>');
            ?>
        </div>



        <?php
        echo "<h2>" . '<img src="' . plugins_url('images/epicon.png', __FILE__) . '" /> ' . __('Priority Support') . "</h2>";
        ?>
        <div class="epindent">
            <p>
                <strong>PRO users:</strong> Below, We've enabled the ability to have priority support with our team.  Use this to get one-on-one help with any issues you might have or to send us suggestions for future features.  We typically respond within minutes during normal work hours.  
            </p>
            <div id="nonprosupport">
                <h3>Support tips for non-PRO users</h3>
                We've found that a common support request has been from users that are pasting video links on single lines, as required, but are not seeing the video embed show up. One of these suggestions is usually the fix:
                <ul class="reglist">
                    <li>Make sure the url is really on its own line by itself</li>
                    <li>Make sure the url is not an active hyperlink (i.e., it should just be plain text). Otherwise, highlight the url and click the "unlink" button in your editor: <img src="<?php echo plugins_url('images/unlink.png', __FILE__) ?>"/>.</li>
                    <li>Make sure you did <strong>not</strong> format or align the url in any way. If your url still appears in your actual post instead of a video, highlight it and click the "remove formatting" button (formatting can be invisible sometimes): <img src="<?php echo plugins_url('images/erase.png', __FILE__) ?>"/></li>
                    <li>Finally, there's a slight chance your custom theme is the issue, if you have one. To know for sure, we suggest temporarily switching to one of the default WordPress themes (e.g., "Twenty Thirteen") just to see if your video does appear. If it suddenly works, then your custom theme is the issue. You can switch back when done testing.</li>
                </ul>                
                </p>
            </div>
            <iframe src="<?php echo self::$epbase ?>/dashboard/prosupport.aspx?&prokey=<?php echo $all[self::$opt_pro]; ?>&domain=<?php echo site_url(); ?>" width="500" height="500"></iframe>
        </div>
        <br>

        <!--
        <?php
        echo "<h2>" . '<img src="' . plugins_url('images/epicon.png', __FILE__) . '" /> ' . __("What's next for us? Take this survey.") . "</h2>";
        ?>

        <div class="epindent">
        <div id="surveyMonkeyInfo" style="width:600px;font-size:10px;color:#666;border:1px solid #ccc;padding:4px;"><div><iframe id="sm_e_s" src="http://www.surveymonkey.com/jsEmbed.aspx?sm=fzDp_2bBj61z8oFK7Z7Kd3lQ_3d_3d" width="600" height="900" style="border:0px;padding-bottom:4px;" frameborder="0" allowtransparency="true" ></iframe></div>
        </div>
        </div>
        -->
        <script type="text/javascript">
            function savevalidate()
            {
                var valid = true;
                var alertmessage = '';

                if (jQuery("#<?php echo self::$opt_schemaorg; ?>").is(":checked"))
                {
                    if (!(jQuery.trim(jQuery("#<?php echo self::$opt_apikey; ?>").val()).length > 0))
                    {
                        alertmessage += "Please enter a valid YouTube API key, or uncheck the 'Video SEO Tags' option.";
                        jQuery("#boxschemaorg input").css("background-color", "#ffcccc").css("border", "2px solid #000000");
                        valid = false;
                    }
                }


                if (!valid)
                {
                    alert(alertmessage);
                }
                return valid;
            }


            var prokeyval;
            jQuery(document).ready(function($) {

                jQuery('#<?php echo self::$opt_schemaorg; ?>').change(function()
                {
                    if (jQuery(this).is(":checked"))
                    {
                        jQuery("#boxschemaorg").show(500);
                    }
                    else
                    {
                        jQuery("#boxschemaorg").hide(500);
                    }
                });


                jQuery('#showprokey').click(function() {
                    jQuery('.submitpro').show(500);
                    return false;
                });


                jQuery('#prokeysubmit').click(function() {
                    jQuery(this).attr('disabled', 'disabled');
                    jQuery('#prokeyfailed').hide();
                    jQuery('#prokeysuccess').hide();
                    jQuery('#prokeyloading').show();
                    prokeyval = jQuery('#opt_pro').val();

                    var tempscript = document.createElement("script");
                    tempscript.src = "//www.embedplus.com/dashboard/wordpress-pro-validatejp.aspx?prokey=" + prokeyval;
                    var n = document.getElementsByTagName("head")[0].appendChild(tempscript);
                    setTimeout(function() {
                        n.parentNode.removeChild(n)
                    }, 500);
                    return false;
                });

                window.embedplus_record_prokey = function(good) {
                    var wpajaxurl = "<?php echo admin_url('admin-ajax.php') ?>";
                    if (window.location.toString().indexOf('https://') == 0)
                    {
                        wpajaxurl = wpajaxurl.replace("http://", "https://");
                    }

                    jQuery.ajax({
                        type: "post",
                        dataType: "json",
                        timeout: 30000,
                        url: wpajaxurl,
                        data: {action: 'my_embedplus_pro_record', <?php echo self::$opt_pro; ?>: (good ? prokeyval : "")},
                        success: function(response) {
                            if (response.type == "success") {
                                jQuery("#prokeysuccess").show();
                            }
                            else {
                                jQuery("#prokeyfailed").show();
                            }
                        },
                        error: function(xhr, ajaxOptions, thrownError) {
                            jQuery('#prokeyfailed').show();
                        },
                        complete: function() {
                            jQuery('#prokeyloading').hide();
                            jQuery('#prokeysubmit').removeAttr('disabled');
                        }

                    });

                };
                chkhidefade();
                jQuery('#chkhideall input').change(chkhidefade);

            });

            function chkhidefade()
            {
                if (jQuery('#chkhideall input').is(":checked"))
                {
                    jQuery(".panhideget input, .panshowreact input").prop('checked', false).prop('disabled', true);
                    jQuery(".panhideget, .panshowreact").fadeTo(500, .4);
                }
                else
                {
                    jQuery(".panhideget, .panshowreact").fadeTo(500, 1);
                    jQuery(".panhideget input, .panshowreact input").prop('disabled', false);
                }

            }
        </script>
        <?php
    }

}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//class start
class Add_new_tinymce_btn_EmbedPlus
{

    public $btn_arr;
    public $js_file;

    /*
     * call the constructor and set class variables
     * From the constructor call the functions via wordpress action/filter
     */

    function __construct($seperator, $btn_name, $javascrip_location)
    {
        $this->btn_arr = array("Seperator" => $seperator, "Name" => $btn_name);
        $this->js_file = $javascrip_location;
        add_action('init', array($this, 'add_tinymce_button'));
        add_filter('tiny_mce_version', array($this, 'refresh_mce_version'));
    }

    /*
     * create the buttons only if the user has editing privs.
     * If so we create the button and add it to the tinymce button array
     */

    function add_tinymce_button()
    {
        if (!current_user_can('edit_posts') && !current_user_can('edit_pages'))
            return;
        if (get_user_option('rich_editing') == 'true')
        {
            //the function that adds the javascript
            add_filter('mce_external_plugins', array($this, 'add_new_tinymce_plugin'));
            //adds the button to the tinymce button array
            add_filter('mce_buttons', array($this, 'register_new_button'));
        }
    }

    /*
     * add the new button to the tinymce array
     */

    function register_new_button($buttons)
    {
        array_push($buttons, $this->btn_arr["Seperator"], $this->btn_arr["Name"]);
        return $buttons;
    }

    /*
     * Call the javascript file that loads the
     * instructions for the new button
     */

    function add_new_tinymce_plugin($plugin_array)
    {
        $plugin_array[$this->btn_arr['Name']] = $this->js_file;
        return $plugin_array;
    }

    /*
     * This function tricks tinymce in thinking
     * it needs to refresh the buttons
     */

    function refresh_mce_version($ver)
    {
        $ver += 3;
        return $ver;
    }

}

//class end

register_activation_hook(__FILE__, array('EmbedPlusOfficialPlugin', 'install'));

$embedplusoplg = new EmbedPlusOfficialPlugin();

$embedplusmce = new Add_new_tinymce_btn_EmbedPlus('|', 'embedpluswiz', plugins_url() . '/embedplus-for-wordpress/js/embedplus_mce.js');
//$epstatsmce = new Add_new_tinymce_btn_EmbedPlus('|', 'embedplusstats', plugins_url() . '/embedplus-for-wordpress/js/embedplusstats_mce.js');

add_action('admin_enqueue_scripts', 'embedplus_admin_enqueue_scripts');
add_action("wp_ajax_my_embedplus_pro_record", array('EmbedPlusOfficialPlugin', 'my_embedplus_pro_record'));

function embedplus_admin_enqueue_scripts()
{

    add_action('wp_print_scripts', 'embedplus_output_scriptvars');
    wp_enqueue_style('embedpluswiz', plugins_url() . '/embedplus-for-wordpress/css/embedplus_mce.css');


    if ((get_bloginfo('version') >= '3.3') && EmbedPlusOfficialPlugin::custom_admin_pointers_check())
    {
        add_action('admin_print_footer_scripts', 'EmbedPlusOfficialPlugin::custom_admin_pointers_footer');

        wp_enqueue_script('wp-pointer');
        wp_enqueue_style('wp-pointer');
    }
}

function embedplus_output_scriptvars()
{

    EmbedPlusOfficialPlugin::$scriptsprinted++;
    if (EmbedPlusOfficialPlugin::$scriptsprinted == 1)
    {

        $blogwidth = null;
        try
        {
            $embed_size_w = intval(get_option('embed_size_w'));

            global $content_width;
            if (empty($content_width))
            {
                $content_width = $GLOBALS['content_width'];
            }
            $blogwidth = $embed_size_w ? $embed_size_w : ($content_width ? $content_width : 450);
        }
        catch (Exception $ex)
        {
            
        }

        $epprokey = EmbedPlusOfficialPlugin::$alloptions[EmbedPlusOfficialPlugin::$opt_pro];

        $eadopt = get_option('embedplusopt_enhance_youtube') === false ? '0' : '1';
        ?>
        <script type="text/javascript">
            var epdomain = '<?php echo (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : ""); ?>';
            var epblogwidth = <?php echo $blogwidth; ?>;
            var epprokey = '<?php echo $epprokey; ?>';
            var epbasesite = '<?php echo EmbedPlusOfficialPlugin::$epbase; ?>';
            var epversion = '<?php echo EmbedPlusOfficialPlugin::$version; ?>';
            var epeadopt = '<?php echo $eadopt; ?>';
            var epdefaults = '<?php echo urlencode(http_build_query(EmbedPlusOfficialPlugin::$alloptions)) ?>';

            // Create IE + others compatible event handler
            var epeventMethod = window.addEventListener ? "addEventListener" : "attachEvent";
            var epeventer = window[epeventMethod];
            var epmessageEvent = epeventMethod == "attachEvent" ? "onmessage" : "message";

            // Listen to message from child window
            epeventer(epmessageEvent, function(e)
            {
                var embedcode = "";
                try
                {
                    if (e.data.indexOf("embedplusforwordpress") == 0)
                    {

                        embedcode = e.data.split("|")[1];
                        if (window.tinyMCE !== null && window.tinyMCE.activeEditor !== null && !window.tinyMCE.activeEditor.isHidden())
                        {
                            if (typeof window.tinyMCE.execInstanceCommand !== 'undefined')
                            {
                                window.tinyMCE.execInstanceCommand(
                                        window.tinyMCE.activeEditor.id,
                                        'mceInsertContent',
                                        false,
                                        embedcode);
                            }
                            else
                            {
                                send_to_editor(embedcode);
                            }
                        }
                        else
                        {
                            embedcode = embedcode.replace('<p>', '\n').replace('</p>', '\n');
                            if (typeof QTags.insertContent === 'function')
                            {
                                QTags.insertContent(embedcode);
                            }
                            else
                            {
                                send_to_editor(embedcode);
                            }
                        }
                        //tb_remove();
                        window.tinyMCE.activeEditor.windowManager.close();

                    }
                }
                catch (err)
                {
                    if (typeof console !== 'undefined')
                        console.log(err.message);
                }


            }, false);




        </script>
        <?php
    }
}
