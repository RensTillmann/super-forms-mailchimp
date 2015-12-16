<?php
/**
 * Super Forms Mailchimp
 *
 * @package   Super Forms Mailchimp
 * @author    feeling4design
 * @link      http://codecanyon.net/item/super-forms-drag-drop-form-builder/13979866
 * @copyright 2015 by feeling4design
 *
 * @wordpress-plugin
 * Plugin Name: Super Forms Mailchimp
 * Plugin URI:  http://codecanyon.net/item/super-forms-drag-drop-form-builder/13979866
 * Description: Subscribes and unsubscribes users from a specific Mailchimp list
 * Version:     1.0.0
 * Author:      feeling4design
 * Author URI:  http://codecanyon.net/user/feeling4design
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if(!class_exists('SUPER_Mailchimp')) :


    /**
     * Main SUPER_Mailchimp Class
     *
     * @class SUPER_Mailchimp
     * @version	1.0.0
     */
    final class SUPER_Mailchimp {
    
        
        /**
         * @var string
         *
         *	@since		1.0.0
        */
        public $version = '1.0.0';

        
        /**
         * @var SUPER_Mailchimp The single instance of the class
         *
         *	@since		1.0.0
        */
        protected static $_instance = null;

        
        /**
         * Contains an array of registered script handles
         *
         * @var array
         *
         *	@since		1.0.0
        */
        private static $scripts = array();
        
        
        /**
         * Contains an array of localized script handles
         *
         * @var array
         *
         *	@since		1.0.0
        */
        private static $wp_localize_scripts = array();
        
        
        /**
         * Main SUPER_Mailchimp Instance
         *
         * Ensures only one instance of SUPER_Mailchimp is loaded or can be loaded.
         *
         * @static
         * @see SUPER_Mailchimp()
         * @return SUPER_Mailchimp - Main instance
         *
         *	@since		1.0.0
        */
        public static function instance() {
            if(is_null( self::$_instance)){
                self::$_instance = new self();
            }
            return self::$_instance;
        }

        
        /**
         * SUPER_Mailchimp Constructor.
         *
         *	@since		1.0.0
        */
        public function __construct(){
            $this->init_hooks();
            do_action('super_mailchimp_loaded');
        }

        
        /**
         * Define constant if not already set
         *
         * @param  string $name
         * @param  string|bool $value
         *
         *	@since		1.0.0
        */
        private function define($name, $value){
            if(!defined($name)){
                define($name, $value);
            }
        }

        
        /**
         * What type of request is this?
         *
         * string $type ajax, frontend or admin
         * @return bool
         *
         *	@since		1.0.0
        */
        private function is_request($type){
            switch ($type){
                case 'admin' :
                    return is_admin();
                case 'ajax' :
                    return defined( 'DOING_AJAX' );
                case 'cron' :
                    return defined( 'DOING_CRON' );
                case 'frontend' :
                    return (!is_admin() || defined('DOING_AJAX')) && ! defined('DOING_CRON');
            }
        }

        
        /**
         * Hook into actions and filters
         *
         *	@since		1.0.0
        */
        private function init_hooks() {
            
            // Filters since 1.0.0

            // Actions since 1.0.0
            add_filter( 'super_shortcodes_after_form_elements_filter', array( $this, 'add_mailchimp_element' ), 10, 2 );

            if ( $this->is_request( 'frontend' ) ) {
                
                // Filters since 1.0.0

                // Actions since 1.0.0
                
            }
            
            if ( $this->is_request( 'admin' ) ) {
                
                // Filters since 1.0.0
                add_filter( 'super_settings_after_smtp_server_filter', array( $this, 'add_mailchimp_settings' ), 10, 2 );
                add_filter( 'super_enqueue_styles', array( $this, 'add_stylesheet' ), 10, 1 );

                // Actions since 1.0.0
                add_action( 'super_before_load_form_dropdown_hook', array( $this, 'add_ready_to_use_forms' ) );
                add_action( 'super_after_load_form_dropdown_hook', array( $this, 'add_ready_to_use_forms_json' ) );

            }
            
            if ( $this->is_request( 'ajax' ) ) {

                // Filters since 1.0.0

                // Actions since 1.0.0
                add_action( 'super_before_sending_email_hook', array( $this, 'update_mailchimp_subscribers' ) );

            }
            
        }

        
        /**
         * Hook into the load form dropdown and add some ready to use forms
         *
         *  @since      1.0.0
        */
        public static function add_ready_to_use_forms() {
            $html = '<option value="mailchimp-email">Mailchimp - Subscribe email address only</option>';
            $html .= '<option value="mailchimp-name">Mailchimp - Subscribe with first and last name</option>';
            $html .= '<option value="mailchimp-interests">Mailchimp - Subscribe with interests</option>';
            echo $html;
        }


        /**
         * Hook into the after load form dropdown and add the json of the ready to use forms
         *
         *  @since      1.0.0
        */
        public static function add_ready_to_use_forms_json() {
            $html  = '<textarea hidden name="mailchimp-email">';
            $html .= '[{"tag":"text","group":"form_elements","inner":"","data":{"name":"email","email":"Email","label":"","description":"","placeholder":"Your Email Address","tooltip":"","validation":"email","error":"","grouped":"0","maxlength":"0","minlength":"0","width":"0","exclude":"0","error_position":"","icon_position":"outside","icon_align":"left","icon":"envelope","conditional_action":"disabled","conditional_trigger":"all","conditional_items":[{"field":"name","logic":"contains","value":""}]}},{"tag":"column","group":"layout_elements","inner":[{"tag":"text","group":"form_elements","inner":"","data":{"name":"first_name","email":"First name:","label":"","description":"","placeholder":"Your First Name","tooltip":"","validation":"empty","error":"","grouped":"0","maxlength":"0","minlength":"0","width":"0","exclude":"0","error_position":"","icon_position":"outside","icon_align":"left","icon":"user","conditional_action":"disabled","conditional_trigger":"all","conditional_items":[{"field":"email","logic":"contains","value":""}]}}],"data":{"size":"1/2","margin":"","conditional_action":"disabled"}},{"tag":"column","group":"layout_elements","inner":[{"tag":"text","group":"form_elements","inner":"","data":{"name":"last_name","email":"Last name:","label":"","description":"","placeholder":"Your Last Name","tooltip":"","validation":"empty","error":"","grouped":"0","maxlength":"0","minlength":"0","width":"0","exclude":"0","error_position":"","icon_position":"outside","icon_align":"left","icon":"user","conditional_action":"disabled","conditional_trigger":"all","conditional_items":[{"field":"email","logic":"contains","value":""}]}}],"data":{"size":"1/2","margin":"","conditional_action":"disabled"}},{"tag":"mailchimp","group":"form_elements","inner":"","data":{"list_id":"53e03de9e1","display_interests":"yes","send_confirmation":"yes","email":"","label":"Interests","description":"Select one or more interests","tooltip":"","validation":"empty","error":"","maxlength":"0","minlength":"0","display":"horizontal","grouped":"0","width":"0","exclude":"2","error_position":"","icon_position":"inside","icon_align":"left","icon":"star","conditional_action":"disabled","conditional_trigger":"all","conditional_items":[{"field":"email","logic":"contains","value":""}]}}]';
            $html .= '</textarea>';

            $html .= '<textarea hidden name="mailchimp-name">';
            $html .= '[{"tag":"text","group":"form_elements","inner":"","data":{"name":"email","email":"Email","label":"","description":"","placeholder":"Your Email Address","tooltip":"","validation":"email","error":"","grouped":"0","maxlength":"0","minlength":"0","width":"0","exclude":"0","error_position":"","icon_position":"outside","icon_align":"left","icon":"envelope","conditional_action":"disabled","conditional_trigger":"all","conditional_items":[{"field":"name","logic":"contains","value":""}]}},{"tag":"column","group":"layout_elements","inner":[{"tag":"text","group":"form_elements","inner":"","data":{"name":"first_name","email":"First name:","label":"","description":"","placeholder":"Your First Name","tooltip":"","validation":"empty","error":"","grouped":"0","maxlength":"0","minlength":"0","width":"0","exclude":"0","error_position":"","icon_position":"outside","icon_align":"left","icon":"user","conditional_action":"disabled","conditional_trigger":"all","conditional_items":[{"field":"email","logic":"contains","value":""}]}}],"data":{"size":"1/2","margin":"","conditional_action":"disabled"}},{"tag":"column","group":"layout_elements","inner":[{"tag":"text","group":"form_elements","inner":"","data":{"name":"last_name","email":"Last name:","label":"","description":"","placeholder":"Your Last Name","tooltip":"","validation":"empty","error":"","grouped":"0","maxlength":"0","minlength":"0","width":"0","exclude":"0","error_position":"","icon_position":"outside","icon_align":"left","icon":"user","conditional_action":"disabled","conditional_trigger":"all","conditional_items":[{"field":"email","logic":"contains","value":""}]}}],"data":{"size":"1/2","margin":"","conditional_action":"disabled"}},{"tag":"mailchimp","group":"form_elements","inner":"","data":{"list_id":"53e03de9e1","display_interests":"yes","send_confirmation":"yes","email":"","label":"Interests","description":"Select one or more interests","tooltip":"","validation":"empty","error":"","maxlength":"0","minlength":"0","display":"horizontal","grouped":"0","width":"0","exclude":"2","error_position":"","icon_position":"inside","icon_align":"left","icon":"star","conditional_action":"disabled","conditional_trigger":"all","conditional_items":[{"field":"email","logic":"contains","value":""}]}}]';
            $html .= '</textarea>';

            $html .= '<textarea hidden name="mailchimp-interests">';
            $html .= '[{"tag":"text","group":"form_elements","inner":"","data":{"name":"email","email":"Email","label":"","description":"","placeholder":"Your Email Address","tooltip":"","validation":"email","error":"","grouped":"0","maxlength":"0","minlength":"0","width":"0","exclude":"0","error_position":"","icon_position":"outside","icon_align":"left","icon":"envelope","conditional_action":"disabled","conditional_trigger":"all","conditional_items":[{"field":"name","logic":"contains","value":""}]}},{"tag":"column","group":"layout_elements","inner":[{"tag":"text","group":"form_elements","inner":"","data":{"name":"first_name","email":"First name:","label":"","description":"","placeholder":"Your First Name","tooltip":"","validation":"empty","error":"","grouped":"0","maxlength":"0","minlength":"0","width":"0","exclude":"0","error_position":"","icon_position":"outside","icon_align":"left","icon":"user","conditional_action":"disabled","conditional_trigger":"all","conditional_items":[{"field":"email","logic":"contains","value":""}]}}],"data":{"size":"1/2","margin":"","conditional_action":"disabled"}},{"tag":"column","group":"layout_elements","inner":[{"tag":"text","group":"form_elements","inner":"","data":{"name":"last_name","email":"Last name:","label":"","description":"","placeholder":"Your Last Name","tooltip":"","validation":"empty","error":"","grouped":"0","maxlength":"0","minlength":"0","width":"0","exclude":"0","error_position":"","icon_position":"outside","icon_align":"left","icon":"user","conditional_action":"disabled","conditional_trigger":"all","conditional_items":[{"field":"email","logic":"contains","value":""}]}}],"data":{"size":"1/2","margin":"","conditional_action":"disabled"}},{"tag":"mailchimp","group":"form_elements","inner":"","data":{"list_id":"53e03de9e1","display_interests":"yes","send_confirmation":"yes","email":"","label":"Interests","description":"Select one or more interests","tooltip":"","validation":"empty","error":"","maxlength":"0","minlength":"0","display":"horizontal","grouped":"0","width":"0","exclude":"2","error_position":"","icon_position":"inside","icon_align":"left","icon":"star","conditional_action":"disabled","conditional_trigger":"all","conditional_items":[{"field":"email","logic":"contains","value":""}]}}]';
            $html .= '</textarea>';
            echo $html;
        }


        /**
         * Hook into elements and add Mailchimp element
         * This element specifies the Mailchimp List by it's given ID and retrieves it's Groups
         *
         *  @since      1.0.0
        */
        public static function add_stylesheet( $array ) {
            $suffix         = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
            $assets_path    = str_replace( array( 'http:', 'https:' ), '', plugin_dir_url( __FILE__ ) ) . '/assets/';
            $backend_path   = $assets_path . 'css/backend/';
            $array['super-mailchimp'] = array(
                'src'     => $backend_path . 'mailchimp' . $suffix . '.css',
                'deps'    => '',
                'version' => SUPER_VERSION,
                'media'   => 'all',
                'screen'  => array( 
                    'super-forms_page_super_create_form'
                ),
                'method'  => 'enqueue',
            );
            return $array;
        }


        /**
         * Handle the Mailchimp element output
         *
         *  @since      1.0.0
        */
        public static function mailchimp( $tag, $atts ) {
            if( !isset( $atts['display_interests'] ) ) $atts['display_interests'] = 'no';

            if( $atts['display_interests']=='no' ) {
                $atts['label'] = '';
                $atts['description'] = '';
                $atts['icon'] = '';
            }

            $tag = 'checkbox';
            $classes = ' display-' . $atts['display'];
            $result = SUPER_Shortcodes::opening_tag( $tag, $atts, $classes );

            $show_hidden_field = true;

            // Retrieve groups based on the given List ID:
            $settings = get_option('super_settings');

            // Check if the API key has been set
            if( ( !isset( $settings['mailchimp_key'] ) ) || ( $settings['mailchimp_key']=='' ) ) {
                $show_hidden_field = false;
                $result .= '<strong style="color:red;">Please setup your API key in (Super Forms > Settings > Mailchimp)</strong>';
            }else{
                if( ( !isset( $atts['list_id'] ) ) || ( $atts['list_id']=='' ) ) {
                    $show_hidden_field = false;
                    $result .= '<strong style="color:red;">Please enter your List ID and choose wether or not to retrieve Groups based on your List.</strong>';
                }else{
                    $list_id = sanitize_text_field( $atts['list_id'] );
                    if( $atts['display_interests']=='yes' ) {
                        $api_key = $settings['mailchimp_key'];
                        $datacenter = explode('-', $api_key);
                        $datacenter = $datacenter[1];
                        $endpoint = 'https://' . $datacenter . '.api.mailchimp.com/3.0/';
                        $request = 'lists/' . $list_id . '/interest-categories/';
                        $url = $endpoint . $request;
                        $ch = curl_init();
                        curl_setopt( $ch, CURLOPT_URL, $url );
                        curl_setopt( $ch, CURLOPT_HTTPHEADER, array( 'content-type: application/json' ) );
                        curl_setopt( $ch, CURLOPT_USERPWD, 'anystring:' . $api_key ); 
                        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
                        curl_setopt( $ch, CURLOPT_ENCODING, '' );
                        $output = curl_exec( $ch );
                        $output = json_decode( $output );
                        if( !isset( $output->categories ) ) {
                            $result .= '<strong style="color:red;">The List ID seems to be invalid, please make sure you entered to correct List ID.</strong>';
                        }else{
                            $result .= SUPER_Shortcodes::opening_wrapper( $atts );
                            foreach( $output->categories as $k => $v ) {
                                $request = $request . $v->id . '/interests/';
                                $url = $endpoint.$request;
                                $ch = curl_init();
                                curl_setopt( $ch, CURLOPT_URL, $url );
                                curl_setopt( $ch, CURLOPT_HTTPHEADER, array( 'content-type: application/json' ) );
                                curl_setopt( $ch, CURLOPT_USERPWD, 'anystring:' . $api_key ); 
                                curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
                                curl_setopt( $ch, CURLOPT_ENCODING, '' );
                                $output = curl_exec( $ch );
                                $output = json_decode( $output );
                                foreach( $output->interests as $ik => $iv ) {
                                    $result .= '<label><input type="checkbox" value="' . esc_attr( $iv->id ) . '" />' . $iv->name . '</label>';
                                }
                            }
                            $result .= '<input class="super-shortcode-field" type="hidden"';
                            $result .= ' name="mailchimp_interests" value=""';
                            $result .= SUPER_Shortcodes::common_attributes( $atts, $tag );
                            $result .= ' />';
                            $result .= '</div>';
                        }
                    }
                }
            }
            $result .= SUPER_Shortcodes::loop_conditions( $atts );
            $result .= '</div>';

            // Add the hidden fields
            if( $atts['send_confirmation']=='yes' ) {
                $atts['label'] = '';
                $atts['description'] = '';
                $atts['icon'] = '';
                $classes = ' hidden';
                $result .= SUPER_Shortcodes::opening_tag( 'hidden', $atts, $classes );
                $result .= '<input class="super-shortcode-field" type="hidden" value="1" name="mailchimp_send_confirmation" data-exclude="2" />';
                $result .= '</div>';
            }
            if( $show_hidden_field==true ) {
                $atts['label'] = '';
                $atts['description'] = '';
                $atts['icon'] = '';
                $classes = ' hidden';
                $result .= SUPER_Shortcodes::opening_tag( 'hidden', $atts, $classes );
                $result .= '<input class="super-shortcode-field" type="hidden" value="' . $list_id . '" name="mailchimp_list_id" data-exclude="2" />';
                $result .= '</div>';
            }

            return $result;
        }


        /**
         * Hook into elements and add Mailchimp element
         * This element specifies the Mailchimp List by it's given ID and retrieves it's Groups
         *
         *  @since      1.0.0
        */
        public static function add_mailchimp_element( $array, $attributes ) {

            // Include the predefined arrays
            require(SUPER_PLUGIN_DIR.'/includes/shortcodes/predefined-arrays.php' );

            $array['form_elements']['shortcodes']['mailchimp'] = array(
                'callback' => 'SUPER_Mailchimp::mailchimp',
                'name' => __( 'Mailchimp', 'super' ),
                'icon' => 'mailchimp',
                'atts' => array(
                    'general' => array(
                        'name' => __( 'General', 'super' ),
                        'fields' => array(
                            'list_id' => array(
                                'name'=>__( 'Mailchimp List ID', 'super' ), 
                                'desc'=>__( 'Your List ID for example: 9e67587f52', 'super' ),
                                'default'=> (!isset($attributes['list_id']) ? '' : $attributes['list_id']),
                                'required'=>true, 
                            ),
                            'display_interests' => array(
                                'name'=>__( 'Display interests', 'super' ),
                                'desc'=>__( 'Allow users to select one or more interests (retrieved by given List ID)', 'super' ),
                                'type' => 'select',
                                'default'=> (!isset($attributes['interests']) ? 'no' : $attributes['interests']),
                                'values' => array(
                                    'no' => __( 'No', 'super' ), 
                                    'yes' => __( 'Yes', 'super' ), 
                                ),
                            ),
                            'send_confirmation' => array(
                                'name'=>__( 'Send the Mailchimp confirmation email', 'super' ),
                                'desc'=>__( 'Users will receive a confirmation email before they are subscribed', 'super' ),
                                'type' => 'select',
                                'default'=> (!isset($attributes['send_confirmation']) ? 'no' : $attributes['send_confirmation']),
                                'values' => array(
                                    'no' => __( 'No', 'super' ), 
                                    'yes' => __( 'Yes', 'super' ), 
                                ),
                            ),                            
                            'email' => SUPER_Shortcodes::email($attributes, $default='Interests'),
                            'label' => $label,
                            'description'=> $description,
                            'tooltip' => $tooltip,
                            'validation' => $validation_empty,
                            'error' => $error,  
                        )
                    ),
                    'advanced' => array(
                        'name' => __( 'Advanced', 'super' ),
                        'fields' => array(
                            'maxlength' => $maxlength,
                            'minlength' => $minlength,
                            'display' => array(
                                'name'=>__( 'Vertical / Horizontal display', 'super' ), 
                                'type' => 'select',
                                'default'=> (!isset($attributes['display']) ? 'vertical' : $attributes['display']),
                                'values' => array(
                                    'vertical' => __( 'Vertical display ( | )', 'super' ), 
                                    'horizontal' => __( 'Horizontal display ( -- )', 'super' ), 
                                ),
                            ),
                            'grouped' => $grouped,                    
                            'width' => $width,
                            'exclude' => $exclude, 
                            'error_position' => $error_position_left_only,
                            
                        ),
                    ),
                    'icon' => array(
                        'name' => __( 'Icon', 'super' ),
                        'fields' => array(
                            'icon_position' => $icon_position,
                            'icon_align' => $icon_align,
                            'icon' => SUPER_Shortcodes::icon($attributes,'check-square-o'),
                        ),
                    ),
                    'conditional_logic' => $conditional_logic_array
                ),
            );
            return $array;
        }


        /**
         * Hook into settings and add Mailchimp settings
         *
         *  @since      1.0.0
        */
        public static function add_mailchimp_settings( $array, $settings ) {
            $array['mailchimp'] = array(        
                'name' => __( 'Mailchimp', 'super' ),
                'label' => __( 'Mailchimp Settings', 'super' ),
                'fields' => array(
                    'mailchimp_key' => array(
                        'name' => __( 'API key', 'super' ),
                        'default' => SUPER_Settings::get_value( 0, 'mailchimp_key', $settings['settings'], '' ),
                    )
                )
            );
            return $array;
        }


        /**
         * Hook into before sending email and check for subscribe or unsubscribe action
         * After that do a curl request to mailchimp to update the list by the given List ID
         *
         *  @since      1.0.0
        */
        public static function update_mailchimp_subscribers( $atts ) {
            
            $data = $atts['post']['data'];
            if( isset( $data['mailchimp_list_id'] ) ) {

                // Retreive the list ID
                $list_id = sanitize_text_field( $data['mailchimp_list_id']['value'] );
                
                // Setup CURL
                $settings = get_option('super_settings');
                $api_key = $settings['mailchimp_key'];
                $datacenter = explode('-', $api_key);
                $datacenter = $datacenter[1];
                $endpoint = 'https://' . $datacenter . '.api.mailchimp.com/3.0/';
                $request = 'lists/' . $list_id . '/interest-categories/';

                $email = sanitize_email( $data['email']['value'] );
                $email = strtolower($email);
                $email_md5 = md5($email);
                $request = 'lists/' . $list_id . '/members/';
                $url = $endpoint.$request;
                $patch_url = $url . $email_md5;

                // Setup default user data
                $user_data['email_address'] = $email;
                if( isset( $data['first_name'] ) ) {
                    $user_data['merge_fields']['FNAME'] = $data['first_name']['value'];
                }
                if( isset( $data['last_name'] ) ) {
                    $user_data['merge_fields']['LNAME'] = $data['last_name']['value'];
                }
                if( $data['mailchimp_send_confirmation']['value']==1 ) {
                    $user_data['status'] = 'pending';
                }else{
                    $user_data['status'] = 'subscribed';
                }

                // Find out if we have some selected interests
                if( isset( $data['mailchimp_interests'] ) ) {
                    $interests = explode( ', ', $data['mailchimp_interests']['value'] );
                    foreach($interests as $k => $v ){
                        $user_data['interests'][$v] = true;
                    }
                }
                
                $data_string = json_encode($user_data); 
                
                $ch = curl_init();
                curl_setopt( $ch, CURLOPT_URL, $url );
                curl_setopt( $ch, CURLOPT_HTTPHEADER, array( 'content-type: application/json' ) );
                curl_setopt( $ch, CURLOPT_USERPWD, 'anystring:' . $api_key ); 
                curl_setopt( $ch, CURLOPT_POSTFIELDS, $data_string );
                curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
                curl_setopt( $ch, CURLOPT_ENCODING, '' );
                $output = curl_exec( $ch );
                $output = json_decode( $output );

                // User already exists for this list, lets update the user with a PUT request
                if( $output->status==400 ) {

                    // Only delete interests if this for is actually giving the user the option to select interests
                    if( isset( $data['mailchimp_interests'] ) ) {
                        // First get all interests, and set each interests to false
                        $ch = curl_init();
                        curl_setopt( $ch, CURLOPT_URL, $patch_url );
                        curl_setopt( $ch, CURLOPT_HTTPHEADER, array( 'content-type: application/json' ) );
                        curl_setopt( $ch, CURLOPT_USERPWD, 'anystring:' . $api_key ); 
                        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
                        curl_setopt( $ch, CURLOPT_ENCODING, '' );
                        $output = curl_exec( $ch );
                        $output = json_decode( $output );
                        
                        // Create a new object with all interests set to false
                        foreach( $output->interests as $k => $v ) {
                            $deleted_user_data['interests'][$k] = false;
                        }
                        $deleted_data_string = json_encode($deleted_user_data); 
                        
                        // Now update the user with it's new interests
                        $ch = curl_init();
                        curl_setopt( $ch, CURLOPT_URL, $patch_url );
                        curl_setopt( $ch, CURLOPT_HTTPHEADER, array( 'content-type: application/json' ) );
                        curl_setopt( $ch, CURLOPT_USERPWD, 'anystring:' . $api_key ); 
                        curl_setopt( $ch, CURLOPT_POSTFIELDS, $deleted_data_string );
                        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
                        curl_setopt( $ch, CURLOPT_ENCODING, '' );
                        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH' );
                        $output = curl_exec( $ch );
                        $output = json_decode( $output );
                    }

                    // Now update the user with it's new interests
                    $ch = curl_init();
                    curl_setopt( $ch, CURLOPT_URL, $patch_url );
                    curl_setopt( $ch, CURLOPT_HTTPHEADER, array( 'content-type: application/json' ) );
                    curl_setopt( $ch, CURLOPT_USERPWD, 'anystring:' . $api_key ); 
                    curl_setopt( $ch, CURLOPT_POSTFIELDS, $data_string );
                    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
                    curl_setopt( $ch, CURLOPT_ENCODING, '' );
                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH' );
                    $output = curl_exec( $ch );
                    $output = json_decode( $output );

                }
            }
        }
    }
        
endif;


/**
 * Returns the main instance of SUPER_Mailchimp to prevent the need to use globals.
 *
 * @return SUPER_Mailchimp
 */
function SUPER_Mailchimp() {
    return SUPER_Mailchimp::instance();
}


// Global for backwards compatibility.
$GLOBALS['super_mailchimp'] = SUPER_Mailchimp();
