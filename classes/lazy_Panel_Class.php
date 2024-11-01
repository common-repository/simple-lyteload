<?php
/**
 * lyteload_panel Extention class to SimplePanel
 *
 * @version 0.1
 * @author Ohad Raz <admin@bainternet.info>
 * @copyright 2013 Ohad Raz
 * 
 */
if (!class_exists('lyteload_panel')){
	/**
	* lyteload_panel
	*/
	class lyteload_panel extends SimplePanel{

		public $txtDomain = 'wp_lazyload';

		public function extra_hooks(){
			add_action('load-'.$this->slug, array($this,'_help_tab'));
			add_action( get_class($this).'add_meta_boxes', array($this,'add_meta_boxes' ));
		}
		public function admin_menu(){
			parent::admin_menu();
			//help tabs
			
		}

		/**
		 * add_meta_boxes to page
		 */
		public function add_meta_boxes(){
			add_meta_box( 'Savebox', __('Save Changes'), array($this,'savec'), get_class($this), 'side','low');
			add_meta_box( 'Credit_sidebar', __('Credits'), array($this,'credits'), get_class($this), 'side','low');
			add_meta_box( 'News', __('Latest From Bainternet'), array($this,'news'), get_class($this), 'side','low');
			foreach ($this->sections as $s) {
				add_meta_box( $s['id'], $s['title'], array($this,'main_settings'), get_class($this), 'normal','low',$s);
			}
		}

		/**
		 * news metabox
		 * @return [type] [description]
		 */
		public function news(){
			$news = get_transient( 'bainternetNews' );
			if ( !$news ) {
				if (!function_exists('fetch_feed'))
					include_once(ABSPATH . WPINC . '/feed.php');
				// Get a SimplePie feed object from the specified feed source.
				$rss = fetch_feed('http://en.bainternet.info/feed');
				ob_start();
				$maxitems = 0;

				if (!is_wp_error( $rss ) ) {
				    $maxitems = $rss->get_item_quantity(5); 
				    $rss_items = $rss->get_items(0, $maxitems); 
				}
				?>

				<ul>
				    <?php if ($maxitems == 0) echo '<li>No items.</li>';
				    else
				    // Loop through each feed item and display each item as a hyperlink.
				    foreach ( $rss_items as $item ) : ?>
				    <li>
				    	<span><?php echo 'Posted '.$item->get_date('j F Y | g:i a'); ?></span><br/>
				        <a target="_blank" href='<?php echo esc_url( $item->get_permalink() ); ?>'
				        title='<?php echo 'Posted '.$item->get_date('j F Y | g:i a'); ?>'>
				        <?php echo esc_html( $item->get_title() ); ?></a>
				    </li>
				    <?php endforeach; ?>
				</ul>
				<?php
				$news = ob_get_clean();
				set_transient( 'bainternetNews', $news, 60 * 60 * 24 * 3 );
			}
			echo $news;
		}

		/**
		 * generate plugin button metabox
		 * @return [type] [description]
		 */
		public function savec(){
			echo '<span class="working" style="display:none;"><img src="images/wpspin_light.gif"></span>';
			submit_button(__('Save Changes'));
		}

		/**
		 * main settings metaboxs
		 * @return [type] [description]
		 */
		function main_settings($args,$s = null){
        	
				echo '<table class="form-table">';
        		do_settings_fields(get_class($this),$s['id']);
        		echo '</table>';
		}

		/**
		 * credits metabox
		 * @return [type] [description]
		 */
		function credits(){
			?>
			<p><strong>
				<?php echo __( 'Want to help make this plugin even better? All donations are used to improve and support, so donate $20, $50 or $100 now!' ); ?></strong></p>
			<a class="" href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=K4MMGF5X3TM5L" target="_blank"><img type="image" src="https://www.paypalobjects.com/<?php echo get_locale(); ?>/i/btn/btn_donate_LG.gif" border="0" alt="PayPal Ñ The safer, easier way to pay online."></a>
            <p><?php _e( 'Or you could:', 'bpm' ); ?></p>
            <ul>
                    <li><a href="http://wordpress.org/extend/plugins/author-category/"><?php _e( 'Rate the plugin 5&#9733; on WordPress.org' ); ?></a></li>
                    <li><a href="http://wordpress.org/extend/plugins/author-category/"><?php _e( 'Blog about it &amp; link to the plugin page'); ?></a></li>
            </ul>
            <?php
		}

		/**
		 * show_page 
		 *
		 * this function displays the page with the metaboxes
		 * @return [type] [description]
		 */
		public function show_page(){
			wp_enqueue_script('post');
			do_action(get_class($this).'add_meta_boxes');
			?>
		    <div class="wrap">
		    	<?php screen_icon('plugins'); ?>
		        <h2><?php echo $this->name; ?></h2>
		        <div id="message" class="below-h2"></div>
		        <?php do_action($this->slug.'_before_Form',$this); ?>
		         <form id="BPM_FORM" action="options.php" method="POST">
		         	<div id="poststuff" class="metabox-holder has-right-sidebar columns-2">
					    <div class="inner-sidebar">
					    	<!-- SIDEBAR BOXES -->
					    	<?php do_action($this->slug.'_before_sidebar',$this); ?>
					    	<?php do_meta_boxes( get_class($this), 'side',$this ); ?>
					    	<?php do_action($this->slug.'_after_sidebar',$this); ?>
					    </div>
					    <div id="post-body" style="background-color: transparent;">
					        <div id="post-body-content">
					            <div id="titlediv"></div>
					            <div id="postdivrich" class="postarea"></div>
					            <div id="normal-sortables" class="meta-box-sortables ui-sortable">
					                <!-- BOXES -->
					                <?php do_action($this->slug.'_before_metaboxes',$this); ?>
									<?php
					                	foreach ($this->sections as $s) {
						        			settings_fields($s['option_group']);
						        		}
					                	do_meta_boxes( get_class($this), 'normal',$this ); 
					                ?>
					                <?php do_action($this->slug.'_after_metaboxes',$this); ?>
					            </div>
					        </div>
					    </div>
					    <br class="clear">
					</div>
		            <?php do_action($this->slug.'_after_Fields',$this); ?>
		        </form>
		        <?php do_action($this->slug.'_after_Form',$this); ?>
		    </div>
		    <style>
		    .error{ background-color: #FFEBE8;border-color: #C00;}
		    .error input, .error textarea{ border-color: #C00;}
		    </style>
		    <?php
		}


		public function register_settings(){
			foreach ($this->sections as $s) {
				add_settings_section( $s['id'], $s['title'], array($this,'section_callback') , get_class($this) );
				register_setting( $s['option_group'], $this->option, array($this,'sanitize_callback') );
				
			}
			foreach ($this->fields as $f) {
				add_settings_field( $f['id'], $f['label'], array($this,'show_field'), get_class($this), $f['section'], $f ); 
			}
		}

		
	}//end class

	$p = new lyteload_panel(
		array(
			'title'      => __('LyteLoad Settings'),
			'name'       => __('LyteLoad Settings'),
			'capability' => 'edit_plugins',
			'option'     => 'simple_lyteload'
		)
	);
	
	//main plugin fields
	include_once(dirname(__FILE__).'/../config/main_plugin_fields.php');
	
	
	$p->add_help_tab(array(
		'id'      => 'lyteload',
		'title'   => 'LyteLoad',
		'content' => '<div style="min-height: 350px">
                <h2 style="text-align: center;">'.__('Simple LyteLoad').'</h2>
                <div>
                		<p>'.__('If you have any questions or problems head over to').' <a href="http://wordpress.org/support/plugin/simple_lyteload">' . __('Plugin Support') . '</a></p>
                        <p>' .__('If you like my wrok then please ') .'<a class="button button-primary" href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=K4MMGF5X3TM5L" target="_blank">' . __('Donate') . '</a>
                </div>
        </div>
        '
        )
	);
	$GLOBALS['wp_lazyload_panel'] = $p;
}//end if