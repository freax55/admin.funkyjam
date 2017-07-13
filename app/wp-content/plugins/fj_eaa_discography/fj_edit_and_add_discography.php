<?php
/*
Plugin Name: fj Edit & Add Discography
Description: ディスコグラフィ管理
Version:     1.0
Author:      y.y. untamed,Inc.
*/


function _array_artist_data(){
	return array(
		array(
			'name' => '久保田利伸',
			'param' => 'kubota'
		),
		array(
			'name' => '浦嶋りんこ',
			'param' => 'urashima'
		),
		array(
			'name' => '森大輔',
			'param' => 'mori'
		),
		array(
			'name' => 'ブラウンアイドソウル',
			'param' => 'bes'
		)
	);
}

function get_disc_types() {
	return array(
		'kubota' => [
			'album' => 'Album',
			'bestalbum' => 'Best Album',
			'usalbum' => 'U.S. Album',
			'single' => 'Single',
			'dvd' => 'DVD/Videos',
			'book' => 'Book'
		],
		'urashima' => [
			'album' => 'Album'
		],
		'mori' => [
			'album' => 'Album',
			'single' => 'Single',
			'dom' => 'Digital only Movie'
		],
		'bes' => [
			'album' => 'Album'
		],
	);
}

function get_publish_url() {
	global $wpdb;
	$value = $wpdb->get_col("SELECT option_value FROM wp_options WHERE option_name = 'site_publish_url'");
	return $value;
}


function fj_edit_and_add_discography() {
	add_menu_page('ディスコグラフィ管理', 'ディスコグラフィ管理', 7, 'fj_edit_and_add_discography.php', 'edit_discograpies', null, 42);
	// $ary_artist = array_artist_data();
	// foreach($ary_artist as $k => $v) {
	// 	add_submenu_page('fj_custom_header.php', $v['name']. 'ヘッダー管理', $v['param'], 7, 'artist_' . $v['param'],'fj_custom_header_artist');
	// }
	// add_submenu_page('fj_custom_header.php', '固定ページ共通ヘッダー管理', '固定ページ共通', 7, 'pages_header','fj_custom_header_pages');
}

function edit_discograpies() {
	$host = get_publish_url()[0];
	// var_dump($host);
	echo '<h2>ディスコグラフィ管理</h2>';
	foreach (_array_artist_data() as $va) {
		echo '<h3 style="margin:20px 0 0 0;">' . $va['name'] . '</h3>';
		echo '<ul style="margin:0 0 10px 0;">';
		foreach(get_disc_types()[$va['param']] as $vk => $vt){
			echo '<li>';
			echo '<h4 style="margin:5px 0;">' . $vt . '</h4>';
			echo '<a href="' . $host . '/discography_data/index/' . $va['param'] . '/' . $vk . '/" target="_blank">編集ページ</a></li>';
		}
		echo '</ul>';
	}
	// echo '<script>';
	// echo 'jQuery(function($){
	// 		$(function(href, width, height){
	// 			window.open(href, "", "width=" + width + ", height=" + height + ", menubar=no, scrollbars=yes");
	// 		})
	// 	})';
	// echo '</script>';
			// echo '<script src="https://code.jquery.com/jquery-1.8.3.min.js"></script>';
			// echo '<script src="https://code.jquery.com/ui/1.11.3/jquery-ui.js"></script>';

}

add_action('admin_menu', 'fj_edit_and_add_discography');
