<?php
/*
Plugin Name: Network Posts Ext
Plugin URI: https://wp-plugins.johncardell.com/network-posts-extended/
Description: Network Posts Extended plugin enables you to share posts over WP Multi Site network.  You can display on any blog in your network the posts selected by taxonomy from any blogs including main.
Version: 0.2.4
Author: John Cardell
Author URI: http://www.johncardell.com

Copyright 2014 John Cardell

*/
if (realpath(__FILE__) == realpath($_SERVER['SCRIPT_FILENAME']))
{
	exit('Please don\'t access this file directly.');
}
############  SETUP  ####################
add_action("plugins_loaded","net_shared_posts_init");
add_shortcode('netsposts','netsposts_shortcode');
add_action('admin_menu', 'add_netsposts_toolpage');
add_action('admin_enqueue_scripts', 'netsposts_init_settings_page');
//This variable is needed for WP_EStore thumbnails
$img_sizes;
/*
 * Custom thumbnail maximum width
 */
define('DEFAULT_THUMBNAIL_WIDTH', 300);
define('BASE_PATH', plugins_url('/network-posts-extended/js'));

$thumbnail_manager = null;
init_thumbnails_manager();
// Setup functions
function super_unique($array,$key)
{

	$temp_array = array();

	foreach ($array as $v) {

		if (!isset($temp_array[$v[$key]]))

			$temp_array[$v[$key]] = $v;

	}

	$array = array_values($temp_array);

	return $array;

}


function removeElementWithValue($array, $key, $value){
	foreach($array as $subKey => $subArray){
		if($subArray[$key] == $value){
			unset($array[$subKey]);
		}
	}
	return $array;
}

function ShortenText($text, $limit)

{

	$chars_limit = $limit;

	$chars_text = strlen($text);

	$text = $text." ";

	$text = substr($text,0,$chars_limit);

	$text = substr($text,0,strrpos($text,' '));

	if ($chars_text > $chars_limit)

	{

		$text = $text."...";

	}

	return $text;

}
// Add settings link on plugin page
function netsposts_plugin_settings_link($links) {
	$settings_link = '<a href="options-general.php?page=netsposts_page">Settings</a>';
	array_unshift($links, $settings_link);
	return $links;
}

$plugin = plugin_basename(__FILE__);
add_filter("plugin_action_links_$plugin", 'netsposts_plugin_settings_link' );

function add_netsposts_toolpage()
{
	add_options_page( 'Network Posts Ext', 'Network Posts Ext', 'manage_options', 'netsposts_page', 'netsposts_page' );;
}


function net_shared_posts_init()
{
	//init_thumbnails_manager();
	register_uninstall_hook(__FILE__, 'net_shared_posts_uninstall');
	add_action('wp_enqueue_scripts', 'netposts_add_stylesheet');

	load_plugin_textdomain('netsposts', false, basename( dirname( __FILE__ ) ) . '/language');
}
function init_thumbnails_manager(){
	global $thumbnail_manager;
	global $wpdb;
	require_once('components/netsposts-thumbnails.php');
	$thumbnail_manager = new \NetworkPosts\Components\Netsposts_thumbnails($wpdb);
	$thumbnail_manager->init(is_admin());
}

function netposts_add_stylesheet(){
	wp_register_style( 'netsposts_css', plugins_url('/css/net_posts_extended.css', __FILE__) );
	wp_enqueue_style( 'netsposts_css' );
}

function netsposts_init_settings_page(){
	if($_GET['page'] == 'netsposts_page') {
		wp_register_style('netsposts_admin_css', plugins_url('/css/settings.css', __FILE__));
		wp_enqueue_style('netsposts_admin_css');

		global $thumbnail_manager;
		$thumbnail_manager->register_scripts();
	}
}

function net_shared_posts_uninstall()
{
	remove_shortcode('netsposts');
}

function netsposts_shortcode($atts)
{
	/* below is my updates */
	extract(shortcode_atts(array(
		'limit' => '',
		'days' => 0,
		'page_title_style' => '',
		'title' => '',
		'titles_only' => false,
		'include_link_title'=> false,
		'exclude_link_title_posts'=>'',
		'wrap_start' => null,
		'wrap_end' => null,
		'thumbnail' => false,
		'post_type' => 'post',
		'include_blog' => null,
		'exclude_blog' => null,
		'exclude_post' => null,
		'include_post' => null,
		'title_length' => 999,
		'taxonomy' => '',
		'exclude_taxonomy' => '',
		'include_price' => '',
		'paginate' => false,
		'pages' => null,
		'list' => 10,
		'excerpt_length' => null,
		'excerpt_letters_length' => 400,
		'auto_excerpt' => false,
		'show_author' => false,
		'full_text' =>  false,
		'size' => 'thumbnail',
		'image_class' => 'post-thumbnail',
		'date_format' => 'n/j/Y',
		'end_size'     => '',
		'mid_size'  => '',
		'prev_next' => false,
		'prev' => '&laquo; Previous',
		'next' =>  'Next &raquo;',
		'column' => '1',
		'column_width' => '200',
		'title_color' => '',
		'text_color' => '',
		'meta_info' => 'true',
		'wrap_title_start' => '',
		'wrap_title_end' => '',
		'wrap_image_start' => '',
		'wrap_image_end' => '',
		'wrap_text_start' => '',
		'wrap_text_end' => '',
		'wrap_price_start'=>'',
		'wrap_price_end'=>'',
		'meta_width' => '100%',
		'menu_name' => '',
		'menu_class' => '',
		'container_class' => '',
		'post_height' => null,
		'manual_excerpt_length' => null,
		'manual_excerpt_letters_length' => null,
		'random' => false,
		'order_post_by' => '',
		'use_image' => '',
	), $atts));
	/* my updates are finished here */

########  OUTPUT STAFF  ####################
	$titles_only = strtolower($titles_only) == 'true'? true: false;
	$thumbnail = strtolower($thumbnail) == 'true'? true: false;
	$paginate = strtolower($paginate) == 'true'? true: false;
	$auto_excerpt = strtolower($auto_excerpt) == 'true'? true: false;
	$show_author = strtolower($show_author) == 'true'? true: false;
	$full_text = strtolower($full_text) == 'true'? true: false;
	$prev_next = strtolower($prev_next) == 'true'? true: false;

	/* below is my updates */
	$random = strtolower($random) == 'true'? true: false;
	/* my updates are finished here */
	$price_woocommerce = false;
	$price_estore = false;

	if($include_link_title && !empty($exclude_link_title_posts))
		$exclude_title_links = explode(',', $exclude_link_title_posts);

	global $img_sizes;

	global $wpdb;

	if(isset($include_price)){
		$pos = strpos($include_price,'|');
		if($pos !== false){
			$exs = explode('|',$include_price);
			foreach($exs as $ex){
				if($ex == 'woocommerce'){
					$price_woocommerce = true;
				}
				elseif($ex == 'estore'){
					$price_estore = true;
				}
			}
		}
		elseif($include_price == 'woocommerce'){
			$price_woocommerce = true;
		}
		elseif($include_price == 'estore'){
			$price_estore = true;
		}
	}

	$woocommerce_installed = is_installed($wpdb, 'woocommerce');
	$estore_installed = is_installed($wpdb, 'estore');

	global $table_prefix;

	define("WOOCOMMERCE","woocommerce");
	define("WPESTORE","estore");

	if($limit) $limit = " LIMIT 0,$limit ";
	## Params for taxonomy
	if($cat)
	{
		if ($tag)
		{
			implode(',',$cat, $tag);
		}
	} else $cat = $tag;
	## Include blogs
	if($include_blog) {
		$include_arr = explode(",",$include_blog);
		$include = " AND (";
		foreach($include_arr as $included_blog)
		{
			$include .= " blog_id = $included_blog  OR";
		}
		$include = substr($include,0,strlen($include)-2);
		$include .= ")";
	} else {  if($exclude_blog)   {$exclude_arr = explode(",",$exclude_blog); foreach($exclude_arr as $exclude_blog)	{$exclude .= "AND blog_id != $exclude_blog  "; }}}
	$BlogsTable = $wpdb->base_prefix.'blogs';

	/* below is my updates */
	if($random){
		$page = get_query_var('paged');
		if(!$page)  $page = get_query_var('page');
		if(!$page)  $page = 1;
		if($page > 1 && $paginate ){
			$blogs = $wpdb->get_col($wpdb->prepare(

				"SELECT blog_id FROM $BlogsTable WHERE public = %d AND archived = %d AND mature = %d AND spam = %d AND deleted = %d $include $exclude  ", 1, 0, 0, 0, 0

			));
		}
		else{
			$blogs = $wpdb->get_col($wpdb->prepare(

				"SELECT blog_id FROM $BlogsTable WHERE public = %d AND archived = %d AND mature = %d AND spam = %d AND deleted = %d $include $exclude ORDER BY RAND() ", 1, 0, 0, 0, 0

			));
		}
	}
	else{
		$blogs = $wpdb->get_col($wpdb->prepare(

			"SELECT blog_id FROM $BlogsTable WHERE public = %d AND archived = %d AND mature = %d AND spam = %d AND deleted = %d $include $exclude  ", 1, 0, 0, 0, 0

		));
	}
	/* my updates are finished here */

	## Getting posts
	$postdata = array();
	$prices = array();
	if ($blogs)
	{
		$img_sizes = get_image_sizes($blogs);
		foreach ($blogs as $blog_id)
		{
			if( $blog_id == 1 )
			{
				$OptionsTable = $wpdb->base_prefix."options";
				$PostsTable = $wpdb->base_prefix."posts";
				$TermRelationshipTable = $wpdb->base_prefix."term_relationships";
				$TermTaxonomyTable = $wpdb->base_prefix."term_taxonomy";
				$TermsTable = $wpdb->base_prefix."terms";
				if($estore_installed) {
					$EStoreTable = $wpdb->base_prefix . "wp_eStore_tbl";
					$EStoreCategoryTable = $wpdb->base_prefix . "wp_eStore_cat_tbl";
					$EStoreCategoryRelationships = $wpdb->base_prefix . "wp_eStore_cat_prod_rel_tbl";
				}
			}
			else {
				$OptionsTableTable = $wpdb->base_prefix.$blog_id."_options";
				$PostsTable = $wpdb->base_prefix.$blog_id."_posts";
				$TermRelationshipTable = $wpdb->base_prefix.$blog_id."_term_relationships";
				$TermTaxonomyTable = $wpdb->base_prefix.$blog_id."_term_taxonomy";
				$TermsTable = $wpdb->base_prefix.$blog_id."_terms";
				if($estore_installed) {
					$EStoreTable = $wpdb->base_prefix . $blog_id . "_wp_eStore_tbl";
					$EStoreCategoryTable = $wpdb->base_prefix . $blog_id . "_wp_eStore_cat_tbl";
					$EStoreCategoryRelationships = $wpdb->base_prefix . $blog_id . "_wp_eStore_cat_prod_rel_tbl";
				}
			}
			if ($days > 0) 	$old = "AND $PostsTable.post_date >= DATE_SUB(CURRENT_DATE(), INTERVAL $days DAY)"; else $old = "";

			$ids = '';
			$estore_ids = '';

			$include_posts_id = null;
			if(isset($include_post))
				$include_posts_id = explode(',', $include_post);

			## Taxonomy
			if($estore_installed)
				$taxonomy_tables = array(
					'TermsTable'=> $TermsTable, 'EStoreCategoryTable' => $EStoreCategoryTable,
					'TermTaxonomyTable' => $TermTaxonomyTable, 'TermRelationshipTable' => $TermRelationshipTable,
					'PostsTable' => $PostsTable, 'EStoreCategoryRelationships' => $EStoreCategoryRelationships, 'EStoreTable' => $EStoreTable
					);
			else $taxonomy_tables = array(
					'TermsTable'=> $TermsTable,	'TermTaxonomyTable' => $TermTaxonomyTable,
					'TermRelationshipTable' => $TermRelationshipTable,
					'PostsTable' => $PostsTable
				);

			if($taxonomy)
			{
				$post_ids = get_category_post_ids($wpdb, $taxonomy, $taxonomy_tables, true, $estore_installed, $include_posts_id);
				$ids = $post_ids['ids'];
				if(strlen($ids) > 0)
					$ids = ' AND (' . $ids . ')';
				$estore_ids = $post_ids['estore_ids'];
			}
			if($exclude_taxonomy){

				$post_ids = get_category_post_ids($wpdb, $exclude_taxonomy, $taxonomy_tables, false, $estore_installed, $include_posts_id);
				if(!empty($post_ids['ids'])) {
					$ids .= " AND ({$post_ids['ids']})";
				}
				if(!empty($post_ids['estore_ids'])) {
					if (!empty($estore_ids))
						$estore_ids .= ' AND (';
					$estore_ids .= $post_ids['estore_ids'] . ')';
				}
			}
			/* below is my updates */


			$order_by = "";
			$aorder = array();
			$aorder1 = array();
			if($order_post_by){
				$tab_order_by1 = explode(" ", $order_post_by);
				if(($tab_order_by1[0] == "page_order" || $tab_order_by1[0] == "alphabetical_order" || $tab_order_by1[0] == "date_order") && (trim($tab_order_by1[1]) == "" || strtoupper($tab_order_by1[1]) == "DESC" || strtoupper($tab_order_by1[1]) == "ASC")){
					$order_by .= " ORDER BY ".$PostsTable.".".$order_post_by;
					$ordad = ($tab_order_by1[1]) ? $tab_order_by1[1] : "ASC";
					$aorder = array_merge($aorder,array($tab_order_by1[0] => $ordad));

					$ordad0 = "ID";
					if($tab_order_by1[0] == "date_order" )
						$ordad0 = "post_date";
					else if($tab_order_by1[0] == "alphabetical_order" )
						$ordad0 = "post_title";
					if(strtoupper($tab_order_by1[1]) == "DESC")
						$ordad1 = SORT_DESC;
					else
						$ordad1 = SORT_ASC;
					$aorder1 = array_merge($aorder1,array($ordad0 => $ordad1));
				}
			}
			if($post_type) {
				$post_type_array = mb_split(",", $post_type);
				$post_type_search = "";
				if(count($post_type_array) > 0) {
					$post_type_search = "(";
					foreach ($post_type_array as $type) {
						$post_type_search .= "(post_type = '" . trim($type) . "') OR ";
					}
					$search_len = strlen($post_type_search);
					if ($search_len > 4) {
						$post_type_search = mb_substr($post_type_search, 0, $search_len - 4) . ")";
					}
				}
				else{
					$post_type_search .= "(post_type = '" . trim($post_type) . "')";
				}
			}
			else{
				$post_type_search = "(post_type  = 'post' OR post_type = 'product')";
			}

			if($random){
				if($page > 1 && $paginate ){
					$the_post = $wpdb->get_results( $wpdb->prepare(
						"SELECT $PostsTable.ID, $PostsTable.post_title, $PostsTable.post_excerpt, $PostsTable.post_content, $PostsTable.post_author, $PostsTable.post_date, $PostsTable.guid, $PostsTable.post_type, $BlogsTable.blog_id
						FROM $PostsTable, $BlogsTable WHERE $BlogsTable.blog_id  =  $blog_id  AND $PostsTable.post_status = %s $ids  AND $post_type_search  $old  $limit"
						, 'publish'
					), ARRAY_A);
				}
				else{
					$the_post = $wpdb->get_results( $wpdb->prepare(
						"SELECT $PostsTable.ID, $PostsTable.post_title, $PostsTable.post_excerpt, $PostsTable.post_content, $PostsTable.post_author, $PostsTable.post_date, $PostsTable.guid, $PostsTable.post_type, $BlogsTable.blog_id
						FROM $PostsTable, $BlogsTable WHERE $BlogsTable.blog_id  =  $blog_id  AND $PostsTable.post_status = %s $ids  AND $post_type_search  $old ORDER BY RAND() $limit"
						, 'publish'
					), ARRAY_A);
				}
			}
			else{
				$the_post = $wpdb->get_results( $wpdb->prepare(
					"SELECT $PostsTable.ID, $PostsTable.post_title, $PostsTable.post_excerpt, $PostsTable.post_content, $PostsTable.post_author, $PostsTable.post_date, $PostsTable.guid, $PostsTable.post_type, $BlogsTable.blog_id
					FROM $PostsTable, $BlogsTable WHERE $BlogsTable.blog_id  =  $blog_id  AND $PostsTable.post_status = %s $ids  AND $post_type_search  $old    $limit"
					, 'publish'
				), ARRAY_A);



			}
			/* my updates are finished here */

			$postdata = array_merge_recursive($postdata, $the_post);

				if($estore_installed) {
					if (!empty($estore_ids))
						$estore_ids = ' WHERE ' . $estore_ids;
//print_r($estore_ids);
					if ($random) {
						if ($page > 1 && $paginate) {
//					var_dump("SELECT $EStoreTable.id AS ID, $EStoreTable.name AS post_title, $EStoreTable.description AS post_content, $EStoreTable.author_id AS post_author, $EStoreTable.product_url AS guid FROM $EStoreTable $limit;");
							$the_post = $wpdb->get_results("SELECT $EStoreTable.id AS ID,'estore' AS post_type, $EStoreTable.name AS post_title, $EStoreTable.price AS price, $EStoreTable.description AS post_content, $EStoreTable.author_id AS post_author, $EStoreTable.product_url AS guid, $EStoreTable.thumbnail_url FROM $EStoreTable $estore_ids $limit;", ARRAY_A);
						} else {
							$the_post = $wpdb->get_results("SELECT $EStoreTable.id AS ID,'estore' AS post_type, $EStoreTable.name AS post_title, $EStoreTable.price AS price, $EStoreTable.description AS post_content, $EStoreTable.author_id AS post_author, $EStoreTable.product_url AS guid, $EStoreTable.thumbnail_url FROM $EStoreTable $estore_ids ORDER BY RAND() $limit;", ARRAY_A);
//					var_dump("SELECT $EStoreTable.id AS ID, $EStoreTable.name AS post_title, $EStoreTable.description AS post_content, $EStoreTable.author_id AS post_author, $EStoreTable.product_url AS guid FROM $EStoreTable ORDER BY RAND() $limit;");
						}

					} else {
						$the_post = $wpdb->get_results("SELECT $EStoreTable.id AS ID,'estore' AS post_type, $EStoreTable.name AS post_title, $EStoreTable.price AS price, $EStoreTable.description AS post_content, $EStoreTable.author_id AS post_author, $EStoreTable.product_url AS guid, $EStoreTable.thumbnail_url FROM $EStoreTable $estore_ids $limit;", ARRAY_A);
						// var_dump("SELECT $EStoreTable.id AS ID,'estore' AS post_type, $EStoreTable.name AS post_title, $EStoreTable.description AS post_content, $EStoreTable.author_id AS post_author, $EStoreTable.product_url AS guid FROM $EStoreTable $limit;");
					}
//print_r($the_post);
					if ($the_post) {
						foreach ($the_post as &$item) {
							$item['blog_id'] = $blog_id;
						}
					}
					$postdata = array_merge_recursive($postdata, $the_post);
				}
			$ids='';
		}
	}

	/* below is my updates */
	if(!$random ){
		if($order_by == "")
			usort($postdata, "custom_sort");
		else{

			$postdata = array_msort($postdata, $aorder1);
		}
	}
	/* my updates are finished here */

	/* below is my updates */
	$exclude_post2 = explode(",",$exclude_post);

	foreach($exclude_post2 as $row){

		$postdata = removeElementWithValue($postdata, "ID", $row);

	}
	/* my updates are finished here */

	if(isset($include_post)){

		$include_post2 = explode(",",$include_post);

		foreach($postdata as $postX){

			if(in_array($postX['ID'], $include_post2)){

				$newPostdata[] = $postX;
			}

		}

		$postdata = $newPostdata;
	}
	if(is_array($postdata)) {
		if ($paginate) {
			if ($column > 1) {
				$column_list = ceil($list / $column);
				$list = $column_list * $column;
				if (!$list) {
					$list = $column;
					$column_list = 1;
				}
			}
			$page = get_query_var('paged');
			if (!$page) $page = get_query_var('page');
			if (!$page) $page = 1;

			/* below is my updates */
			//$postdata = super_unique($postdata,"ID");
			/* my updates are finished here */


			/* below is my updates */
			/*$exclude_post2 = explode(",",$exclude_post);

            foreach($exclude_post2 as $row){

                $postdata = removeElementWithValue($postdata, "ID", $row);

            }*/
			/* my updates are finished here */

			//if(!in_array($the_post['ID'], $exclude_post2)){


			$total_records = count($postdata);
			$total_pages = ceil($total_records / $list);
			$postdata = array_slice($postdata, ($page - 1) * $list, $list);

		} /* below is my updates */
		else {
			$postdata = array_slice($postdata, 0, $list);
		}
		/* my updates are finished here */
		if ($column > 1) {
			$count = count($postdata);
			if (!$paginate) $column_list = ceil($count / $column);
			for ($i = 0; $i < $column; ++$i) {
				if ($count < ($column_list * $column)) $column_list = ceil($count / $column);
				$colomn_data[$i] = array_slice($postdata, ($i) * $column_list, $column_list);
			}
		} else {
			$colomn_data[0] = $postdata;
		}
	}

	## OUTPUT
	if($page_title_style) {
		?>

		<style type="text/css">
			h2.pagetitle
			{
			<?php echo  $page_title_style; ?>
			<?php //echo get_option('net-style'); ?>
			}
		</style>
		<?php
	}
	$html = '<div id="netsposts-menu">';
	if($menu_name)
	{
		$menu=array('menu'=>$menu_name, 'menu_class'=>$menu_class, 'container_class' => $container_class);
		wp_nav_menu($menu);
	}
	$html .= '</div>';
	if($postdata)
	{
		//$html .= "<style>";
		//$html .= get_option('net-style');
		//$html .= "</style>";

		$html .= '<div id="block-wrapper">';

		if(isset($post_height)){
			$height_content = "height: ".$post_height."px;";
		}else{
			$height_content = "";
		}

		if($title) $html .= '<span class="netsposts-title">'.$title.'</span><br />';

		foreach($colomn_data as  $data)
		{

			if($column > 1) $html .= '<div class ="netsposts-column" style="width: '.$column_width.'px;">';

			foreach($data as $key => $the_post)
			{

				$blog_details = get_blog_details( $the_post['blog_id']);
				$blog_name = $blog_details->blogname;
				$blog_url = $blog_details->siteurl;

				if($titles_only) $title_class = 'netsposts-post-titles-only'; else $title_class = 'netsposts-posttitle';
				$html .= html_entity_decode($wrap_start).'<div class="netsposts-content" style="'.$height_content.'">';
				$html .= htmlspecialchars_decode($wrap_title_start);
				/*
				 * This code creates title link
				 */
				if($include_link_title){
					if(!$exclude_title_links || !array_has_value($the_post['ID'], $exclude_title_links)){
						$html .= '<a href="' . $the_post['guid'] . '">' . ShortenText($the_post['post_title'],$title_length) . '</a>';
					}
					else{
						$html .= '<span class="'.$title_class.'" style="color: '.$title_color.';">' . ShortenText($the_post['post_title'],$title_length) . '</span>';
					}
				}
				else{
					$html .= '<span class="'.$title_class.'" style="color: '.$title_color.';">' . ShortenText($the_post['post_title'],$title_length) . '</span>';
				}
				$html .= htmlspecialchars_decode($wrap_title_end);
				if(!$titles_only)
				{
					$date_post = '';
					if(array_key_exists('post_date', $the_post)) {
						$date = new DateTime(trim($the_post['post_date']));
						$date_post = $date->format($date_format);
					}
					if ($meta_info != "false") {

						if ($meta_width == "100%") {
							$width = 'width: 100%;';
						} else {
							$width = "width: " . $meta_width . "px;";
						}

						$html .= '<span class="netsposts-source" style="height: 24px; margin-bottom: 5px; overflow: hidden; ' . $width . '"> ' . __('<span>Published</span>', 'netsposts') . ' ' . $date_post . ' ' . __('<span>in</span>', 'netsposts') . '  <a href="' . $blog_url . '">' . $blog_name . '</a>';
					}

					##  Full metadata
					if( $show_author)
					{
						if($column > 1) $html .= '<br />';
						$html .= ' ' . __('Author','netsposts'). ' ' . '<a href="'.$blog_url .'?author='.  $the_post['post_author'] .'">'. get_the_author_meta( 'display_name' , $the_post['post_author'] ) . ' </a>';
					}
					$html .= '</span>';
					if($thumbnail)
					{
						$html .= htmlspecialchars_decode($wrap_image_start);
						if($the_post['post_type'] != 'estore')
							$html .= '<a href="'.$the_post['guid'].'">'.get_thumbnail_by_blog($the_post['blog_id'],$the_post['ID'],$size, $image_class, $column).'</a>';
						else $html .= '<a href="'.$the_post['guid'].'">' . create_estore_product_thumbnail($the_post['thumbnail_url'], $the_post['post_title'], $size, $image_class, $column) . '</a>';
						$html .= htmlspecialchars_decode($wrap_image_end);
						$html .= htmlspecialchars_decode($wrap_text_start);
						$html .= '<p class="netsposts-excerpt" style="color: '.$text_color.';">';
						$the_post['post_content'] = preg_replace("/<img[^>]+\>/i", "", $the_post['post_content']);
					}
					/*Show price*/
					if($the_post['post_type'] == 'product' && $price_woocommerce == true && $woocommerce_installed){
						$_current_product = wc_get_product( $the_post['ID'] );
						if($_current_product) {
							$html .= htmlspecialchars_decode($wrap_price_start);
							$html .= '<p class="netsposts-price">' . wc_price($_current_product->get_regular_price()) . '</p>';
							$html .= htmlspecialchars_decode($wrap_price_end);
						}
					}
					if($the_post['post_type'] == 'estore' && $price_estore == true && $estore_installed){
						$html .= htmlspecialchars_decode($wrap_price_start);
						$html .= '<p class="netsposts-price">'.wc_price($the_post['price']).'</p>';
						$html .= htmlspecialchars_decode($wrap_price_end);
					}
					/*End show*/
					if($auto_excerpt || !$the_post['post_excerpt'])  {
						if($excerpt_length)
							$exerpt  = get_words_excerpt($excerpt_length, $the_post['post_content'], $the_post['guid']);
						else if($excerpt_letters_length)
							$exerpt = get_letters_excerpt($excerpt_letters_length, $the_post['post_content'], $the_post['guid']);
					}else $exerpt  = $the_post['post_excerpt'];
					if($full_text){
						$text = $the_post['post_content'];
					}else{
						if($manual_excerpt_length)
							$text  = get_words_excerpt($manual_excerpt_length, $the_post['post_content'], $the_post['guid']);
						else if($manual_excerpt_letters_length)
							$text = get_letters_excerpt($manual_excerpt_letters_length, $the_post['post_content'], $the_post['guid']);
						else{
							$text = $exerpt;
						}
					}
					$html .= strip_shortcodes( $text);
					$hide_all_links = get_option('hide_all_readmore_links');
					if(empty($hide_all_links) || $hide_all_links == false) {
						$show_link = true;
						$pages_string = get_option('hide_readmore_link_pages');
						$pages_without_readmore_link = array();
						$title = $the_post['post_title'];
						if (!empty($pages_string) > 0) {
							$pages_without_readmore_link = explode(';', $pages_string);
							foreach ($pages_without_readmore_link as $item) {
								if (trim(strtolower($title)) == trim(strtolower($item))) {
									$show_link = false;
								}
							}
						}
						if ($show_link) {
							$html .= ' <a href="' . $the_post['guid'] . '">read more&rarr;</a></p>';
						}
					}
					$html .= htmlspecialchars_decode($wrap_text_end);
				}

				$html .= "</div>";
				$html .= "<br />";

				$html .= html_entity_decode($wrap_end);

				$html .= "<div style='clear: both;'></div>";

			}
			if($column > 1) $html .= '</div>';
		}
		$html .= '<div class="clear"></div>';
		if(($paginate) and ($total_pages>1))
		{
			$html .= '<div id="netsposts-paginate">';
			$big = 999999999;
			$html .= paginate_links( array(
				'base' => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
				'format' => '?paged=%#%',
				'current' => $page,
				'total' => $total_pages,
				'prev_text'    => __($prev),
				'next_text'    => __($next),
				'end_size'     => $end_size,
				'mid_size'     =>  $mid_size
			) );

			$html .= '</div>';

		}
		$html .= '</div>';
	}

	return $html;
}
##########################################################

function get_thumbnail_by_blog($blog_id=NULL,$post_id=NULL,$size='thumbnail',$image_class, $column)
{
	if( !$blog_id  or !$post_id ) return;
	switch_to_blog($blog_id);
	$thumb_id = has_post_thumbnail( $post_id );
	if(!$thumb_id)
	{
		restore_current_blog();
        return FALSE;
	}
	$blogdetails = get_blog_details( $blog_id );

	$sizes = get_image_sizes(array($blog_id));

	if(array_key_exists($size, $sizes))
		$image_size = $sizes[$size];
	else{
		$image_size = $sizes['thumbnail'];
	}

	if($column > 1){
		$image_class = $image_class." more-column";
	}
	$attrs = array('class'=> $image_class);
	$thumbcode = str_replace( $current_blog->domain . $current_blog->path, $blogdetails->domain . $blogdetails->path, get_the_post_thumbnail( $post_id, array($image_size['width'], $image_size['height']), $attrs ) );
	restore_current_blog();
	return $thumbcode;
}

function get_image_sizes($blog_ids) {
	global $_wp_additional_image_sizes;
	$sizes = array();
	foreach($blog_ids as $id) {
		switch_to_blog($id);
		foreach (get_intermediate_image_sizes() as $_size) {
			if (in_array($_size, array('thumbnail', 'medium', 'medium_large', 'large'))) {
				$sizes[$_size]['width'] = get_option("{$_size}_size_w");
				$sizes[$_size]['height'] = get_option("{$_size}_size_h");
				$sizes[$_size]['crop'] = (bool)get_option("{$_size}_crop");
			} elseif (isset($_wp_additional_image_sizes[$_size])) {
				$sizes[$_size] = array(
					'width' => $_wp_additional_image_sizes[$_size]['width'],
					'height' => $_wp_additional_image_sizes[$_size]['height'],
					'crop' => $_wp_additional_image_sizes[$_size]['crop'],
				);
			}
		}
        restore_current_blog();
	}
	return $sizes;
}
/*
 * Function to search value in array excluding spaces
 */
function array_has_value($needle, $array){
	foreach($array as $value){
		if(trim($needle) == trim($value))
			return true;
	}
	return false;
}

function create_estore_product_thumbnail($image_url, $alt, $size = 'thumbnail', $image_class, $column = 1){
	global $img_sizes;
	if(!empty($image_url)){
		$img = '<img src="' . $image_url . '" alt="' . $alt . '" ';
		if($image_class)
			$img .= 'class="' . $image_class;
		if($column > 1)
			$img .= ' more-column';
		$img .= '"';
		if(array_key_exists($size, $img_sizes)){
			$img .= 'width="' . $img_sizes[$size]['width'] .'px" ';
			$img .= 'height="' . $img_sizes[$size]['height'] . 'px"';
		}
		$img .= '/>';
		return $img;
	}
	return '';
}

function get_words_excerpt($length,$content,$permalink)
{
	if(!$length) return $content;
	else {
		$content = strip_tags($content);
		$words = explode(' ', $content);
		if(count($words) > $length) {
			$words = array_slice($words, 0, $length);
			$content = implode(' ', $words);

			/* Original Code return   $content.'... <a href="'.$permalink.'">   '.__('read more&rarr;','trans-nlp').'</a>'; */
			/* Edited Code Turned argument 'read more&rarr;' to ''*/
			return $content . '... <a href="' . $permalink . '">   ' . __('', 'trans-nlp') . '</a>';
		}
		else {
			$content = implode(' ', $words);
			/* Original Code return   $content.' <a href="'.$permalink.'">   '.__('read more&rarr;','trans-nlp').'</a>'; */
			/* Edited Code Turned argument 'read more&rarr;' to ''*/
			return $content . ' <a href="' . $permalink . '">   ' . __('', 'trans-nlp') . '</a>';
		}
	}
}

function get_letters_excerpt($length,$content,$permalink)
{
	if(!$length) return $content;
	else {
		$content = strip_tags($content);
		$content = substr($content, 0,  intval($length));
		$words = explode(' ', $content);
		array_pop($words);
		$content = implode(' ', $words);

		/* Original Code return   $content.'... <a href="'.$permalink.'">   '.__('read more&rarr;','trans-nlp').'</a>'; */
		/* Edited Code Turned argument 'read more&rarr;' to ''*/
		return   $content.'... <a href="'.$permalink.'">   '.__('','trans-nlp').'</a>';
	}
}

function get_category_post_ids($wpdb, $taxonomy, $db_tables, $include = true, $estore_installed, &$include_posts){
	if(strpos($taxonomy,',') > 0)
		$categories = explode(',',$taxonomy);
	else $categories = [$taxonomy];
	$cat_arr = array();
	$estore_cat_array = array();
	$result_ids = array('ids'=>'', 'estore_ids' => '');
	if($include) {
		$symbol = "=";
		$union = 'OR';
	}
	else {
		$symbol = "!=";
		$union = 'AND';
	}
	foreach($categories as $category)
	{
		$cat_id = $wpdb->get_var($wpdb->prepare("SELECT term_id FROM " . $db_tables['TermsTable'] . " WHERE LOWER(slug) = LOWER('%s') ", $category));
		if($cat_id){
			$cat_arr[] = $cat_id;
		}
		else if($estore_installed){
			$cat_id = $wpdb->get_var($wpdb->prepare("SELECT cat_id FROM " . $db_tables['EStoreCategoryTable'] . " WHERE LOWER(cat_name) = LOWER('%s') " , $category));
			if($cat_id){
				$estore_cat_array[] = $cat_id;
			}
		}
	}
	$taxonomy_arr = array();
	foreach($cat_arr as $cat_id)
	{
		$tax_id = $wpdb->get_var($wpdb->prepare("SELECT term_taxonomy_id FROM " . $db_tables['TermTaxonomyTable'] . " WHERE  term_id = %s", $cat_id));
		if($tax_id) $taxonomy_arr[] = $tax_id;
	}

	foreach($taxonomy_arr as $tax_id)
	{
		$post_ids = $wpdb->get_results($wpdb->prepare("SELECT object_id FROM " . $db_tables['TermRelationshipTable'] . " WHERE term_taxonomy_id = %s", $tax_id), ARRAY_A);
		if( !empty($post_ids) )
		{
			foreach($post_ids as $key=>$object_id)
			{
				if(!$include_posts || in_array($object_id['object_id'], $include_posts))
					$result_ids['ids'] .=  " (" . $db_tables['PostsTable'] . ".ID " . $symbol . " ".$object_id['object_id']. ') ' . $union;
			}
		}
	}

	if(strlen($result_ids['ids']) > 2)
		$result_ids['ids'] = substr($result_ids['ids'], 0, strrpos($result_ids['ids'], ' '));
	elseif($include)
		$result_ids['ids'] = $db_tables['PostsTable'] . ".ID = NULL ";

	if($estore_installed) {
		foreach ($estore_cat_array as $estore_category) {
			$estore_post_ids = $wpdb->get_results($wpdb->prepare("SELECT prod_id FROM " . $db_tables['EStoreCategoryRelationships'] . " WHERE cat_id = %s", $estore_category), ARRAY_A);
			if (!empty($estore_post_ids)) {
				foreach ($estore_post_ids as $key => $prod_id) {
					if(!$include_posts || in_array($prod_id['prod_id'], $include_posts))
						$result_ids['estore_ids'] .= " (" . $db_tables['EStoreTable'] . ".id " . $symbol . " " . $prod_id['prod_id'] . ') ' . $union;
				}
			}
		}

		if (strlen($result_ids['estore_ids']) > 2)
			$result_ids['estore_ids'] = substr($result_ids['estore_ids'], 0, strrpos($result_ids['estore_ids'], ' '));
		elseif ($include)
			$result_ids['estore_ids'] = $db_tables['EStoreTable'] . ".id = NULL ";
	}
	if($include_posts)
		$include_posts = array();
	return $result_ids;
}


function custom_sort($a,$b)
{
	if(array_key_exists('post_date', $a) && array_key_exists('post_date', $b)) {
		return $a['post_date'] < $b['post_date'];
	}
	else if(array_key_exists('post_date', $a)){
		return false;
	}
	else if(array_key_exists('post_date', $b)){
		return true;
	}
	else return false;
}

function is_installed($wpdb, $plugin){
	if($plugin == 'woocommerce'){
		$result = $wpdb->get_results("SHOW TABLES LIKE '" . $wpdb->base_prefix . "woocommerce_termmeta'", ARRAY_A);
		return count($result) == 1;
	}
	if($plugin == 'estore'){
		$result = $wpdb->get_results("SHOW TABLES LIKE '" . $wpdb->base_prefix . "wp_estore_tbl'", ARRAY_A);
		return count($result) == 1;
	}
}

/* below is my updates */

function array_msort($array, $cols)
{
	$colarr = array();
	foreach ($cols as $col => $order) {
		$colarr[$col] = array();
		foreach ($array as $k => $row) { $colarr[$col]['_'.$k] = strtolower($row[$col]); }
	}
	$eval = 'array_multisort(';
	foreach ($cols as $col => $order) {
		$eval .= '$colarr[\''.$col.'\'],'.$order.',';
	}
	$eval = substr($eval,0,-1).');';
	eval($eval);
	$ret = array();
	foreach ($colarr as $col => $arr) {
		foreach ($arr as $k => $v) {
			$k = substr($k,1);
			if (!isset($ret[$k])) $ret[$k] = $array[$k];
			$ret[$k][$col] = $array[$k][$col];
		}
	}
	return $ret;

}

/* my updates are finished here */

###################  TOOL PAGE  #########################

function netsposts_page()
{
	?>
	<div class="wrap">
		<table style="width:100%;">
			<tbody>
			<tr>
				<td colspan="2">
					<div id="icon-users" class="icon32"><br /></div>
					<h2>Network Posts Extended Help</h2>
					<hr />
				</td>
			</tr>
			<tr>
				<td>
					<form method="post" action="options.php" id="netspostssettings">
						<?php wp_nonce_field('update-options'); ?>
						<?php //$styling  = get_option('net-style'); ?>
						<?php
							$pages = get_option('hide_readmore_link_pages');
						?>
						<!--Add extra css styling: <?php //echo "Here is a good source for custom css styling: <a target='ejejcsingle' href='http://www.w3schools.com/css/css_id_class.asp'>w3schools class tutorial</a>"; ?></br>
        <textarea style="width: 500px; height: 500px;" name="net-style" ><?php //echo $styling; ?></textarea>-->
						<br/><br/>
						<div>
							<input type="checkbox" name="hide_all_readmore_links" id="hide_all_readmore_links" value="1" <?php checked('1', get_option('hide_all_readmore_links'));?>/>
							<label for="hide_all_readmore_links">Hide all 'read more' links</label>
						</div>
						<br/><br/>
						Pages without "read more" links (Write titles of pages. Each title must ends with ";" symbol):<br/>
						<textarea style="width: 500px; height: 500px;" name="hide_readmore_link_pages"><?php echo $pages; ?></textarea>
						</br>
						<input type="hidden" name="action" value="update" />
						<input type="hidden" name="page_options" value="hide_readmore_link_pages, hide_all_readmore_links" />
						<input type="submit" value="Save Changes" class="btn btn-success"/>
					</form>
					<?php global $thumbnail_manager;
					echo $thumbnail_manager->get_form();?>
				</td>
				<td style="vertical-align:top;margin-left:100px;">
					If you like this plugin please donate:
					<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
						<input type="hidden" name="cmd" value="_donations">
						<input type="hidden" name="business" value="john@johncardell.com">
						<input type="hidden" name="lc" value="US">
						<input type="hidden" name="item_name" value="Network Shared Posts">
						<input type="hidden" name="no_note" value="0">
						<input type="hidden" name="currency_code" value="USD">
						<input type="hidden" name="bn" value="PP-DonationsBF:btn_donateCC_LG.gif:NonHostedGuest">
						<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
						<img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
					</form>

					</br></br>

					<?php
					echo "For a complete tutorial visit:<br /><a target='ejecsingle' href='https://wp-plugins.johncardell.com/network-posts-extended/'>https://wp-plugins.johncardell.com/network-posts-extended/</a><br /><br />";
					echo "Need professional help for your blog, plugin, or script? Try Freelancer:<br /><a target='ejejcsingle' href='https://www.freelancer.com/affiliates/johnzenavw' title='Higher a Freelancer at Freelancer.com'><img alt='Freelance Jobs at Freelancer.com' src='/wp-content/plugins/network-posts-extended/pictures/Freelancer-black.jpg' style='width:480px;height:auto;' class='img-hover' /></a><br />";
					echo "For quality web hosting use <a href='https://interserver.net/dock/website-289738.html' title='Supreme Web Hosting'>Interserver.net</a>:<br /><a target='ejejcsingle' href='https://interserver.net/dock/website-289738.html' title='Quality Affordable Web Hosting'><img alt='Interserver.net quality web hosting' src='/wp-content/plugins/network-posts-extended/pictures/interserverwebhosting.gif' style='width:480px;height:auto;' class='img-hover' /></a><br />";
					?>
					</br></br>
				</td>
			</tr>
			</tbody>
		</table>
	</div>
	<?php

}


?>