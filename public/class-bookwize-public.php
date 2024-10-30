<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://www.bookwize.com/
 * @since      1.5
 *
 * @package    Bookwize Integrated Cinnamon
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Bookwize Integrated Cinnamon
 * @author     bookwize.com
 */
class Bookwize_Integrated_Public
{

    public $settings = [];
    /**
     * Initialize the class and set its properties.
     *
     * @since      1.5
     *
     * @param      string $bookwize The name of the plugin.
     * @param      string $version The version of this plugin.
     */
    /**
     * The ID of this plugin.
     *
     * @since      1.5
     * @access   private
     * @var      string $bookwize The ID of this plugin.
     */
    private $bookwize;
    /**
     * The version of this plugin.
     *d
     * @since      1.5
     * @access   private
     * @var      string $version The current version of this plugin.
     */
    private $version;

    private $_settings = [
        'debug' => false,
        'useCache' => true,
        'defaultCache' => 'sessionStorage',
        'cacheLevel' => 1,
        'lang' => 'en-US',
        // if we want all supported languages, set as en empty array
        'languages' => ['en-US'],
        'discount' => '%', // % = percentage, else numeric
        'currency' => 'EUR',
        'defaultBoardType' => 'BedAndBreakfast',
        'roomBackgroundImage' => '',
        'baseUrl' => '',
        'localizationUrl' => '',
        'googleAnalyticsEnabled' => true,
        'sortRoomsBy' => ['availability.desc'],
        'hotelId' => '',
        'apiKey' => '',
        'apiBaseUrl' => 'https://app.bookwize.com/api/v1.4',
    ];

    public function __construct($bookwize, $version)
    {
        $this->bookwize = $bookwize;
        $this->version = $version;
    }

    public function init()
    {
        global $post;
        add_action('wp_head', [&$this, 'wp_head'], 10, 2);
        add_filter('redirect_canonical', [&$this, 'redirect_canonical']);
        add_filter('body_class', [&$this, 'body_class']);
        add_action('template_redirect', [&$this, 'template_redirect']);
        if (isset($post) && (get_post_meta($post->ID, 'bookwize_integrated_page_type', true) === 'integrated')) {
            add_filter('the_content', 'bw_load_booking_engine');
        }
        add_action('bookwize_enqueue_scripts_and_styles',[$this, 'enqueue_scripts_and_styles']);            
    }



    public function template_redirect()
    {
        if (bw_is_booking_page()) {
           $this->force_ssl();
        }
    }

    /**
     * @param $classes
     *
     * @return mixed
     */
    public function body_class($classes)
    {
        $classes[] = 'ibe';

        return $classes;
    }

    public function redirect_canonical($redirect_url)
    {
        if (bw_is_booking_page()) {
            $redirect_url = false;
        }

        return $redirect_url;
    }


    /**
     * Register the stylesheets for the public-facing side of the site.
     *
     * @since      1.5
     */
    public function enqueue_styles()
    {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Bookwize_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Bookwize_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        wp_register_style($this->bookwize . '-print', plugin_dir_url(__FILE__) . 'css/bookwize-print.css', [], false, 'all');
        wp_register_style($this->bookwize . '-vendor', plugin_dir_url(__FILE__) . 'css/bookwize-vendor.css', [], false, 'all');
        wp_register_style($this->bookwize, plugin_dir_url(__FILE__) . 'css/bookwize-public.css', [
            $this->bookwize . '-vendor',
            $this->bookwize . '-print',
        ], $this->version, 'all');


        if( $this->is_integrated_page()) {
            $this->enqueue_registered_styles();
        }
    }

    /**
     * Register the stylesheets for the public-facing side of the site.
     *
     * @since      1.5
     */
    public function enqueue_scripts()
    {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Bookwize_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Bookwize_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        $this->config();
        
        //wp_register_script($this->bookwize . '-vendors', 'https://bookwizecdn.azureedge.net/vendors/vendors-v3-1.4.min.js', [], false, true);        
        wp_register_script($this->bookwize . '-vendors', plugin_dir_url(__FILE__) . 'js/vendors-v3.min.js', [], "1.6", false);                
        wp_add_inline_script($this->bookwize . '-vendors', 'var IBEConfig =' . json_encode($this->settings) );        

        //wp_register_script($this->bookwize . '-app', 'https://bookwizecdn.azureedge.net/app/ibe-5.3.15.min.js', [$this->bookwize . '-vendors'], false, true);
        wp_register_script($this->bookwize . '-app', plugin_dir_url(__FILE__) . 'js/ibe.min.js', [$this->bookwize . '-vendors'], "5.3.19", false);
        wp_register_script($this->bookwize . '-site', plugin_dir_url(__FILE__) . 'js/bookwize-public.min.js', [  
            $this->bookwize . '-app',
        ], false, true);

		wp_register_script($this->bookwize . '-jccresponse', plugin_dir_url(__FILE__) . 'js/jccresponse.viewmodel.js', array(), false, false);
        

        if( $this->is_integrated_page()) {
            $this->enqueue_registered_scripts();
        }
    }

    public function enqueue_registered_styles(){
        wp_enqueue_style($this->bookwize . '-print');
        wp_enqueue_style($this->bookwize . '-vendor');
        wp_enqueue_style($this->bookwize);
    }

    public function enqueue_registered_scripts(){
        wp_enqueue_script($this->bookwize . '-vendors');
        wp_enqueue_script($this->bookwize . '-app');
        wp_enqueue_script($this->bookwize . '-site');
        wp_enqueue_script($this->bookwize . '-jccresponse');
    }

    public function enqueue_scripts_and_styles(){
        $this->enqueue_registered_styles();
        $this->enqueue_registered_scripts();
    }

    public function wp_head()
    {       
        if(get_option('bw_preload', false)) {
            $this->bw_preload_scripts();
        }    
    }

    public function bw_preload_scripts(){
        if (bw_is_booking_page()) {
            echo '<link rel="preload" href="'.plugin_dir_url(__FILE__) . 'js/vendors-v3.min.js" as="script" />';
            echo '<link rel="preload" href="'.plugin_dir_url(__FILE__) . 'js/ibe.min.js" as="script" />';
        }
    }

    /**
     * IBE configuration variables.
     */
    protected function config()
    {
        $tmp_languages = (new Bookwize_Integrated_Languages())->get_languages();

        if (defined('ICL_LANGUAGE_CODE')) {
            $lang = ICL_LANGUAGE_CODE;
        } else {
            $lang = get_bloginfo("language");
        }

        $languages = [];
        if (!empty($tmp_languages)) {
            foreach ($tmp_languages as $code => $name) {
                $languages[] = $code;
            }
        } else {
            $languages[] = 'en';
        }

        $base_url = plugin_dir_url(__FILE__);
        $bw_apiKey = get_option('bw_apiKey');
        $bw_apiBaseUrl = 'https://app.bookwize.com/api/v1.4';
        $currency = get_option('bw_currency');
        $bw_enable_jcc = get_option('bw_enable_jcc');
        if (empty($currency)) {
            $currency = 'EUR';
        }
        $this->settings = array_merge($this->_settings, [
            'hotelId' => get_option('bw_hotelId'),
            'apiKey' => $bw_apiKey,
            'apiBaseUrl' => $bw_apiBaseUrl,
            'languages' => $languages,
            'lang' => $lang,
            'baseUrl' => $base_url,
            'debug' => (get_option('bw_debug') == '') ? false : true,
            'display_header' => (get_option('bw_header') == '') ? false : true,
            'display_group_text' => (get_option('bw_group_text','') == '') ? false : true,
            'roomBackgroundImage' => $base_url . 'img/missing-room-image.jpg',
            'localizationUrl' => $bw_apiBaseUrl . '/system/resources/?apikey=' . $bw_apiKey,
            'currency' => $currency,
            'currencySelector' => true,
            'integrated_page' => $this->get_integrated_page(),
            'redirect_page' => $this->get_integrated_redirect_page(),
            'theme' => get_option('bw_theme','c'), 
            'userCookieAcceptance' => true,
            'jcc_enable'	=> $bw_enable_jcc,
            'allCreditCards' => 'MasterCard,VISA,AmEx,DinersClub,Discover,JCB,VisaElectron,Solo',
            'pluginVersion' => $this->version,
            'pluginUrl' => plugin_dir_url(__FILE__)
        ]);
    }

    protected function force_ssl()
    {
        if (is_ssl() === false) {
            wp_redirect('https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'], 301);
            die();
        }
    }

    public function get_integrated_page()
    {
        $args = [
            'meta_key' => 'bookwize_integrated_page_type',
            'meta_value' => 'integrated',
            'post_type' => get_post_types(['public' => true]),
            'post_status' => 'any',
            'posts_per_page' => 1,
            'suppress_filters' => false
        ];
        $posts = @get_posts($args);
        if (isset($posts[0])) {
            return get_permalink($posts[0]->ID);
        }
        return [];
    }

    public function get_integrated_redirect_page()
    {
        $args = [
            'meta_key' => 'bookwize_integrated_page_type',
            'meta_value' => 'integrated_redirect',
            'post_type' => get_post_types(['public' => true]),
            'post_status' => 'any',
            'posts_per_page' => 1,
            'suppress_filters' => false
        ];
        $posts = @get_posts($args);
        if (isset($posts[0])) {
            return get_permalink($posts[0]->ID);
        }
        return [];
    }

    protected function is_integrated_page(){
        global $post;
        $postMetaValue=get_post_meta($post -> ID,"bookwize_integrated_page_type",true);
        if($postMetaValue=='integrated')
        {
            return true;
        }

        return false;
    }
}
