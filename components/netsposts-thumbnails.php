<?php
/*
Plugin Name: Network Posts Ext Thumbnails
Plugin URI: https://wp-plugins.johncardell.com/network-posts-extended/
Description: Network Posts Extended plugin enables you to share posts over WP Multi Site network.  You can display on any blog in your network the posts selected by taxonomy from any blogs including main.
Version: 0.1.0
Author: John Cardell
Author URI: http://www.johncardell.com

Copyright 2014 John Cardell

*/
namespace NetworkPosts\Components;

use NetworkPosts\Components\WebSocket;

class Netsposts_thumbnails
{
    protected $netsposts_thumbnails = array();
    protected $keys = array();
    protected $db = null;
    private $initialized = false;
    private $is_admin = false;
    private $action_progress;

    const SETTINGS = 'netsposts-settings';
    const SIZES = 'netsposts-thumbnail-sizes';

    public function __construct($wpdb)
    {
        $this->db = $wpdb;
    }

    public function init($admin){
        $this->load_settings();
        $this->initialized  = true;
        $this->is_admin = $admin;
        $this->add_hooks();
    }

    public function is_initialized(){
        return $this->initialized;
    }

    public function add_hooks()
    {
        add_action('init',array($this, 'init_sizes'));
        if($this->is_admin) {
            add_filter('image_size_names_choose', array($this, 'add_size_name'));

            add_action('admin_post_netsposts_add_size', array($this, 'manage_post_data'));
            add_action('admin_post_netsposts_remove_size', array($this, 'remove_size'));
            add_action('admin_post_generate_images', array($this, 'generate_images'));

            add_action('wp_ajax_netsposts_get_sizes', array($this, 'get_thumbnail_sizes'));
            add_action('wp_ajax_netsposts_get_size', array($this, 'get_by_id'));
            add_action('wp_ajax_get_add_size_form', array($this, 'get_add_size_form'));
            add_action('wp_ajax_get_image_generator_form', array($this, 'get_image_generator_form'));
            add_action('admin_post_get_dummy_thumbnails', array($this, 'get_dummy_thumbnails'));
        }
    }

    function get_dummy_thumbnails(){
        ob_start(null, 0, PHP_OUTPUT_HANDLER_CLEAN);
        for($i = 0; $i < 100; $i++){
            $string = '{file:' . '"file"' . $i . '.png' . ', progress:' . $i . '}';
            echo $string . PHP_EOL . PHP_EOL;
            sleep(500);
            ob_flush();
            flush();
        }
    }

    function init_sizes(){

        if($this->is_initialized()) {
            if (function_exists('add_image_size')) {
                $this->add_new_sizes();
            }
        }
    }

    public function add_size_name($sizes)
    {
        $count = count($this->netsposts_thumbnails);
        for ($i = 0; $i < $count; $i++) {
            $size = $this->netsposts_thumbnails[$i];
            $sizes[$size['data']['a']] = $size['data']['n'];
        }
        return $sizes;
    }

    public function add_new_sizes()
    {
        $count = count($this->netsposts_thumbnails);
        for ($i = 0; $i < $count; $i++) {
            $size = $this->netsposts_thumbnails[$i]['data'];
            if (isset($size['h']) && is_numeric($size['h'])) {
                $height = $size['h'];
            } else $height = 9999;
            if ($size['c'] == "true") {
                add_image_size($size['a'], $size['w'], $height, true, array($size['cx'], $size['cy']));
            } else add_image_size($size['a'], $size['w'], $height, false);
        }
    }

    public function register_scripts()
    {
        $dir = dirname(__FILE__);

        wp_register_script('settings-ajax', plugins_url(
            '/js/settings-ajax.js', $dir)
        );
        wp_enqueue_script('settings-ajax');

        $this->register_table_scripts($dir);
        $this->register_new_size_form_scripts($dir);
        $this->register_image_generator_form_scripts($dir);

        wp_register_script('netsposts_admin_script', plugins_url('/js/settings.js', dirname(__FILE__)),
            array('additional-sizes-table-view', 'add-size-form-view', 'image-generator-view'), '1.0.0', true);
        wp_enqueue_script('netsposts_admin_script');
        wp_localize_script('netsposts_admin_script', 'data', array(
            'loading_gif' => plugins_url('/pictures/3.gif', dirname(__FILE__)), 'base_path'=>BASE_PATH,
            'empty_table' => 'No entries',
            'error_message'=> 'Something went wrong.',
            'create_item_title'=> 'Create item',
            'modify_item_title'=> 'Modify item',
            'notifier_url'=>plugins_url('/components/action-progress.php', dirname(__FILE__))
        ));
    }

    private function register_table_scripts($dir){
        wp_register_script('additional-sizes-table-presenter', plugins_url(
            '/js/settings/AdditionalSizesTable/TablePresenter.js', $dir),
            array('settings-ajax'), '1.0.0', true
        );
        wp_enqueue_script('additional-sizes-table-presenter');

        wp_register_script('additional-sizes-table-view', plugins_url(
            '/js/settings/AdditionalSizesTable/TableView.js', $dir),
            array('additional-sizes-table-presenter'), '1.0.0', true
        );
        wp_enqueue_script('additional-sizes-table-view');
    }

    private function register_new_size_form_scripts($dir){
        wp_register_script('add-size-form-presenter', plugins_url(
            '/js/settings/AddSizeForm/FormPresenter.js', $dir),
            array('settings-ajax'), '1.0.0', true
        );
        wp_enqueue_script('add-size-form-presenter');

        wp_register_script('add-size-form-view', plugins_url(
            '/js/settings/AddSizeForm/FormView.js', $dir),
            array('add-size-form-presenter'), '1.0.0', true
        );
        wp_enqueue_script('add-size-form-view');
    }

    private function register_image_generator_form_scripts($dir){
        wp_register_script('image-generator-form-presenter', plugins_url(
            '/js/settings/ImageGeneratorForm/ImageGeneratorPresenter.js', $dir),
            array('settings-ajax'), '1.0.0', true
        );
        wp_enqueue_script('image-generator-form-presenterr');

        wp_register_script('image-generator-view', plugins_url(
            '/js/settings/ImageGeneratorForm/ImageGeneratorView.js', $dir),
            array('image-generator-form-presenter'), '1.0.0', true
        );
        wp_enqueue_script('image-generator-view');
    }

    public function is_model_valid($model)
    {
        if (!empty($model)) {
            if (!isset($model['name']))
                return false;
            if (!is_numeric($model['width']))
                return false;
            return true;
        } else return false;
    }

    public function manage_post_data()
    {
        if ($this->is_model_valid($_POST)) {
            $keys_modified = false;
            $keys_count = count($this->keys);
            if ($keys_count == 0 || $keys_count < count($_POST)) {
                $this->keys = $this->generate_keys($_POST);
                $keys_modified = true;
            }
            if(isset($_POST['id'])) {
                $option = $this->find_with_id($_POST['id']);
                $modify = true;
            }
            else{
                $count = count($this->netsposts_thumbnails);
                if($count > 0) {
                    $option['id'] = $this->netsposts_thumbnails[$count - 1]['id'] + 1;
                }
                else $option['id'] = 0;
                $option['data'] = array();
                $modify = false;
            }
            unset($_POST['id']);
            if ($_POST['crop'] == "false") {
                unset($_POST['crop_x']);
                unset($_POST['crop_y']);
            }
            if(!isset($option['data']['a'])) {
                $option['data']['a'] = $this->create_alias($_POST['name']);
                $_POST['alias'] = $option['data']['a'];
            }
            $option['data'] = $this->replace_keys($option['data'], $_POST, $this->keys);
            if ($modify) {
                $this->netsposts_thumbnails = $this->replace_size_with_id($option, $this->netsposts_thumbnails);
            } else $this->netsposts_thumbnails[] = $option;

            $result_object = '{"id":"' . $option['id'] . '",';
            $data = json_encode($_POST);
            $result_object .= '"data":' . $data . '}';
            echo $result_object;

            if($this->get_option_for_plugin(Netsposts_thumbnails::SIZES)) {
                $this->update_option_for_plugin( Netsposts_thumbnails::SIZES, json_encode( $this->netsposts_thumbnails ) );
            }
            else{
                $this->insert_option_for_plugin(Netsposts_thumbnails::SIZES, json_encode( $this->netsposts_thumbnails ));
            }
            if($keys_modified) {
                if($this->get_option_for_plugin(Netsposts_thumbnails::SETTINGS)) {
                    $this->update_option_for_plugin( Netsposts_thumbnails::SETTINGS, json_encode( $this->keys ) );
                }
                else{
                    $this->insert_option_for_plugin(Netsposts_thumbnails::SETTINGS, json_encode( $this->keys ));
                }
            }
        } else http_response_code(400);
    }

    protected function generate_keys($array)
    {
        $items = array();
        foreach ($array as $key => $value) {
            $words = mb_split('_', $key);
            $new_key = '';
            foreach ($words as $word) {
                $new_key .= substr($word, 0, 1);
            }
            $items[$key] = $new_key;
        }
        return $items;
    }

    protected function translate_names($array, $items)
    {
        $result = array();
        foreach ($items as $key => $value) {
            if (isset($array[$value]))
                $result[$key] = $array[$value];
        }
        return $result;
    }

    protected function replace_keys($source, $replacement, $items)
    {
        foreach ($items as $key => $value) {
            $source[$value] = $replacement[$key];
        }
        return $source;
    }

    protected function replace_size_with_id($size, $arr)
    {
        if (isset($size['id']) && !empty($arr)) {
            foreach ($arr as $key => $value) {
                if ($value['id'] == $size['id']) {
                    $arr[$key] = $size;
                }
            }
        }
        return $arr;
    }

    protected function get_keys()
    {
        $setting_string = $this->get_option_for_plugin(Netsposts_thumbnails::SETTINGS);
        $keys = array();
        if (!empty($setting_string)) {
            $keys = json_decode($setting_string, true);
        }
        return $keys;
    }

    public function remove_size()
    {
        if (isset($_POST['id'])) {
            if (count($this->netsposts_thumbnails) > 0) {
                global $removed;
                $removed = false;
                $new_options = array_filter($this->netsposts_thumbnails, function ($item) {
                    if ($item['id'] != $_POST['id']) {
                        return true;
                    } else {
                        global $removed;
                        $removed = true;

                        return false;
                    }
                });
                if ($removed) {
                    $this->netsposts_thumbnails = $new_options;
                    http_response_code(200);
                    $this->update_option_for_plugin(Netsposts_thumbnails::SIZES, json_encode($this->netsposts_thumbnails));
                } else {
                    http_response_code(404);
                }
            }
        } else http_response_code(400);
    }

    public function get_thumbnail_sizes()
    {
        if(count($this->netsposts_thumbnails) > 0){
            $result_items = array();
            foreach ($this->netsposts_thumbnails as $item) {
                $new_item = array();
                $new_item['id'] = $item['id'];
                $new_item['data'] = $this->translate_names($item['data'], $this->keys);
                $result_items[] = $new_item;
            }
            echo json_encode($result_items);
        }
        else {
            http_response_code(204);
        }
    }

    public function get_by_id()
    {
        if (isset($_GET['id'])) {
            $item = $this->find_with_id($_GET['id']);
            if($item) {
                $item['data'] = $this->translate_names($item['data'], $this->keys);
                echo json_encode($item);
            }
            else http_response_code(204);
        } else http_response_code(400);
    }

    public function get_single_value($array)
    {
        $result = null;
        foreach ($array as $item) {
            $result = $item;
        }
        return $result;
    }

    public function generate_images(){
        session_start();
        if(isset($_POST['sizes'])){
            $item = $this->netsposts_thumbnails[$_POST['sizes']];
            if($item){
                if($_POST['select_images'] == 'selected_number' && isset($_POST['images_count']))
                $attachments = $this->get_all_attachments((int)$_POST['images_count']);
                else $attachments = $this->get_all_attachments();
                $count = 1;

                ob_start(0, null, PHP_OUTPUT_HANDLER_CLEANABLE);
                for($i = 1; $i < count($attachments); $i++) {
                    if(count($attachments[$i]) > 0) {
                        switch_to_blog($i);
                        $dir = wp_upload_dir();
                        foreach ($attachments[$i] as $attachment) {
                            $image = wp_get_attachment_metadata($attachment['id']);
                            if ($image) {
                                if(!$item['data']['c'])
                                    $result = image_make_intermediate_size($dir['basedir'] . '/' .  $image['file'], $item['data']['w'], $item['data']['h'], false);
                                else $result = image_make_intermediate_size($dir['basedir'] . '/' .  $image['file'], $item['data']['w'],
                                    $item['data']['h'], array($item['data']['cx'], $item['data']['cy']));
                                if($result){
                                    $result['file'] = substr($result['file'], strrpos('/', $result['file']));
                                    $image['sizes'][$item['data']['a']] = $result;
                                    wp_update_attachment_metadata($attachment['id'], $image);
                                }
                                echo '{"progress":'  .$count / $attachments['count'] * 100 .', "data":"' . $image['file'] . '"}';
                                ob_flush();
                                flush();
                                $count++;
                                //$_SESSION['netsposts_progress'] = $count / $attachments['count'] * 100;
                                //$_SESSION['netsposts_data'] = $image['file'];
                                usleep(300);
                            }
                        }
	                    restore_current_blog();
                    }
                }

                http_response_code(200);
            }
        }
    }

    public function create_alias($name)
    {
        return str_replace(' ', '-', strtolower($name));
    }

    public function get_image_generator_form(){

        echo '<div class="card invisible" id="image_generator_form_wrapper">
                <form id="image_generator_form" method="POST" action="admin-post.php?action=netsposts_regenerate_images">
						        <h3 id="form_title">Generate images</h3>
						        <fieldset>
                                    <input type="hidden" name="id" id="size_id" />
                                     <select name="sizes">
                                    </select>
                                    <div>
                                            <p>
                                                <label><input type="radio" name="select_images" value="all" checked/>All</label>
                                            </p>
                                            <p>
                                                <label><input type="radio" name="select_images" value="selected_number"/>Select number</label>
                                            </p>
                                    </div>
                                    <div>
                                        <div class="param-group">
                                            <label for="images_count">Images count</label>
                                            <input type="number" name="images_count" id="images_count" class="form-control"/>
                                            <label class="error-text"></label>
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-success" id="regenerate">Generate</button>
                                    <button type="button" class="btn btn-remove btn-flat" id="close_generator_form">Close</button>
                                </fieldset>
						</form>
						<div class="progress-bar-wrapper invisible" id="regeneration_progress">
                            <label id="image_file_name"></label>
                            <progress id="regeneration_progress" max="100" value="0"/>
                        </div>
					</div>';
    }

    public function get_add_size_form(){
        echo '<form id="size_parameters" class="card invisible" method="POST" action="admin-post.php?action=netsposts_add_size">
						        <h3 id="form_title">Create item</h3>
						        <div class="model-loading">
						            <p>
						                <img src="' . plugins_url('/pictures/3.gif', dirname(__FILE__)) . '" width="30px" alt="loading"/>
                                        Loading model
                                    </p>
                                </div>
						        <fieldset>
                                    <input type="hidden" name="id" id="size_id" />
                                    <div>
                                        <div class="param-group">
                                            <label for="name">Caption</label>
                                            <input type="text" name="name" id="name" class="form-control"/>
                                            <label class="error-text"></label>
                                        </div>
                                        <button type="submit" class="btn btn-success" id="add_size">Create</button>
                                        <button type="button" class="btn btn-remove btn-flat" id="close_add_size_form">Close</button>
                                    </div>
                                    <div>
                                        <div class="param-group">
                                            <label for="alias">Alias</label>
                                            <input type="text" name="alias" id="alias" class="form-control"/>
                                            <label class="error-text"></label>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="param-group">
                                            <label for="image_width">Maximum width</label>
                                            <input type="number" name="width" id="width" class="form-control"/>
                                            <label class="error-text"></label>
                                        </div>
                                        <div class="param-group">
                                            <label for="height">Maximum height</label>
                                            <input type="number" name="height" id="height" class="form-control"/>
                                            <label class="error-text"></label>
                                        </div>
                                    </div>
                                    <div>
                                        <label><input type="checkbox" id="crop" name="crop"/>Crop</label>
                                        <br/>
                                    </div>
                                    <div id="crop_directions">
                                        <div class="param-group">
                                            <h4>Crop direction X</h4>
                                            <p>
                                                <label><input type="radio" name="crop_x" value="center" checked/>Center</label>
                                            </p>
                                            <p>
                                                <label><input type="radio" name="crop_x" value="left"/>Left</label>
                                            </p>
                                            <p>
                                                <label><input type="radio" name="crop_x" value="right"/>Right</label>
                                            </p>
                                        </div>
                                        <div class="param-group">
                                            <h4>Crop direction Y</h4>
                                            <p>
                                                <label><input type="radio" name="crop_y" value="center" checked/>Center</label>
                                            </p>
                                            <p>
                                                <label><input type="radio" name="crop_y" value="top"/>Top</label>
                                            </p>
                                            <p>
                                                <label><input type="radio" name="crop_y" value="bottom"/>Bottom</label>
                                            </p>
                                        </div>
                                    </div>
                                </fieldset>
						</form>';
    }

    public function get_form()
    {
        return '<div class="form-group" id="forms">
                    <hr/>
                    <h2>Thumbnails</h2>
					<div id="buttons">
						<button class="btn btn-primary btn-disabled" type="button" id="show_form">+ Add image size</button>
						<button class="btn btn-primary btn-disabled" type="button" id="show_image_generator_form">Regenerate images</button>
					</div>
				</div>
				<div class="table-wrapper card">
			        <h3>Network Posts Thumbnails Sizes</h3>
					<table class="table" id="sizes_table">
						<thead>
							<tr>
								<th>Size name</th>
								<th>Alias</th>
								<th>Width</th>
								<th>Height</th>
								<th>Crop</th>
								<th>Crop direction</th>
								<th>Actions</th>
							</tr>
						</thead>
						<tbody>
						</tbody>
					</table>
				</div>';
    }

    protected function load_settings(){
        $sizes_string = $this->get_option_for_plugin(Netsposts_thumbnails::SIZES);
        if(!empty($sizes_string)) {
            $thumbnails = json_decode($sizes_string, true);
            $this->netsposts_thumbnails = array_merge($this->netsposts_thumbnails, $thumbnails);
        }
        $this->keys = array_merge($this->keys, $this->get_keys());
    }

    protected function get_option_for_plugin($option_name){
        $options_table = $this->db->base_prefix . 'options';
        return $this->db->get_var("SELECT option_value FROM $options_table WHERE option_name = '$option_name'");
    }

    protected function update_option_for_plugin($option_name, $value){
        $options_table = $this->db->base_prefix . 'options';
        $this->db->query($this->db->prepare("UPDATE $options_table SET option_value=%s WHERE option_name= '$option_name'", $value));
    }

    protected function insert_option_for_plugin($option_name, $value){
        $options_table = $this->db->base_prefix . 'options';
        $this->db->query($this->db->prepare("INSERT INTO $options_table (option_name, option_value) VALUES('$option_name', %s)", $value));
    }

    private function find_with_id($id){
        $size = array_filter($this->netsposts_thumbnails, function ($item) use ($id){
            if ($item['id'] == $id)
                return true;
            else return false;
        });
        if (count($size) > 0) {
            $item = $this->get_single_value($size);
            return $item;
        }
        else return null;
    }

    private function get_all_attachments($limit = 0){
        $attachments = array();
        $count = 0;
        $blogs_table = $this->db->base_prefix.'blogs';
        $blogs = $this->db->get_col($this->db->prepare(

            "SELECT blog_id FROM $blogs_table WHERE public = %d AND archived = %d AND mature = %d AND spam = %d AND deleted = %d  ", 1, 0, 0, 0, 0
        ));
        foreach($blogs as $blog){
            if($blog == 1){
                $posts_table = $this->db->base_prefix . "posts";
            }
            else $posts_table = $this->db->base_prefix . "{$blog}_posts";
            $attachments[$blog] = $this->db->get_results("SELECT id FROM $posts_table WHERE post_type = 'attachment'", ARRAY_A);
            $count += count($attachments[$blog]);
            if($limit > 0) {
                if ($count > 0 && $count > $limit) {
                    $attachments[$blog] = array_slice($attachments[$blog], 0, $count - $limit - 1);
                    break;
                } elseif ($count == $limit) break;
            }
        }
        $attachments['count'] = $count;
        return $attachments;
    }
}