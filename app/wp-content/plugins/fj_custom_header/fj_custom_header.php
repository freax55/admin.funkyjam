<?php
/*
Plugin Name: fj Customize Header
Description: ヘッダー管理
Version:     1.0
Author:      y.y. untamed,Inc.
*/


function array_artist_data(){
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

function get_artist_header_id($artist){
	$option_name = 'header_' . $artist;
	global $wpdb;
	$value = $wpdb->get_results("SELECT option_id, option_value FROM wp_options WHERE option_name = '$option_name'", ARRAY_A);
	return $value;
}

function get_header_image_by_id($id){
	// var_dump($id);
	global $wpdb;
	$image = $wpdb->get_results("SELECT ID, post_title, guid FROM wp_posts WHERE post_mime_type LIKE 'image/%' AND ID = '$id'", ARRAY_A);
	return $image;
}

function fj_custom_header() {
	add_menu_page('ヘッダー管理', 'ヘッダー管理', 7, 'fj_custom_header.php', 'header_customize', null, 35);
	$ary_artist = array_artist_data();
	foreach($ary_artist as $k => $v) {
		add_submenu_page('fj_custom_header.php', $v['name']. 'ヘッダー管理', $v['param'], 7, 'artist_' . $v['param'],'fj_custom_header_artist');
	}
	add_submenu_page('fj_custom_header.php', '固定ページ共通ヘッダー管理', '固定ページ共通', 7, 'pages_header','fj_custom_header_pages');

}

//pages_header
function fj_custom_header_pages(){
	echo '<h2>固定ページ共通ヘッダー画像管理</h2>';
	$exist_media_id = @get_artist_header_id('pages')[0]['option_id'];
	$media_id = @get_artist_header_id('pages')[0]['option_value'];
	$data_image = get_header_image_by_id($media_id);
	$img_src = (empty($data_image))?'':'<img src="' . $data_image[0]['guid'] . '" alt="">';
	$img_id = (empty($data_image))?'':$data_image[0]['ID'];
	if(!empty($_POST)){
		// var_dump($_POST);
		global $wpdb;
		$wpdb->update(
			'wp_options',
			array(
				'option_value' => $_POST['mediaid'],
			),
			array(
				'option_id' => $exist_media_id,
			)
		);
		$media_id = @get_artist_header_id('pages')[0]['option_value'];
		$data_image = get_header_image_by_id($media_id);
		$img_src = (empty($data_image))?'':'<img src="' . $data_image[0]['guid'] . '" alt="">';
		$img_id = (empty($data_image))?'':$data_image[0]['ID'];
	}
	echo '<form method="post">';
?>
	<input name="mediaid" type="hidden" value="<?= $img_id ?>" />
	<input type="button" name="media" value="選択" />
	<?php
	/*
	<input type="button" name="media-clear" value="クリア" />
	*/
	?>
	<input type="submit" value="更新" />
	<div id="media"><?= $img_src ?></div>
<?php
	echo '</form>';
	script_custom_uploader();
	
}

// function fj_ch_list(){
// 	return array(
// 		'file1',
// 		'file2',
// 		'file3',
// 		'file4',
// 		'file5',
// 		'kubota',
// 		'urashima',
// 		'mori',
// 		'bes'
// 	);
// }

// data selectBox
function get_select_news_list() {
	global $wpdb;
	$news_list = $wpdb->get_results("SELECT ID, post_title, post_type FROM wp_posts WHERE post_status = 'publish' AND post_type LIKE '%_news' ORDER BY post_date DESC",ARRAY_A);

	return $news_list;
}

// artist_header
function fj_custom_header_artist() {
	$pre = 'artist_';
	$artist = str_replace($pre, '', strstr($_SERVER['REQUEST_URI'], $pre));
	echo '<h2>artist/' . $artist . ' ヘッダー画像管理</h2>';
	$exist_media_id = @get_artist_header_id($artist)[0]['option_id'];
	$media_id = @get_artist_header_id($artist)[0]['option_value'];
	$data_image = get_header_image_by_id($media_id);
	$img_src = (empty($data_image))?'':'<img src="' . $data_image[0]['guid'] . '" alt="">';
	$img_id = (empty($data_image))?'':$data_image[0]['ID'];
	if(!empty($_POST)){
		global $wpdb;
		$wpdb->update(
			'wp_options',
			array(
				'option_value' => $_POST['mediaid'],
			),
			array(
				'option_id' => $exist_media_id,
			)
		);
		$media_id = @get_artist_header_id($artist)[0]['option_value'];
		$data_image = get_header_image_by_id($media_id);
		$img_src = (empty($data_image))?'':'<img src="' . $data_image[0]['guid'] . '" alt="">';
		$img_id = (empty($data_image))?'':$data_image[0]['ID'];
	}
	echo '<form method="post">';
?>
	<input name="mediaid" type="hidden" value="<?= $img_id ?>" />
	<input type="button" name="media" value="選択" />
	<?php
	/*
	<input type="button" name="media-clear" value="クリア" />
	*/
	?>
	<input type="submit" value="更新" />
	<div id="media"><?= $img_src ?></div>
<?php
	echo '</form>';
	script_custom_uploader();
}

// header_top_js
function script_custom_uploader_multi(){
	for ($i=0; $i < 5; $i++) {
		echo "<script>";
		echo "(function ($) {
	    var custom_uploader;
	    $(\"input:button[name=media" . $i . "]\").click(function(e) {
	        e.preventDefault();
	        if (custom_uploader) {
	            custom_uploader.open();
	            return;
	        }
	        custom_uploader = wp.media({
	            title:\"Choose Image\",
	            library: {
	                type: \"image\"
	            },
	            button: {
	                text: \"Choose Image\"
	            },
	            multiple: false
	        });
	        custom_uploader.on(\"select\", function() {
	            var images = custom_uploader.state().get(\"selection\");
	             images.each(function(file){
	                $(\"input:hidden[name=mediaid]\").val(\"\");
	                $(\"#media" . $i . "\").empty();
	                $(\"input:hidden[name=mediaid" . $i . "]\").val(file.id);
	                $(\"#media" . $i . "\").append('<img style=\"width:99%;\" src=\"'+file.attributes.sizes.full.url+'\" />');
	            });
	        });
	        custom_uploader.open();
	    });
		})(jQuery);";
		echo "</script>";
	}
}
// js
function script_custom_uploader(){
	echo "<script>";
	echo "(function ($) {
    var custom_uploader;
    $(\"input:button[name=media]\").click(function(e) {
        e.preventDefault();
        if (custom_uploader) {
            custom_uploader.open();
            return;
        }
        custom_uploader = wp.media({
            title:\"Choose Image\",
            library: {
                type: \"image\"
            },
            button: {
                text: \"Choose Image\"
            },
            multiple: false
        });
        custom_uploader.on(\"select\", function() {
            var images = custom_uploader.state().get(\"selection\");
             images.each(function(file){
                $(\"input:hidden[name=mediaid]\").val(\"\");
                $(\"#media\").empty();
                $(\"input:hidden[name=mediaid]\").val(file.id);
                $(\"#media\").append('<img src=\"'+file.attributes.sizes.full.url+'\" />');
            });
        });
        custom_uploader.open();
    });
 
    $(\"input:button[name=media-clear]\").click(function() {
        $(\"input:text[name=mediaid]\").val(\"\");
        $(\"#media\").empty();
    });
 
	})(jQuery);";
	echo "</script>";
}

// top
function header_customize(){
	echo '<h2>トップページヘッダー画像管理</h2>';
	if(!empty($_POST)){
		// var_dump($_POST);
		// $ary = 
		foreach($_POST as $kp => $vp) {
			$num = substr($kp, -1);
			$str = substr($kp, 0, -1);
			$ary[$num][$str] = $vp;
			if($str == 'sort'){
				$sort[$num] = empty($vp)?($num+5):$vp;
			}
		}
		// var_dump($ary);
		array_multisort($sort, SORT_ASC, $ary);

		foreach($ary as $va){
			$image = get_header_image_by_id($va['mediaid'])[0]['guid'];
			$update[] = ['news_id' => $va['news_id'], 'mediaid' => $va['mediaid'], 'image' => $image];
		}

		// var_dump($update);
		$target_option = get_artist_header_id('top');
		global $wpdb;
		$wpdb->update(
			'wp_options',
			array(
				'option_value' => json_encode($update),
			),
			array(
				'option_id' => $target_option[0]['option_id'],
			)
		);
	}
	$news_list = get_select_news_list();

	$option = get_artist_header_id('top');
	// var_dump($option);

	$headers = json_decode($option[0]['option_value'], true);
	// $ary_headers = [100,200,300,400,500];
	echo '<form method="post">';
	foreach ($headers as $k => $v) {
?>
	<div style="padding:10px; border: solid 1px #CCC; border-radius:4px; ;margin-bottom:30px; width:40%;">
	<input name="mediaid<?= $k ?>" type="hidden" value="<?= $v['mediaid'] ?>" />
	<input type="button" name="media<?= $k ?>" value="選択"/>

	<?php
	echo '<select name="news_id' . $k . '" style="width:95%;">';
	foreach($news_list as $vn) {
		echo '<option value="' . $vn['ID'] . '"' . (($vn['ID'] == $v['news_id'])?' selected':'') . '>' . $vn['post_title'] . '</option>';
	}
	echo '</select>';
	echo '<input type="number" name="sort' . $k .'" min="1" max="5" style="margin-bottom:5px;"><br>';

	/*
	<input type="button" name="media-clear" value="クリア" />
	*/
	?>
	<div id="media<?= $k ?>"><?= (empty($v['image'])?'':'<img style="width:99%;" src="' . $v['image'] . '" />') ?></div>
	</div>
<?php
	}
	echo '<input type="submit" value="更新" />';
	echo '</form>';
	script_custom_uploader_multi();
}

function my_admin_scripts() {
    wp_register_script(
        'mediauploader',
        plugin_dir_url( __FILE__ ) . '/media-uploader.js',
        array( 'jquery' ),
        false,
        true
    );
    /* メディアアップローダの javascript API */
    wp_enqueue_media();
    /* 作成した javascript */
    wp_enqueue_script( 'mediauploader' );
}
add_action( 'admin_print_scripts', 'my_admin_scripts' );
add_action('admin_menu', 'fj_custom_header');
