<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class num_elementor extends \Elementor\Widget_Base {

	public function get_name() {
		return 'num-elementor';
	}

	public function get_title() {
		return __( 'Number Elementor', 'num-elementor' );
	}

	public function get_icon() {
		return 'fa fa-code';
	}

	public function get_categories() {
		return [ 'general' ];
	}

	protected function _register_controls() {
		$this->start_controls_section(
			'content_section',
			[
				'label' => __( 'Settings', 'num-elementor' ),
				'tab' => 'content',
			]
		);

		$this->add_control(
			'chosen_number_placeholder',
			[
				'label'   => __( 'Chosen number placeholder', 'num-elementor' ),
				'type'    => 'text',
				'default' => __( ' چهار رفم انتخابی ', 'num-elementor' ),
			]
		);

		$this->add_control(
			'your_number_placeholder',
			[
				'label'   => __( 'Your number placeholder', 'num-elementor' ),
				'type'    => 'text',
				'default' => __( 'شماره تماس شما ', 'num-elementor' ),
			]
		);

		$this->add_control(
			'user_details_placeholder',
			[
				'label'   => __( 'User Details placeholder', 'num-elementor' ),
				'type'    => 'text',
				'default' => __( 'نام و نام خانوادگی ', 'num-elementor' ),
			]
		);

		$this->add_control(
			'final_submit_msg',
			[
				'label'   => __( 'Final Success Message', 'num-elementor' ),
				'type'    => 'text',
				'default' => __( 'درخواست شما با موفقیت برای مدیر سایت فرستاده شد ', 'num-elementor' ),
			]
		);

		$this->add_control(
			'submit_txt',
			[
				'label'   => __( 'Submit button text', 'num-elementor' ),
				'type'    => 'text',
				'default' => __( ' ثبت درخواست ', 'num-elementor' ),
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'style_section',
			[
				'label' => __( 'Settings', 'num-elementor' ),
				'tab' => 'style',
			]
		);

		$this->add_responsive_control(
			'submit_btn_width',
			[
				'label'      => __( 'Submit button Width', 'num-elementor' ),
				'type'       => 'slider',
				'size_units' => [ 'px', '%' ],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 1000,
						'step' => 5,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default'    => [
					'unit' => '%',
					'size' => 100,
				],
				'selectors'  => [
					'{{WRAPPER}} .submit' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			'background',
			[
				'name'     => 'submit_background',
				'label'    => __( 'Submit Button Background', 'num-elementor' ),
				'types'    => [ 'classic', 'gradient', ],
				'selector' => '{{WRAPPER}} .submit',
			]
		);

		$this->add_control(
			'title_color',
			[
				'label' => __( 'Submit Button Color', 'num-elementor' ),
				'type' => 'color',
				'selectors' => [
					'{{WRAPPER}} .submit' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'fields_width',
			[
				'label'      => __( 'Fields Width', 'num-elementor' ),
				'type'       => 'slider',
				'size_units' => [ 'px', '%' ],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 1000,
						'step' => 5,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default'    => [
					'unit' => '%',
					'size' => 100,
				],
				'selectors'  => [
					'{{WRAPPER}} .fields' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		wp_enqueue_style( 'bs4-isolated' );
		wp_enqueue_script( 'bs4popper' );
		wp_enqueue_script( 'bs4js' );
		?>
			<?php $options = $this->get_cities_code(); ?>
			<script>
				mid_codes = '';
				<?php if ( '' !== $this->extract_values( $options ) ) : ?>
					mid_codes = '<?php echo $this->extract_values( $options ); ?>';
				<?php endif; ?>
			</script>
			<div class="wrapper">
				<div class="form">
					<div class="bootstrapiso">
						<div class="container" dir="rtl">
							<div class="ele-number-result text-right"></div>
							<form id="number-ele-form">
								<div class="form-group">
									<select class="form-control h-auto fields" id="num-elementor-cities">
										<?php $this->extract_city_and_codes( $options ); ?>
									</select>
								</div>
								<div class="form-group">
									<select class="form-control h-auto fields" id="num-elementor-values">
									</select>
								</div>
								<div class="form-group">
									<input id="number-ele-last" type="tel" pattern="[0-9]{4}" class="form-control h-auto fields" placeholder="<?php echo $settings['chosen_number_placeholder']; ?>" data-toggle="tooltip" data-placement="top" title="از 0000 تا 9999 . مقادیری حارج از این محدوده پذیرفته نمی شود">
								</div>
								<button type="submit" class="submit num-elementor-submit"><?php echo $settings['submit_txt']; ?></button>
								<input type="hidden" id="admin-ajax" value="<?php echo admin_url( 'admin-ajax.php' ) ?>">
								<input type="hidden" id="ele_number_final_submit_msg" value="<?php echo $settings['final_submit_msg']; ?>">
							</form>
							<div id="mini-second-form" class="d-none">
								<div class="form-group">
									<input id="ele-number-user-phone" class="form-control h-auto fields" placeholder="<?php echo $settings['your_number_placeholder']; ?>">
								</div>
								<div class="form-group">
									<input id="ele-number-user-details" class="form-control h-auto fields" placeholder="<?php echo $settings['user_details_placeholder']; ?>">
								</div>
								<span class="final-buy btn btn-success"> ثبت سفارش </span>
								<span class="final-back btn btn-danger"> بازگشت </span>
							</div>
						</div>
					</div>
				</div>
			</div>

		<?php
	}

	private function get_cities_code() {
		$option = get_option( 'ele-number-codes' );
		$count  = $option['ele-number-codes-3-count'];
		$all = [];
		for ( $i = 1; $i <= $count; $i++ ) {
			$all['city' . $i] = array (
				'code'  => $option['ele-number-codes-3-' . $i],
				'city'  => $option['ele-number-codes-3-' . $i . '-city'],
				'value' => $option['ele-number-codes-3-' . $i . '-values'],
			);
		}

		return $all ;
	}

	private function extract_city_and_codes( $array ) {
		foreach( $array as $city ) {
			if ( ! empty( $city['code'] ) ) :
				echo '<option value="' . $city['code'] . '">' . $city['city'] . ' (' . $city['code'] . ')</option>';
			endif;
		}
	}

	private function extract_values( $array ) {
		$pre = [];
		foreach( $array as $city ) {
			if ( ! empty( $city['code'] ) ) :
				$values =  explode( PHP_EOL, $city['value'] );
				$pre[$city['code']] = $values;
			endif;
		}
		return json_encode( $pre );
	}
}
