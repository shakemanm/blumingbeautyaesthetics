<?php
/*
Plugin Name: Poptin
Contributors: galdub, tomeraharon
Description: Use Poptin to get more leads, sales, and email subscribers. Create targeted beautiful pop ups and forms in less than 2 minutes with ease.
Version: 1.3.1
Author: Poptin
Author URI: https://www.poptin.com
Text Domain: poptin
Domain Path: /lang
License: GPL2
*/

// Prevent direct file access
use Hummingbird\Core\Utils;

if (!defined('ABSPATH')) {
    exit;
}

define('POPTIN_VERSION', '1.3.1');
define('POPTIN_PATH', dirname(__FILE__));
define('POPTIN_PATH_INCLUDES', dirname(__FILE__) . '/inc');
define('POPTIN_FOLDER', basename(POPTIN_PATH));
define('POPTIN_URL', plugins_url() . '/' . POPTIN_FOLDER);
define('POPTIN_URL_INCLUDES', POPTIN_URL . '/inc');

define('POPTIN_ID', get_option('poptin_id'));
define('POPTIN_USER_ID', get_option('poptin_user_id'));
define('POPTIN_MARKETPLACE_TOKEN', get_option('poptin_marketplace_token'));
define('POPTIN_MARKETPLACE_EMAIL_ID', get_option('poptin_marketplace_email_id'));
define('POPTIN_FRONT_SITE', 'https://www.poptin.com');

define('POPTIN_MARKETPLACE', 'Wrdprs');
define('POPTIN_MARKETPLACE_LOGIN_URL', 'https://app.popt.in/api/marketplace/auth');
define('POPTIN_MARKETPLACE_REGISTER_URL', 'https://app.popt.in/api/marketplace/register');
define('POPTIN_CACERT_PATH', POPTIN_PATH . "/assets/ca-cert/cacert-2017-06-07.pem");


if (is_admin()) {
    include_once "class-affiliate.php";
}
class POPTIN_Plugin_Base
{
    public function __construct()
    {
        /**
         * Activation Hook
         * Deactivation Hook
         */
        register_activation_hook(__FILE__, 'poptin_activation_hook');
        register_deactivation_hook(__FILE__, 'poptin_deactivation_hook');

        add_action('admin_enqueue_scripts', array($this, 'poptin_add_admin_javascript'));
        add_action('admin_enqueue_scripts', array($this, 'poptin_add_admin_css'));
        // register admin pages for the plugin
        add_action('admin_menu', array($this, 'poptin_admin_pages_callback'));

        // Translation-ready
        add_action('plugins_loaded', array($this, 'poptin_add_textdomain'));

        $poptinidcheck = get_option('poptin_id', (isset($myOption_def) ? $myOption_def : false));
        if ($poptinidcheck) {
            $poptinid = get_option('poptin_id');
            if (strlen($poptinid) != 13) {
                update_option('poptin_id', '');
            } else {
                add_action('wp_head', array($this, 'poptin_add_script_frontend'));
            }
        }
        // Add actions for storing value and fetching URL
        // use the wp_ajax_nopriv_ hook for non-logged users (handle guest actions)

        /**
         * AJAX Calls registration
         * Action => name followed after wp_ajax
         */
        add_action('wp_ajax_poptin_register', array($this, 'poptin_marketplace_registration'));
        add_action('wp_ajax_poptin_logmein', array($this, 'poptin_markplace_login'));
        add_action('wp_ajax_delete-id', array($this, 'delete_poptin_id'));
        add_action('wp_ajax_add-id', array($this, 'add_poptin_id'));

        /**
         *
         * Admin Initialization calls registration
         * We need this to send user to Poptin's Admin page on activation
         *
         */
        add_action('admin_init', 'poptin_plugin_redirect');
        add_filter('admin_footer_text', array($this, 'replace_footer_text'));

        /**
         *
         * Admin Initialization calls registration
         * We need this to send user to Poptin's Admin page on activation
         *
         */
        if (isset($_GET['poptin_logmein'])) {
            if (!function_exists('is_user_logged_in')) {
                require_once(ABSPATH . "wp-includes/pluggable.php");
            }
            if (is_user_logged_in()) {
                if (current_user_can('administrator')) {
                    $after_registration = '';
                    if (isset($_GET['after_registration'])) {
                        $after_registration = $_GET['after_registration'];
                    }
                    $this->poptin_markplace_login_direct($after_registration);
                } else {
                    echo '<style>html {background: #f1f1f1;}body{margin:0;padding:0;}h1 {background: #fff;font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif;padding: 1em 2em;-webkit-box-shadow: 0 1px 3px rgba(0,0,0,0.13);text-align:center;color: #666;font-size: 24px;margin: 30px auto 0;padding:20px;display:table;border-radius:5px}</style>';
                    echo '<h1>Please login as administrator</h1>';
                    die();
                }
            } else {
                wp_redirect(wp_login_url());
            }
        }

        /**
         * Clean Up Of the URL
         * Not sure why is this here
         */
        add_filter('clean_url', 'async_scripts', 11, 1);

        add_action('admin_footer', array($this, 'deactivate_modal'));
        add_action('wp_ajax_poptin_plugin_deactivate', array($this, 'poptin_plugin_deactivate'));
    }

    /**
     * Retrieve the original footer text
     *
     * @return string
     */
    private function get_original_text()
    {
        global $wp_version;

        /* The way of determining the default footer text was changed in 3.9 */
        if (version_compare($wp_version, '3.9', '<')) {
            $text = __('Thank you for creating with <a href="http://wordpress.org/">WordPress</a>.');
        } else {
            /* translators: %s: https://wordpress.org/ */
            $text = sprintf(__('Thank you for creating with <a href="%s">WordPress</a>.'), __('https://wordpress.org/'));
        }

        return $text;
    }
    /**
     * Replace the admin footer text
     *
     * @param string $footer_text The current footer text
     *
     * @return string The new footer text
     */
    function replace_footer_text($footer_text)
    {
        return str_replace($this->get_original_text(), '', $footer_text);
    }

    public function poptin_plugin_deactivate()
    {
        if (current_user_can('manage_options')) {
            $postData = $_POST;
            $errorCounter = 0;
            $response = array();
            $response['status'] = 0;
            $response['message'] = "";
            $response['valid'] = 1;
            $reason = filter_input(INPUT_POST, 'reason');
            $nonce = filter_input(INPUT_POST, 'nonce');
            if (empty($reason)) {
                $errorCounter++;
                $response['message'] = "Please provide reason";
            } else if (empty($nonce)) {
                $response['message'] = esc_html__("Your request is not valid", 'poptin');
                $errorCounter++;
                $response['valid'] = 0;
            } else {
                if (!wp_verify_nonce($nonce, 'poptin_deactivate_nonce')) {
                    $response['message'] = esc_html__("Your request is not valid", 'poptin');
                    $errorCounter++;
                    $response['valid'] = 0;
                }
            }
            if ($errorCounter == 0) {
                global $current_user;
                $email = "none@none.none";
                if (isset($postData['email_id']) && !empty($postData['email_id']) && filter_var($postData['email_id'], FILTER_VALIDATE_EMAIL)) {
                    $email = $postData['email_id'];
                }
                $domain = site_url();
                $user_name = $current_user->first_name . " " . $current_user->last_name;
                $subject = "Poptin was removed from {$domain}";
                $headers = "MIME-Version: 1.0\r\n";
                $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
                $headers .= 'From: ' . $user_name . ' <' . $email . '>' . PHP_EOL;
                $headers .= 'Reply-To: ' . $user_name . ' <' . $email . '>' . PHP_EOL;
                $headers .= 'X-Mailer: PHP/' . phpversion();
                ob_start();
?>
                <table border="0" cellspacing="0" cellpadding="5">
                    <tr>
                        <th>Plugin</th>
                        <td>Poptin</td>
                    </tr>
                    <tr>
                        <th>Plugin Version</th>
                        <td><?php echo esc_attr(POPTIN_VERSION) ?></td>
                    </tr>
                    <tr>
                        <th>Domain</th>
                        <td><?php echo esc_url($domain) ?></td>
                    </tr>
                    <tr>
                        <th>Email</th>
                        <td><?php echo esc_attr($email) ?></td>
                    </tr>
                    <tr>
                        <th>Reason</th>
                        <td><?php echo nl2br(esc_attr($reason)) ?></td>
                    </tr>
                    <tr>
                        <th>WordPress Version</th>
                        <td><?php echo esc_attr(get_bloginfo('version')) ?></td>
                    </tr>
                    <tr>
                        <th>PHP Version</th>
                        <td><?php echo esc_attr(PHP_VERSION) ?></td>
                    </tr>
                </table>
<?php
                $content = ob_get_clean();
                $email_id = "contact@poptin.on.crisp.email";
                wp_mail($email_id, $subject, $content, $headers);
                $response['status'] = 1;
            }
            echo json_encode($response);
            wp_die();
        }
    }

    public function deactivate_modal()
    {
        if (current_user_can('manage_options')) {
            global $pagenow;

            if ('plugins.php' !== $pagenow) {
                return;
            }

            include 'deactivate-form.php';
        }
    }

    /**
     *
     *
     * Scope:       Private
     * Function:    poptin_marketplace_registration
     * Description: Will be called via the Admin Page AJAX
     *              Will be checked with WP nounce.
     *              If the verification is OK, we go ahead and create the account.
     *              Email Address - Would be pre-filled in the form in the Admin Page.
     *              User wants, can change the email id.
     *              We store the registration email ID, to ensure that we do not have issues in future.
     *              Basic wrapper function to the poptin_middleware_registration_curl
     *              Responds in JSON from the cURL call.
     * Parameters:  email   (argument)
     *              domain      -   retrieved from the Wordpress base.
     *              first_name  -   derived from the nice_name of the existing logged in user.
     *              last_name  -   derived from the nice_name of the existing logged in user.
     * Return:      Return response is derived directly from the chain function poptin_middleware_registration_curl
     *
     *
     */
    function poptin_marketplace_registration()
    {
        if (!current_user_can('manage_options')) {
            die('You are now allowed to do this.');
        }

        $email = sanitize_email($_POST['email']);
        /**
         *
         * We check the sanitization here again for the email id.
         * This is for AJAX call, hence the double check is required.
         * If this is okay, we go ahead and send the data poptin_marketplace_registration function.
         *
         */
        if ($email) {
        } else {
            $return_array = array();
            $return_array['success'] = false;
            $return_array['message'] = 'Invalid Email Address found.';
            echo json_encode($return_array);
            exit(0);
        }

        if (check_ajax_referer('poptin-fe-register', 'security')) {
            $domain = site_url();
            if (!function_exists('wp_get_current_user')) {
                require_once(ABSPATH . "wp-includes/pluggable.php");
            }
            $user_data = wp_get_current_user();
            $user_nice_name = $user_data->data->user_nicename;
            $user_nice_name_array = explode(" ", $user_nice_name);
            $first_name = $user_nice_name_array[0];
            $last_name = "";
            if (isset($user_nice_name_array[1])) {
                $last_name = $user_nice_name_array[1];
            } else {
                $last_name = $user_nice_name_array[0];
            }
            $this->poptin_middleware_registration_curl($first_name, $last_name, $domain, $email);
        } else {
            $return_array = array();
            $return_array['success'] = false;
            $return_array['message'] = 'Invalid request. Please refresh & try again.';
            echo json_encode($return_array);
            exit(0);
        }
    }



    /**
     *
     *
     * Scope:       Private
     * Function:    poptin_markplace_login_direct
     * Description: Based on the Options stored in the WP database
     *              We make request to the marketplace api for login
     *              In simpler terms this is a wrapper function for making AJAX call -> to cURL call -> to respond back.
     * Parameters:  POST Data NOT required
     *              - token [Generated at the time of registration from the marketplace API ONLY]
     *              - email [Email ID used at the time of registration from the marketplace API ONLY]
     * Return:      login_url   -   if it went successful
     *              success     -   true/false
     *
     *
     */
    function poptin_markplace_login_direct($after_registration = '')
    {
        $token = POPTIN_MARKETPLACE_TOKEN;
        $user_id = POPTIN_USER_ID;
        $curl_URL = POPTIN_MARKETPLACE_LOGIN_URL;
        $curl_post_array = array(
            'token' => $token,
            'user_id' => $user_id
        );
        $curl_options = $this->generate_curl_options($curl_URL, $curl_post_array);
        $curl = curl_init();
        curl_setopt_array($curl, $curl_options);
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        $response_return_array = array();
        if ($err) {
            $response_return_array['success'] = false;
            echo json_encode($response_return_array);
            exit(0);
        } else {
            $response_array = json_decode($response);
            if ($response_array->success) {
                $login_url = $response_array->login_url;
                // If user just registered
                if ($after_registration != '') {
                    $login_url .= '&utm_source=wordpress';
                }
                header("Location: " . $login_url);
                exit(0);
            } else {
                exit(wp_redirect("admin.php?page=Poptin"));
            }
        }
        exit(wp_redirect("admin.php?page=Poptin"));
    }


    /**
     * Scope:       Public
     * Function:    poptin_add_admin_javascript
     * Description: Will be called via the Admin Page AJAX
     *              Will be checked with WP nounce.
     *              If the verification is OK, we go ahead and create the account.
     *              Email Address - Would be pre-filled in the form in the Admin Page.
     *              User wants, can change the email id.
     *              We store the registration email ID, to ensure that we do not have issues in future.
     *              Basic wrapper function to the poptin_middleware_registration_curl
     *              Responds in JSON from the cURL call.
     * Parameters:  email   (argument)
     *              domain      -   retrieved from the Wordpress base.
     *              first_name  -   derived from the nice_name of the existing logged in user.
     *              last_name  -   derived from the nice_name of the existing logged in user.
     * Return:      Return response is derived directly from the chain function poptin_middleware_registration_curl
     */
    public function poptin_add_admin_javascript($hook)
    {
        if ('toplevel_page_Poptin' === $hook) {
            wp_enqueue_script('jquery');
            wp_register_script('poptin-admin', plugins_url('assets/js/poptin-admin.js', __FILE__), array('jquery'), '1.0.6', true);
            wp_enqueue_script('poptin-admin');
            wp_register_script('bootstrap-modal', plugins_url('assets/js/bootstrap.min.js', __FILE__), array('jquery'), '1.0', true);
            wp_enqueue_script('bootstrap-modal');
        }
    }



    /**
     * Scope:       Public
     * Function:    poptin_add_script_frontend
     * Description: Will add the Poptin JS code to the system.
     * Parameters:  None
     */
    public function poptin_add_script_frontend()
    {
        echo "<script id='pixel-script-poptin' src='https://cdn.popt.in/pixel.js?id=" . POPTIN_ID . "' async='true'></script> ";
    }

    /**
     * Scope:       Public
     * Function:    poptin_add_admin_css
     * Description: Will add the backend CSS required for the display of poptin settings page.
     * Parameters:  hook | Not used.
     */
    public function poptin_add_admin_css($hook)
    {
        if ('toplevel_page_Poptin' === $hook) {
            wp_register_style('poptin-admin', plugins_url('assets/css/poptin-admin.css', __FILE__), array(), '1.1');
            wp_enqueue_style('poptin-admin');
            wp_register_style('bootstrap-modal-css', plugins_url('assets/css/bootstrap.min.css', __FILE__), array(), '1.0');
            wp_enqueue_style('bootstrap-modal-css');
        }
    }

    /**
     * Scope:       Public
     * Function:    poptin_admin_pages_callback
     * Description: Will add the Poptin Page into the Menu System of Wordpress
     * Parameters:  None
     */
    public function poptin_admin_pages_callback()
    {
        //$this->check_if_poptin_is_connected();
        add_menu_page(__("Poptin", 'ppbase'), __("Poptin", 'ppbase'), 'manage_options', 'Poptin', array($this, 'poptin_admin_view'), POPTIN_URL . '/assets/images/menu-icon.png');
    }


    /**
     * Scope:       Public
     * Function:    poptin_admin_view
     * Description: The URL link is added to render the view setup as per the function
     * Parameters:  None
     */
    public function poptin_admin_view()
    {
        include_once(POPTIN_PATH . '/views/poptin_admin_view.php');
    }


    /**
     * Scope:       Public
     * Function:    poptin_add_textdomain
     * Description: --
     * Parameters:  None
     */
    public function poptin_add_textdomain()
    {
        load_plugin_textdomain('poptin', false, dirname(plugin_basename(__FILE__)) . '/lang/');
    }



    /**
     * Scope:       Public
     * Function:    delete_poptin_id
     * Description: AJAX wrapper for removing Poptin ID
     * Parameters:  None
     */
    function delete_poptin_id()
    {
        if (!current_user_can('manage_options')) {
            die('You now allowed to do this.');
        }


        if (!isset($_POST['data']['nonce']) || !wp_verify_nonce($_POST['data']['nonce'], "ppFormIdDeactivate")) {
            die('You now allowed to do this.');
        }

        update_option('poptin_id', '');
        update_option('poptin_marketplace_token', '');
        update_option('poptin_marketplace_email_id', '');
        update_option('poptin_user_id', '');
        poptin_clear_all_caches();
        die(json_encode(
            array(
                'success' => true,
                'message' => 'Database updated successfully.'
            )
        ));
    }

    /**
     * Scope:       Public
     * Function:    add_poptin_id
     * Description: AJAX wrapper for adding Poptin ID, only used when user enters the Poptin ID manually.
     * Parameters:  None
     */
    function add_poptin_id()
    {
        if (!current_user_can('manage_options')) {
            die('You now allowed to do this.');
        }
        if (!isset($_POST['data']['nonce']) || !wp_verify_nonce($_POST['data']['nonce'], "ppFormIdRegister")) {
            die('You now allowed to do this.');
        }

        if (isset($_POST['data']) && isset($_POST['data']['poptin_id'])) {
            $id = $_POST['data']['poptin_id'];
            update_option('poptin_id', $id);
            poptin_clear_all_caches();
            die(json_encode(
                array(
                    'success' => true,
                    'message' => 'Database updated successfully.'
                )
            ));
        } else {
            die(json_encode(
                array(
                    'success' => false,
                    'message' => 'Wrong id.'
                )
            ));
        }
    }


    /**
     *
     *
     * Scope:       Private
     * Function:    poptin_middleware_registration_curl
     * Description:
     * Arguments:
     * Return:      JSON Response
     *
     *
     **/
    private function poptin_middleware_registration_curl($first_name, $last_name, $domain, $email)
    {
        /*
         * Because Wordpress doesn't provide one
         * We will rely on the Middleware's Country Code for this one
         * Overriding Country Code
        */
        /*
         * Not sending First + Last Name as per Gal Dubinski
         * first_name => $first_name,
            last_name => $last_name,
         */
        $curl_URL = POPTIN_MARKETPLACE_REGISTER_URL;
        $curl_post_array = array(
            'domain' => $domain,
            'marketplace' => POPTIN_MARKETPLACE,
            'email' => $email
        );

        $curl_options = $this->generate_curl_options($curl_URL, $curl_post_array);
        $curl = curl_init();
        curl_setopt_array($curl, $curl_options);
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        $response_return_array = array();
        if ($err) {
            $response_return_array['success'] = false;
            $response_return_array['message'] = "Internal error occurred. Please try again later.";
            echo json_encode($response_return_array);
            exit(0);
        } else {
            $response_array = json_decode($response);
            if ($response_array->success) {
                $response_return_array['success'] = true;
                $response_return_array['message'] = "Registration successful";
                $response_return_array['js_client_id'] = $response_array->client_id;
                $response_return_array['user_token'] = $response_array->token;


                /**
                 * On Success
                 * We setup the update options
                 */
                update_option("poptin_id", $response_array->client_id);
                update_option("poptin_user_id", $response_array->user_id);
                update_option("poptin_marketplace_token", $response_array->token);
                update_option("poptin_marketplace_email_id", $email);
                poptin_clear_all_caches();
                echo json_encode($response_return_array);
                exit(0);
            } else {
                $response_return_array['success'] = false;
                $response_return_array['message'] = $response_array->message;
                echo json_encode($response_return_array);
                exit(0);
            }
        }
    }

    /**
     * Scope:       Private
     * Function:    generate_curl_options
     * Description: This is Utility Function generates the POST cURL calls options.
     *              Have placed a function to ensure it remains generic and updates do not require many changes.
     *              Uses the CA Cert certificate.
     * Return:      Array | Options Array for cURL Post method call.
     **/
    private function generate_curl_options($curl_URL, $curl_post_array)
    {
        $curl_options_array = array(
            CURLOPT_URL => $curl_URL,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 20,
            CURLOPT_TIMEOUT => 120,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $curl_post_array,
            CURLOPT_CAINFO => POPTIN_CACERT_PATH
        );
        return $curl_options_array;
    }
}

/**
 * Scope:       Public
 * Function:    poptin_shortcode
 * Description: Converts the content value to poptin div.
 *              [poptin-embed 123456789] => <div class='poptin-123456789'></div>
 * Return:      Echo of generated <div></div>
 **/
function poptin_shortcode($arguments)
{
    if(isset($arguments[0]) && !empty($arguments[0])) {
        $poptin_div_id = $arguments[0];
        return "<div class='poptin-'".esc_attr($poptin_div_id)."'></div>";
    }
}
// add_shortcode('poptin-embed', 'poptin_shortcode');

/**
 * Scope:       Public
 * Function:    poptin_shortcode_form
 * Description: Converts the content value to poptin form.
 *              [poptin-form 123456789] => <div class="poptin-embedded" data-id="123456789"></div> 
 * Return:      Echo of generated <div></div>
 **/
function poptin_shortcode_form($arguments)
{
    if(isset($arguments[0]) && !empty($arguments[0])) {
        $poptin_div_id = $arguments[0];
        return "<div class='poptin-embedded' data-id='" . esc_attr($poptin_div_id) . "'></div>";
    }
}
add_shortcode('poptin-form', 'poptin_shortcode_form');

/**
 * Scope:       Public
 * Function:    async_scripts
 * Description: Async URL for changes from asyncload to async='async'
 * Return:      Async URL
 **/
function async_scripts($url)
{
    if (strpos($url, '#asyncload') === false)
        return $url;
    else if (is_admin())
        return str_replace('#asyncload', '', $url);
    else
        return str_replace('#asyncload', '', $url) . "' async='async";
}

/**
 * Scope:       Public
 * Function:    poptin_plugin_redirect
 * Description: Called via admin_init action in Constructor
 *              Will redirect to the plugin page if the poptin_plugin_redirection is setup.
 *              Once redirection is pushed, the key is removed.
 * Return:      void
 **/
function poptin_plugin_redirect()
{
    if (get_option('poptin_plugin_redirection', false)) {
        delete_option('poptin_plugin_redirection');
        exit(wp_redirect("admin.php?page=Poptin"));
    }
}


/**
 * Scope:       Public
 * Function:    poptin_activation_hook
 * Description: On installation of the plugin this will be called.
 *              We want to setup/update poptin related options at this time.
 *              Options to change to blank
 *                  - poptin_id                     [Poptin ID used for JS injection]
 *                  - poptin_marketplace_token      [Poptin Marketplace Token | Generated at the time of registration from the plugin ONLY]
 *                  - poptin_marketplace_email_id   [Poptin Marketplace Email ID | used at the time of registration from the plugin ONLY]
 * Return:      void
 **/
function poptin_activation_hook()
{
    /**
     * We want these three options to be blank on activation/installation of the plugin
     */
    add_option('poptin_id', '', '', 'yes');
    add_option("poptin_marketplace_token", "", '', 'yes');
    add_option("poptin_marketplace_email_id", "", '', 'yes');
    add_option("poptin_user_id", "", '', 'yes');

    /**
     * We want to take the user to the Plugin Page on installation.
     * Hence setting up a temporary redirection key.
     * It gets removed as soon as it's called for the first time.
     * Ussage at : poptin_plugin_redirect, and called with admin_init
     */
    add_option('poptin_plugin_redirection', true);
}


/**
 * Scope:       Public
 * Function:    poptin_decativation_hook
 * Description: On deactivation of the plugin this will be called.
 *              We want to delete poptin related options at this time.
 *              Options to change to remove
 *                  - poptin_id                     [Poptin ID used for JS injection]
 *                  - poptin_marketplace_token      [Poptin Marketplace Token | Generated at the time of registration from the plugin ONLY]
 *                  - poptin_marketplace_email_id   [Poptin Marketplace Email ID | used at the time of registration from the plugin ONLY]
 * Return:      void
 **/
function poptin_deactivation_hook()
{
    delete_option('poptin_id');
    delete_option('poptin_marketplace_token');
    delete_option('poptin_marketplace_email_id');
    delete_option('poptin_user_id');

    /**
     * If At all this was not removed already
     */
    delete_option('poptin_plugin_redirection');
    poptin_clear_all_caches();
}

/**
 * Scope:       Public
 * Function:    poptin_clear_all_caches
 * Description: Clears caches from below plugins if any
 *              - W3Total Cache
 *              - SuperCache
 *              - WPFastestCache
 *              - WP Engine
 * Return:      void
 **/
function poptin_clear_all_caches()
{
    try {
        global $wp_fastest_cache;
        // if W3 Total Cache is being used, clear the cache
        if (function_exists('w3tc_flush_all')) {
            w3tc_flush_all();
        }
        /* if WP Super Cache is being used, clear the cache */
        if (function_exists('wp_cache_clean_cache')) {
            global $file_prefix, $supercachedir;
            if (empty($supercachedir) && function_exists('get_supercache_dir')) {
                $supercachedir = get_supercache_dir();
            }
            wp_cache_clean_cache($file_prefix);
        }

        if (class_exists('WpeCommon')) {
            //be extra careful, just in case 3rd party changes things on us
            if (method_exists('WpeCommon', 'purge_memcached')) {
                //WpeCommon::purge_memcached();
            }
            if (method_exists('WpeCommon', 'clear_maxcdn_cache')) {
                //WpeCommon::clear_maxcdn_cache();
            }
            if (method_exists('WpeCommon', 'purge_varnish_cache')) {
                //WpeCommon::purge_varnish_cache();
            }
        }

        if (method_exists('WpFastestCache', 'deleteCache') && !empty($wp_fastest_cache)) {
            $wp_fastest_cache->deleteCache();
        }
        if (function_exists('rocket_clean_domain')) {
            rocket_clean_domain();
            // Preload cache.
            if (function_exists('run_rocket_sitemap_preload')) {
                run_rocket_sitemap_preload();
            }
        }

        if (class_exists("autoptimizeCache") && method_exists("autoptimizeCache", "clearall")) {
            autoptimizeCache::clearall();
        }

        if (class_exists("LiteSpeed_Cache_API") && method_exists("autoptimizeCache", "purge_all")) {
            LiteSpeed_Cache_API::purge_all();
        }

        if (class_exists('\Hummingbird\Core\Utils')) {

            $modules   = Utils::get_active_cache_modules();
            foreach ($modules as $module => $name) {
                $mod = Utils::get_module($module);

                if ($mod->is_active()) {
                    if ('minify' === $module) {
                        $mod->clear_files();
                    } else {
                        $mod->clear_cache();
                    }
                }
            }
        }
    } catch (Exception $e) {
        return 1;
    }
}
$poptinBase = new POPTIN_Plugin_Base();
?>