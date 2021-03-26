jQuery(document).ready(function ($) {
	$('[data-toggle="tooltip"]').tooltip();
	if (typeof mid_codes !== 'undefined') {
		mid_codes = JSON.parse(mid_codes);
		// normal load
		$('#num-elementor-values').empty();
		var j = 0;
		for (key in mid_codes) {
			if (j === 0) {
				var values = mid_codes[key];
				for (var i = 0; i < values.length; i++) {
					$('#num-elementor-values').append('<option value="' + values[i] + '">' + values[i] + '</option>');
				}
			}
			j++;
		}
		// on change
		$('#num-elementor-cities').change(function () {
			$('#num-elementor-values').empty();
			var target = $('#num-elementor-cities').val();
			for (key in mid_codes) {
				if (key === target) {
					var values = mid_codes[key];
					for (var i = 0; i < values.length; i++) {
						$('#num-elementor-values').append('<option value="' + values[i] + '">' + values[i] + '</option>');
					}
				}
			}
		});
	}

	jQuery('#number-ele-form').on('submit', function (e) {
		e.preventDefault();
		const CityCode = $('#num-elementor-cities').val();
		const MidNumber = $('#num-elementor-values').val();
		const LastNumber = $('#number-ele-last').val();
		jQuery.ajax({
			url: $('#admin-ajax').val(),
			type: 'POST',
			beforeSend: function () {
				$('#number-ele-form').css('opacity', '0.5');
			},
			data: {
				action: 'check_ele_number_stat',
				city: CityCode,
				mid: MidNumber,
				last: LastNumber,
			},
		})
			.done(function (data) {
				$('#number-ele-form').css('opacity', '1.0');

				console.log(data);
				if (false === data.success) {
					var errors = data.errors;;
					if (111 === data.code) {
						for (key in errors) {
							$('.ele-number-result').html('<p>' + errors[key][0] + '</p>');
						}
					}
					if (110 === data.code) {
						$('.ele-number-result').html('<p>' + errors[0] + '</p>');
					}
					if (112 === data.code) {
						$('.ele-number-result').html('<p>' + data.errors + '</p>');
					}
				}
				if (true === data.success) {
					if (100 === data.code) {
						if ('gray' === data.result.status || 'green' === data.result.status) {
							$('.ele-number-result').html('<p>  شماره ' + data.result.pre_number + data.result.mid_number + data.result.number + ' با دسته بندی  ' + data.result.category + ' را می توانید همین الان خریداری کنید </p>');
							$('.ele-number-result').append('<span class="elenum-buy btn btn-success mx-2">خرید</span><span class="elenum-back btn btn-info mx-2">بازگشت</span>');
							$('#number-ele-form').addClass('d-none');
							save_num_ele(data);
						} else {
							$('.ele-number-result').html('<p> این شماره خریداری شده و یا رزرو شده است . لطفا یک شماره دیگر انتخاب کنید . </p>');
						}
					}
				}
			});
	});
	function save_num_ele(data) {
		$('.elenum-back').click(function () {
			$('.ele-number-result').empty();
			$('#mini-second-form').addClass('d-none');
			$('#number-ele-form').removeClass('d-none');
		});
		$('.elenum-buy').click(function () {
			$('.ele-number-result').empty();
			$('#mini-second-form').removeClass('d-none');
		})

		$('.final-back').click(function () {
			$('.ele-number-result').empty();
			$('#mini-second-form').addClass('d-none');
			$('#number-ele-form').removeClass('d-none');
		})

		$('.final-buy').click(function () {
			var UserPhone = $('#ele-number-user-phone').val();
			var UserDetails = $('#ele-number-user-details').val();
			if ('' === UserPhone) {
				alert('برای ثبت درخواست باید شماره تماس خود را وارد کنید ');
				return false;
			}
			if ('' === UserDetails) {
				alert('برای ثبت نهایی در خواست نام و نام خانوادگی خود را وارد کنید ');
				return false;
			}
			var CityCode = data.result.pre_number;
			var MidNumber = data.result.mid_number;
			var LastNumber = data.result.number;
			jQuery.ajax({
				url: $('#admin-ajax').val(),
				type: 'POST',
				data: {
					action: 'save_ele_number_stat',
					city: CityCode,
					mid: MidNumber,
					last: LastNumber,
					phone: UserPhone,
					details: UserDetails,
				},
			})
				.done(function (data) {
					var msg = $('#ele_number_final_submit_msg').val();
					$('.final-buy').off();
					$('.ele-number-result').html( '<h4><b>' + msg + '</b></h4>' );
				});
		});


	}
});
