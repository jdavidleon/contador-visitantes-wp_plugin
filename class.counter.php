<?php 

/**
* 
*/
class Counter
{	
	// Dates
	static private $_today;
	static private $_yesterday;
	static private $_fd_this_week;
	static private $_ld_this_week;
	static private $_month;
	static private $_fd_last_month;
	static private $_ld_last_month;

	// Visitors
	static public $visitors_today;
	static public $visitors_yesterday;
	static public $visitors_week;
	static public $visitors_month;
	static public $visitors_lastmonth;
	static public $visitors_total;

	function __construct()
	{
		self::$_today = date('Y-m-d');
		self::$_yesterday = date("Y-m-d", strtotime("yesterday"));
		self::$_month = date('Y-m-01');
		self::$_fd_last_month = date("Y-m-d", strtotime("first day of previous month"));
		self::$_ld_last_month = date("Y-m-d", strtotime("last day of previous month"));
		self::$_fd_this_week = date("Y-m-d", strtotime('monday this week'));
		self::$_ld_this_week = date("Y-m-d", strtotime('sunday this week'));

		// Execute count by days
		self::$visitors_today = self::count_in_database( array( 'start' => self::$_today ) );
		self::$visitors_yesterday = self::count_in_database( array( 'start' => self::$_yesterday ) );
		self::$visitors_week = self::count_in_database( array( 'start' => self::$_fd_this_week, 'end' => self::$_ld_this_week ) );
		self::$visitors_month = self::count_in_database( array( 'start' => self::$_month, 'end' => self::$_today ) );
		self::$visitors_lastmonth = self::count_in_database( array( 'start' => self::$_fd_last_month, 'end' => self::$_ld_last_month ) );
		self::$visitors_total = self::count_in_database( array( 'start' => 'all' ) );

	}

	static public function args()
	{
		return (object) array(
			'today' => number_format(self::$visitors_today,0,'.',','),
			'yesterday' => number_format(self::$visitors_yesterday,0,'.',','),
			'week' => number_format(self::$visitors_week,0,'.',','),
			'month' => number_format(self::$visitors_month,0,'.',','),
			'last_mont' => number_format(self::$visitors_lastmonth,0,'.',','),
			'total' => number_format(self::$visitors_total,0,'.',','),
		);
	}

	static public function views( $view = '', $args = [] )
	{		
		if ( $view === 'widget' ) {

			$w = ''; 

			$w .= '<div class="row">';   
			$w .= '<div class="card text-center my-4 mx-auto">';   
			$w .= '<div class="card-header p-2 rounded-top">';   
			$w .= 'Contador de visitas';   
			$w .= '</div>';   
			$w .= '<div class="card-body">';   
			$w .= '<div class="p-2 mb-3 total-container">';   
			$w .= '<h5 class="card-title m-0">';   
			$w .= '<i class="fas fa-users"></i> ';   
			$w .= $args->total;   
			$w .= '</h5>';   
			$w .= '</div>';   
			$w .= '<table class="table m-0">';   
			$w .= '<tbody>';   
			$w .= '<tr>';   
			$w .= '<th class="p-1 text-left" scope="row">';   
			$w .= '<i class="fas fa-user-alt"></i> ';   
			$w .= 'Hoy';   
			$w .= ' </th>';   
			$w .= '<td class="p-1 text-right">'.$args->today.'</td>';   
			$w .= '</tr>';   
			$w .= '<tr>';   
			$w .= '<th class="p-1 text-left" scope="row">';   
			$w .= '<i class="fas fa-user-alt"></i> ';   
			$w .= 'Ayer';   
			$w .= '</th>';   
			$w .= '<td class="p-1 text-right">'.$args->yesterday.'</td>';   
			$w .= '</tr>';  
			$w .= '<tr>';   
			$w .= '<th class="p-1 text-left" scope="row">';   
			$w .= '<i class="fas fa-user-alt"></i> ';   
			$w .= 'Esta semana';   
			$w .= '</th>';   
			$w .= '<td class="p-1 text-right">'.$args->week.'</td>';   
			$w .= '</tr>';  
			$w .= '<tr>';   
			$w .= '<th class="p-1 text-left" scope="row">';   
			$w .= '<i class="fas fa-user-alt"></i> ';   
			$w .= 'Este mes';   
			$w .= '</th>';   
			$w .= '<td class="p-1 text-right">'.$args->month.'</td>';   
			$w .= '</tr>';  
			$w .= '<tr>';   
			$w .= '<th class="p-1 text-left" scope="row">';   
			$w .= '<i class="fas fa-user-alt"></i> ';   
			$w .= 'Mes pasado';   
			$w .= '</th>';   
			$w .= '<td class="p-1 text-right">'.$args->last_mont.'</td>';   
			$w .= '</tr>';  
			$w .= '<tr>';   
			$w .= '<th class="p-1 text-left" scope="row">';   
			$w .= '<i class="fas fa-user-alt"></i> ';   
			$w .= 'Total';   
			$w .= '</th>';   
			$w .= '<td class="p-1 text-right">'.$args->total.'</td>';   
			$w .= '</tr>';   
			$w .= '</tbody>';   
			$w .= '</table>';   
			$w .= '</div>';   
			$w .= '<div class="card-footer p-1">';   
			$w .= '<small class="my-auto">TU IP: '.VisitorC::getUserIP().'</small>';             
			$w .= '</div>';             
			$w .= '</div>';             
			$w .= '</div>';   

			echo $w;

		}
	}


	static private function count_in_database($days = [])
	{		
		/**
		*	$days = array( 'start', 'end' )
		*/

		Installer::include_upgrade();
		$wpdb = Installer::$wpdb;

		$sql = "SELECT SUM(cantidad) as cantidad FROM {$wpdb->prefix}cvdq_days_counter ";
		$data = '';

		if ( $days['start'] === 'all' ) {
			$sql .= ' WHERE fecha <> %s ';
			$data .= 'date';
		}elseif( isset($days['start']) AND $days['start'] !== '' ){
			if ( !isset($days['end']) OR $days['end'] == '' ) {
				$sql .= " WHERE fecha = %s ";
				$data .= $days['start'];
			}
		}else{
			return false;
		}

		if ( isset($days['end']) && $days['end'] !== '' && $days['start'] !== 'all' ) {
			$dates = self::datePeriod_start_end($days['start'],$days['end']);

			$sql .= " WHERE ";

			$data = array();
			foreach ($dates as $date) {
				$sql .= " fecha = %s OR ";
				$data[] = $date;
			}

			$sql = substr($sql, 0, -3);

		}

		$results = $wpdb->get_results( 
            $wpdb->prepare($sql, $data)
        );

		if (  $results[0]->cantidad === null ) {
			return 0;
		}

        return $results[0]->cantidad;

	}

	static private function datePeriod_start_end($begin,$end){

        $begin = new DateTime($begin);

        $end = new DateTime($end.' +1 day');

        $daterange = new DatePeriod($begin, new DateInterval('P1D'), $end);

        foreach($daterange as $date){
            $dates[] = $date->format("Y-m-d");
        }
        return $dates;

    }


}