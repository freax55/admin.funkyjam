<?php
/*
Plugin Name: sort News
Description: ニュース並び替え
Version:     1.0
Author:      y.y. untamed,Inc.
*/

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
		// echo '1';
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
		// echo '0';
		$wpdb->print_error();
	}
}

function fj_news_sort() {
	add_menu_page('トップページニュース並び替え', 'トップニュース並び替え', 7, 'sort_news.php', 'sort_news_top', null, 30);

}

// function get_news_terms(){
// 	global $wpdb;
// 	return $wpdb->get_col("SELECT `Term`.`term_id` FROM `wp_funkyjam`.`wp_terms` AS `Term` WHERE `Term`.`name` LIKE '%news'");
// }

function get_ary_post_types(){
	return array(
		'久保田利伸' => 'kubota_news',
		'浦嶋りんこ' => 'urashima_news',
		'森大輔' => 'mori_news',
		'bes' => 'bes_news',
		'バナー' => 'extend_news' 
	);
}

function get_news_by_post_types(){
	$ary = get_ary_post_types();
	return implode(',', $ary);
}

function get_news_id_list() {
	global $wpdb;
	$news_terms = $wpdb->get_col("SELECT term_id FROM wp_terms WHERE name LIKE '%news'");
	$where_in = implode(',', $news_terms);
	$news_ids = $wpdb->get_results("SELECT object_id , term_taxonomy_id FROM wp_term_relationships WHERE term_taxonomy_id IN ($where_in) ORDER BY object_id DESC",ARRAY_A);
	return $news_ids;
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
	// $_news_ids = get_news_id_list();
	// foreach($_news_ids as $v) {
	// 	$news_ids[] = $v['object_id'];
	// 	$ids_by_artist[$v['object_id']] = $v['term_taxonomy_id'];
	// }
	// $where_in = implode(',', $news_ids);
	$where_in = get_news_by_post_types();
	// var_dump($where_in);
	// $_news_list = $wpdb->get_results("SELECT ID, post_title FROM wp_posts WHERE post_status = 'publish' AND ID IN ($where_in) ORDER BY post_date ASC",ARRAY_A);
	$_news_list = $wpdb->get_results("SELECT ID, post_title, post_type FROM wp_posts WHERE post_status = 'publish' AND post_type LIKE '%_news' ORDER BY post_date ASC",ARRAY_A);
	// var_dump($_news_list);
	$custom_order = $wpdb->get_col("SELECT option_value FROM wp_options WHERE option_name = 'custom_order'");
	foreach($_news_list as $v) {
		$news_list[$v['ID']] = $v;
	}
	echo '<form action="" method="post">';
	echo '表示件数<input type="text" name="sort[limit]" />';
	echo '<button id="submit">submit</button>';
	$ary_name_by_post_type = array_flip(get_ary_post_types());
	echo '<ul class="sortable">';
	if($custom_order != null){
		$ary_custom_order = json_decode($custom_order[0], true);
		foreach($ary_custom_order as $id) {
			echo '<li style="width:80%; padding: 10px 10px 10px 10px; background-color:#FFB;" id="' . $id . '">(' . $ary_name_by_post_type[$news_list[$id]['post_type']] . ')' . $news_list[$id]['post_title'] . $id;
			echo '</li>';
			unset($news_list[$id]);
		}
	}
	// var_dump($news_list);
	foreach($news_list as $v){
		echo '<li style="width:80%; padding: 10px 10px 10px 10px; background-color:#FFF;" id="' . $v['ID'] . '">(' . $ary_name_by_post_type[$v['post_type']] . ')' . $v['post_title'] . $v['ID'];
		echo '</li>';
	}
	echo '</ul>';
	echo '<input type="hidden" id="result" name="sort[result]" />';

	echo '</form>';


}
add_action('admin_menu', 'fj_news_sort');