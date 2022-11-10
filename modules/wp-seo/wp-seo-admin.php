<?php
if (!defined('ABSPATH')) {
    exit;
}

add_filter('qtranslate_admin_config', 'qtranxf_wpseo_load_admin_page_config');
function qtranxf_wpseo_load_admin_page_config($page_configs)
{
    assert(!isset($page_configs['yoast_wpseo']));

    $page_configs['yoast_wpseo'] = array (
        'pages' => array ('admin.php' => 'wpseo_titles'),
        'forms' => array (
            array (
                'form' => array ('wpseo-conf'),
                'fields' => array (
                    array ('id' => 'company_name'),
                )
            )
        )
    );

    return $page_configs;
}

function qtranxf_wpseo_get_meta_keys()
{
    return array (
        'wpseo_title',
        'wpseo_desc',
        'wpseo_metakey',
        'wpseo_meta',
        'wpseo_metadesc',
        'wpseo_canonical',
        'wpseo_bctitle',
        //'wpseo_noindex',
        'wpseo_focuskw',
        //'wpseo_sitemap_include',
        // Social fields.
        'wpseo_opengraph-title',
        'wpseo_opengraph-description',
        //'wpseo_opengraph-image',
        'wpseo_twitter-title',
        'wpseo_twitter-description',
        //'wpseo_twitter-image',
    );
}

function qtranxf_wpseo_admin_filters()
{
    global $pagenow, $q_config;
    switch ($pagenow) {
        case 'edit.php':
            $ids = qtranxf_wpseo_get_meta_keys();

            foreach ($ids as $id) {
                add_filter($id, 'qtranxf_useCurrentLanguageIfNotFoundUseDefaultLanguage');
            }

            break;
        case 'post.php':
        case 'post-new.php':
            if ($q_config['editor_mode'] == QTX_EDITOR_MODE_SINGLE) {
                add_filter('get_post_metadata', 'qtranxf_wpseo_get_post_metadata', 5, 4);
                //add_filter( 'option_blogname', 'qtranxf_useCurrentLanguageIfNotFoundShowEmpty');
            }

            //to prevent the effect of 'strip_tags' in function 'retrieve_sitename' in '/wp-content/plugins/wordpress-seo/inc/class-wpseo-replace-vars.php'
            add_filter('option_blogname', 'qtranxf_wpseo_encode_swirly');
            add_filter('option_blogdescription', 'qtranxf_wpseo_encode_swirly');
            if (defined('WPSEO_VERSION') && intval(substr(WPSEO_VERSION, 0, 1)) < 3) {
                //to make "Page Analysis" work in Single Language Mode
                add_filter('wpseo_pre_analysis_post_content', 'qtranxf_useCurrentLanguageIfNotFoundShowEmpty');
            }
            break;
    }

    add_action('admin_init', 'qtranxf_wpseo_script_deps', 99);

    if (isset($_POST['yoast_wpseo_focuskw_text_input'])) {
        unset($_POST['yoast_wpseo_focuskw_text_input']);
    } // this causes creation a ghost db entry in wp_postmeta with meta_key '_yoast_wpseo_focuskw_text_input', while the wanted value is stored in '_yoast_wpseo_focuskw'
}

qtranxf_wpseo_admin_filters();

/**
 * Modifies dependencies of the Yoast scripts.
 * @return void
 */
function qtranxf_wpseo_script_deps()
{
    global $pagenow;
    switch ($pagenow) {
        case 'edit-tags.php':
        case 'term.php':
            $handles = array ('term-scraper' => 'qwpseo-prep');
            break;
        case 'post.php':
        case 'post-new.php':
            $handles = array ('post-scraper' => 'qtranslate-admin-common');
            break;
        default:
            return;
    }

    $scripts = wp_scripts();
    $registered = $scripts->registered;

    //$handles = array('post-scraper', 'term-scraper', 'replacevar-plugin', 'admin-global-script', 'metabox');
    //$handles = array('term-scraper');
    foreach ($handles as $handle => $dep) {
        $key = WPSEO_Admin_Asset_Manager::PREFIX . $handle;
        if (!isset($registered[$key])) {
            continue;
        }
        $r = &$registered[$key];
        if (!isset($r->deps)) {
            $r->deps = array ();
        }
        $r->deps[] = $dep;
    }
}

function qtranxf_wpseo_get_post_metadata($original_value, $object_id, $meta_key = '', $single = false)
{
    global $q_config;

    if (empty($meta_key)) {
        //very ugly hack
        $trace = debug_backtrace();
        //qtranxf_dbg_log('qtranxf_wpseo_get_post_metadata: $trace: ',$trace);
        //qtranxf_dbg_log('qtranxf_wpseo_get_post_metadata: $trace[6][args][0]: ',$trace[6]['args'][0]);
        //qtranxf_dbg_log('qtranxf_wpseo_get_post_metadata: $trace[7][function]: ',$trace[7]['function']);
        if (isset($trace[7]['function']) && $trace[7]['function'] === 'calculate_results' &&
            isset($trace[6]['args'][0]) && $trace[6]['args'][0] === 'focuskw'
        ) {
            //qtranxf_dbg_log('qtranxf_wpseo_get_post_metadata: $object_id: ',$object_id);
            //qtranxf_dbg_log('qtranxf_wpseo_get_post_metadata: $single: ',$single);
            $key = WPSEO_Meta::$meta_prefix . 'focuskw';
            $focuskw = get_metadata('post', $object_id, $key, true);
            //qtranxf_dbg_log('qtranxf_wpseo_get_post_metadata: $focuskw: ',$focuskw);
            $focuskw = qtranxf_use_language($q_config['language'], $focuskw);
            return array ($key => array ($focuskw));
        }
    }
    return $original_value;
}

function qtranxf_wpseo_encode_swirly($value)
{
    //qtranxf_dbg_log('qtranxf_wpseo_encode_swirly: $value: ',$value);
    $value = preg_replace('#\[:([a-z]{2}|)\]#i', '{:$1}', $value);
    return $value;
}

/**
 * Translate blog name
 */
add_filter('pre_option_blogname', 'wp_docs_pre_filter_option');
function wp_docs_pre_filter_option($pre_option)
{
    if (isset($_GET['action']) && $_GET['action'] === 'edit') {
        global $wpdb;
        $query = "SELECT * from $wpdb->options where option_name = 'blogname'";
        $result = $wpdb->get_results($query, ARRAY_A);

        if (!empty($result)) {
            $option_value = $result[0]['option_value'];

            return __($option_value);
        }
    }

    return $pre_option;
}
