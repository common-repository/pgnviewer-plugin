<?php
/* Plugin Name: PGNViewer
Description:  A simple plugin that allows you to embed ChessTempo's PGNViewer into a post or page to show chess games from PGN format files. USAGE:  While editing a post, create a custom field with key PGN and paste the URL of the PGN file into the Value field.
Version: 0.7
Author: Mikel Larreategi
Author URI: http://eibar.org/blogak/erral
 */


function myplugin_init() {
 $plugin_dir = basename(dirname(__FILE__));
 load_plugin_textdomain( 'pgnviewer-plugin', false, $plugin_dir );
}

add_action('init', 'myplugin_init');

function pgnviewer_js_and_css(){
        wp_enqueue_script("jquery");
	wp_enqueue_script('pgnyui', 'http://chesstempo.com/js/pgnyui.js');
	wp_enqueue_script('pgnviewer', 'http://chesstempo.com/js/pgnviewer.js');
	wp_enqueue_style('pgnviewer-css', plugins_url('css/board-min.css', __FILE__));
}

add_action('wp_enqueue_scripts', 'pgnviewer_js_and_css');


function insert_pgnviewer($content) {
	 global $post;
	 $download_games = __('Download games');
	 $no_javascript = __('Your browser do not support JavaScript. Visit the website to see the games in the website');
	 $chesstempo_pgnviewer = __('ChessTempo PGN Viewer');
	 $out = $content; // get the html of the whole current post/page 
	 $pgnurls = get_post_meta($post->ID, "PGN", false);
	 if ($pgnurls != null) {
           $i = 0;
           foreach ( $pgnurls as $pgnurl) { 
		 $i ++;
		 $div_id = $post->ID . "-" . $i . "-pgn";

		 $template = <<<EOD
<script type="text/javascript">   
   jQuery(document).ready(function(){
    new PgnViewer(   
                  { boardName: "game$div_id" ,
                    pgnFile: "$pgnurl",
                    pieceSet: "merida",   
                    highlightFromTo: true, 
                    showCoordinates: true, 
                    pieceSize: 35, 
                    addVersion: false, 
		    squareColorClass:"-lightgrey"
                   });
    }); 
</script> 
<noscript>$no_javascript</noscript> 
<div class="game"> 
 <div id="game$div_id-container" class="game-container"></div>
 <div class="data-moves-container">
   <div class="game-data"><span id="game$div_id-whitePlayer">&nbsp;</span> (<span id="game$div_id-whiteElo">&nbsp;</span>) - <span id="game$div_id-blackPlayer">&nbsp;</span> (<span id="game$div_id-blackElo">&nbsp;</span>) <br/> <span id="game$div_id-event">&nbsp;</span>&nbsp;(<span id="game$div_id-round">&nbsp;</span>)</div> 
   <div id="game$div_id-moves" class="game-moves"></div> 
 </div>  
 <p class="visualClear game-download"><a href="$pgnurl">$download_games</a><br/><a href="http://chesstempo.com/pgn-viewer.html">$chesstempo_pgnviewer</a></p>
</div>
EOD;
$out = $out . $template;
}
return $out;
}
else{
   return $content;
}
}

add_filter('the_content', 'insert_pgnviewer');
?>
