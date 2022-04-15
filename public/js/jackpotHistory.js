$(document).ready(function() {
	var room = null;
    
    $('.rooms .btn').click(function(e) {
        room = localStorage.setItem('room', $(this).attr('data-room'));

        $('.rooms .btn').removeClass('isActive');
        $(this).addClass('isActive');

        getCurrentRoom();

        e.preventDefault();
    });

    function getCurrentRoom() {
        room = localStorage.getItem('room') || 'easy';
        $('.rooms .btn.' + room).addClass('isActive');
        JackpotHistoryInit(room);
    }

    getCurrentRoom();
	
	function JackpotHistoryInit(room) {
		$.ajax({
            url: '/jackpot/initHistory',
            type: 'post',
            data: {
                room: room
            },
            success: r => {
                if(r.success) {
					var html = '';
					r.history.forEach(game => {
						html += '<tr><td>'+ game.game_id +'</td><td class="username"><button type="button" class="btn btn-link" data-id="'+ game.winner_id +'"> <span class="sanitize-user"><div class="sanitize-avatar"><img src="'+ game.winner_avatar +'" alt=""></div> <span class="sanitize-name">'+ game.winner_name +'</span> </span> </button></td><td><div class="bet-number"> <span class="bet-wrap"> <span>'+ game.winner_balance +'</span> <svg class="icon icon-coin balance"> <use xlink:href="/img/symbols.svg#icon-coin"></use> </svg> </span></div> / <div class="bet-number"> <span class="bet-wrap"> <span>'+ game.winner_bonus +'</span> <svg class="icon icon-coin bonus"> <use xlink:href="/img/symbols.svg#icon-coin"></use> </svg> </span></div></td><td>'+ game.winner_chance +'%</td><td><div class="bet-number rtl"> <span class="bet-wrap"> <span>'+ game.winner_ticket +'</span> <svg class="icon"> <use xlink:href="/img/symbols.svg#icon-ticket"></use> </svg> </span></div></td><td><button class="btn btn-primary checkGame" data-hash="'+ game.hash +'">Check</button></td></tr>';
					});
					$('#history').html(html);
				}
            },
            error: e => console.log(e.responseText)
        });
	}
});