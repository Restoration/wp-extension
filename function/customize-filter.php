<?php
//カスタムポストタイプ
//--------------------------------------------------------------
function create_posttype() {
	register_post_type( 'custom_post',
		array(
			'labels' => array(
			'name' => __( 'カスタムポスト' ),
				'singular_name' => __( 'カスタムポスト' )
			),
			'public' => true,
			'has_archive' => true,
			'rewrite' => array('slug' => 'custom_post'),
		)
	);
}
add_action( 'init', 'create_posttype' );

function add_custom_post_type(){
	$labels = array(
		'name' => _x('カスタムポストタイプ', 'post type general name'),
		'singular_name' => _x('カスタムポストタイプ一覧', 'post type singular name'),
		'add_new' => _x('カスタムポストタイプを投稿する', 'blog'),
		'add_new_item' => __('カスタムポストタイプを投稿する'),
		'edit_item' => __('カスタムポストタイプを編集'),
		'new_item' => __('新しいカスタムポストタイプ'),
		'view_item' => __('カスタムポストタイプを見る'),
		'search_items' => __('カスタムポストタイプを探す'),
		'not_found' =>  __('カスタムポストタイプはありません'),
		'not_found_in_trash' => __('ゴミ箱にカスタムポストタイプはありません'),
		'parent_item_colon' => '',
		'all_items' => __('カスタムポストタイプ一覧')
	);

	$support_array = array('title','author','editor','thumbnail','custom-fields','revisions');
	$args = array(
		'labels' => $labels,
		'public' => true,
		'publicly_queryable' => true,
		'show_ui' => true,
        'show_in_nav_menus'   => true,
        'show_in_admin_bar'   => true,
		'query_var' => true,
		'rewrite' => true,
		'capability_type' => 'custom_post',
		'hierarchical' => false,
		'menu_position' => 5,
		'supports' => $support_array,
		'has_archive' => true,
        'can_export'          => true,
        'has_archive'         => true,
        'exclude_from_search' => false,
        'publicly_queryable'  => true,
	);
	register_post_type('custom_post',$args);

	register_taxonomy(
		'custom_post_cat',
		'custom_post',
		array(
			'hierarchical' => true,
			'update_count_callback' => '_update_post_term_count',
			'label' => 'カスタムポストタイプのカテゴリー',
			'singular_label' => 'カスタムポストタイプのカテゴリー',
			'public' => true,
			'show_ui' => true,
		)
	);
}
add_action('init', 'add_custom_post_type', 0);


//ロゴ変更
//--------------------------------------------------------------
function custom_login_logo() {
    echo '<style type="text/css">h1 a { background: url('.get_bloginfo('template_directory').'/images/login-logo.png) 50% 50% no-repeat !important; }</style>';
}
//add_action('login_head', 'custom_login_logo');


// バージョン更新を非表示にする
//--------------------------------------------------------------
add_filter('pre_site_transient_update_core', '__return_zero');
remove_action('wp_version_check', 'wp_version_check');// APIによるバージョンチェックの通信をさせない
remove_action('admin_init', '_maybe_update_core');


//ダッシュボードの不要な widget を消す
//--------------------------------------------------------------
remove_all_actions('wp_dashboard_setup');
function remove_dashboard_widgets() {
	global $wp_meta_boxes;
	unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_recent_comments']);
	unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_incoming_links']);
	unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_plugins']);
	unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_quick_press']);
	unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_recent_drafts']);
	unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_primary']);
	unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_secondary']);
}
add_action('wp_dashboard_setup', 'remove_dashboard_widgets' );


// 投稿画面の項目を非表示にする
//--------------------------------------------------------------
function remove_default_post_screen_metaboxes() {
	if (!current_user_can('level_10')) { // level10以下のユーザーの場合メニューをremoveする
		remove_meta_box( 'postcustom','post','normal' ); // カスタムフィールド
		remove_meta_box( 'postexcerpt','post','normal' ); // 抜粋
		remove_meta_box( 'commentstatusdiv','post','normal' ); // ディスカッション
		remove_meta_box( 'commentsdiv','post','normal' ); // コメント
		remove_meta_box( 'trackbacksdiv','post','normal' ); // トラックバック
		remove_meta_box( 'authordiv','post','normal' ); // 作成者
		remove_meta_box( 'slugdiv','post','normal' ); // スラッグ
		remove_meta_box( 'revisionsdiv','post','normal' ); // リビジョン
	}
}
add_action('admin_menu','remove_default_post_screen_metaboxes');



// メニューを非表示にする
//--------------------------------------------------------------
function remove_menus () {
	if (!current_user_can('level_10')) { //level10以下のユーザーの場合メニューをunsetする
		remove_menu_page('wpcf7'); //Contact Form 7
		global $menu;
		unset($menu[2]); // ダッシュボード
		unset($menu[4]); // メニューの線1
		unset($menu[5]); // 投稿
		unset($menu[10]); // メディア
		unset($menu[15]); // リンク
		unset($menu[20]); // ページ
		unset($menu[25]); // コメント
		unset($menu[59]); // メニューの線2
		unset($menu[60]); // テーマ
		unset($menu[65]); // プラグイン
		unset($menu[70]); // プロフィール
		unset($menu[75]); // ツール
		unset($menu[80]); // 設定
		unset($menu[90]); // メニューの線3
	}
}
add_action('admin_menu', 'remove_menus');


//管理バーにログアウトを追加
//--------------------------------------------------------------
function add_new_item_in_admin_bar() {
	global $wp_admin_bar;
	$wp_admin_bar->add_menu(array(
		'id' => 'new_item_in_admin_bar',
		'title' => __('ログアウト'),
		'href' => wp_logout_url()
	));
}
add_action('wp_before_admin_bar_render', 'add_new_item_in_admin_bar');


//管理バーの項目を非表示
//--------------------------------------------------------------
function remove_admin_bar_menu( $wp_admin_bar ) {
	$wp_admin_bar->remove_menu( 'wp-logo' ); // WordPressシンボルマーク
	$wp_admin_bar->remove_menu('my-account'); // マイアカウント
}
add_action( 'admin_bar_menu', 'remove_admin_bar_menu', 70 );


//記事公開時にアラートを出す
//--------------------------------------------------------------
$c_message = '記事を公開します。宜しいでしょうか？';
function confirm_publish(){
	// JavaScriptを管理画面フッターに挿入
	global $c_message;
	echo '<script type="text/javascript"><!--
	var publish = document.getElementById("publish");
	if (publish !== null) publish.onclick = function(){
	return confirm("'.$c_message.'");
	};
	// --></script>';
}
//add_action('admin_footer', 'confirm_publish');


//ログアウト後のページ遷移変更
//--------------------------------------------------------------
function page_redirect(){
	wp_safe_redirect(site_url());
	exit;
}
//add_action('wp_logout','page_redirect');


//ログアウト後のページ遷移変更
//--------------------------------------------------------------
function redirect_dashiboard() {
	if ( '/wp-admin/index.php' == $_SERVER['SCRIPT_NAME'] ) {
	wp_redirect( admin_url( 'edit.php' ) );
	}
}
add_action( 'admin_init', 'redirect_dashiboard' );



// 404へリダイレクト
//--------------------------------------------------------------
function member_page_redirect() {
	if(stristr($_SERVER["REQUEST_URI"],'')){
		wp_safe_redirect( home_url().'/404error', 303 );
		exit;
	}
}
//add_action( 'init', 'member_page_redirect', 20 );



//ダッシュボードにウィジェットを追加
//--------------------------------------------------------------
function my_dashboard_widget() {
	wp_add_dashboard_widget(
		'my_widget', //ウィジェット スラッグ
		'ウィジェットタイトル', //ウィジェット タイトル
		'my_widget_display' //ウィジェット コンテンツ
	);
}
function my_widget_display() {
	echo 'ここにテキスト。ここにテキスト。ここにテキスト。ここにテキスト。ここにテキスト。ここにテキスト。ここにテキスト。ここにテキスト。ここにテキスト。ここにテキスト。ここにテキスト。ここにテキスト。ここにテキスト。ここにテキスト。';
}
add_action( 'wp_dashboard_setup', 'my_dashboard_widget' );



//メディアライブラリにカテゴリー機能を追加
//--------------------------------------------------------------
function wptp_add_categories_to_attachments() {
    register_taxonomy_for_object_type( 'category', 'attachment' );
}
//add_action( 'init' , 'wptp_add_categories_to_attachments' );



//メディアライブラリにタグ機能を追加
//--------------------------------------------------------------
function wptp_add_tags_to_attachments() {
    register_taxonomy_for_object_type( 'post_tag', 'attachment' );
}
//add_action( 'init' , 'wptp_add_tags_to_attachments' );

