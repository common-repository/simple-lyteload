<?php
/*
Plugin Name: Simple Lyteload 
Plugin URI: http://en.bainternet.info 
Description: A simple plugin to integrate jQuery lazyLoad  for lazy loading images.  
Version: 0.1.1
Author: Bainternet 
Author Email: admin@bainternet.info 
License:

  Copyright  © Bainternet (admin@bainternet.info)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License, version 2, as 
  published by the Free Software Foundation.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
  
*/
if (!class_exists('wp_lazyload')){
	/**
	 *	wp_lazyload 
	 *
	 * Main plugin class 
	 * @author Ohad Raz <admin@bainternet.info>
	 */
    class wp_lazyload {
		/****************
		 *  Public Vars *
		 ***************/
        /**
         * $dir 
         * 
         * olds plugin directory
         * @since 0.1
         * @access public
         * @var string
         */
		public $dir = '';
		/**
		 * $url 
		 * 
		 * holds assets url
		 * @since 0.1
         * @access public
		 * @var string
		 */
        public $url = '';
        /**
         * $txdomain 
         *
         * holds plugin textDomain
         * @since 0.1
         * @access public
         * @var string
         */
        public $txdomain = 'wp_lazyload';
		/**
		 * $className 
		 *
		 * Holds selector class name
		 * @since 0.1
         * @access public
		 * @var string
		 */
		public $className = 'lyte';
		/**
		 * $options 
		 *
		 * Holds plugin options
		 * @since 0.1
         * @access public
		 * @var array
		 */
		public $options = null;
		/**
		 * $defImg 
		 *
		 * Holds place holder image
		 * @since 0.1
         * @access public
		 * @var string
		 */
		public $defImg = '';

		/****************
		 *    Methods   *
		 ***************/ 

        /**
         * Plugin class Constructor
         *
         * class constructor
         * @since 0.1
         * @access public
         */
        function __construct() {
			$this->setProperties();
        	$this->dir = plugin_dir_path(__FILE__);
        	$this->url = plugins_url('assets/', __FILE__);
			$this->hooks();
        }
        
        /**
         * hooks 
         *
         * function used to add action and filter hooks 
         * Used with `adminHooks` and `clientHokks`
         *
         * hooks for both admin and client sides should be added at the buttom
         * @since 0.1
         * @access public
         * @return void
         */
		public function hooks(){
			if(is_admin())
				$this->adminHooks();
			else
				$this->clientHooks();
			
			/**
			 * hooks for both admin and client sides
			 * hooks should be here
			 */
		}
		
		/**
		 * adminHooks 
		 * 
		 * Admin side hooks should go here
		 * @since 0.1
         * @access public
		 * @return void
		 */
		function adminHooks(){
			//add admin panel
			if (!class_exists('SimplePanel'))
                require_once(plugin_dir_path(__FILE__).'classes/Simple_Panel_Class.php');
			require_once(plugin_dir_path(__FILE__).'classes/lazy_Panel_Class.php');
		}

		/**
		 * clientHooks
		 *
		 * client side hooks should go here
		 * @since 0.1
         * @access public
		 * @return void
		 */
		function clientHooks(){
			$options = $this->getOptions();

			$this->className = $options['cssClass'];
			$this->defImg = $this->url .'images/grey.gif';
			if (isset($options['defImg']['url']) && !empty($options['defImg']['url']))
				$this->defImg = $options['defImg']['url'];

			add_action('wp_head', array($this, 'head_css'));
			add_action('wp_enqueue_scripts', array($this, 'action_enqueue_scripts'));
			
			//the content
			if ($options['the_content'])
				add_filter('the_content', array($this, 'replace_image'),140);
			//post thumbnail
			if ($options['thumbnail'])
				add_filter('post_thumbnail_html', array($this, 'replace_image'),140);
			//avatar
			if ($options['avatar'])
				add_filter('get_avatar', array($this, 'replaceImage'),140);

			//add_filter('get_avatar', array($this, 'dieOu'));
			//wp_get_attachment_link
			add_filter('wp_get_attachment_link', array($this, 'replace_image'),140);
		}

		
		/**
 		 * setProperties 
 		 *
 		 * function to set class Properties
 		 * @since 0.1
         * @access public
 		 * @param array   $args       array of arguments
 		 * @param boolean $properties arguments to set
 		 */
 		public function setProperties($args = array(), $properties = false){
			if (!is_array($properties))
				$properties = array_keys(get_object_vars($this));
 
			foreach ($properties as $key ) {
			  	$this->$key = (isset($args[$key]) ? $args[$key] : $this->$key);
			}
		}

		/**
		 * head_css
		 *
		 * Adds css rule to lyte load
		 * @since 0.1
         * @access public
		 * @return void
		 */
		public function head_css() {
			echo "<style type='text/css'>img.".$this->className." { display: none; }</style>";
		}

		/**
		 * action_enqueue_scripts
		 *
		 * enqueue scripts
		 * @since 0.1
         * @access public
		 * @return void
		 */
		public function action_enqueue_scripts() { 
			$suffix = ( defined('SCRIPT_DEBUG') && SCRIPT_DEBUG )? '' : '.min';
			wp_enqueue_script('jquery_lazy_load',$this->url.'js/jquery.lazyload'.$suffix.'.js',array('jquery'),'1.7.1' );
		}

		/**
		 * getOptions 
		 *
		 * gets the saved options and sets the defaults
		 * @since 0.1
         * @access public
		 * @return array
		 */
		public function getOptions(){
			if($this->options == null){
				$def = array(
					'cssClass'    => 'lyte',
					'avatar'      => false,
					'thumbnail'   => false,
					'the_content' => true,
					'defImg'      => array()
				);
				$tmp = get_option('simple_lyteLoad',array());
				$this->options = array_merge($def,$tmp);
			}
			return $this->options;
		}

		/**
		 * replaceImage 
		 * @since 0.1
         * @access public
		 * @param  string $Image Image image element
		 * @return string
		 */
		function replaceImage($Image){
			add_action('wp_footer', array($this, 'footer_js'));
			if(is_admin()) return $Image;
			
			if (is_array($Image)) $Image = $Image[0];

			$store_old = $Image;
			preg_match_all('/src=\'[^\']*\'|src="[^"]*"/',$Image, $out);
			$tmp = $out[0][0];
			
			//replace src with place holder
			$Image = str_replace($tmp, 'src="' . $this->defImg. '"', $Image);

			//add data-original attribute
			$tmp = str_replace('src' ,'data-original', $tmp);
			$Image = str_replace('<img' , '<img '.$tmp, $Image);

			//add class
			if (false === strpos($Image, 'class="'))
				$Image = str_replace('<img', '<img class="'.$this->className .'"', $Image);
			else
				$Image = str_replace('class="', 'class="'.$this->className . ' ', $Image);

			//add inline style
			if (false === strpos($Image, 'style="'))
				$Image = str_replace('<img', '<img style="display: inline;"', $Image);
			else
				$Image = str_replace('style="', 'style="display: inline; ', $Image);
			
			//return modified img tag and add a noscript fallback
			return $Image.'<noscript>'.$store_old.'</noscript>';
		}



		/**
		 * replace_image 
		 * @since 0.1
         * @access public
		 * @param  string $content 
		 * @return string
		 */
		function replace_image($content) {
			// Don't lazyload for feeds, previews
			if( is_feed() || is_preview()  )
				return $content;
			
			return  preg_replace_callback('/(<\s*img[^>]+)(src\s*=\s*"[^"]+")([^>]+>)/i', array($this, 'replaceImage'), $content);

		}

		/**
		 * footer_js 
		 *
		 * Adds jquery call to lyteload images
		 * @since 0.1
         * @access public
		 * @return void
		 */
		function footer_js() {
			static $done = false;
			if (!$done){
				?> <script type="text/javascript">(function($){ $("img.<?php echo $this->className; ?>").show().lazyload({effect: "fadeIn"}); })(jQuery); </script> <?php
			}
		}
    } // end class
}//end if
$GLOBALS['wp_lazyload'] = new wp_lazyload();