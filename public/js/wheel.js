$(document).ready(function() {
	$(document).on('click', '.btn-play', function() {
		$.ajax({
			url: '/wheel/newBet',
			type: 'post',
			data: {
				balance: localStorage.getItem('balance') || 'balance',
				color: $('.btnToggle .btn.isActive').data('color'),
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
	
	socket.on('wheel', function(data) {
		if(data.type == 'bets') parseBets(data.bets);
		if(data.type == 'timer') {
			var sec = data.sec,
				min = data.min
			if(sec < 10) sec = '0' + sec;
			if(min < 10) min = '0' + min;
			$('.wheel-game .time .value').html(min + ':' + sec);
		}
        if(data.type == 'slider') {
            $('.wheel-game .time').hide();
            $('.wheel-game .wheel-img').css({
				transition: '-webkit-transform 9000ms cubic-bezier(0.32, 0.64, 0.45, 1)',
                transform: 'rotate('+data.slider.rotate+'deg)'
            });
        }
        if(data.type == 'newGame') {
			$('#gameId').text(data.id);
			$('.game_Wheel .hash .text').text(data.hash);
            $('.game_Wheel .history_history').prepend('<div class="item history_item history_'+data.history.color+' history_isAnimate checkGame" data-hash="'+data.history.hash+'"></div>');
            $('.game_Wheel .history_history .item').addClass('history_isAnimate');
			if($('.game_Wheel .history_history .item').length >= 15) $('.game_Wheel .history_history .item:nth-child(15)').remove();
			$('.table-stats-wrap tbody').html('');
            $('.wheel-game .wheel-img').css({
				transition: 'none',
                transform: 'rotate('+data.slider.rotate+'deg)'
            });
            $('.wheel-game .time .value').text(data.slider.time[0] + ':' + data.slider.time[1]);
			$('.wheel-game .time').show();
			setTimeout(function() {
				$('.game_Wheel .history_history .item').removeClass('history_isAnimate');
			}, 1000);
        }
	});
});

function parseBets(bets) {
	var list = [];
	for(var i in bets) {
		let bet = bets[i];
		list += '<tr data-userid="'+bet.user_id+'"><td class="username"><button type="button" class="btn btn-link" data-id="'+bet.unique_id+'"><span class="sanitize-user"><div class="sanitize-avatar"><img src="'+bet.avatar+'" alt=""></div><span class="sanitize-name">'+bet.username+'</span></span></button></td><td><div class="bet-number"><span class="bet-wrap"><span>'+bet.sum+'</span><svg class="icon icon-coin '+bet.balance+'"><use xlink:href="/img/symbols.svg#icon-coin"></use></svg></span></div></td><td><span class="bet-type bet_'+bet.color+'">'+ (bet.color == 'black' ? 'x2' : (bet.color == 'red' ? 'x3' : (bet.color == 'green' ? 'x5' : 'x50'))) +'</span></td></tr>';
	}
	$('.table-stats-wrap tbody').html(list);
}