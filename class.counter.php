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
			'today' => self::$visitors_today,
			'yesterday' => self::$visitors_yesterday,
			'week' => self::$visitors_week,
			'month' => self::$visitors_month,
			'last_mont' => self::$visitors_lastmonth,
			'total' => self::$visitors_total,
		);
	}

	static public function views( $view = '', $title, $args = [] )
	{		
		if ( $view === 'widget' ) {
			
			$w = '';
			$w .= '<div class="card text-center border-success mx-auto" style="border-width: 3px;">';
			$w .= '<div class="card-header text-white bg-success p-0 mb-1">';
			$w .= '<small><strong class="text-uppercase">'.$title.'</strong></small>';
			$w .= '</div>';
			$w .= '<div class="card-body text-success p-4">';
			$w .= '<h4 class="card-title bg-success text-white">';
			$w .= number_format($args->total,0,'.',',');
			$w .= '</h4>';
			$w .= '<table class="table">';
			$w .= '<tbody>';
			$w .= '<tr class="">';
			$w .= '<th scope="row"  class="p-0 border-0 text-left">Hoy</th>';    
			$w .= '<td class="p-0 border-0 text-right">'.$args->today.'</td>';    
			$w .= '</tr>';    
			$w .= '<tr class="">';    
			$w .= '<th scope="row"  class="p-0 border-0 text-left">Ayer</th>';    
			$w .= '<td class="p-0 border-0 text-right">'.$args->yesterday.'</td>';    
			$w .= '</tr>';    
			$w .= '<tr class="">';    
			$w .= '<th scope="row"  class="p-0 border-0 text-left">Esta Semana</th>';    
			$w .= '<td class="p-0 border-0 text-right">'.$args->week.'</td>';    
			$w .= '</tr>';    
			$w .= '<tr class="">';    
			$w .= '<th scope="row"  class="p-0 border-0 text-left">Este mes</th>';    
			$w .= '<td class="p-0 border-0 text-right">'.$args->month.'</td>';    
			$w .= '</tr>';        
			$w .= '<tr class="">';        
			$w .= '<th scope="row"  class="p-0 border-0 text-left">Mes pasado</th>';        
			$w .= '<td class="p-0 border-0 text-right">'.$args->last_mont.'</td>';        
			$w .= '</tr>';        
			$w .= '<tr class="">';        
			$w .= '<th scope="row"  class="p-0 border-0 text-left">Total</th>';        
			$w .= '<td class="p-0 border-0 text-right">'.$args->total.'</td>';        
			$w .= '</tr>';        
			$w .= '</tbody>';        
			$w .= '</table>';        
			$w .= '</div>';        
			$w .= '<div class="card-footer text-white p-0 border-0" style="background-color: rgb(89, 175, 108);">';
			$w .= 'Tu IP: ';        
			$w .= VisitorC::getUserIP();
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