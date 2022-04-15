<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', ['as' => 'index', 'uses' => 'JackpotController@index']);
Route::get('/jackpot/history', ['as' => 'jackpot.history', 'uses' => 'JackpotController@history']);
Route::post('/jackpot/init', 'JackpotController@initRoom');
Route::post('/jackpot/initHistory', 'JackpotController@initHistory');
Route::get('/wheel', ['as' => 'wheel', 'uses' => 'WheelController@index']);
Route::get('/bets/{id}', 'JackpotController@parseJackpotGame');
Route::get('/crash', ['as' => 'crash', 'uses' => 'CrashController@index']);
Route::get('/getFloat', ['as' => 'crash', 'uses' => 'CrashController@getFloat']);
Route::get('/coinflip', ['as' => 'coinflip', 'uses' => 'CoinFlipController@index']);
Route::get('/battle', ['as' => 'battle', 'uses' => 'BattleController@index']);
Route::get('/dice', ['as' => 'dice', 'uses' => 'DiceController@index']);
Route::get('/bomb', ['as' => 'bomb', 'uses' => 'BombController@index']);
Route::get('/faq', ['as' => 'faq', 'uses' => 'PagesController@faq']);
Route::post('/getUser', 'PagesController@getUser');
Route::post('/fair/check', 'PagesController@fairCheck');
Route::any('/result/cp', ['as' => 'result.cp', 'uses' => 'PagesController@result_cp']);
Route::any('/result/pm', ['as' => 'result.pm', 'uses' => 'PagesController@result_pm']);
Route::any('/success', ['as' => 'success', 'uses' => 'PagesController@success']);
Route::any('/fail', ['as' => 'fail', 'uses' => 'PagesController@fail']);

Route::group(['prefix' => '/auth'], function () {
    Route::get('/{provider}', ['as' => 'auth', 'uses' => 'AuthController@login']);
    Route::get('/callback/{provider}', 'AuthController@callback');
});

Auth::routes();

Route::group(['middleware' => 'auth'], function () {
	Route::get('/profile/history', ['as' => 'profile.history', 'uses' => 'PagesController@profileHistory']);
	Route::get('/affiliate', ['as' => 'affiliate', 'uses' => 'PagesController@affiliate']);
	Route::post('/affiliate/get', 'PagesController@affiliateGet');
	Route::get('/free', ['as' => 'free', 'uses' => 'PagesController@free']);
	Route::post('/free/getWheel', 'PagesController@freeGetWheel');
	Route::post('/free/spin', 'PagesController@freeSpin');
	Route::post('/promo/activate', 'PagesController@promoActivate');
    Route::get('/logout', ['as' => 'logout', 'uses' => 'AuthController@logout']);
	Route::post('/chat', 'ChatController@add_message');
	Route::post('/unbanMe', 'PagesController@unbanMe');
	Route::post('/exchange', 'PagesController@exchange');
	Route::post('/pay', 'PagesController@pay');
	Route::post('/withdraw', 'PagesController@userWithdraw');
	Route::post('/withdraw/cancel', 'PagesController@userWithdrawCancel');
	
	Route::post('/jackpot/newBet', 'JackpotController@newBet');
	Route::post('/wheel/newBet', 'WheelController@newBet');
	Route::post('/battle/newBet', 'BattleController@newBet');
	Route::post('/dice/play', 'DiceController@play');
	Route::group(['prefix' => 'bomb'], function() {
		Route::post('/newBet', 'BombController@createRoom');
		Route::post('/joinGame', 'BombController@joinGame');
	});
	Route::group(['prefix' => 'coinflip'], function() {
		Route::post('/newBet', 'CoinFlipController@createRoom');
		Route::post('/joinGame', 'CoinFlipController@joinGame');
	});
	Route::group(['prefix' => 'crash'], function() {
		Route::post('/newBet', 'CrashController@newBet');
		Route::post('/cashout', 'CrashController@Cashout');
	});
});

Route::group(['prefix' => '/admin', 'middleware' => 'auth', 'middleware' => 'access:admin'], function () {
	Route::get('/', ['as' => 'admin.index', 'uses' => 'AdminController@index']);
	Route::get('/users', ['as' => 'admin.users', 'uses' => 'AdminController@users']);
	Route::get('/user/{id}', ['as' => 'admin.user', 'uses' => 'AdminController@user']);
	Route::get('/settings', ['as' => 'admin.settings', 'uses' => 'AdminController@settings']);
	Route::get('/bots', ['as' => 'admin.bots', 'uses' => 'AdminController@bots']);
	Route::get('/bots/delete/{id}', 'AdminController@botsDelete');
	Route::get('/bonus', ['as' => 'admin.bonus', 'uses' => 'AdminController@bonus']);
	Route::get('/bonus/delete/{id}', 'AdminController@bonusDelete');
	Route::get('/promo', ['as' => 'admin.promo', 'uses' => 'AdminController@promo']);
	Route::get('/promo/delete/{id}', 'AdminController@promoDelete');
	Route::get('/filter', ['as' => 'admin.filter', 'uses' => 'AdminController@filter']);
	Route::get('/filter/delete/{id}', 'AdminController@filterDelete');
	Route::get('/withdraws', ['as' => 'admin.withdraws', 'uses' => 'AdminController@withdraws']);
    Route::get('/withdraw/{id}', 'AdminController@withdrawSend');
    Route::get('/return/{id}', 'AdminController@withdrawReturn');
	
	Route::post('/setting/save', 'AdminController@settingsSave');
	Route::post('/ban', 'ChatController@ban');
	Route::post('/unban', 'ChatController@unban');
	Route::post('/clear', 'ChatController@clear');
	Route::post('/chatdel', 'ChatController@delete_message');
    Route::post('/user/save', 'AdminController@userSave');
	Route::post('/usersAjax', 'AdminController@usersAjax');
	Route::post('/getVKUser', 'AdminController@getVKUser');
	Route::post('/fakeSave', 'AdminController@fakeSave');
	Route::post('/promo/new', 'AdminController@promoNew');
	Route::post('/promo/save', 'AdminController@promoSave');
	Route::post('/filter/new', 'AdminController@filterNew');
	Route::post('/filter/save', 'AdminController@filterSave');
	Route::post('/bonus/new', 'AdminController@bonusNew');
	Route::post('/bonus/save', 'AdminController@bonusSave');
	Route::post('/getBanned', 'AdminController@getBanned');
	Route::post('/socket/start', 'AdminController@socketStart');
	Route::post('/socket/stop', 'AdminController@socketStop');
	Route::post('/getUserByMonth', 'AdminController@getUserByMonth');
	Route::post('/getDepsByMonth', 'AdminController@getDepsByMonth');
	Route::post('/chatSend', 'AdminController@add_message');
	Route::post('/gotJackpot', 'JackpotController@gotThis');
	Route::post('/betJackpot', 'JackpotController@adminBet');
	Route::post('/gotWheel', 'WheelController@gotThis');
	Route::post('/betWheel', 'WheelController@adminBet');
	Route::post('/gotCrash', 'CrashController@gotThis');
	Route::post('/betDice', 'DiceController@adminBet');
	Route::post('/gotBattle', 'BattleController@gotThis');
	Route::post('/betBattle', 'BattleController@adminBet');
});

Route::group(['prefix' => '/moder', 'middleware' => 'auth', 'middleware' => 'access:moder'], function () {
	Route::post('/getBanned', 'AdminController@getBanned');
	Route::post('/ban', 'ChatController@ban');
	Route::post('/unban', 'ChatController@unban');
	Route::post('/clear', 'ChatController@clear');
	Route::post('/chatdel', 'ChatController@delete_message');
});

Route::group(['prefix' => '/api', 'middleware' => 'secretKey'], function() {
	Route::group(['prefix' => '/jackpot'], function() {
		Route::post('/slider', 'JackpotController@getSlider');
		Route::post('/newGame', 'JackpotController@newGame');
		Route::post('/getGame', 'JackpotController@getGame');
		Route::post('/getRooms', 'JackpotController@getRooms');
		Route::post('/addBetFake', 'JackpotController@addBetFake');
	});
	Route::group(['prefix' => '/wheel'], function() {
		Route::post('/newGame', 'WheelController@newGame');
		Route::post('/slider', 'WheelController@getSlider');
		Route::post('/updateStatus', 'WheelController@updateStatus');
		Route::post('/getGame', 'WheelController@getGame');
		Route::post('/addBetFake', 'WheelController@addBetFake');
	});
	Route::group(['prefix' => 'dice'], function() {
		Route::post('/addBetFake', 'DiceController@addBetFake');
    });
	Route::group(['prefix' => 'crash'], function() {
        Route::post('/init', 'CrashController@init');
		Route::post('/slider', 'CrashController@startSlider');
		Route::post('/newGame', 'CrashController@newGame');
    });
	Route::group(['prefix' => 'battle'], function() {
		Route::post('/newGame', 'BattleController@newGame');
		Route::post('/getSlider', 'BattleController@getSlider');
		Route::post('/getStatus', 'BattleController@getStatus');
		Route::post('/setStatus', 'BattleController@setStatus');
		Route::post('/addBetFake', 'BattleController@addBetFake');
    });
	Route::post('/unBan', 'ChatController@unBanFromUser');
	Route::post('/getCurrency', 'AdminController@getCurrency');
});
