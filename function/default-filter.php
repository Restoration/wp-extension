<?php
//Script/CSS読み込み
//--------------------------------------------------------------
function default_init(){
	wp_enqueue_style('admin_customize',MY_PLUGIN_DIR.'/css/admin_customize.css');
	wp_enqueue_script('admin_function',MY_PLUGIN_DIR.'/js/admin_function.js',array('jquery'),'1.0.0', true);
}
add_action('init', 'default_init');

