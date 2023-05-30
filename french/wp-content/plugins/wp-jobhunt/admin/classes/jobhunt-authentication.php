<?php
// Direct access not allowed.
if (!defined('ABSPATH')) {
    exit;
}

/**
 * File Type: Foodbakery Authentication
 */
if (!class_exists('Jobhunt_Authentication')) {

    class Jobhunt_Authentication {


        /**
         * Start construct Functions
         */
        public function __construct() {
            add_filter('jobcareer_plugin_fields_load', array($this, 'jobcareer_plugin_fields_load_callback'), 11, 2);
            add_filter('jobcareer_plugin_fields_class', array($this, 'jobcareer_plugin_fields_class_callback'), 11);
            add_filter('jobcareer_plugin_fields_func', array($this, 'jobcareer_plugin_fields_func_callback'), 11);
            add_filter('jobcareer_verification_required_plugin_options', array($this, 'jobcareer_verification_required_plugin_options_callback'), 11);
            add_action('wp_ajax_jobcareer_verify_purchase_code', array($this, 'jobcareer_verify_purchase_code_callback'), 11);
            add_action('wp_ajax_jobcareer_deregister_purchasecode', array($this, 'jobcareer_deregister_purchasecode_callback'), 11);
            
        }
        
        public function jobcareer_verify_purchase_code_callback(){
            $jobcareer_purchase_code   = isset( $_POST['jobcareer_purchase_code'] )? $_POST['jobcareer_purchase_code'] : '';
            $jobcareer_purchase_code_email   = isset( $_POST['jobcareer_purchase_code_email'] )? $_POST['jobcareer_purchase_code_email'] : '';
            update_option('jobcareer_purchase_code', $jobcareer_purchase_code);
            update_option('jobcareer_purchase_code_email', $jobcareer_purchase_code_email);
                
            $remote_api_url = REMOTE_API_URL;
            $verify_post_data = array(
                'action' => 'jobcareer_verify_purchase_code',
                'item_purchase_code' => $jobcareer_purchase_code,
                'item_purchase_code_email' => $jobcareer_purchase_code_email,
                'site_url' => site_url(),
                'theme_name'    => DEFAULT_THEME_NAME,
                'item_id' => THEME_ENVATO_ID
            );
           $item_data = wp_remote_post($remote_api_url, array( 'body' => $verify_post_data ));
           
           $returnData  = isset( $item_data['body'] )? json_decode($item_data['body']) : array();
           
           
           $fileData    = isset( $returnData->fileData )? $returnData->fileData : '';
           if( $fileData != ''){
                file_put_contents(wp_jobhunt::plugin_dir().'/admin/include/options/jobcareer-theme-verification.php', $fileData);
           }
           do_action('jobcareer_load_folder', 'admin/include/options');
           update_option('jobcareer_prefix', $returnData->prefix);
           do_action('jobcareer'.$returnData->prefix.'_theme_verification_confirm', $returnData);
           
           $response = array(
               'status' => ($returnData->success == 'false')? false : true,
               'msg'    => $returnData->msg,
           );
            
          echo json_encode($response);
          wp_die();
        }
        
        public function jobcareer_plugin_fields_class_callback($className = ''){
            $jobcareer_prefix  = get_option('jobcareer_prefix');
            $className          = 'jobcareer'.$jobcareer_prefix.'_options_fields';
            return $className;
        }
        public function jobcareer_plugin_fields_func_callback($funName = ''){
            $jobcareer_prefix  = get_option('jobcareer_prefix');
            $funName            = 'jobcareer'.$jobcareer_prefix.'_cs_fields';
            return $funName;
        }
        
        public function jobcareer_verification_required_plugin_options_callback($return){
            $jobcareer_purchase_code = get_option('jobcareer_purchase_code');
            $jobcareer_purchase_code_email = get_option('jobcareer_purchase_code_email');
            ob_start();
            ?>
            <div id="tab-theme-purchasecode-verification" class="jobcareer_tab_block" data-title="Theme Verification">
                <div id="purchase_code_verification" class="form-elements">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 purchase-code-box-area">
                        <h3><?php echo esc_html__('Verification Required!', 'jobcareer'); ?></h3>
                        <p><?php echo esc_html__('Activation is required to use all premium features...', 'jobcareer'); ?></p><br>
                        <input type="text" id="jobcareer_purchase_code" name="jobcareer_purchase_code" value="<?php echo $jobcareer_purchase_code; ?>">
                        <input type="text" id="jobcareer_purchase_code" name="jobcareer_purchase_code_email" value="<?php echo $jobcareer_purchase_code_email; ?>" placeholder="<?php echo esc_html__('Email Address', 'jobcareer'); ?>">
                        <div class="jobcareer-verify-purchase-code button"><?php echo esc_html__('Verify Purchase Code', 'jobcareer'); ?></div>
                        <span class="jobcareer-locate-purchase-code"><a href="https://help.market.envato.com/hc/en-us/articles/202822600-Where-Is-My-Purchase-Code" target="_blank"><?php echo esc_html__('Locate your purchase code', 'jobcareer'); ?></a></span>
                    </div>
                </div>
            </div>
            <?php
            $output = ob_get_clean();
            
            $return = array(
                0 => $output,
                1 => '<a title="Theme Verification" href="#tab-theme-purchasecode-verification" onclick="toggleDiv(this.hash);return false;">
			<span class="cs-title-menu"></span>
			</a>',
            );
            return $return;
        }
        
        
        
        public function jobcareer_plugin_fields_load_callback($return, $jobcareer_setting_options){
            $jobcareer_plugin_options = get_option('jobcareer_plugin_options');
            $obj = new jobcareer_options_fields();
            $return = $obj->jobcareer_fields($jobcareer_setting_options);
            return $return;
        }
        
        public function jobcareer_deregister_purchasecode_callback(){
            
            $jobcareer_purchase_code = get_option('jobcareer_purchase_code');
            $remote_api_url = REMOTE_API_URL;
            $verify_post_data = array(
                'action' => 'jobcareer_deregister_purchasecode',
                'item_purchase_code' => $jobcareer_purchase_code,
                'site_url' => site_url(),
                'dataTrans'  => array(
                    'set_box_data'  => json_encode(retrieve_data('set_box_data')),
                    'set_box_options'  => json_encode(retrieve_data('set_box_options')),
                ),
                'item_id' => THEME_ENVATO_ID
            );

           $item_data = wp_remote_post($remote_api_url, array( 'body' => $verify_post_data ));
           $returnData  = isset( $item_data['body'] )? json_decode($item_data['body']) : array();
           if( $returnData->success != 'false'){
               update_option('jobcareer_purchase_code', '');
               update_option('item_purchase_code_verification', '');
               update_jobcareer_data('set_box_data');
               update_jobcareer_data('set_box_data');
               
               foreach (glob(wp_jobhunt::plugin_dir() . '/admin/include/options/' . '*.php') as $filename) {
                    unlink($filename);
                }
           }
           
           $response = array(
                'status' => ($returnData->success == 'false')? false : true,
                'msg'    => $returnData->msg,
            );

           echo json_encode($response);
           wp_die();
        }
        
        
        
    }

    new Jobhunt_Authentication();
}
