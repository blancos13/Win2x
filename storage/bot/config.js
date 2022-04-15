require('dotenv').config({path: __dirname+'/./../../.env'});

module.exports = {
	domain: process.env.APP_DOMAIN || 'localhost',
    port: process.env.APP_PORT || 7777,
    https: (process.env.APP_HTTPS == 'true') || false,
    ssl: {
        key: process.env.SSL_KEY_PATH || null,
        cert: process.env.SSL_CERT_PATH || ''
    }
};