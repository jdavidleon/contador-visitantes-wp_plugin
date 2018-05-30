<?php 



	/**
	*  Clase para crear Widget de contador de Visitas en el Sitebar.php
	*/
	class visitor_counter_widget extends WP_widget
	{
		
		public function __construct()
		{
			parent::__construct(
				'counter-visitor',
				'Contador de Visitas',
				array( 'description' => __( 'Muestra en detalle el número de visitantes por dia, mes y total del sitio', '' ), )
			);

		}

		public function widget( $args, $instance ) {
	        extract( $args );
	        $title = apply_filters( 'widget_title', $instance['title'] );
	 
	        echo $before_widget;
	        if ( empty( $title ) ) {
	            $title = '';
	        }

	        /*Content Widget*/
	        $counter = new Counter;
	        Counter::views( 'widget', $title, Counter::args() );


	        echo $after_widget;
	    }

	    public function form( $instance ) {
	        if ( isset( $instance[ 'title' ] ) ) {
	            $title = $instance[ 'title' ];
	        }
	        else {
	            $title = __( 'Contador de Visitas', '' );
	        }
	        ?>
	        <p>
	        <label for="<?php echo $this->get_field_name( 'title' ); ?>">
	        	Titulo: 
	        </label>
	        <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
	        </p>
	        <?php
	    }

	    public function update( $new_instance, $old_instance ) {
	        $instance = array();
	        $instance['title'] = ( !empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
	 
	        return $instance;
	    }

	}

	add_action('widgets_init', function(){
		register_widget( 'visitor_counter_widget' );
	});