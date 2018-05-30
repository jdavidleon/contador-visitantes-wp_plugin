<?php 


	/**
	* 
	*/
	class VisitorC
	{
		
		static public  $_userip;
		static private  $_visitor = array();

		public static function widget()
		{	
			$widget_register = new visitor_counter_widget();				
		}

		public static function visitors()
		{
			self::$_userip = self::getUserIP();

			if ( self::check_ip_used() ) {	

				self::adjust_visitor_date();

				$date_db = new DateTime(self::$_visitor->fecha);
				$today = new DateTime("now");
				$diff = $date_db->diff($today);

				$sec = $diff->s / 60;
				$hour = $diff->h * 60 * 60;
				$day = $diff->d * 60 * 60 * 24;
				$month = $diff->m * 60 * 60 * 24 * 30;
				$year = $diff->y * 60 * 60 * 24 * 30 * 365;

				$total_minutes = $sec + $hour + $day + $month + $year;

				if ( $total_minutes <= 40 ) {
					return false;
				}

			}else{
				self::register_ip();
			}

			self::add_visitor();

		}

		// Buscar ip en BD
		static public function check_ip_used()
		{	
			Installer::include_upgrade();
			$wpdb = Installer::$wpdb;
			$results = $wpdb->get_results( 
                $wpdb->prepare("SELECT * FROM {$wpdb->prefix}cvdq_ips_visitor WHERE ip=%s", self::$_userip)
            );

            if ( count($results) > 0 ) {
            	self::$_visitor = $results[0];
            	return true;
            }
            return false;
		}

		// Nuevo usuario ip en BD
		static public function register_ip()
		{
			Installer::include_upgrade();
			$wpdb = Installer::$wpdb;
			$set = array(
				'ip' => self::$_userip
			);
			$format = array( '%s' );
			$insert = $wpdb->insert($wpdb->prefix.'cvdq_ips_visitor',$set,$format);
		}


		static public function adjust_visitor_date()
		{
			$today = date("Y-m-d H:i:s");
			
			Installer::include_upgrade();
			$wpdb = Installer::$wpdb;

        	$set = array(
        		'fecha' => $today
        	);
        	$where = array( 'ip' => self::$_userip );
        	$format = array( '%s' );
        	$where_format = array( '%s' );
        	$update = $wpdb->update($wpdb->prefix.'cvdq_ips_visitor',$set,$where,$format,$where_format);  


		}

		static public function add_visitor()
		{
			$today = date("Y-m-d");

			Installer::include_upgrade();
			$wpdb = Installer::$wpdb;
			$results = $wpdb->get_results( 
                $wpdb->prepare("SELECT * FROM {$wpdb->prefix}cvdq_days_counter WHERE fecha=%s", $today)
            );

            if ( count($results)  === 1 ) {
            	$cantidad = $results[0]->cantidad + 1;
            	$set = array(
            		'cantidad' => $cantidad
            	);
            	$where = array( 'fecha' => $results[0]->fecha );
            	$format = array( '%d' );
            	$where_format = array( '%s' );
            	$update = $wpdb->update($wpdb->prefix.'cvdq_days_counter',$set,$where,$format,$where_format);     	
            }elseif( count($results) === 0 ){
            	$set = array(
            		'fecha' => $today
            	);
            	$format = array( '%s' );
            	$insert = $wpdb->insert($wpdb->prefix.'cvdq_days_counter',$set,$format);
            }
            return true;
		}

		static public function getUserIP()
	    {
	        $client  = @$_SERVER['HTTP_CLIENT_IP'];
	        $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
	        $remote  = $_SERVER['REMOTE_ADDR'];

	        if(filter_var($client, FILTER_VALIDATE_IP))
	        {
	            $ip = $client;
	        }
	        elseif(filter_var($forward, FILTER_VALIDATE_IP))
	        {
	            $ip = $forward;
	        }
	        else
	        {
	            $ip = $remote;
	        }

	        return $ip;
	    }



	}

