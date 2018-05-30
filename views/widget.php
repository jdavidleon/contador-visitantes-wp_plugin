<div class="row">
	<table class="table table-sm border-0">
		<thead>
		    <tr>
		      	<th scope="col" colspan="2">
		      		Contador de Visitas
		      	</th>
		    </tr>
		</thead>
		<tbody>
		    <tr>
		     	<th scope="row">Hoy</th>
		      	<td>Mark</td>
		    </tr>
		    <tr>
		      	<th scope="row">Ayer</th>
		      	<td>Jacob</td>
		    <tr>
		      	<th scope="row">Esta Semana</th>
		      	<td>Jacob</td>
		    </tr>
		    <tr>
		      	<th scope="row">Este Mes</th>
		      	<td>Jacob</td>
		    </tr>
		    <tr>
		      	<th scope="row">El mes pasado</th>
		      	<td>Jacob</td>
		    </tr>
		    <tr>
		      	<th scope="row">Total Visitantes</th>
		      	<td>Jacob</td>
		    </tr>
		</tbody>
	</table>
</div>


<?php 

	public function table_visitors($title,$args = [])
	{
		$w = '';
		$w .= '<div class="row">';
		$w .= '<table class="table table-sm border-0">';
		$w .= '<thead>';
		$w .= '<tr>';
		$w .= '<th scope="col" colspan="2">';
		$w .= $title;
		$w .= '</th>';
		$w .= '</tr>';
		$w .= '</thead>';
		$w .= '<tbody>';
		$w .= '<tr>';
		$w .= '<th scope="row">Hoy</th>';
		$w .= '<td>'.$args->today.'</td>';
		$w .= '</tr>';
		$w .= '<tr>';
		$w .= '<th scope="row">Ayer</th>';
		$w .= '<td>'.$args->yesterday.'</td>';
		$w .= '<tr>';
		$w .= '<th scope="row">Esta Semana</th>';
		$w .= '<td>'.$args->week.'</td>';
		$w .= '</tr>';		
		$w .= '<tr>';		
		$w .= '<th scope="row">Este Mes</th>';		
		$w .= '<td>'.$args->month.'</td>';		
		$w .= '</tr>';		
		$w .= '<tr>';		
		$w .= '<th scope="row">El mes pasado</th>';		
		$w .= '<td>'.$args->last_mont.'</td>';		
		$w .= '</tr>';		
		$w .= '<tr>';		
		$w .= '<th scope="row">Total Visitantes</th>';		
		$w .= '<td>'.$args->total.'</td>';		
		$w .= '</tr>';	
		$w .= '</tbody>';	
		$w .= '</table>';	
		$w .= '</div>';	

		echo $w;
	}