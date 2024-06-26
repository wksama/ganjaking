<?php

class userpro_msg_admin {

	var $options;

	function __construct() {
	
		/* Plugin slug and version */
		$this->slug = 'userpro';
		$this->subslug = 'userpro-msg';
		require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		$this->plugin_data = get_plugin_data( userpro_msg_path . 'index.php', false, false);
		$this->version = $this->plugin_data['Version'];
		
		/* Priority actions */
		add_action('userpro_admin_menu_hook', array(&$this, 'add_menu'), 9);
		add_action('admin_enqueue_scripts', array(&$this, 'add_styles'), 9);
		add_action('admin_head', array(&$this, 'admin_head'), 9 );
		add_action('admin_init', array(&$this, 'admin_init'), 9);
		
	}
	
	function admin_init() {
		$this->tabs = array(
			'options' => __('Private Messaging','userpro-msg'),
			'msg-mail' => __('Email Notifications','userpro-msg'),
			'licensing' => __('Licensing','userpro-msg')
		);
		$this->default_tab = 'options';
		
		$this->options = get_option('userpro_msg');
		if (!get_option('userpro_msg')) {
			update_option('userpro_msg', userpro_msg_default_options() );
		}
		
	}
	
	function get_pending_verify_requests_count(){
		$pending = get_option('userpro_verify_requests');
		if (is_array($pending) && count($pending) > 0){
			return '<span class="upadmin-bubble-new">'.count($pending).'</span>';
		}
	}
	
	function admin_head(){

	}

	function add_styles(){
	

		
	}
	
	function add_menu() {
		add_submenu_page( 'userpro', __('Private Messaging','userpro-msg'), __('Private Messaging','userpro-msg'), 'manage_options', 'userpro-msg', array(&$this, 'admin_page') );
	}

	function admin_tabs( $current = null ) {
			$tabs = $this->tabs;
			$links = array();
			if ( isset ( $_GET['tab'] ) ) {
				$current = $_GET['tab'];
			} else {
				$current = $this->default_tab;
			}
			foreach( $tabs as $tab => $name ) :
				if ( $tab == $current ) :
					$links[] = "<a class='nav-tab nav-tab-active' href='?page=".$this->subslug."&tab=$tab'>$name</a>";
				else :
					$links[] = "<a class='nav-tab' href='?page=".$this->subslug."&tab=$tab'>$name</a>";
				endif;
			endforeach;
			foreach ( $links as $link )
				echo $link;
	}

	function get_tab_content() {
		$screen = get_current_screen();
		if( strstr($screen->id, $this->subslug ) ) {
			if ( isset ( $_GET['tab'] ) ) {
				$tab = $_GET['tab'];
			} else {
				$tab = $this->default_tab;
			}
			require_once userpro_msg_path.'admin/panels/'.$tab.'.php';
		}
	}
	
	function save() {			
		if(!empty($_POST['roles_that_can_recive_message']) && !empty($_POST['roles_that_can_send_message']))
		{	
			$_POST['roles_that_can_send']='';

		}
	
		/* other post fields */
		foreach($_POST as $key => $value) {
			if ($key != 'submit') {
				if (!is_array($_POST[$key])) {
					$this->options[$key] = esc_attr($_POST[$key]);
				} else {
					$this->options[$key] = $_POST[$key];
				}
			}
		}
		if(isset($_POST['roles_that_can_send'])){
			update_option('roles_that_can_send',$_POST['roles_that_can_send']);
		}
		if( isset( $_POST['up_msg_license_verify'] ) ){
			$code = $_POST['userpro_msg_envato_code'];
			global $userpro;

			if ($code == ''){
				echo '<div class="error"><p><strong>'.__('Please enter a purchase code.','userpro').'</strong></p></div>';
			} else {
				if ( $userpro->verify_purchase($code, '13z89fdcmr2ia646kphzg3bbz0jdpdja', 'DeluxeThemes', '5958681') ){
					echo '<div class="updated fade"><p><strong>'.__('Thanks for activating UserPro Private Messaging Addon!','userpro-msg').'</strong></p></div>';
				} else {
					echo '<div class="error"><p><strong>'.__('You have entered an invalid purchase code or the Envato API could be down at the moment.','userpro-msg').'</strong></p></div>';
				}
			}
		}
		update_option('userpro_msg', $this->options);
		echo '<div class="updated"><p><strong>'.__('Settings saved.','userpro-msg').'</strong></p></div>';
	}

	function reset() {
		update_option('userpro_msg', userpro_msg_default_options() );
		$this->options = array_merge( $this->options, userpro_msg_default_options() );
		echo '<div class="updated"><p><strong>'.__('Settings are reset to default.','userpro-msg').'</strong></p></div>';
	}
	
	function rebuild_pages() {
		userpro_msg_setup($rebuild=1);
		echo '<div class="updated"><p><strong>'.__('Your plugin pages have been rebuilt successfully.','userpro-msg').'</strong></p></div>';
	}

	function admin_page() {

		if (isset($_POST['submit']) || isset($_POST['up_msg_license_verify'])) {
			$this->save();
		}

		if (isset($_POST['reset-options'])) {
			$this->reset();
		}
		
		if (isset($_POST['rebuild-pages'])) {
			$this->rebuild_pages();
		}
		
	?><div class="wrap <?php echo $this->slug; ?>-admin">
			
			<?php userpro_admin_bar(); ?>
			
			<h2 class="nav-tab-wrapper"><?php $this->admin_tabs(); ?></h2>

			<div class="<?php echo $this->slug; ?>-admin-contain">
				
				<?php $this->get_tab_content(); ?>
				
				<div class="clear"></div>
				
			</div>
			
		</div>

	<?php }

}

$userpro_msg_admin = new userpro_msg_admin();
