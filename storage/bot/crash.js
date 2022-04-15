const {promisify} = require('util');
const requestify = require('requestify');
const RedisClient = require('redis').createClient({
    path : '/var/run/redis/redis.sock'
});
const getAsync = promisify(RedisClient.get).bind(RedisClient);
const setAsync = promisify(RedisClient.set).bind(RedisClient);
const config = require('./config');
var fs = require('fs'),
	getProtocolOptions = () => config.https ? {
		protocol: 'https',
		protocolOptions: {
			key: fs.readFileSync(config.ssl.key),
			cert: fs.readFileSync(config.ssl.cert)
		}
	} : {
		protocol: 'http'
	},
	options = getProtocolOptions();

exports.cvars = {timer  : 0}
exports._i = 0;
exports._now = 1;
exports._data = [1];

exports.reload = function() {
    setTimeout(() => {
		return this.init();
	}, 2000);
}

exports.init = async() => {
    let res = await this.request('crash/init');
    if(!res) return this.reload();

    this.cvars.timer = res.timer;

    if(res.status == 0) this.startTimer();
    if(res.status == 1) this.startSlider();
    if(res.status == 2) this.newGame();
}

exports.startTimer = async() => {
    let currentTime = this.cvars.timer*10;
    let timer = setInterval(() => {
        if(currentTime <= 0) {
            clearInterval(timer);
            this.startSlider();
            return;
        }
        currentTime--;
        this.emit({
            type : 'timer',
            value : (currentTime/10).toFixed(1)
        });
    }, 100);
}

exports.startSlider = async() => {
    let res = await this.request('crash/slider');
    if(!res) return this.reload();
    
    if(typeof this.animateInterval != 'undefined') clearInterval(this.animateInterval);

    await setAsync('cashout', 1); // accept cashout

    this.float = parseFloat(res.multiplier); // float
    this._i = 0;
    this._now = 0;
    this._data = [];
    this._label = [];

    this.animateInterval = setInterval(async() => {
        this._i++;
        this._now = parseFloat(Math.pow(Math.E, 0.00006*this._i*1000/20));
        let crashed = false;
        if(this._now > this.float) {
            clearInterval(this.animateInterval);
            this._now = this.float;
            crashed = true;
            await setAsync('cashout', 0)
            if(crashed) setTimeout(() => {
                this.newGame();
            }, 3000);
        }

        this._data.push(this._now);
        this._label.push(this._i);
		
        await setAsync('float', this._now.toFixed(2));

        this.emit({
            type : 'slider',
            data : this._data,
            label : this._label,
            float : parseFloat(this._now.toFixed(2)),
            crashed : crashed,
            color : this.getColors(this._now)
        });
    }, 50)
}

exports.newGame = async() => {
    let res = await this.request('crash/newGame');
    if(!res) return this.reload();

    if(res.success) 
    {
        this.startTimer();
    }
}

exports.getFrame = (float) => {
    return new Promise((res, rej) => {
        let i = 0, now = 0;
        while(now < float)
        {
            i++;
            now = parseFloat(Math.pow(Math.E, 0.00006*i*1000/20));
        }
        return res(i);
    });
}

exports.request = (path, body) => {
    return new Promise(async(response, reject) => {
        requestify.post(options.protocol + '://' + config.domain + '/api/' + path, body || {})
        .then((res) => {
            return response(JSON.parse(res.body));
        }, (err) => {
            console.log('[API] error with request to /api/' + path);
            console.log(err.body);
            return response(false);
        });
    });
}

exports.log = (log) => console.log('[CRASH] ' + log);

exports.emit = (json) => RedisClient.publish('crash', JSON.stringify(json));

exports._colors = {
    one: '#a6caf0',
    two: '#afdafc',
    three: '#ccccff',
    four: '#dcd0ff',
    five: '#eebef1',
    red: '#aa3737',
    text: '#ffc645'
}

exports.getColors = function(n)
{
    if(n > 6.49) return this._colors.five;
    if(n > 4.49) return this._colors.four;
    if(n > 2.99) return this._colors.three;
    if(n > 1.99) return this._colors.two;
    return this._colors.one;
}

this.init();