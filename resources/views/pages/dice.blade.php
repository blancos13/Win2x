@extends('layout')

@section('content')
<link rel="stylesheet" href="/css/dice.css">
<script type="text/javascript" src="/js/dice.js"></script>
<div class="section game-section">
    <div class="container">
        <div class="game">
            <div class="game-sidebar">
                <div class="sidebar-block">
                    <div class="bet-component">
                        <div class="bet-form">
                            <div class="form-row">
                                <label>
                                    <div class="form-label"><span>Bet Amount</span></div>
                                    <div class="form-row">
                                        <div class="form-field">
                                            <input type="text" name="sum" class="input-field no-bottom-radius" value="0.00" id="sum">
                                            <button type="button" class="btn btn-bet-clear" data-action="clear">
												<svg class="icon icon-close">
													<use xlink:href="/img/symbols.svg#icon-close"></use>
												</svg>
                                            </button>
                                            <div class="buttons-group no-top-radius">
                                                <button type="button" class="btn btn-action" data-action="plus" data-value="0.01">+0.01</button>
                                                <button type="button" class="btn btn-action" data-action="plus" data-value="0.10">+0.10</button>
                                                <button type="button" class="btn btn-action" data-action="plus" data-value="0.50">+0.50</button>
                                                <button type="button" class="btn btn-action" data-action="multiply" data-value="2">2X</button>
                                                <button type="button" class="btn btn-action" data-action="divide" data-value="2">1/2</button>
                                                <button type="button" class="btn btn-action" data-action="all">MAX</button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="two-cols">
										<div class="form-row">
											<label>
												<div class="form-label"><span>Rate</span></div>
												<div class="form-field">
													<div class="input-valid">
														<input class="input-field" readonly="" value="1.92" id="coef">
														<div class="input-suffix"><span id="coef_val">1.92</span> x</div>
														<div class="valid"></div>
													</div>
												</div>
											</label>
										</div>
										<div class="form-row">
											<label>
												<div class="form-label"><span>Chance</span></div>
												<div class="form-field">
													<div class="input-valid">
														<input class="input-field" readonly="" value="50.00" id="chance">
														<div class="input-suffix"><span id="chance_val">50.00</span> %</div>
														<div class="valid"></div>
													</div>
												</div>
											</label>
										</div>
									</div>
									<div class="form-row">
										<label>
											<div class="form-label"><span>Win</span></div>
											<div class="form-field">
												<input class="input-field" readonly="" value="0.00" id="win">
											</div>
										</label>
									</div>
                                </label>
                            </div>
                            <button type="button" class="btn btn-green btn-play"><span>Make bet</span></button>
                        </div>
                        <div class="bet-footer">
                            <button type="button" class="btn btn-light" data-toggle="modal" data-target="#fairModal">
                                <svg class="icon icon-fairness">
                                    <use xlink:href="/img/symbols.svg#icon-fairness"></use>
                                </svg><span>Fair game</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
			<div class="game-component">
				<div class="game-block">
					<div class="game-area__wrap">
						<div class="game-area">
							<div class="progress-wrap">
								<div class="progress-item left">
									<div class="title">Min Bet: <span id="minBet">{{$settings->dice_min_bet}}</span> <svg class="icon icon-coin"><use xlink:href="/img/symbols.svg#icon-coin"></use></svg></div>
									<div class="title">Max Bet: <span id="maxBet">{{$settings->dice_max_bet}}</span> <svg class="icon icon-coin"><use xlink:href="/img/symbols.svg#icon-coin"></use></svg></div>
								</div>
							</div>
							<div class="top-corners"></div>
							<div class="bottom-corners"></div>
							<div class="game-area-content">
								<div class="dice">
									<div class="game-dice"><img src="/img/dice-bg.svg" alt=""><span class="result"></span></div>
									<div class="game-bar">
										<div class="dice-roll">
											<div class="dice__cube"></div>
										</div>
										<span class="input-range__slider-container">
											<span class="input-range__label input-range__label--value">
												<span class="input-range__label-container">50.00</span>
											</span>
										</span>
										<div aria-disabled="false" class="input-range">
											<div id="range" class="cntr"></div>
											<input type="range" id="r1" style="background: -webkit-linear-gradient(left, #62ca5b 50%, #62ca5b, #e86376 0%, #e86376 50% 100%);" min="0" value="50" max="100" step="0.01" class="range">
										</div>
										<div class="bar-component">
											<div class="bar-labels">
												<div class="item"><span>0</span></div>
												<div class="item"><span>25</span></div>
												<div class="item"><span>50</span></div>
												<div class="item"><span>75</span></div>
												<div class="item"><span>100</span></div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="game-history__wrap">
					<div class="hash">
						<span class="title">HASH:</span> <span class="text">{{ $hash }}</span>
					</div>
				</div>
				@guest
				<div class="game-sign">
					<div class="game-sign-wrap">
						<div class="game-sign-block auth-buttons">
							To play, you must be authorized
							<button type="button" class="btn" data-toggle="modal" data-target="#signinModal">Log In</button>
						</div>
					</div>
				</div>
				@endguest
			</div>
        </div>
    </div>
</div>
<div class="section bets-section">
	<div class="container">
		<div class="game-stats">
			<div class="table-heading">
				<div class="thead">
					<div class="tr">
						<div class="th">User</div>
						<div class="th">Bet</div>
						<div class="th">Number</div>
						<div class="th">Rate</div>
						<div class="th">Chance</div>
						<div class="th">Win</div>
						<div class="th"></div>
					</div>
				</div>
			</div>
			<div class="table-stats-wrap" style="min-height: 530px; max-height: 100%;">
				<div class="table-wrap">
					<table class="table">
						<tbody>
							@foreach($game as $g)
							<tr>
								<td class="username">
									<button type="button" class="btn btn-link" data-id="{{$g['unique_id']}}">
										<span class="sanitize-user">
											<div class="sanitize-avatar"><img src="{{$g['avatar']}}" alt=""></div>
											<span class="sanitize-name">{{$g['username']}}</span>
										</span>
									</button>
								</td>
								<td>
									<div class="bet-number">
										<span class="bet-wrap">
											<span>{{$g['sum']}}</span>
											<svg class="icon icon-coin {{$g['balType']}}">
												<use xlink:href="/img/symbols.svg#icon-coin"></use>
											</svg>
										</span>
									</div>
								</td>
								<td>{{$g['num']}}</td>
								<td>х{{$g['vip']}}</td>
								<td>{{$g['perc']}}%</td>
								<td>
									<div class="bet-number">
										<span class="bet-wrap">
											<span class="{{ $g['win'] ? 'win' : 'lose' }}">{{ $g['win'] ? '+'.$g['win_sum'] : $g['win_sum'] }}</span>
											<svg class="icon icon-coin">
												<use xlink:href="/img/symbols.svg#icon-coin"></use>
											</svg>
										</span>
									</div>
								</td>
								<td><button class="btn btn-primary checkGame" data-hash="{{$g['hash']}}">Check</button></td>
							</tr>
							@endforeach
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection