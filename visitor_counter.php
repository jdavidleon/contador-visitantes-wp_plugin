<?php
/*
Plugin Name:  Contador de Visitas
Description:  	Contador de Visitas detallado, Después de activo se debe buscar Apariencia->widgets y usarlo en las 				zonas de widgets disponibles, no requiere configuración. Software libre, úsalo como quieras.
Version:      1.1	
Author:       JD León
License:      GPL2
License URI:  https://www.gnu.org/licenses/gpl-2.0.html
Domain Path:  /languages


Contador de Visitas Plugin is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.

Contador de Visitas Plugin is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
 
You should have received a copy of the GNU General Public License
along with Contador de Visitas Plugin. 
*/
	
	/*=============================================*/
	// config datetime
		date_default_timezone_set('America/Bogota');
	/*=============================================*/
	
	define( 'COUNTER__PLUGIN_DIR', plugin_dir_path( __FILE__ ) );


	register_activation_hook( __FILE__, array('Installer','visitor_counter_install') );
	register_deactivation_hook( __FILE__, array('Installer','visitor_counter_deactivation') );
	register_uninstall_hook( __FILE__, array('Installer','visitor_counter_uninstall') );

	require_once( COUNTER__PLUGIN_DIR . 'class.visitorc.php' );
	require_once( COUNTER__PLUGIN_DIR . 'class.counter.php' );
	require_once( COUNTER__PLUGIN_DIR . 'class.visitor_counter_widget.php' );
	require_once( COUNTER__PLUGIN_DIR . 'class.installer.php' );


	if ( !is_admin() ) {
		
		add_action( 'get_footer', array( 'Visitorc', 'visitors' ) );

	}
