<?php 


	/**
	* 
	*/
	class Installer extends Visitorc
	{	

		public static $wpdb;
		
		public static function visitor_counter_install()
		{
			// Create tables for 
			self::createTables();
			parent::widget();
	    	flush_rewrite_rules();

		}

		public static function visitor_counter_deactivation()
		{
			// clear the permalinks to remove our post type's rules
	    	flush_rewrite_rules();
		}

		public static function visitor_counter_uninstall()
		{
			// Clear visitor Counter Instalation
			self::include_upgrade();
			self::$wpdb->query("DROP TABLE IF EXISTS ".self::$wpdb->prefix."cvdq_days_counter");
			self::$wpdb->query("DROP TABLE IF EXISTS ".self::$wpdb->prefix."cvdq_ips_visitor");

			delete_option('visitor_counter_uninstall');
		}

		private static function createTables()
		{	

			self::include_upgrade();

			/*Days Counter*/
			$nombreTabla1 = self::$wpdb->prefix . "cvdq_days_counter";
			  
			$create_days_table = dbDelta(  
			    "CREATE TABLE $nombreTabla1 (
			      daysID bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			      fecha DATE NOT NULL,
			      cantidad bigint(20) NOT NULL DEFAULT 1,
			      PRIMARY KEY (daysID)
			    ) CHARACTER SET utf8 COLLATE utf8_general_ci;"
			);

			/*Ip's Table*/
			$nombreTabla2 = self::$wpdb->prefix . "cvdq_ips_visitor";
			  
			$create_ips_table = dbDelta(  
			    "CREATE TABLE $nombreTabla2 (
			      ipvisitorID bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			      ip varchar(20) NOT NULL UNIQUE DEFAULT '',
			      fecha DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
			      cantidad bigint(20) NOT NULL DEFAULT 1,
			      PRIMARY KEY (ipvisitorID)
			    ) CHARACTER SET utf8 COLLATE utf8_general_ci;"
			);

		}

		public static function include_upgrade()
		{
			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			global $wpdb;
			self::$wpdb = $wpdb;
		}

	}

