$(document).ready(function () {
	var socket = io.connect(':7777');
	
	socket.on('new.bomb', function (data) {
		if(USER_ID == data.unique_id) {
			var you = '<div class="game-coin flip_'+ data.id +'"><div class="top"><div class="left"><div class="players block"><div class="user"><div class="ava user-link" data-id="'+ data.unique_id +'"><img src="'+ data.avatar +'"></div><div class="info"><div class="name user-link" data-id="'+ data.unique_id +'">'+ data.username +'</div></div></div></div></div><div class="center"><div class="vs"><svg class="icon icon-bomb"><use xlink:href="/img/symbols.svg#icon-bomb"></use></svg></div><div class="arrow"></div><div class="fixed-height"><div class="slider"><ul></ul></div></div></div><div class="right"><div class="players block"><div class="user"><div class="ava"><img src="/img/no_avatar.jpg"></div><div class="info"><div class="name">Expect opponent</div></div></div></div></div></div><div class="bottom"><div class="info block"><div class="bank"><span class="type">Bank:</span></div><span class="val"><span>'+ data.bank +'</span> <svg class="icon icon-coin '+ data.balType +'"><use xlink:href="/img/symbols.svg#icon-coin"></use></svg></span></div></div></div>';
		}
		
		var all = '<div class="game-coin flip_'+ data.id +'"><div class="top"><div class="left"><div class="players block"><div class="user"><div class="ava user-link" data-id="'+ data.unique_id +'"><img src="'+ data.avatar +'"></div><div class="info"><div class="name user-link" data-id="'+ data.unique_id +'">'+ data.username +'</div></div></div></div></div><div class="center"><div class="vs"><svg class="icon icon-bomb"><use xlink:href="/img/symbols.svg#icon-bomb"></use></svg></div><div class="arrow"></div><div class="fixed-height"><div class="slider"><ul></ul></div></div></div><div class="right"><div class="players block"><div class="user"><div class="buttons"><button type="button" class="btn btn-red btn-join" data-id="'+ data.id +'" data-color="red"><span>Red</span></button><button type="button" class="btn btn-primary btn-join" data-id="'+ data.id +'" data-color="blue"><span>Blue</span></button></div></div></div></div></div><div class="bottom"><div class="info block"><div class="bank"><span class="type">Bank:</span></div><span class="val"><span>'+ data.bank +'</span> <svg class="icon icon-coin '+ data.balType +'"><use xlink:href="/img/symbols.svg#icon-coin"></use></svg></span></div></div></div>';
		
		if(USER_ID == data.unique_id) $('.yours .scroll').append(you);
		$('.actives .scroll').append(all);
	});
	socket.on('end.bomb', function (data) {
		var html = '<div class="user"><div class="ava user-link" data-id="'+ data.user2.unique_id +'"><img src="'+ data.user2.avatar +'"></div><div class="info"><div class="name user-link" data-id="'+ data.user2.unique_id +'">'+ data.user2.username +'</div></div></div>';
		
		var stat = '<tr><td class="username"><button type="button" class="btn btn-link" data-id="'+ data.user1.unique_id +'"><span class="sanitize-user"><div class="sanitize-avatar"><img src="'+ data.user1.avatar +'" alt=""></div><span class="sanitize-name">'+ data.user1.username +'</span></span></button></td><td class="username"><button type="button" class="btn btn-link" data-id="'+ data.user2.unique_id +'"><span class="sanitize-user"><div class="sanitize-avatar"><img src="'+ data.user2.avatar +'" alt=""></div><span class="sanitize-name">'+ data.user2.username +'</span></span></button></td><td class="username"><button type="button" class="btn btn-link" data-id="'+ data.winner.unique_id +'"><span class="sanitize-user"><div class="sanitize-avatar"><img src="'+ data.winner.avatar +'" alt="" style="border: solid 1px #4986f5;"></div><span class="sanitize-name">'+ data.winner.username +'</span></span></button></td><td><div class="bet-number"><span class="bet-wrap"><span>'+ parseFloat(data.game.winner_sum).toFixed(2) +'</span><svg class="icon icon-coin '+ data.game.balType +'"><use xlink:href="/img/symbols.svg#icon-coin"></use></svg></span></div></td></tr>';
		
		var users = [{
				img: '<svg class="icon icon-boom"><use xlink:href="/img/symbols.svg#icon-boom"></use></svg>'
			},{
				img: '<svg class="icon icon-defuse"><use xlink:href="/img/symbols.svg#icon-defuse"></use></svg>'
			}];
		var list = [];
		users.forEach(function(user) {
			var img = user.img;
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
		var winType;
		if(data.winner.type) winType = '<svg class="icon icon-defuse"><use xlink:href="/img/symbols.svg#icon-defuse"></use></svg>';
		else winType = '<svg class="icon icon-boom"><use xlink:href="/img/symbols.svg#icon-boom"></use></svg>';
		list[2] = winType;
		var slider = '';
		list.forEach(function (img) {
			slider += '<li>' + img + '</li>';
		});
		
		$('.flip_'+ data.game.id + ' .right .players').html(html);
		$('.flip_'+ data.game.id + ' .slider ul').html(slider);
		$('.flip_'+ data.game.id + ' .bank .val span').text(data.game.bank);
		
		setTimeout(function() {
			handleTimer(data.game.id);
		}, 2000);
		
		setTimeout(function() {
			$('.flip_'+ data.game.id + ' .slider ul').css({
				transform: 'translate(0px, 1803px)',
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
			url: '/bomb/newBet',
			type: 'post',
			data: {
				balance: localStorage.getItem('balance') || 'balance',
				color: $('.btnToggle .btn-bet.isActive').data('color'),
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
			url: '/bomb/joinGame',
			type: 'post',
			data: {
				id: $(this).data('id'),
				color: $(this).data('color'),
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
        if(second >= seconds) {
			block.hide();
            clearInterval(interval);
        }
        second++;
    }, 1000);
}