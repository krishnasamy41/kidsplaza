<?php
    define('CT_TEMPLATE_PATH', get_template_directory());
    define('CT_INCLUDES_PATH', CT_TEMPLATE_PATH . '/includes');
    define('CT_LOCAL_PATH', get_template_directory_uri());
    require_once(CT_INCLUDES_PATH . '/plugins/aq_resizer.php');
    include_once(CT_INCLUDES_PATH . '/plugins/love-it-pro/love-it-pro.php');
    include_once(TEMPLATEPATH . '/includes/widget-view-post.php');
    include_once('admin/index.php');
    //menu
    function register_main_menus() {
        register_nav_menus(
            array(
                'page-menu' => __( 'Page Menu' ),
                'main-menu' => __( 'Main Menu' ),
                'categories-menu' => __( 'Categories Menu' ),
                'footer-menu1' => __( 'Footer Menu1' ),
                'footer-menu2' => __( 'Footer Menu2' ),
                'footer-menu3' => __( 'Footer Menu3' ),
                'footer-menu4' => __( 'Footer Menu4' ),
                'footer-menu5' => __( 'Footer Menu5' ),
                'footer-menu6' => __( 'Footer Menu6' ),
                'footer-menu7' => __( 'Footer Menu7' ),
            )
        );
    };
    if (function_exists('register_nav_menus')) add_action( 'init', 'register_main_menus' );

    //slidebar
    register_sidebar(array(
        'name' => 'Header',
        'before_widget' => '',
        'after_widget' => '',
        'before_title' => '<span class="title">',
        'after_title' => '</span>',
    ));

    register_sidebar(array(
        'name' => 'Sidebar',
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3>',
        'after_title'   => '</h3>'
    ));

    register_sidebar(array(
        'name' => 'Footer1',
        'before_widget' => '<div id="%1$s" class="widget %2$s"><em></em>',
        'after_widget'  => '</div>',
        'before_title'  => '<h3>',
        'after_title'   => '</h3>'
    ));
    
    register_sidebar(array(
        'name' => 'Footer2',
        'before_widget' => '<div id="%1$s" class="widget %2$s"><em></em>',
        'after_widget'  => '</div>',
        'before_title'  => '<h3>',
        'after_title'   => '</h3>'
    ));
        register_sidebar(array(
        'name' => 'Footer3',
        'before_widget' => '<div id="%1$s" class="widget %2$s"><em></em>',
        'after_widget'  => '</div>',
        'before_title'  => '<h3>',
        'after_title'   => '</h3>'
    )); 
    register_sidebar(array(
        'name' => 'Footer4',
        'before_widget' => '<div id="%1$s" class="widget %2$s"><em></em>',
        'after_widget'  => '</div>',
        'before_title'  => '<h3>',
        'after_title'   => '</h3>'
    ));
        register_sidebar(array(
        'name' => 'Footer5',
        'before_widget' => '<div id="%1$s" class="widget %2$s"><em></em>',
        'after_widget'  => '</div>',
        'before_title'  => '<h3>',
        'after_title'   => '</h3>'
    ));
    
    register_sidebar(array(
        'name' => 'Footer6',
        'before_widget' => '<div id="%1$s" class="widget %2$s"><em></em>',
        'after_widget'  => '</div>',
        'before_title'  => '<h3>',
        'after_title'   => '</h3>'
    ));
        register_sidebar(array(
        'name' => 'Footer7',
        'before_widget' => '<div id="%1$s" class="widget %2$s"><em></em>',
        'after_widget'  => '</div>',
        'before_title'  => '<h3>',
        'after_title'   => '</h3>'
    )); 
    register_sidebar(array(
        'name' => 'Footer8',
        'before_widget' => '<div id="%1$s" class="widget %2$s"><em></em>',
        'after_widget'  => '</div>',
        'before_title'  => '<h3>',
        'after_title'   => '</h3>'
    )); 

	/*-----------------------------------------------------------------------------------*/
    /* Add Theme Support
    /*-----------------------------------------------------------------------------------*/
    add_theme_support('post-thumbnails', array('post'));
    add_theme_support('post-formats', array('image', 'video', 'audio'));
	// Add RSS links to <head> section
	automatic_feed_links();
	
	// Load jQuery
	if ( !is_admin() ) {
        wp_deregister_script('jquery');
    	wp_register_script('jquery', ("https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"), false);
    	wp_enqueue_script('jquery');
        wp_register_script('bootstrap-min', get_stylesheet_directory_uri() .'/js/bootstrap.min.js');
        wp_enqueue_script('bootstrap-min');
        wp_register_script('html5shiv', get_stylesheet_directory_uri() .'/js/html5shiv.js');
        wp_enqueue_script('html5shiv');
        wp_register_script('respond', get_stylesheet_directory_uri() .'/js/respond.min.js');
        wp_enqueue_script('respond');
        wp_register_script('selectbox', get_stylesheet_directory_uri() .'/js/jquery.selectbox.js');
        wp_enqueue_script('selectbox');
	}

    //Load css
    function alx_styles() 
    {
        wp_enqueue_style( 'font-awesome', get_template_directory_uri().'/css/font-awesome.min.css' );
        wp_enqueue_style( 'ss-gizmo', get_template_directory_uri().'/css/ss-gizmo.css' );
        wp_enqueue_style( 'bootstrap', get_template_directory_uri().'/css/bootstrap.min.css' );
        wp_enqueue_style( 'responsive', get_template_directory_uri().'/css/responsive.css' );
    }
    add_action( 'wp_enqueue_scripts', 'alx_styles' );
	
	// Clean up the <head>
	function removeHeadLinks() {
    	remove_action('wp_head', 'rsd_link');
    	remove_action('wp_head', 'wlwmanifest_link');
    }
    add_action('init', 'removeHeadLinks');
    remove_action('wp_head', 'wp_generator');


    //excerpt de hien thi link
    function new_excerpt_length() {
        return 30;
    }
    add_filter('excerpt_length', 'new_excerpt_length');
    function new_excerpt_more($more) {
        global $post;
        return "<a class='more-link' href='".get_permalink($post->ID)."'>Xem tiếp</a>";
    }
    add_filter('excerpt_more', 'new_excerpt_more');

    // Some Defaults
global $SBC_settings;
$SBC_settings                           = array();
$SBC_settings['focus']                  = 'In All Categories';
$SBC_settings['hide_empty']             = '1'; // 1 means true
$SBC_settings['excluded_cats']          = array();
$SBC_settings['search_text']            = 'Search For...';
$SBC_settings['exclude_child']          = '0'; // 0 means false
$SBC_settings['raw_excluded_cats']      = array();
$SBC_settings['sbc_style']              = '1';
$SBC_settings['inall_exclude']          = array();

function sbc_activate(){
    global $SBC_settings;
    
    if(!get_option('sbc-focus')) {
        update_option("sbc-settings", $SBC_settings);
    }
    else {
        // Upgrade from 1.x to 1.5
        $SBC_settings['focus']                  = get_option('sbc-focus');
        $SBC_settings['hide_empty']             = get_option('sbc-hide-empty');
        $SBC_settings['excluded_cats']          = get_option('sbc-excluded-cats');
        $SBC_settings['search_text']            = get_option('sbc-search-text');
        $SBC_settings['exclude_child']          = get_option('sbc-exclude-child');
        $SBC_settings['raw_excluded_cats']      = get_option('sbc-selected-excluded');
        $SBC_settings['sbc_style']              = get_option('sbc-style');
        
        add_option("sbc-settings", $SBC_settings);
        
        delete_option("sbc-focus");
        delete_option("sbc-hide-empty");
        delete_option("sbc-excluded-cats");
        delete_option("sbc-search-text");
        delete_option("sbc-selected-excluded");
        delete_option("sbc-exclude-child");
        delete_option("sbc-style");
    }
}
register_activation_hook( __FILE__ , 'sbc_activate' );

// Start the plugin
if ( ! class_exists( 'SBC_Admin' ) ) {

    class SBC_Admin {

        // prep options page insertion
        function add_config_page() {
            if ( function_exists('add_submenu_page') ) {
                add_options_page('Search By Category Options', 'Search By Category', 10, basename(__FILE__), array('SBC_Admin','config_page'));
            }
        }

        // Options/Settings page in WP-Admin
        function config_page() {
            if ( isset($_POST['submit']) ) {
                $nonce = $_REQUEST['_wpnonce'];
                if (! wp_verify_nonce($nonce, 'sbc-updatesettings') ) die('Security check failed'); 
                if (!current_user_can('manage_options')) die(__('You cannot edit the search-by-category options.'));
                check_admin_referer('sbc-updatesettings');
                
                // Get our new option values
                $SBC_settings                           = get_option('sbc-settings');
                $SBC_settings['focus']                  = mysql_real_escape_string($_POST['focus']);
                $SBC_settings['hide_empty']             = $_POST['hide-empty'];
                $SBC_settings['search_text']            = mysql_real_escape_string($_POST['search-text']);
                $SBC_settings['exclude_child']          = $_POST['exclude-child'];
                $SBC_settings['sbc_style']              = $_POST['sbc-style'];
                $SBC_settings['inall_exclude']          = $_POST['inall_exclude'];
                
                if(isset($_POST['post_category'])){
                    $SBC_settings['raw_excluded_cats']      = $_POST['post_category'];
                    
                    // Fix our excluded category return values
                    $fix = $SBC_settings['raw_excluded_cats'];
                    array_unshift($fix, "1");
                    $SBC_settings['excluded_cats']          = implode(',',$fix);
                }
                
                // Make sure checkboxes are set right
                if (empty($SBC_settings['hide_empty'])) $SBC_settings['hide_empty'] = '0'; // 0 means false
                if (empty($SBC_settings['exclude_child'])) $SBC_settings['exclude_child'] = '0'; // 0 means false
                if (empty($SBC_settings['sbc_style'])) $SBC_settings['sbc_style'] = '0'; // 0 means false 
                
                // Update the DB with the new option values
                update_option("sbc-settings", $SBC_settings);
            }
            
            $SBC_settings           = get_option("sbc-settings");
            $focus                  = $SBC_settings['focus'];
            $hide_empty             = $SBC_settings['hide_empty'];
            $search_text            = $SBC_settings['search_text'];
            $excluded_cats          = $SBC_settings['excluded_cats'];
            $exclude_child          = $SBC_settings['exclude_child'];
            $raw_excluded_cats      = $SBC_settings['raw_excluded_cats'];
            $sbc_style              = $SBC_settings['sbc_style'];
            $inall_exclude          = $SBC_settings['inall_exclude'];
            
            ?>
            <div class="wrap">
                <div id="icon-options-general" class="icon32"><br /></div>
                <h2>Seach By Category Options</h2>
                
                <form action="" method="post" id="sbc-config">
                <?php if (function_exists('wp_nonce_field')) { wp_nonce_field('sbc-updatesettings'); } ?>
                    <table class="form-table">
                        <tr>
                            <th scope="row" valign="top"><label for="search-text">Hiển thị văn bản trong hộp tìm kiếm:</label></th>
                            <td><input type="text" name="search-text" id="search-text" class="regular-text" value="<?php echo $search_text; ?>"/></td>
                        </tr>
                        <tr>
                            <th scope="row" valign="top"><label for="focus">Hiển thị văn bản trong lựa chọn thả xuống:</label></th>
                            <td><input type="text" name="focus" id="focus" class="regular-text" value="<?php echo $focus; ?>"/></td>
                        </tr>
                        <tr>
                            <th scope="row" valign="top"><label for="hide-empty">Ẩn danh mục không có bài?</label></th>
                            <td><input type="checkbox" name="hide-empty" id="hide-empty" value="1" <?php if ($hide_empty === '1') echo 'checked="checked"'; ?> /></td>
                        </tr>
                        <tr>
                            <th scope="row" valign="top"><label for="exclude-child">Loại trừ category khỏi danh sách?</label></th>
                            <td><input type="checkbox" name="exclude-child" id="exclude-child" value="1" <?php if ($exclude_child === '1') echo 'checked="checked"'; ?> /></td>
                        </tr>
                        <tr>
                            <th scope="row" valign="top"><label>Loại để loại trừ:</label></th>
                            <td><ul><?php wp_category_checklist(0,0,$raw_excluded_cats); ?></ul></td>
                        </tr>
                        <tr>
                            <th scope="row" valign="top"><label>Categories loại trừ từ kết quả tìm kiếm:</label></th>
                            <td><input type="text" name="inall_exclude" id="inall_exclude" class="regular-text" value="<?php echo $inall_exclude; ?>"/><p><em><strong>Thêm các mục như dấu phẩy tách ID (EX: 1,3,15,7)</strong></em></p></td>
                            <td><span class="description">Đôi khi bạn không muốn người dùng để xem bài viết mỗi thể loại, loại được liệt kê ở đây sẽ bị loại khỏi xử lý nếu người dùng không chọn một thể loại cụ thể từ trình đơn thả xuống.</span></td>
                        </tr>
                    </table>
                    
                    <p class="submit"><input type="submit" id="submit" name="submit" class="button-primary" value="Save Changes" /></p>
                </form>
            </div>
<?php       }
    }
}


// Base function
function sbc($focus = null, $hide_empty = null, $search_text = null, $only_cat = null, $excluded_cats = null, $exclude_child = null, $inall_exclude = null) {
    
    $SBC_settings           = get_option("sbc-settings");
    
    // Set default values of arguments are not set
    $focus                  = !$focus ? $SBC_settings['focus'] : $focus;
    $hide_empty             = !$hide_empty ? $SBC_settings['hide_empty'] : $hide_empty;
    $search_text            = !$search_text ? $SBC_settings['search_text'] : $search_text;
    $search_text_default    = $search_text;
    $excluded_cats          = !$excluded_cats ? $SBC_settings['excluded_cats'] : $excluded_cats;
    $exclude_child          = !$exclude_child ? $SBC_settings['exclude_child'] : $exclude_child;
    $inall_exclude          = !$inall_exclude ? ','.$SBC_settings['inall_exclude'] : $inall_exclude;
    
    if(!$only_cat){
        $cat_id = (int)$_GET['cat'];
        // if $only_cat is still null, use settings from admin menu
        $exclude_setting = $excluded_cats.$inall_exclude;
        $settings = array('show_option_all' => $focus,
                        'show_option_none' => '',
                        'orderby' => 'name', 
                        'order' => 'ASC',
                        'show_last_update' => 0,
                        'show_count' => 0,
                        'hide_empty' => $hide_empty, 
                        'child_of' => 0,
                        'exclude' => $exclude_setting,
                        'echo' => 0,
                        'selected' => $cat_id,
                        'hierarchical' => 1, 
                        'name' => 'cat',
                        'class' => 'postform',
                        'depth' => $exclude_child);
        $input = wp_dropdown_categories($settings);
        $input_class = 'multi-cat';
    }
    else{
        $cat_id = !is_numeric($only_cat) ? get_cat_ID($only_cat) : $only_cat; // if it's not an ID, try getting it via the name
        $cat_id = ($cat_id === 0) ? get_category_by_slug($only_cat) : $cat_id; // if name doesn't work try it as a slug
        $input = "<input type='hidden' name='cat' id='cat' value='{$cat_id}' />\r\n";
        $input_class = 'single-cat';
    }
    
    if(is_category() && !is_tag() && !is_author() && !is_date()) $cat_id = get_cat_id(single_cat_title('' , false));

    if(is_search()){
        $cat_id = $_GET['cat'] ? (int) $_GET['cat'] : 0;
        $search_text = esc_attr(apply_filters('the_search_query', get_search_query()));
    }
    
    $blog_url = get_bloginfo("url");
    
    $form = <<< EOH
    <div id="sbc">
        <form method="get" id="sbc-search" action="{$blog_url}">
            <label class="all-catgory">
            {$input}
            </label>
            <input type="text" placeholder="{$search_text}" name="s" id="s" class="{$input_class}" onblur="if (this.value == '') {this.value = '{$search_text_default}';}"  onfocus="if (this.value == '{$search_text_default}') {this.value = '';}" />
            <div class="b-search">
                <input type="submit" id="sbc-submit" value="Search" />
            </div>
        </form>
    </div>  
EOH;
    
    echo $form;
}

function sbc_shortcode($atts){
    extract( shortcode_atts(array(
        'focus' => null,
        'hide_empty' => null,
        'search_text' => null,
        'only_cat' => null,
        'excluded_cats' => null,
        'exclude_child' => null,
        'inall_exclude' => null
    ), $atts) );
    
    sbc($focus, $hide_empty, $search_text, $only_cat, $excluded_cats, $exclude_child, $inall_exclude);
}

// Get results only from selected category
function return_only_selected_category() {
    if ( isset($_REQUEST['s']) && isset($_REQUEST['cat']) ){
        global $wp_query; $SBC_settings = get_option('sbc-settings');
        
        $desired_cat = $_REQUEST['cat'];
        if($desired_cat === '0') $desired_cat = $SBC_settings['inall_exclude'];
        
        $excluded = get_categories("hide_empty=false&exclude={$desired_cat}");
        
        $wp_query->query_vars['cat'] = "-{$excluded}";
    }
}

$sbc_settings = get_option("sbc-settings");

// Highjack the search
add_filter('pre_get_posts', 'return_only_selected_category');

// insert into admin panel
add_action('admin_menu', array('SBC_Admin','add_config_page'));

// Allow use of [sbc]
add_shortcode('sbc', 'sbc_shortcode');






?>