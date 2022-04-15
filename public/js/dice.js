$(document).ready(function() {
	var timerId;
	var socket = io.connect(':7777');
	
	$('.btn-play').click(function(e) {
		if($('.btn-play').attr('disabled') == 'true') return;
		e.preventDefault();
		clearTimeout(timerId);
		$('.btn-play').html('Roll the dice...');
		$('.btn-play').prop('disabled', true);
		$.ajax({
			url: "/dice/play",
			type: 'post',
			data: {
				balance: localStorage.getItem('balance') || 'balance',
				sum: $('#sum').val(),
				perc: $('#r1').val()
			},
			success: function(data) {
				if(data.status == 'success') {
					$('.dice-roll .dice__cube').addClass('visible');
					$('.dice-roll').css({
						transform: 'translate('+ data.chislo +'%, 0px)'
					});
					$('.game-dice .result').addClass('visible');
					var currentNumber = $('.game-dice .result').text();
					$({numberValue: currentNumber}).animate({numberValue: data.chislo}, {
						duration: 300,
						easing: 'linear',
						step: function (now) {
							$('.game-dice .result').text(now.toFixed(2)); 
						}
					});
					$('.dice-roll .dice__cube').removeClass('positive');
						$('.game-dice .result').removeClass('positive');
					$('.dice-roll .dice__cube').removeClass('negative');
					$('.game-dice .result').removeClass('negative');
					setTimeout(function() {
						if(data.win == 1) {
							$('.dice-roll .dice__cube').addClass('positive')
							$('.game-dice .result').addClass('positive')
						} else {
							$('.dice-roll .dice__cube').addClass('negative');
							$('.game-dice .result').addClass('negative');
						}
					}, 200);
					timerId = setTimeout(function() {
						$('.dice-roll .dice__cube').removeClass('visible')
					}, 4000)
					$('.hash .text').text(data.hash);
				} else {
					$.notify({
						type: data.type,
						message: data.msg
					});
				}
				$('.btn-play').html('Make bet');
				$('.btn-play').prop('disabled', false)
			}
		})
	});
	
	socket.on('dice', function (data) {
		if(data.win == 0) {
			var status = 'lose';
			var win_sum = data.win_sum;
		} else {
			var status = 'win';
			var win_sum = '+'+data.win_sum;
		}
		
		var html = '';
		html += '<tr><td class="username"><button type="button" class="btn btn-link" data-id="'+ data.unique_id +'"><span class="sanitize-user"><div class="sanitize-avatar"><img src="'+ data.avatar +'" alt=""></div><span class="sanitize-name">'+ data.username +'</span></span></button></td><td><div class="bet-number"><span class="bet-wrap"><span>'+ parseFloat(data.sum).toFixed(2) +'</span><svg class="icon icon-coin '+ data.balType +'"><use xlink:href="/img/symbols.svg#icon-coin"></use></svg></span></div></td><td>'+ data.num +'</td><td>х'+ parseFloat(data.vip).toFixed(2) +'</td><td>'+ parseFloat(data.perc).toFixed(2) +'%</td><td><div class="bet-number"><span class="bet-wrap"><span class="'+ status +'">'+ parseFloat(win_sum).toFixed(2) +'</span><svg class="icon icon-coin"><use xlink:href="/img/symbols.svg#icon-coin"></use></svg></span></div></td><td><button class="btn btn-primary checkGame" data-hash="'+ data.hash +'">Check</button></td></tr>';

		$('.table-stats-wrap tbody').prepend(html);
		if($('.table-stats-wrap tbody tr').length >= 20) $('.table-stats-wrap tbody tr:nth-child(21)').remove();
	});
	
	$('.range').on('mousedown mousemove touchstart', function() {
		$('.input-range__slider-container .input-range__label--value').addClass('isActive');
	});
	$('.range').on('mouseup mouseout touchend', function() {
		setTimeout(function() {
			$('.input-range__slider-container .input-range__label--value').removeClass('isActive');
		}, 1000);
	});
	$('#sum, .range').on('change keydown paste input', function() {
		calc();
	});
});
function calc() {
    var val = $('.range').val();
	if(val < 1) val = 1;
	if(val > 95) val = 95;
	$('.range').val(val)
	$('.input-range__slider-container').css({
		position: 'absolute',
		left: val + '%'
    });
	$('.input-range__slider-container .input-range__label-container').text(val);
    $('.range').css({
        'background': '-webkit-linear-gradient(left, #62ca5b 0%, #62ca5b ' + val + '%, #e86376 ' + val + '%, #e86376 100%)'
    });
    var chance = parseFloat($('#r1').val());
    var viplata = 96 / chance;
    $('#chance').val(chance);
    $('#chance_val').text(chance);
    $('#coef').val(viplata.toFixed(2));
    $('#coef_val').text(viplata.toFixed(2));
    var summ = $("#sum").val();
    var win1 = $('#coef').val();
    var summa = summ * win1;
    $("#win").val(summa.toFixed(2));
}