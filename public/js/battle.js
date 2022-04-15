$(document).ready(function() {
	build(0.5);
	$(document).on('click', '.btn-play', function() {
		$.ajax({
			url: '/battle/newBet',
			type: 'post',
			data: {
				balance: localStorage.getItem('balance') || 'balance',
				type: $('.btnToggle .btn-bet.isActive').data('team'),
				sum: $('#sum').val()
			},
			success: function(data) {
				$.notify({
					type: data.type,
					message: data.msg
				});
			}
		})
	});
	
	var socket = io.connect(':7777');
	
	socket.on('battle.newBet', function(data) {
		var list = [];
		for(var i in data.bets) {
			let bet = data.bets[i];
			list += '<tr><td class="username"><button type="button" class="btn btn-link" data-id="'+ bet.unique_id +'"><span class="sanitize-user"><div class="sanitize-avatar"><img src="'+ bet.avatar +'" alt=""></div><span class="sanitize-name">'+ bet.username +'</span></span></button></td><td><div class="bet-number"><span class="bet-wrap"><span>'+ bet.price +'</span><svg class="icon icon-coin '+ bet.balType +'"><use xlink:href="/img/symbols.svg#icon-coin"></use></svg></span></div></td><td><span class="bet-type bet_'+ bet.color +'">'+ (bet.color == 'red' ? 'Red' : 'Blue') +'</span></td></tr>';
		}
		$('.table-stats-wrap tbody').html(list);
		
		$('#blue_sum').html(data.bank[1].toFixed(2));
		$('#red_sum').html(data.bank[0].toFixed(2));
		$('#red_persent').text(data.chances[0] + '%');
		$('#blue_persent').text(data.chances[1] + '%');
		$('#red_factor').text('x'+data.factor[0]);
		$('#blue_factor').text('x'+data.factor[1]);
		$('#red_tickets').text('1 - ' + data.tickets[0]);
		$('#blue_tickets').text(data.tickets[1] + ' - 1000');
		build(data.chances[1] / 100);
    });
	
	socket.on('battle.timer', function(data) {
        var sec = data.sec,
			min = data.min
		if(sec < 10) sec = '0' + sec;
		if(min < 10) min = '0' + min;
		$('#timer').html(min + ':' + sec);
    });
	
	socket.on('battle.slider', function(data) {
		$('#circle').css('transition', 'transform 4s cubic-bezier(0.15, 0.15, 0, 1)');
		$('#circle').css('transform', 'rotate(' + (3600 + data.ticket * 0.36) + 'deg)');
		setTimeout(function() {
            $('.game_Wheel .history_history .item').addClass('history_isAnimate');
			$('.game_Wheel .history_history').prepend('<div class="item history_item history_'+ data.game.winner_team +' history_isAnimate checkGame" data-hash="'+ data.game.hash +'"></div>');
			$('.game_Wheel .history_history').children().slice(15).remove();
			setTimeout(function() {
				$('.game_Wheel .history_history .item').removeClass('history_isAnimate');
			}, 1000);
		}, 4000);
    });
	
	socket.on('battle.newGame', function(data) {
		$('#gameId').text(data.game.id);
		$('#timer').html(data.time[0] + ':' + data.time[1]);
		$('.table-stats-wrap tbody').html('');
		$('#circle').css('transition', '');
		$('#circle').css('transform', 'rotate(0deg)');
		$('#red_persent').html('50%');
		$('#blue_persent').html('50%');
		$('#red_tickets').html('1 - ' + 500);
		$('#blue_tickets').html(501 + ' - 1000');
		$('#red_factor').html('x2');
		$('#blue_factor').html('x2');
		$('#blue_sum').html('0.00');
		$('#red_sum').html('0.00');
		$('.hash .text').text(data.game.hash);
		build(0.5);
    });
});

function build(blue_cur) {
  var blue = d3.arc()
      .innerRadius(200)
      .outerRadius(180)
      .startAngle(0)
      .endAngle(2 * Math.PI * blue_cur);
  $('#blue').attr('d', blue());
  var red = d3.arc()
      .innerRadius(200)
      .outerRadius(180)
      .startAngle(2 * Math.PI * blue_cur)
      .endAngle(2 * Math.PI);
  $('#red').attr('d', red());
}