$(document).ready(function () {
	var socket = io.connect(':7777');
	
	socket.on('new.flip', function (data) {
		if(USER_ID == data.unique_id) {
			var you = '';
				you += '<div class="game-coin flip_'+ data.id +'">';
				you += '<div class="top">';
				you += '<div class="left">';
				you += '<div class="players block">';
				you += '<div class="user">';
				you += '<div class="ava user-link" data-id="'+ data.unique_id +'">';
				you += '<img src="'+ data.avatar +'">';
				you += '</div>';
				you += '<div class="info">';
				you += '<div class="name user-link" data-id="'+ data.unique_id +'">'+ data.username +'</div>';
				you += '<p>'+ data.heads_from +' - '+ data.heads_to +' <svg class="icon"><use xlink:href="/img/symbols.svg#icon-ticket"></use></svg></p>';
				you += '</div>';
				you += '</div>';
				you += '</div>';
				you += '</div>';
				you += '<div class="center">';
				you += '<div class="vs">VS</div>';
				you += '<div class="arrow"></div>';
				you += '<div class="fixed-height">';
				you += '<div class="slider">';
				you += '<ul></ul>';
				you += '</div>';
				you += '</div>';
				you += '</div>';
				you += '<div class="right">';
				you += '<div class="players block">';
				you += '<div class="user">';
				you += '<div class="ava">';
				you += '<img src="/img/no_avatar.jpg">';
				you += '</div>';
				you += '<div class="info">';
				you += '<div class="name">Expect opponent</div>';
				you += '<p>0 - 0 <svg class="icon"><use xlink:href="/img/symbols.svg#icon-ticket"></use></svg></p>';
				you += '</div>';
				you += '</div>';
				you += '</div>';
				you += '</div>';
				you += '</div>';
				you += '<div class="bottom">';
				you += '<div class="info block">';
				you += '<div class="bank">';
				you += '<span class="type">Bank:</span>';
				you += '<span class="val"><span>'+ data.bank +'</span> <svg class="icon icon-coin '+ data.balType +'"><use xlink:href="/img/symbols.svg#icon-coin"></use></svg></span>';
				you += '</div>';
				you += '<div class="ticket">';
				you += '<span class="type">Lucky ticket:</span>';
				you += '<span class="val"><span>???</span> <svg class="icon"><use xlink:href="/img/symbols.svg#icon-ticket"></use></svg></span>';
				you += '</div>';
				you += '</div>';
				you += '<div class="hash">';
				you += '<span class="title">HASH:</span> <span class="text" id="hash">'+ data.hash +'</span>';
				you += '</div>';
				you += '</div>';
				you += '</div>';
		}
		
		var all = '';
			all += '<div class="game-coin flip_'+ data.id +'">';
			all += '<div class="top">';
			all += '<div class="left">';
			all += '<div class="players block">';
			all += '<div class="user">';
			all += '<div class="ava user-link" data-id="'+ data.unique_id +'">';
			all += '<img src="'+ data.avatar +'">';
			all += '</div>';
			all += '<div class="info">';
			all += '<div class="name user-link" data-id="'+ data.unique_id +'">'+ data.username +'</div>';
			all += '<p>'+ data.heads_from +' - '+ data.heads_to +' <svg class="icon"><use xlink:href="/img/symbols.svg#icon-ticket"></use></svg></p>';
			all += '</div>';
			all += '</div>';
			all += '</div>';
			all += '</div>';
			all += '<div class="center">';
			all += '<div class="vs">VS</div>';
			all += '<div class="arrow"></div>';
			all += '<div class="fixed-height">';
			all += '<div class="slider">';
			all += '<ul></ul>';
			all += '</div>';
			all += '</div>';
			all += '</div>';
			all += '<div class="right">';
			all += '<div class="players block">';
			all += '<div class="user">';
			all += '<button type="button" class="btn btn-primary btn-join" data-id="'+ data.id +'"><span>Join</span></button>';
			all += '</div>';
			all += '</div>';
			all += '</div>';
			all += '</div>';
			all += '<div class="bottom">';
			all += '<div class="info block">';
			all += '<div class="bank">';
			all += '<span class="type">Bank:</span>';
			all += '<span class="val"><span>'+ data.bank +'</span> <svg class="icon icon-coin '+ data.balType +'"><use xlink:href="/img/symbols.svg#icon-coin"></use></svg></span>';
			all += '</div>';
			all += '<div class="ticket">';
			all += '<span class="type">Lucky ticket:</span>';
			all += '<span class="val"><span>???</span> <svg class="icon"><use xlink:href="/img/symbols.svg#icon-ticket"></use></svg></span>';
			all += '</div>';
			all += '</div>';
			all += '<div class="hash">';
			all += '<span class="title">HASH:</span> <span class="text" id="hash">'+ data.hash +'</span>';
			all += '</div>';
			all += '</div>';
			all += '</div>';
		
		if(USER_ID == data.unique_id) $('.yours .scroll').append(you);
		$('.actives .scroll').append(all);
	});
	socket.on('end.flip', function (data) {
		var html = '';
			html = '<div class="user"><div class="ava user-link" data-id="'+ data.user2.unique_id +'"><img src="'+ data.user2.avatar +'"></div><div class="info"><div class="name user-link" data-id="'+ data.user2.unique_id +'">'+ data.user2.username +'</div><p>'+ data.user2.from +' - '+ data.user2.to +' <svg class="icon"><use xlink:href="/img/symbols.svg#icon-ticket"></use></svg></p></div></div>';
		
		var stat = '';
			stat = '<tr><td class="username"><button type="button" class="btn btn-link" data-id="'+ data.user1.unique_id +'"><span class="sanitize-user"><div class="sanitize-avatar"><img src="'+ data.user1.avatar +'" alt=""></div><span class="sanitize-name">'+ data.user1.username +'</span></span></button></td><td class="username"><button type="button" class="btn btn-link" data-id="'+ data.user2.unique_id +'"><span class="sanitize-user"><div class="sanitize-avatar"><img src="'+ data.user2.avatar +'" alt=""></div><span class="sanitize-name">'+ data.user2.username +'</span></span></button></td><td class="username"><button type="button" class="btn btn-link" data-id="'+ data.winner.unique_id +'"><span class="sanitize-user"><div class="sanitize-avatar"><img src="'+ data.winner.avatar +'" alt="" style="border: solid 1px #4986f5;"></div><span class="sanitize-name">'+ data.winner.username +'</span></span></button></td><td><div class="bet-number"><span class="bet-wrap"><span>'+ data.game.bank +'</span><svg class="icon icon-coin '+ data.game.balType +'"><use xlink:href="/img/symbols.svg#icon-coin"></use></svg></span></div></td><td><div class="bet-number"><span class="bet-wrap"><span>'+ data.winner.ticket +'</span><svg class="icon"><use xlink:href="/img/symbols.svg#icon-ticket"></use></svg></span></div></td><td><button class="btn btn-primary checkGame" data-hash="'+ data.game.hash +'">Check</button></td></tr>';
		
		var users = [{
				avatar: data.user1.avatar
			},{
				avatar: data.user2.avatar
			}];
		var list = [];
		users.forEach(function(user) {
			var img = user.avatar;
			for (var i = 0; i <= 50; i++) {
				list.push(img);
			}
		});
		function shuffle(o) {
			for (var j, x, i = o.length; i; j = Math.floor(Math.random() * i), x = o[--i], o[i] = o[j], o[j] = x);
			return o;
		}

		list = shuffle(list);
		list.splice(110, list.length - 110);
		if (list.length < 110) {
			var differ = 110 - list.length;
			for (var i = 0; i < differ; i++) {
				list.push(list[0]);
			}
		}
		list[2] = data.winner.avatar;
		var slider = '';
		list.forEach(function (avatar) {
			slider += '<li><img src="' + avatar + '"></li>';
		});
		
		$('.flip_'+ data.game.id + ' .right .players').html(html);
		$('.flip_'+ data.game.id + ' .slider ul').html(slider);
		$('.flip_'+ data.game.id + ' .bank .val span').text(data.game.bank);
		
		setTimeout(function() {
			handleTimer(data.game.id);
		}, 2000);
		
		setTimeout(function() {
			$('.flip_'+ data.game.id + ' .slider ul').css({
				transform: 'translate(0px, 1750px)',
				transition: 7000 + 'ms cubic-bezier(0.32, 0.64, 0.45, 1)'
			});
		}, 8000);
		
		setTimeout(function() {
			$('.flip_'+ data.game.id +' .slider ul li:nth-child(3)').addClass('winner');
			$('.flip_'+ data.game.id +' .ticket .val span').html(data.winner.ticket);
			setTimeout(function() { 
				$('.flip_'+ data.game.id).remove();
				$('.table-stats-wrap tbody').prepend(stat);
				$('.table-stats-wrap tbody tr:nth-child(10)').remove();
			  }, 5000);
		}, 15000);
	});
	
	$(document).on('click', '.btn-play', function() {
		$.ajax({
			url: '/coinflip/newBet',
			type: 'post',
			data: {
				balance: localStorage.getItem('balance') || 'balance',
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
	
	$(document).on('click', '.btn-join', function() {
		$.ajax({
			url: '/coinflip/joinGame',
			type: 'post',
			data: {
				id: $(this).data('id'),
				balance: localStorage.getItem('balance') || 'balance',
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
});

function handleTimer(id) {
    var block = $('.flip_' + id + ' .center .vs'),
        seconds = 5,
        second = 0,
        interval;
	
    interval = setInterval(function() {
		block.addClass('explode');
        block.html((seconds - second));
        if(second >= seconds) {
			block.hide();
            clearInterval(interval);
        }
        second++;
    }, 1000);
}