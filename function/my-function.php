<?php
//データ取得系の関数テンプレート
//--------------------------------------------------------------
function my_get_query($output_type='OBJECT'){
	global $wpdb;
	$tbl_post = $wpdb->prefix.'post';
	$query = "SELECT ID FROM $tbl_post" ;
	$results = $wpdb->get_results($query);
	return $results;
}
//attachmentデータを返す
//--------------------------------------------------------------
function my_get_attachment_data($output_type='OBJECT'){
	global $wpdb;
	$tbl_posts = $wpdb->prefix.'posts';
	$query = "
	SELECT posts.ID, posts.guid, posts.post_title
	FROM $tbl_posts AS posts
	WHERE post_type = 'attachment' ";
	$results = $wpdb->get_results($query);
	return $results;
}
//文字列の抜粋
//--------------------------------------------------------------
function get_the_custom_excerpt($content, $length) {
	$length = ($length ? $length : 70);//デフォルトの長さを指定する
	$content =  preg_replace('/<!--more-->.+/is',"",$content); //moreタグ以降削除
	$content =  strip_shortcodes($content);//ショートコード削除
	$content =  strip_tags($content);//タグの除去
	$content =  str_replace("&nbsp;","",$content);//特殊文字の削除（今回はスペースのみ）
	$content_count = mb_strlen($content);
	$content =  mb_substr($content,0,$length);//文字列を指定した長さで切り取る
	if($content_count > $length){
		$content .= '...';
	}
	return $content;
}

