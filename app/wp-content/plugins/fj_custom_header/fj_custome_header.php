<?php
/*
Plugin Name: fj Customize Header
Description: ヘッダー管理
Version:     1.0
Author:      y.yoshida
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

function fj_custome_header() {
	add_menu_page('ヘッダー管理', 'ヘッダー管理', 7, 'fj_custome_header.php', 'header_customize', null, 35);
	$ary_artist = array_artist_data();
	foreach($ary_artist as $k => $v) {
		add_submenu_page('fj_custome_header.php', $v['name']. 'ヘッダー管理', $v['param'], 7, 'artist_' . $v['param'],'fj_custom_header_artist');
	}
	add_submenu_page('fj_custome_header.php', '固定ページ共通ヘッダー管理', '固定ページ共通', 7, 'pages_header','fj_custom_header_pages');

}

function fj_custom_header_pages(){
	// $pre = 'artist_';
	// $artist = str_replace($pre, '', strstr($_SERVER['REQUEST_URI'], $pre));
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
		// var_dump(expression)
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

function fj_ch_list(){
	return array(
		'file1',
		'file2',
		'file3',
		'file4',
		'file5',
		'kubota',
		'urashima',
		'mori',
		'bes'
	);
}

// function get_news_terms(){
// 	global $wpdb;
// 	return $wpdb->get_col("SELECT `Term`.`term_id` FROM `wp_funkyjam`.`wp_terms` AS `Term` WHERE `Term`.`name` LIKE '%news'");
// }

function get_select_news_list() {
	global $wpdb;
	// return $wpdb->get_col("SELECT `Term`.`term_id` FROM `wp_funkyjam`.`wp_terms` AS `Term` WHERE `Term`.`name` LIKE '%news'");
	$news_terms = $wpdb->get_col("SELECT term_id FROM wp_terms WHERE name LIKE '%news'");
	// return $news_terms;
	$_where_in = implode(',', $news_terms);
	$_news_ids = $wpdb->get_results("SELECT object_id , term_taxonomy_id FROM wp_term_relationships WHERE term_taxonomy_id IN ($_where_in) ORDER BY object_id DESC",ARRAY_A);
	foreach($_news_ids as $v) {
		$news_ids[] = $v['object_id'];
		$ids_by_artist[$v['object_id']] = $v['term_taxonomy_id'];
	}
	// return $news_ids;
	$where_in = implode(',', $news_ids);
	$news_list = $wpdb->get_results("SELECT ID, post_title FROM wp_posts WHERE post_status = 'publish' AND ID IN ($where_in) ORDER BY post_date DESC",ARRAY_A);

	return $news_list;
}

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







	// $paged = get_query_var('query', 0);
	// var_dump($_SERVER["REQUEST_URI"]);
// 	echo $paged;
// }
function script_custom_uploader(){
	echo "<script>
	(function ($) {
 
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
 
})(jQuery);
	</script>";

}

function header_customize(){


	// <form>
	// <input name="mediaid" type="hidden" value="" />
	// <input type="button" name="media" value="選択" />
	// <input type="button" name="media-clear" value="クリア" />
	// <div id="media"></div>
	// </form>
	echo '準備中';

	echo "<script>
	(function ($) {
 
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
 
                $(\"input:text[name=mediaid]\").val(\"\");
                $(\"#media\").empty();
 
                $(\"input:text[name=mediaid]\").val(file.id);
 
                $(\"#media\").append('<img src=\"'+file.attributes.sizes.full.url+'\" />');
 
            });
        });
 
        custom_uploader.open();
 
    });
 
    $(\"input:button[name=media-clear]\").click(function() {
     
        $(\"input:text[name=mediaid]\").val(\"\");
        $(\"#media\").empty();
 
    });
 
})(jQuery);
	</script>";


	// if (!empty($_POST)){
	// 	var_dump($_POST);
	// 	echo 'ppostppostppostppostppostppostppostppostppostppostppostppost';
	// }

	// if(!empty($_FILE)) {
	// 	// var_dump($_FILE);
	// 	// upload_header_image($_FILE);

	// 	$img = file_get_contents($_FILES["file1"]["tmp_name"]);
	// 	$base64 = base64_encode($img);
	// 	print '<img src="data:image/jpeg;base64,${base64}">';
	// 	echo 'hsdkjg;jlsf;sajfmpoiaseugiosecgopisgiosrg;jgfkdjfdkddaoifopaeiopfaeopgjaopegsoepgj';



	// } else {
	// 	echo'ngngngngngngngngngngngngngngngngngngngngnng';
	// }

	// echo '<h2>ヘッダー管理</h2>';
	// // var_dump(get_select_news_list());
	// $news_list = get_select_news_list();




	// echo '<h3>画像アップロード</h3>';
	// foreach(fj_ch_list() as $k => $v){
	// 	if($k<= 4){
	// 		echo '<h4>top' . ($k+1) . '</h4>';
	// 		echo '<form action="" method="post" enctype="multipart/form-data">';
	// 		// echo '<div class="imgInput">';			
	// 		echo '<input type="file" name="' . $v .'">';
	// 		echo '<select name="news_id">';
	// 		foreach($news_list as $vn) {
	// 			echo '<option value="' . $vn['ID'] . '">' . $vn['post_title'] . '</option>';
	// 		}
	// 		echo '</select>';
	// 		echo '<button id="submit' . $k . '">保存</button><br>';
	// 		echo '<img src="img/noimage.png" alt="" class="imgView">';//</div><!--/.imgInput-->';
	// 		echo '</form>';
	// 	} else {
	// 		echo '<h4>/artist/' . $v . '</h4>';
	// 		echo '<form action="" method="post">';
	// 		echo '<div class="imgInput">';
	// 		echo '<input type="file" name="' . $v .'">';
	// 		echo '<button id="submit' . $k . '">保存</button><br>';
	// 		echo '<img src="img/noimage.png" alt="" class="imgView"></div><!--/.imgInput-->';
	// 		echo '</form>';
	// 	}
	// 	// script_upload_preview();
	// }
}
function my_admin_scripts() {
 
    wp_register_script(
        'mediauploader',
        plugin_dir_url( __FILE__ ) . '/js/mediauploader.js',
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







	// echo '<form action="" method="post">';
	// echo '<button id="submit">submit</button>';
	// foreach(fj_ch_list() as $k => $v) {
	// 	if($k <= 4) {
	// 		echo '<h3>トップページヘッダー' . ($k+1) . '</h3>';
	// 		echo '<div class="imgInput">';
	// 		echo '<input type="file" name="' . $v .'">';
	// 		echo '<select name="news_id">';
	// 		foreach($news_list as $vn) {
	// 			echo '<option value="' . $vn['ID'] . '">' . $vn['post_title'] . '</option>';
	// 		}
	// 		echo '</select>';
	// 		echo '<input type="number" name="sort' . $k .'" min="1" max="5"><br>';
	// 		echo '<img src="img/noimage.png" alt="" class="imgView"></div><!--/.imgInput-->';
	// 	} else {
	// 		echo '<h3>/artist/' . $v . 'ヘッダー</h3>';
	// 		echo '<div class="imgInput">';
	// 		echo '<input type="file" name="' . $v .'"><br>';
	// 		echo '<img src="img/noimage.png" alt="" class="imgView"></div><!--/.imgInput-->';
	// 	}
	// }
// 	echo '<div class="imgInput">
// <input type="file" name="file1">
// <img src="img/noimage.png" alt="" class="imgView">
// </div><!--/.imgInput-->
 
// <div class="imgInput">
// <input type="file" name="file2">
// <img src="img/noimage.png" alt="" class="imgView">
// </div><!--/.imgInput-->
 
// <div class="imgInput">
// <input type="file" name="file3">
// <img src="img/noimage.png" alt="" class="imgView">
// </div><!--/.imgInput-->
// <div class="imgInput">
// <input type="file" name="file4">
// <img src="img/noimage.png" alt="" class="imgView">
// </div><!--/.imgInput-->';
function script_upload_preview(){
	echo "<script src=\"http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js\"></script>
<script>
$(function(){
    var setFileInput = $('.imgInput'),
    setFileImg = $('.imgView');
 
    setFileInput.each(function(){
        var selfFile = $(this),
        selfInput = $(this).find('input[type=file]'),
        prevElm = selfFile.find(setFileImg),
        orgPass = prevElm.attr('src');
 
        selfInput.change(function(){
            var file = $(this).prop('files')[0],
            fileRdr = new FileReader();
 
            if (!this.files.length){
                prevElm.attr('src', orgPass);
                return;
            } else {
                if (!file.type.match('image.*')){
                    prevElm.attr('src', orgPass);
                    return;
                } else {
                    fileRdr.onload = function() {
                        prevElm.attr('src', fileRdr.result);
                    }
                    fileRdr.readAsDataURL(file);
                }
            }
        });
    });
});
</script>";
}

function get_header_image_path(){
	global $wpdb;
	$path = $wpdb->get_col("SELECT option_value FROM wp_options WHERE option_name = 'header_image_upload_path'");
	return $path;
}


function upload_header_image($file) {

	$upload_dir = get_header_image_path();
	// $upload_dir = IMG_DIR . strtolower(implode("_", $this->Controller->explodeCase($model))) . DS;
	// $img_id = $this->Controller->getRandStr(16);
	$file_name = $file['kubota']["name"];
	// $file_type = $file["type"];
	$file_tmp  = $file['kubota']["tmp_name"];
	$file_ext  = '.' . substr($file_name, strrpos($file_name, '.') + 1);

	if (!strstr($file_name, ".gif") || !strstr($file_name, ".jpg") || !strstr($file_name, ".jpeg") || !strstr($file_name, ".png")) {
		return;
		// $this->Controller->flash("このファイル形式はサポートしていません。", "/admin/");
	}
	// $new_name = $pre_name.$data_name.$file_ext;
	var_dump($upload_dir . $file_name);
	if (move_uploaded_file($file_tmp, $upload_dir . $file_name)) {
		// if (isset($this->Controller->data[$model][$field])) {
		// 	unlink($upload_dir . $this->Controller->data[$model][$field]);
		// }
		// DBインサート用データ生成
		// $this->Controller->request->data[$model][$field] = $new_name;
		return true;
	} else {
		// $this->Controller->flash("Error!!", false);
		return false;
	}
}

// 	echo '<form action="" method="post">';
// 	echo '<input type="file" id="file" />';
// 	// echo '</form>'
// 	echo '</form>';
// 	echo '<div id="result"></div>';
// 	// echo "<script src=\"https://code.jquery.com/jquery-3.0.0.min.js\"></script>
// echo "<script>
// 		jQuery(function($){

// $(function(){
//     $('#file').change(function(){
//         $('img').remove();
//         var file = $(this).prop('files')[0];
//         if(!file.type.match('image.*')){
//             return;
//         }
//         var fileReader = new FileReader();
//         fileReader.onloadend = function() {
//             $('#result').html('<img src=\"' + fileReader.result + '\"/>');
//         }
//         fileReader.readAsDataURL(file);
//     });
// });
// 		});
// </script>";


// function get_news_terms(){
// 	global $wpdb;
// 	return $wpdb->get_col("SELECT `Term`.`term_id` FROM `wp_funkyjam`.`wp_terms` AS `Term` WHERE `Term`.`name` LIKE '%news'");
// }

// function get_news_id_list() {
// 	global $wpdb;
// 	$news_terms = $wpdb->get_col("SELECT term_id FROM wp_terms WHERE name LIKE '%news'");
// 	$where_in = implode(',', $news_terms);
// 	$news_ids = $wpdb->get_results("SELECT object_id , term_taxonomy_id FROM wp_term_relationships WHERE term_taxonomy_id IN ($where_in) ORDER BY object_id DESC",ARRAY_A);
// 	return $news_ids;
//  }

//  function get_artist_by_taxonomy() {
//  	// return array(4=>'久保田利伸', 8=>'浦島りんこ', 12=>'森大輔', 16=>'BES',30=>'バナー');
//  }

// function sort_news_top() {
// 	echo '<script src="https://code.jquery.com/jquery-1.8.3.min.js"></script>';
// 	echo '<script src="https://code.jquery.com/ui/1.11.3/jquery-ui.js"></script>';
// 	echo '<script>
// 		jQuery(function($){
// 			$(function() {
// 				$(".sortable").sortable();
// 				$(".sortable").disableSelection();
// 				$("#submit").click(function() {
// 					var result = $(".sortable").sortable("toArray");
// 					$("#result").val(result);
// 				});
// 			});
// 		});
// 	</script>';
// 	global $wpdb;
// 	echo '<h2>トップページニュース並び替え</h2>';
// 	$_news_ids = get_news_id_list();
// 	foreach($_news_ids as $v) {
// 		$news_ids[] = $v['object_id'];
// 		$ids_by_artist[$v['object_id']] = $v['term_taxonomy_id'];
// 	}
// 	$where_in = implode(',', $news_ids);
// 	$_news_list = $wpdb->get_results("SELECT ID, post_title FROM wp_posts WHERE post_status = 'publish' AND ID IN ($where_in) ORDER BY post_date ASC",ARRAY_A);
// 	$custom_order = $wpdb->get_col("SELECT option_value FROM wp_options WHERE option_name = 'custom_order'");
// 	foreach($_news_list as $v) {
// 		$news_list[$v['ID']] = $v;
// 	}
// 	echo '<form action="" method="post">';
// 	echo '表示件数<input type="text" name="sort[limit]" />';
// 	echo '<button id="submit">submit</button>';

// 	echo '<ul class="sortable">';
// 	if($custom_order != null){
// 		$ary_custom_order = json_decode($custom_order[0], true);
// 		foreach($ary_custom_order as $id) {
// 			echo '<li style="width:80%; padding: 10px 10px 10px 10px; background-color:#FFB;" id="' . $id . '">(' . get_artist_by_taxonomy()[$ids_by_artist[$id]] . ')' . $news_list[$id]['post_title'] . $id;
// 			echo '</li>';
// 			unset($news_list[$id]);
// 		}
// 	}
// 	foreach($news_list as $v){
// 		echo '<li style="width:80%; padding: 10px 10px 10px 10px; background-color:#FFF;" id="' . $v['ID'] . '">(' . get_artist_by_taxonomy()[$ids_by_artist[$v['ID']]] . ')' . $v['post_title'] . $v['ID'];
// 		echo '</li>';
// 	}
// 	echo '</ul>';
// 	echo '<input type="hidden" id="result" name="sort[result]" />';

// 	echo '</form>';


// }
add_action('admin_menu', 'fj_custome_header');