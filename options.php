<?php
/**
 * Options page
 */

if(!defined('ABSPATH')){
    return;
}

/**
 * Show Admin options page
 * @return void
 */
function wpie_options_generate_page(){
    if( !empty($_POST['do_save']) && $_POST['do_save']){

        check_admin_referer('wpie_option_page_nonce');

        $data = array(
            'enabled'   => boolval($_POST['wpie_enabled']),
            'key'       => $_POST['wpie_key'],
            'key_type'  =>  $_POST['wpie_key_type'],
            'w'         => intval($_POST['wpie_width']),
            'h'         => intval($_POST['wpie_height']),
            'pc'        => intval($_POST['wpie_perc']),
            'cmpr'      => intval($_POST['wpie_cmpr']),
            'fit'       => $_POST['wpie_fit']
        );
        if(empty($_POST['wpie_key'])){
            $data['enabled']=0;
        }
        
        $save_res = update_option('wpie_options',$data);
        if($save_res){
            echo '<div class="updated notice"><p>'.__('Options saved!','imageengine').'</p></div>';
        }else{
            echo '<div class="error notice"><p>'.__('Nothing changed!','imageengine').'</p></div>';
        }

    }
    $data = get_option('wpie_options');
    if(false === $data){
        $data = wpie_get_default_settings();
    }
    $is_local = in_array($_SERVER['REMOTE_ADDR'], array('127.0.0.1','::1'));
    ?>
    <div class="wpie_options_container">
        <h1><?php _e('WP ImageEngine Settings','imageengine'); ?></h1>
        <p><?php _e('Before you start using ImageEngine, you need obtain a unique application token. Please follow the instructions below.','imageengine'); ?></p>
        <p><?php _e('The advanced options are in most cases better off at their default values.','imageengine'); ?></p>

        <div class="panel">
            <form id="wpie_options_form" action="" method="post">
                <?php if(!empty($data['key'])): ?>
                <div class="well well-lg">
                    <?php if('lite' == $data['key_type']): ?>
                    <?php endif; ?>
                    <h3><?php _e('Your ImageEngine hostname is','imageengine'); ?> <span class="label label-default"><?php echo $data['key']; if('lite' == $data['key_type']){echo '.lite'; }?>.imgeng.in</span></h3>
                    <p><?php _e('All your images will be prefixed with this hostname.','imageengine'); ?></p>
                    <?php if('lite' == $data['key_type']): ?>
                        <a class="btn btn-danger" target="_blank" href="https://scientiamobile.com/imageengine/inquiry/?utm_source=wp-imageengine-admin&amp;utm_medium=Wordress&amp;utm_term=wp-imageengine&amp;utm_campaign=wp-imageengine" role="button"><?php _e('Upgrade','imageengine'); ?></a>
                    <?php endif; ?>
                    <a class="btn btn-default" target="_blank" href="https://www.scientiamobile.com/myaccount?utm_source=wp-imageengine-admin&amp;utm_medium=Wordress&amp;utm_term=wp-imageengine&amp;utm_campaign=wp-imageengine" role="button"><?php _e('Access your account','imageengine'); ?></a>
                    <a class="btn btn-default" target="_blank" href="https://docs.scientiamobile.com/documentation/image-engine/image-engine-getting-started?utm_source=wp-imageengine-admin&amp;utm_medium=Wordress&amp;utm_term=wp-imageengine&amp;utm_campaign=wp-imageengine" role="button"><?php _e('Documentation','imageengine'); ?></a>
                </div>
                <?php endif; ?>
                <?php if('lite' == $data['key_type']): ?>
                <h2><?php _e('Upgrade your plan','imageengine'); ?></h2>
                <div class="alert alert-warning" role="alert">
                <h3 class="panel-title">Notice to ImageEngine <sup>Lite</sup> users!</h3>
                <hr>
                <p><?php _e('You are currently using the Lite version of ImageEngine. We strongly recommend you to upgrade your account ','imageengine'); ?> <a id="wpie_register_link" target="_blank" href="#"><?php _e('here','imageengine'); ?></a>.<!-- link href is javascript managed--></p>
                <p><?php _e('Once you have upgraded the account:','imageengine'); ?></p>
                <ol>
                    <li><?php _e('select the non-lite option below (green buttion to the right)','imageengine'); ?></li>
                    <li><?php _e('update your token if changed','imageengine'); ?></li>
                    <li><?php _e('save changes','imageengine'); ?></li>
                </ol>

                </div>
                <div class="btn-group" role="group" aria-label="...">
                    <input value="<?php echo $data['key_type']; ?>" type="hidden" name="wpie_key_type" />
                    <button type="button" id="plan_type_lite" class="btn <?php echo 'lite' == $data['key_type'] ? 'btn-primary' : 'btn-default'; ?>">ImageEngine <sup>Lite</sup></button>
                    <button type="button" id="plan_type_normal" class="btn <?php echo 'normal' == $data['key_type'] ? 'btn-primary' : 'btn-success'; ?>">ImageEngine</button>
                </div>
                <?php endif; ?>
                <h2><?php _e('Your Application Token','imageengine'); ?></h2>
                <?php if(!empty($data['key'])): ?>
                    <p class="wpie_get_key_text">
                        <?php _e('All set! To manage your account,','imageengine'); ?> <a target="_blank" href="https://www.scientiamobile.com/myaccount?utm_source=wp-imageengine-admin&amp;utm_medium=Wordress&amp;utm_term=wp-imageengine&amp;utm_campaign=wp-imageengine"><?php _e('login here','imageengine'); ?></a>.<!-- link href is javascript managed-->
                    </p>
                <div class="input-group">
                <?php else: ?>
                    <p class="wpie_get_key_text bg-danger">
                        <?php _e('To obtain an application token,','imageengine'); ?> <a id="wpie_register_link" target="_blank" href="#"><?php _e('please register here','imageengine'); ?></a>.<!-- link href is javascript managed-->
                        <?php _e('Then, copy your token into the text field below.','imageengine'); ?>
                    </p>
             <div class="input-group has-error">
                <?php endif; ?>
                    <span class="input-group-addon" id="sizing-addon3"><?php _e('Your Token','imageengine'); ?></span>
                    <input type="text" class="form-control" placeholder="token" value="<?php echo $data['key']; ?>" name="wpie_key" aria-describedby="sizing-addon3">
                </div>
                <div class="btn-group" role="group" aria-label="...">
                  <h2><?php _e('Local development','imageengine'); ?></h2>
                  <p><?php _e('If you are developing and testing on localhost, enable "local dev mode".','imageengine'); ?></p>
                    <?php if($is_local): ?>
                        <p><em><?php _e('Seems like you are running Wordpress on your localhost. ImageEngine can not fetch images from localhost, so to show images on your site, enable "Local dev mode" below.','imageengine'); ?></em></p>
                    <?php endif; ?>
                    <input value="<?php echo $data['enabled']; ?>" type="hidden" name="wpie_enabled" />
                    <button type="button" id="wpie_enabled" class="btn <?php echo true == $data['enabled'] ? 'btn-primary' : 'btn-default'; ?>"><?php _e('Local dev mode disabled'); ?></button>
                    <button type="button" id="wpie_disabled" class="btn <?php echo false == $data['enabled'] ? 'btn-danger' : 'btn-default'; ?>"><?php _e('Local dev mode enabled'); ?></button>
                </div>
                <br />
                <br />
                <p><a href="javascript:void(0);"  id="adv"><?php _e('Show advanced options','imageengine'); ?></a></p>
                <div id="advpane" style="display:none;">
                    <div class="input-group">
                        <span class="input-group-addon"><?php _e('Default image width','imageengine'); ?></span>
                        <input class="form-control" value="<?php echo $data['w']; ?>" type="number" id="wpie_width" name="wpie_width" min="0" aria-describedby="basic-addon1" />
                    </div>
                    <div class="input-group">
                        <span class="input-group-addon"><?php _e('Default image height','imageengine'); ?></span>
                        <input class="form-control" value="<?php echo $data['h']; ?>" type="number" id="wpie_height" name="wpie_height" min="0" aria-describedby="basic-addon1" />
                    </div>
                    <div class="input-group">
                        <span class="input-group-addon"><?php _e('Default image width relative to screen size (%)','imageengine'); ?></span>
                        <input class="form-control" value="<?php echo $data['pc']; ?>" type="number" id="wpie_perc" name="wpie_perc" min="0" max="100" aria-describedby="basic-addon1" />
                    </div>
                     <div class="input-group">
                        <span class="input-group-addon"><?php _e('Default compression ammount. High number is more compression','imageengine'); ?></span>
                        <input class="form-control" value="<?php echo $data['cmpr']; ?>" type="number" id="wpie_cmpr" name="wpie_cmpr" min="0" max="100" aria-describedby="basic-addon1" />
                    </div>
                    <div class="input-group">
                        <span class="input-group-addon" id="basic-addon1"><?php _e('Default Fit Method','imageengine'); ?></span>
                        <select class="form-control" name="wpie_fit" id="wpie_fit">
                            <option <?php selected('',$data['fit'])?> value=""><?php _e('Auto (default)','imageengine'); ?></option>
                            <option <?php selected('stretch',$data['fit'])?> value="stretch"><?php _e('Stretch','imageengine'); ?></option>
                            <option <?php selected('box',$data['fit'])?> value="box"><?php _e('Box','imageengine'); ?></option>
                            <option <?php selected('letterbox',$data['fit'])?> value="letterbox"><?php _e('Letterbox','imageengine'); ?></option>
                            <option <?php selected('cropbox',$data['fit'])?> value="cropbox"><?php _e('Cropbox','imageengine'); ?></option>
                        </select>
                    </div>
                </div>
                <input type="submit" class="btn btn-primary btn-lg" value="<?php _e('Save','imageengine'); ?>">
                <?php wp_nonce_field('wpie_option_page_nonce'); ?>
                <input type="hidden" name="do_save" value="1">
            </form>
        </div>
        <div class="panel">
            <img src="<?php echo IMGENG_URL.'/imgs/imgeng_wp_logo.png'; ?>">
            <div>
                <?php _e('Resources','imageengine'); ?>:
                <ul>
                    <li><a target="_blank" href="https://docs.scientiamobile.com/documentation/image-engine/image-engine-getting-started?utm_source=wp-imageengine-admin&amp;utm_medium=Wordress&amp;utm_term=wp-imageengine&amp;utm_campaign=wp-imageengine"><?php _e('Documentation','imageengine'); ?></a></li>
                    <?php if('lite' == $data['key_type']): ?>
                        <li><a target="_blank" href="https://www.scientiamobile.com/page/imageengine?utm_source=wp-imageengine-admin&amp;utm_medium=Wordress&amp;utm_term=wp-imageengine&amp;utm_campaign=wp-imageengine"><?php _e('Upgrade to unlimited ImageEngine account','imageengine'); ?></a></li>
                    <?php endif; ?>
                    <li><a target="_blank" href="https://www.scientiamobile.com/myaccount?utm_source=wp-imageengine-admin&amp;utm_medium=Wordress&amp;utm_term=wp-imageengine&amp;utm_campaign=wp-imageengine"><?php _e('Your account','imageengine'); ?></a></li>
                    <li><a target="_blank" href="https://www.scientiamobile.com/page/WP-ImageEngine?utm_source=wp-imageengine-admin&amp;utm_medium=Wordress&amp;utm_term=wp-imageengine&amp;utm_campaign=wp-imageengine"><?php _e('Plugin Homepage','imageengine'); ?></a></li>
                    <li><a target="_blank" href="https://www.scientiamobile.com/forum/viewforum.php?f=21&amp;utm_source=wp-imageengine-admin&amp;utm_medium=Wordress&amp;utm_term=wp-imageengine&amp;utm_campaign=wp-imageengine"><?php _e('Support Forum','imageengine'); ?></a></li>
            </div>
        </div>
    </div>
    <?php
}

/**
 * Display notifivation when no key present.
 * @return void
 */
function my_wpie_admin_notice() {
    $data = get_option('wpie_options');
    if(empty($data['key'])){
        ?>
        <div class="notice error my_wpie_admin_notice" >
            <p><?php _e( '<a href="options-general.php?page=wpie-options-page">ImageEngine Alert</a>: <b>Application Token missing.</b> You need to <a href="'.IMGENG_LITE_REGISTRATION_URL.'" target="_blank">register to get an application token</a>. Then, copy the token and paste it into the "Your Token" input field below before you save. Until the token is present, the plugin will not optimize your images.', 'imageengine' ); ?></p>
        </div>
    <?php
    }
}
add_action( 'admin_notices', 'my_wpie_admin_notice' );


function my_wpie_admin_notice_inactive() {
    $data = get_option('wpie_options');
    if(!$data['enabled']){
        ?>
        <div class="notice notice-warning my_wpie_admin_notice_inactive" >
            <p><?php _e( '<a href="options-general.php?page=wpie-options-page">ImageEngine Alert</a>: <b>Local development mode is enabled.</b> Images are currently not optimized. Please remember to disable "Local Development Mode", when the site is publicly available.', 'imageengine' ); ?></p>
        </div>
    <?php
    }
}
add_action( 'admin_notices', 'my_wpie_admin_notice_inactive' );


/**
 * Register admin options page
 * @return void
 */
function wpie_options_add_options_page(){
    add_options_page(__('ImageEngine Options', 'imageengine') , __('ImageEngine Options','imageengine'), 'administrator', 'wpie-options-page', 'wpie_options_generate_page');
}

add_action('admin_menu', 'wpie_options_add_options_page');



