<?php
/*
Plugin Name: Neon Early Signup Catcher
Plugin URI: https://neonid.com
Description: Super simple plugin to catch early signups.
Author: Ben Toth
Version: 1.0.1
*/



//load the widget
add_action( 'widgets_init', array('neonid_signup', 'neonid_signup_register_widget' )); //Must be called before construction to hit widgets_init hook
add_action( 'widgets_init', array('neonid_signup','neonid_register_widget_areas' ));


// Widget class.
class neonid_signup extends WP_Widget {

    /* 
     * Settings
     */
    static $version = '1.0.1';
    static $widget_debug = true;

    function __construct() {
        parent::__construct(
                'neon_signup',
                __('NEONID Sign Up Early Widget', 'neonid_signup'),
                array( 'description' => __('Sign up early form sends email to support@neonid.com.', 'neonid_signup'),
                       'classname' => 'neonid_signup' ) 
        );

        \add_action('wp_enqueue_scripts', array(get_class(), 'register_scripts'));
        \add_action('admin_enqueue_scripts', array(get_class(), 'register_admin_scripts'));
        \add_action( 'wp_footer', array(get_class(), 'display_modal_signup' ));    

        // load libraries ##
        self::load_libraries(); 
        
    }


    // Register widget.
    static public function neonid_signup_register_widget() {
        \register_widget( 'neonid_signup' );
    }

    // Register widget areas 
    static public function neonid_register_widget_areas(){
        register_sidebar( array(
            'name'          => 'Modal Popups',
            'id'            => 'neon-modal',
            'before_widget' => '<div class="neon-modal-warehouse"><div class="neon-modal-content">',
            'after_widget'  => '</div></div>',
            'before_title'  => '',
            'after_title'   => '',
            'description'   => __( 'Display in modal window.', 'neonid_signup' ),
        ) );
    }
     
    public static function display_modal_signup() {
        if ( \is_active_sidebar( 'neon-modal' ) ) {
            #\the_widget( 'neonid_signup' );
            \dynamic_sidebar( 'neon-modal' );
        }  
    }   

    public function neonid_signup() {
        
        add_action('wp_enqueue_scripts', array(get_class(), 'register_scripts'));
        add_action('admin_enqueue_scripts', array(get_class(), 'register_admin_scripts'));

        /* Widget settings. */
        $widget_ops = array( 'classname' => 'neonid_signup', 'description' => __('Sign up early form sends email to support@neonid.com.', 'neonid_signup') );

        /* Widget control settings. */
        $control_ops = array( 'id_base' => 'neonid_signup' );

        /* Create the widget. */
        $this->WP_Widget( 'neonid_signup', __('NEONID Sign Up Early Widget', 'neonid_signup'), $widget_ops, $control_ops );

    }


    public static function register_scripts() { 
        \wp_enqueue_script('neonid-signup-js', plugins_url('js/neonid-signup.js', __FILE__), array(), self::$version, false);  
        \wp_enqueue_style('neonid-signup-css', plugins_url('css/style.css', __FILE__), array(), self::$version, 'all');
        \wp_localize_script( 'neonid-signup-js', 'neonid_signup', array('ajax_url' => \admin_url( 'admin-ajax.php' ),'debug' => self::$widget_debug,'load_img' => self::get_plugin_url('css/img/loading-ball.svg') )  );
    }  
    public static function register_admin_scripts($hook) {
        if ($hook != 'widgets.php')
            return;
        \wp_enqueue_script('neonid-signup-admin-js', plugins_url('js/neonid-signup-admin.js', __FILE__), array('jquery'), self::$version, false);  
        \wp_enqueue_style('neonid-signup-admin-css', plugins_url('css/neonid-signup-admin.css', __FILE__), array(), self::$version, 'all');
    }
    

/*  Display Widget */
    
    function widget( $args, $instance ) {
        extract( $args );
        $defaults = $this->get_defaults();
        $instance = wp_parse_args( (array) $instance, $defaults ); 

        /* Before widget (defined by themes). */
        echo $before_widget;
       
        /* Display Widget */
        ?>
            
            <div class="neonid-signup" id="neonid-signup">
                <h3 class="title"><?php echo $instance['title'];?></h4>
                <p class="text"><?php echo $instance['text'];?></p>              
                    <form name="neon_id_signup" onsubmit="event.preventDefault(); return neonid_signup_submit();">
                        <span id="neonid-signup-response"> </span>
                        <input class="email-field" type="text" value="" placeholder="<?php echo $instance['email_placeholder']; ?>" name="email">

                        <button class="submit background-violet font-white d-block centerblock" id="neonid-signup-submit" name="submit" type="submit"><?php echo $instance['button_text']; ?></button>
                        <input type="hidden" id="dest_email" name="dest_email" value="<?php echo $instance['dest_email'] ?>">
                    </form>
                
                <div class="clear"></div>              
            </div><!--neonid_signup_widget-->
        
        <?php

        /* After widget (defined by themes). */
        echo $after_widget;
    }


/*Update Widget */
    
    function update( $new_instance, $old_instance ) {
        $instance = $old_instance;

        $instance = array_merge($instance, $new_instance);

        return $instance;
    }
    

/* Widget Settings */
     
    function form( $instance ) {
        $defaults = $this->get_defaults();
        $instance = \wp_parse_args( (array) $instance, $defaults ); 

        ?>
        <div class="neonid_signup_options_form">

        <?php 
        $this->output_textarea_field('title', __('Title', 'neonid-signup'), $instance['title']);
        $this->output_text_field('text', __('Text', 'neonid-signup'), $instance['text']);
        $this->output_text_field('email_placeholder', __('Email Placeholder', 'neonid-signup'), $instance['email_placeholder']);
        $this->output_text_field('button_text', __('Button Text', 'neonid-signup'), $instance['button_text']);
        $this->output_text_field('success_message', __('Success Message', 'neonid-signup'), $instance['success_message']);
        $this->output_text_field('error_message', __('Error Message', 'neonid-signup'), $instance['error_message']);
        $this->output_text_field('already_subscribed_message', __('Error: Already Subscribed', 'neonid-signup'), $instance['already_subscribed_message']);
        $this->output_text_field('dest_email', __('Send the Email', 'neonid-signup'), $instance['dest_email']);
        ?>


        </div><!-- .wp_subscribe_options_form -->

    <?php
    }


    public function output_text_field($setting_name, $setting_label, $setting_value) {
        ?>

        <p class="neonid-signup-<?php echo $setting_name; ?>-field">
            <label for="<?php echo $this->get_field_id($setting_name) ?>">
                <?php echo $setting_label ?>
            </label>

            <input class="widefat" 
                   id="<?php echo $this->get_field_id($setting_name) ?>" 
                   name="<?php echo $this->get_field_name($setting_name) ?>" 
                   type="text" 
                   value="<?php echo esc_attr($setting_value) ?>" />
        </p>

        <?php
    }

    public function output_textarea_field($setting_name, $setting_label, $setting_value) {
        ?>

        <p class="neonid-signup-<?php echo $setting_name; ?>-field">
            <label for="<?php echo $this->get_field_id($setting_name) ?>">
                <?php echo $setting_label ?>
            </label>

            <textarea class="widefat" id="<?php echo $this->get_field_id($setting_name) ?>" name="<?php echo $this->get_field_name($setting_name) ?>"><?php echo esc_attr($setting_value); ?></textarea>
        </p>

        <?php
    }

    public function output_select_field($setting_name, $setting_label, $setting_values, $selected) {
        ?>

        <p class="neonid-signup-<?php echo $setting_name; ?>-field">
            <label for="<?php echo $this->get_field_id($setting_name) ?>">
                <?php echo $setting_label ?>
            </label>

            <select class="widefat" 
                    id="<?php echo $this->get_field_id($setting_name) ?>" 
                    name="<?php echo $this->get_field_name($setting_name) ?>">

                <?php foreach ($setting_values as $value => $label) : ?>

                    <option value="<?php echo $value; ?>" <?php selected( $selected, $value ); ?>>
                        <?php echo $label; ?>
                    </option>

                <?php endforeach ?>
            </select>
        </p>

        <?php
    }

    public function get_defaults() {
        return array(
            'title' => __('Get more stuff like this<br/> <span>in your inbox</span>', 'neonid-signup'),
            'text' => __('Subscribe to our mailing list and get interesting stuff and updates to your email inbox.', 'neonid-signup'),
            'email_placeholder' => __('Enter your email here', 'neonid-signup'),
            'button_text' => __('Sign Up Now', 'neonid-signup'),
            'success_message' => __('Thank you for subscribing.', 'neonid-signup'),
            'error_message' => __('Something went wrong.', 'neonid-signup'),
            'already_subscribed_message' => __('This email is already subscribed', 'neonid-signup'),
            'dest_email' => __('Send the email to this address', 'neonid-signup')
        );
    }

    function get_widget_settings($widget_id) {
        global $wp_registered_widgets;
        $ret = array();

        if (isset($wp_registered_widgets)) {
            $widget = $wp_registered_widgets[$widget_id];
            $option_data = get_option($widget['callback'][0]->option_name);

            if (isset($option_data[$widget['params'][0]['number']])) {
                $ret = $option_data[$widget['params'][0]['number']];
            }
        }

        return $ret;
    }
    public static function get_plugin_path( $path = '' ) {

        return plugin_dir_path( __FILE__ ).$path;

    }
    public static function get_plugin_url( $path = '' ) {

    #return plugins_url( ltrim( $path, '/' ), __FILE__ );
    return plugins_url( $path, __FILE__ );

}

    private static function load_libraries() {
        require_once self::get_plugin_path( 'admin/admin.php' );
    }
}