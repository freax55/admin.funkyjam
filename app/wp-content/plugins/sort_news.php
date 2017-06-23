<?php
/*
Plugin Name: sort News
Description: ニュース並び替え
Version:     1.0
Author:      y.yoshida
*/
// function artist_add_pages () {
//   add_menu_page('テストタイトル', 'アーティスト', 7, 'index2.php', 'test_page', null, 5);
//   add_submenu_page('index2.php', 'テストタイトルサブ', 'kubota', 7, 'index3.php', 'test_page');
//   add_submenu_page('index2.php', 'テストタイトルサブ', 'urashima', 7, 'index3.php', 'test_page');
//   add_submenu_page('index2.php', 'テストタイトルサブ', 'mori', 7, 'index3.php', 'test_page');
//   add_submenu_page('index2.php', 'テストタイトルサブ', 'bse', 7, 'index3.php', 'test_page');
// }
 
// function test_page() {
//     echo '<h2>メニュー追加テストページ</h2>';
// }
// add_action ( 'admin_menu', 'artist_add_pages' );
// var $wpdb = global $wpdb;

if (isset($_POST['sort'])){
	$n = (empty($_POST['sort']['limit']))?12:$_POST['sort']['limit'];

	$array_result_ids = explode(',', $_POST['sort']['result']);
	$i = 0;
	foreach($array_result_ids as $v){
		if($i == $n){
			break;
		}
		$ary_update[] = $v;
		$i++;
	}
	$json_update = json_encode($ary_update);
	global $wpdb;
	$custom_order_exist_id = $wpdb->get_col("SELECT option_id FROM wp_options WHERE option_name = 'custom_order'");
	if($custom_order_exist_id == null) {
		$wpdb->insert(
			'wp_options',
			array(
				'option_name' => 'custom_order',
				'option_value' => $json_update,
				'autoload' => 'no'
			)
		);
		echo '1';
	} else {
		$order_option_id = $custom_order_exist_id[0];
		$wpdb->update(
			'wp_options',
			array(
				'option_value' => $json_update,
			),
			array(
				'option_id' => $order_option_id,
			)
		);
		echo '0';
		$wpdb->print_error();	
	}
}

function fj_news_sort() {
	add_menu_page('トップページニュース並び替え', 'sort_news(top)', 7, 'sort_news.php', 'sort_news_top', null, 2000);

}

function get_news_terms(){
	global $wpdb;
	return $wpdb->get_col("SELECT `Term`.`term_id` FROM `wp_funkyjam`.`wp_terms` AS `Term` WHERE `Term`.`name` LIKE '%news'");
}

function get_news_id_list() {
	global $wpdb;
	$news_terms = $wpdb->get_col("SELECT term_id FROM wp_terms WHERE name LIKE '%news'");
	$where_in = implode(',', $news_terms);
	$news_ids = $wpdb->get_results("SELECT object_id , term_taxonomy_id FROM wp_term_relationships WHERE term_taxonomy_id IN ($where_in) ORDER BY object_id DESC",ARRAY_A);
	return $news_ids;
 }

 function get_artist_by_taxonomy() {
 	return array(4=>'久保田利伸', 8=>'浦島りんこ', 12=>'森大輔', 16=>'BES',30=>'バナー');
 }

function sort_news_top() {
	echo '<script src="https://code.jquery.com/jquery-1.8.3.min.js"></script>';
	echo '<script src="https://code.jquery.com/ui/1.11.3/jquery-ui.js"></script>';
	echo '<script>
		jQuery(function($){
			$(function() {
				$(".sortable").sortable();
				$(".sortable").disableSelection();
				$("#submit").click(function() {
					var result = $(".sortable").sortable("toArray");
					$("#result").val(result);
				});
			});
		});
	</script>';
	global $wpdb;
	echo '<h2>トップページニュース並び替え</h2>';
	$_news_ids = get_news_id_list();
	foreach($_news_ids as $v) {
		$news_ids[] = $v['object_id'];
		$ids_by_artist[$v['object_id']] = $v['term_taxonomy_id'];
	}
	$where_in = implode(',', $news_ids);
	$_news_list = $wpdb->get_results("SELECT ID, post_title FROM wp_posts WHERE post_status = 'publish' AND ID IN ($where_in) ORDER BY post_date ASC",ARRAY_A);
	$custom_order = $wpdb->get_col("SELECT option_value FROM wp_options WHERE option_name = 'custom_order'");
	foreach($_news_list as $v) {
		$news_list[$v['ID']] = $v;
	}
	echo '<form action="" method="post">';
	echo '表示件数<input type="text" name="sort[limit]" />';
	echo '<button id="submit">submit</button>';

	echo '<ul class="sortable">';
	if($custom_order != null){
		$ary_custom_order = json_decode($custom_order[0], true);
		foreach($ary_custom_order as $id) {
			echo '<li style="width:80%; padding: 10px 10px 10px 10px; background-color:#FFB;" id="' . $id . '">(' . get_artist_by_taxonomy()[$ids_by_artist[$id]] . ')' . $news_list[$id]['post_title'] . $id;
			echo '</li>';
			unset($news_list[$id]);
		}
	}
	foreach($news_list as $v){
		echo '<li style="width:80%; padding: 10px 10px 10px 10px; background-color:#FFF;" id="' . $v['ID'] . '">(' . get_artist_by_taxonomy()[$ids_by_artist[$v['ID']]] . ')' . $v['post_title'] . $v['ID'];
		echo '</li>';
	}
	echo '</ul>';
	echo '<input type="hidden" id="result" name="sort[result]" />';

	echo '</form>';


}
add_action('admin_menu', 'fj_news_sort');