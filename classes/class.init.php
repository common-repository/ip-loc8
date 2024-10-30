<?php
/*
* IP-LOC8
*
* CLASS.INIT.PHP
* Plugin initialisation. 
*/

if (!defined('ABSPATH')) die('opa');

// registers pay method, adds settings

class IpLocInit {
	  
	//public static $visitorGeolocation;
	
	/** @var The single instance of the class */
	private static $_instance = null;	
	
	// Don't load more than one instance of the class
	public static function get_instance() {
		
		if ( null == self::$_instance ) {
            self::$_instance = new self;
        }
        return self::$_instance;
        
    }
    
	public function __construct() {

		add_action( 'plugins_loaded', array($this,'initialize'), 1 );

		add_action( 'init',array($this,'startsession') );
		
	}
	
	/*
	* Create a session if not present
	*
	* @void
	*/
	function initialize() {
		 
		// this is the global variable where all location info will be loaded - for other plugins to use
		global $visitorGeolocation; 

		// check if cookie is present 
		if(isset($_COOKIE['iploc8'])){
			$visitorGeolocation = unserialize(base64_decode($_COOKIE['iploc8']));
		// no cookie = first visit or cookies disabled...
		} else {
			// check if SESSION exists - if not, user just arrived
			if(!session_id()) { 
		        session_start();
		    }
		    if (empty($_SESSION['iploc8'])) {
				// first visit -> get location
				$iploc = new IpLocLocator;
				// get location and set cookie
				$visitorGeolocation = $iploc->setData($_SERVER['REMOTE_ADDR']);
				// set Session
				$_SESSION['iploc8'] = serialize($visitorGeolocation);
				// do redirects/languages
				$iploc->welcome();
			// user already been here, cookies probably disabled, load from SESSION
			} else {
				$visitorGeolocation = unserialize($_SESSION['iploc8']);
			}
		}
				
	}
	
	
	/*
	* Create a session if not present
	*
	* @void
	*/
	function startsession() {
	
		if(!session_id()) {
			session_start();
		}
		if (empty($_SESSION['iploc8'])) {
			global $visitorGeolocation;
		    session_start();
	    	$_SESSION['iploc8'] = serialize($visitorGeolocation);
	    }
	    
	}
	
}