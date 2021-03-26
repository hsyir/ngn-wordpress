<?php

namespace NumElementor ;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Options {

    public function __construct() {
		require plugin_dir_path( __FILE__ ) . 'vendor/autoload.php';
        $this->settings_api = new \WeDevs_Settings_API();
        add_action( 'admin_init', [ $this, 'options' ] );

		add_action( 'admin_menu', [ $this, 'admin_menu' ] );
    }

    public function admin_menu() {
		add_menu_page(
			__( 'Elementor Number', 'ele-number' ),
			__( 'Elementor Number', 'ele-number' ),
			'manage_options',
			'ele-number',
			[ $this, 'panel' ],
			'dashicons-admin-tools',
			90,
		);

		add_submenu_page(
			'ele-number',
			'Requests',
			'Requests',
			'manage_options',
			'ele-number-requests',
			[ $this, 'ele_requests' ],
		);
	}

    public function panel() {
		echo '<div class="wrap" dir="rtl">';
		settings_errors();

		$this->settings_api->show_navigation();
		$this->settings_api->show_forms();

		echo '</div>';
	}

    public function options() {
		$sections = array(
			array(
				'id'    => 'ele-number-server',
				'title' => __( 'جزییات سرور', 'ele-number' ),
			),
            array(
				'id'    => 'ele-number-codes',
				'title' => __( 'شماره ها', 'ele-number' ),
			),
		);

		$fields = array(
			'ele-number-server' => array(
				array(
					'name'  => 'ele-number-server-address',
					'label' => __('Server Address', 'ele-number'),
					'type'  => 'text',
				),
				array(
					'name'  => 'ele-number-server-username',
					'label' => __('Username', 'ele-number'),
					'type'  => 'text',
				),
                array(
					'name'  => 'ele-number-server-password',
					'label' => __('Password', 'ele-number'),
					'type'  => 'text',
				),
			),
            'ele-number-codes' => array(
				array(
					'name'  => 'ele-number-codes-3-count',
					'label' => __('تعداد سر شماره ها', 'ele-number'),
                    'type'  => 'number',
				),
			),
    	);

        $codes = get_option( 'ele-number-codes' );
        if ( is_array( $codes ) ) {
            $count = $codes['ele-number-codes-3-count'];
            for ( $i=1; $i <= $count; $i++ ) {
                array_push( $fields['ele-number-codes'],
                    array(
                        'name'  => 'ele-number-codes-3-' . $i,
                        'label' => __('سر شماره ' . $i, 'ele-number'),
                        'type'  => 'number',
                    )
                );
				array_push( $fields['ele-number-codes'],
                    array(
                        'name'  => 'ele-number-codes-3-' . $i . '-city',
                        'label' => __('عنوان شهر سرشماره ' . $i, 'ele-number'),
                        'type'  => 'text',
                    )
                );
                array_push( $fields['ele-number-codes'],
                    array(
                        'name'  => 'ele-number-codes-3-' . $i . '-values',
                        'label' => __('مقادیر سر شماره ' . $i, 'ele-number'),
                        'type'  => 'textarea',
                    )
                );
            }
        }

		//set sections and fields
		$this->settings_api->set_sections( $sections );
		$this->settings_api->set_fields( $fields );

		//initialize them
		$this->settings_api->admin_init();
	}

	public function ele_requests() {
		$requests = get_option('ele-number-submitted-requests');
		if( is_array( $requests ) ) {
			?>
				<link rel="stylesheet" type="text/css" href="<?php echo plugin_dir_url( __FILE__ ) . 'assets/isolatedbs4.min.css'; ?>" >
				<div class="wrap">
					<div class="bootstrapiso">
						<table class="table table-hover">
							<thead>
								<tr>
									<th>#</th>
									<th> Pre Number</th>
									<th> Mid Number </th>
									<th> Last Number </th>
									<th> User Phone </th>
									<th> User Details </th>
								</tr>
							</thead>
							<tbody>
								<?php
									$i=1;
									foreach( $requests as $key => $details) {
										?>
											<tr>
												<td><?php echo $i; ?></td>
												<td><?php echo $details['pre-code']; ?></td>
												<td><?php echo $details['mid-code']; ?></td>
												<td><?php echo $details['last-code']; ?></td>
												<td><?php echo $details['user-phone']; ?></td>
												<td><?php echo $details['user-details']; ?></td>
											</tr>
										<?php
										$i++;
									}
								?>
							</tbody>
						</table>
					</div>
				</div>
			<?php
		} else {
			echo '<h1> No Request Yet</h1>';
		}
	}
}

new Options();