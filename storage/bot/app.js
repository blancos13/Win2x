let fs 				= require('fs'),
	config 			= require('./config.js'),
	app             = require('express')(),
	server,
	getProtocolOptions = () => config.https ? {
		protocol: 'https',
		protocolOptions: {
			key: fs.readFileSync(config.ssl.key),
			cert: fs.readFileSync(config.ssl.cert)
		}
	} : {
		protocol: 'http'
	},
    crash           = require('./crash'),
	options 		= getProtocolOptions(),
	fakeStatus		= 0;

crash.init();

if(options.protocol == 'https') server = require('https').createServer(options.protocolOptions, app); 
else server = require('http').createServer(app);  

let	io              = require('socket.io')(server),
	redis 			= require('redis'),
    redisClient 	= redis.createClient({
		path : '/var/run/redis/redis.sock'
	}),
	requestify 		= require('requestify'),
	acho 			= require('acho'),
	log 			= acho({
		upper: true
	}),
	online 			= 0,
	ipsConnected	= [];

server.listen(config.port);
log.info('Локальный сервер запущен на '+ options.protocol + '://' + config.domain + ':' + config.port);

io.sockets.on('connection', function(socket) {
	var address = socket.handshake.address;
	if(!ipsConnected.hasOwnProperty(address)) {
		ipsConnected[address] = 1;
		online = online + 1;
	}
	updateOnline(online);
    socket.on('disconnect', function() {
		if(ipsConnected.hasOwnProperty(address)) {
			delete ipsConnected[address];
			online = online - 1;
		}
		updateOnline(online);
	});
});

function updateOnline(online) {
	io.sockets.emit('online', online);
}

redisClient.subscribe('message');
redisClient.subscribe('chat.clear');
redisClient.subscribe('new.msg');
redisClient.subscribe('del.msg');
redisClient.subscribe('ban.msg');
redisClient.subscribe('updateBalance');
redisClient.subscribe('updateBalanceAfter');
redisClient.subscribe('updateBonus');
redisClient.subscribe('updateBonusAfter');
redisClient.subscribe('wheel');
redisClient.subscribe('jackpot.timer');
redisClient.subscribe('jackpot.slider');
redisClient.subscribe('jackpot');
redisClient.subscribe('crash');
redisClient.subscribe('new.flip');
redisClient.subscribe('end.flip');
redisClient.subscribe('new.bomb');
redisClient.subscribe('end.bomb');
redisClient.subscribe('battle.newBet');
redisClient.subscribe('battle.startTime');
redisClient.subscribe('dice');
redisClient.subscribe('bonus');
redisClient.on('message', function(channel, message) {
	if(channel == 'chat.clear') io.sockets.emit('clear', JSON.parse(message));
	if(channel == 'new.msg') io.sockets.emit('chat', JSON.parse(message));
	if(channel == 'del.msg') io.sockets.emit('chatdel', JSON.parse(message));
	if(channel == 'ban.msg') io.sockets.emit('ban_message', JSON.parse(message));
	if(channel == 'updateBalanceAfter') {
		message = JSON.parse(message);
		setTimeout(function() {
			io.sockets.emit('updateBalance', message);
		}, message.timer*1000);
	}
	if(channel == 'updateBonusAfter') {
		message = JSON.parse(message);
		setTimeout(function() {
			io.sockets.emit('updateBonus', message);
		}, message.timer*1000);
	}
	if(channel == 'jackpot.timer') {
		message = JSON.parse(message);
		startJackpotTimer(message);
		return;
	}
	if(channel == 'battle.startTime') {
		message = JSON.parse(message);
        startBattleTimer(message.time);
        return;
    }
	if(channel == 'wheel' && JSON.parse(message).type == 'wheel_timer') {
		message = JSON.parse(message);
        startWheelTimer(message.timer[2]);
		return;
    }
	if(typeof message == 'string') return io.sockets.emit(channel, JSON.parse(message));
	io.sockets.emit(channel, message);
});

/* Jackpot */

var currentTimers = [];
function startJackpotTimer(res) {
	if(typeof currentTimers[res.room] == 'undefined') currentTimers[res.room] = 0;
	if(currentTimers[res.room] != 0 && (currentTimers[res.room] - new Date().getTime()) < ((res.time+1)*1000)) return;
	currentTimers[res.room] = new Date().getTime();
	let time = res.time;
	let timer = setInterval(() => {
		if(time <= 0) {
			clearInterval(timer);
			io.sockets.emit('jackpot', {
				type: 'timer',
				room: res.room,
				data: {
					min: Math.floor(time/60),
					sec: time-(Math.floor(time/60)*60)
				}
			});
			currentTimers[res.room] = 0;
			showJackpotSlider(res.room, res.game);
			return;
		}
		time--;
		io.sockets.emit('jackpot', {
			type: 'timer',
			room: res.room,
			data: {
				min: Math.floor(time/60),
				sec: time-(Math.floor(time/60)*60)
			}
		});
	}, 1*1000)
}

function showJackpotSlider(room, game) {
	requestify.post(options.protocol + '://' + config.domain + '/api/jackpot/slider', {
		room: room,
		game: game
	})
    .then(function(res) {
		let timeout = setTimeout(() => {
			clearInterval(timeout);
			newJackpotGame(room);
		}, 12*1000)
    }, function(res) {
		log.error('Function error slider');
    });
}

function newJackpotGame(room) {
	requestify.post(options.protocol + '://' + config.domain + '/api/jackpot/newGame', {
        room : room
    })
    .then(function(res) {
        res = JSON.parse(res.body);
		io.sockets.emit('jackpot', {
			type: 'newGame',
			room: room,
			data: res
		});
    }, function(res) {
		log.error('[ROOM '+room+'] Function error newGame');
    });
}

function getStatusJackpot(room) {
	requestify.post(options.protocol + '://' + config.domain + '/api/jackpot/getGame', {
        room : room
    })
	.then(function(res) {
		res = JSON.parse(res.body);
		if(res.status == 1) startJackpotTimer(res);
		if(res.status == 2) showJackpotSlider(res.room, res.game);
		if(res.status == 3) newJackpotGame(res.room);
	}, function(res) {
		log.error('[ROOM '+room+'] Function error getStatusJackpot');
	});
}

requestify.post(options.protocol + '://' + config.domain + '/api/jackpot/getRooms')
.then(function(res) {
	rooms = JSON.parse(res.body);
	rooms.forEach(function(room) {
		getStatusJackpot(room.name);
	});
}, function(res) {
	log.error('[APP] Function error getRooms');
});

/* Wheel */
function startWheelTimer(time) {
	updateWheelStatus(1);
	let timer = setInterval(() => {
		if(time <= 0) {
			clearInterval(timer);
			io.sockets.emit('wheel', {
				type: 'timer',
				min: Math.floor(time/60),
				sec: time-(Math.floor(time/60)*60)
			});
			showWheelSlider();
			return;
		}
		time--;
		io.sockets.emit('wheel', {
			type: 'timer',
			min: Math.floor(time/60),
			sec: time-(Math.floor(time/60)*60)
		});
	}, 1*1000)
}

function showWheelSlider() {
	updateWheelStatus(2);
	requestify.post(options.protocol + '://' + config.domain + '/api/wheel/slider')
    .then(function(res) {
        res = JSON.parse(res.body);
		updateWheelStatus(3);
		setTimeout(() => {
            newWheelGame();
        }, res.time);
    }, function(res) {
		log.error('Function error wheelSlider');
    });
}

function newWheelGame() {
	requestify.post(options.protocol + '://' + config.domain + '/api/wheel/newGame')
    .then(function(res) {
        res = JSON.parse(res.body);
    }, function(res) {
		log.error('Function error wheelNewGame');
    });
}

function updateWheelStatus(status) {
	requestify.post(options.protocol + '://' + config.domain + '/api/wheel/updateStatus', {
        status : status
    })
    .then(function(res) {
        res = JSON.parse(res.body);
    }, function(res) {
		log.error('Function error wheelNewGame');
    });
}

requestify.post(options.protocol + '://' + config.domain + '/api/wheel/getGame')
.then(function(res) {
	res = JSON.parse(res.body);
	if(res.status == 1) startWheelTimer(res.timer[2]);
	if(res.status == 2) startWheelTimer(res.timer[2]);
	if(res.status == 3) newWheelGame();
}, function(res) {
	log.error('Function error wheelGetGame');
});

/*Battle*/
function startBattleTimer(time) {
	setBattleStatus(1);
	let timer = setInterval(() => {
		if(time <= 0) {
			clearInterval(timer);
			io.sockets.emit('battle.timer', {
				min: Math.floor(time/60),
				sec: time-(Math.floor(time/60)*60)
			});
			setBattleStatus(2);
			showBattleWinners();
			return;
		}
		time--;
		io.sockets.emit('battle.timer', {
			min: Math.floor(time/60),
			sec: time-(Math.floor(time/60)*60)
		});
	}, 1*1000)
}

function showBattleWinners() {
    requestify.post(options.protocol + '://' + config.domain + '/api/battle/getSlider')
    .then(function(res) {
        res = JSON.parse(res.body);
        io.sockets.emit('battle.slider', res);
		setBattleStatus(3);
		ngTimerBattle();
    }, function(res) {
        log.error('[BATTLE] Function error getSlider');
		setTimeout(BattleShowWinners, 1000);
    });
}

function ngTimerBattle() {
	var ngtime = 6;
	var battlengtimer = setInterval(function() {
		ngtime--;
		if(ngtime <= 0) {
			clearInterval(battlengtimer);
			newBattleGame();
		}
	}, 1000);
}

function newBattleGame() {
    requestify.post(options.protocol + '://' + config.domain + '/api/battle/newGame')
    .then(function(res) {
        res = JSON.parse(res.body);
        io.sockets.emit('battle.newGame', res);
    }, function(res) {
        log.error('[BATTLE] Function error newGame');
		setTimeout(newBattleGame, 1000);
    });
}

function setBattleStatus(status) {
    requestify.post(options.protocol + '://' + config.domain + '/api/battle/setStatus', {
		status : status
    })
    .then(function(res) {
        status = JSON.parse(res.body);
    }, function(res) {
        log.error('[BATTLE] Function error setStatus');
		setTimeout(setBattleStatus, 1000);
    });
}

requestify.post(options.protocol + '://' + config.domain + '/api/battle/getStatus')
.then(function(res) {
	res = JSON.parse(res.body);
	if(res.status == 1) startBattleTimer(res.time);
	if(res.status == 2) startBattleTimer(res.time);
	if(res.status == 3) newBattleGame();
}, function(res) {
	log.error('[BATTLE] Function error getStatus');
});


function unBan() {
    requestify.post(options.protocol + '://' + config.domain + '/api/unBan')
    .then(function(res) {
        var data = JSON.parse(res.body);
        setTimeout(unBan, 60000);
    },function(response){
        log.error('Function error [unBan]');
        setTimeout(unBan, 1000);
    });
}

function getCurrency() {
    requestify.post(options.protocol + '://' + config.domain + '/api/getCurrency')
    .then(function(res) {
		if(!res.success) log.error('[getCurrency] Retry');
        setTimeout(getCurrency, 600000);
    },function(response){
        log.error('Function error [getCurrency]');
        setTimeout(getCurrency, 1000);
    });
}

unBan();
getCurrency();