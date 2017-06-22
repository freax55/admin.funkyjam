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

if (isset($_POST['result'])){
	print_r($_POST['result']);
	// $ids = 

}



function fj_news_sort() {
	add_menu_page('トップページニュース並び替え', 'sort_news(top)', 7, 'sort_news.php', 'sort_news_top', null, 2000);

}

function get_news_terms(){
	// $wpdb = $this->wpdb;
	global $wpdb;
	return $wpdb->get_col("SELECT `Term`.`term_id` FROM `wp_funkyjam`.`wp_terms` AS `Term` WHERE `Term`.`name` LIKE '%news'");
}

function get_news_id_list() {
	global $wpdb;
	// $news_terms = $wpdb->get_col("SELECT `Term`.`term_id` FROM `wp_funkyjam`.`wp_terms` AS `Term` WHERE `Term`.`name` LIKE '%news'");
	$news_terms = $wpdb->get_col("SELECT term_id FROM wp_terms WHERE name LIKE '%news'");
	
	// $n = 
	var_dump($news_terms);
	// $news_terms = array(4,8,12,16);
	$where_in = implode(',', $news_terms);
	var_dump($where_in);
	// $order_option = $wpdb->get_col("SELECT `Option`")
	$news_ids = $wpdb->get_col("SELECT object_id FROM wp_term_relationships WHERE term_taxonomy_id IN ($where_in)");

	// $news_ids = $wpdb->get_col($wpdb->prepare("SELECT `TermRelationship`.`object_id` FROM `wp_funkyjam`.`wp_term_relationships` AS `TermRelationship` WHERE `TermRelationship`.`term_taxonomy_id` IN (%s)", $news_terms));
	// $inClause = substr(str_repeat(',?', count($news_terms)), 1);
	// $news_ids = $wpdb->get_col($wpdb->prepare(sprintf("SELECT `TermRelationship`.`object_id` FROM `wp_funkyjam`.`wp_term_relationships` AS `TermRelationship` WHERE `TermRelationship`.`term_taxonomy_id` IN (%s)", $inClause), $news_terms));

	return $news_ids;
 }

 // function 



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
	if (isset($_POST['sort'])){
		var_dump($_POST['sort']);
	}

	// echo get_news_terms();
	// var_dump(get_news_terms());
	// print_r(get_news_id_list());
	$news_ids = get_news_id_list();
	print_r($news_ids);
	$where_in = implode(',', $news_ids);
	$news_list = $wpdb->get_results("SELECT ID, post_title FROM wp_posts WHERE post_status = 'publish' AND ID IN ($where_in)",ARRAY_A);
	// print_r($news_list);
	echo '<form action="" method="post">';
	echo '表示件数<input type="text" name="sort[\'limit\']" />';
	echo '<ul class="sortable">';
	foreach($news_list as $v){
		// echo $v['ID'];
		echo '<li style="width:80%; padding: 10px 10px 10px 10px; background-color:#fff;" id="' . $v['ID'] . '">' . $v['post_title'];

		echo '</li>';
	}
	echo '<input type="hidden" id="result" name="sort[\'result\']" />';
    echo '<button id="submit">submit</button>';

	echo '</ul>';
	echo '</form>';


}
add_action('admin_menu', 'fj_news_sort');