@extends('layout')

@section('content')
<link rel="stylesheet" href="/css/bomb.css">
<script type="text/javascript" src="/js/bomb.js"></script>
<div class="section game-section">
    <div class="container">
        <div class="game">
            <div class="game-sidebar">
                <div class="sidebar-block">
                    <div class="bet-component">
                        <div class="bet-form">
                            <div class="form-row">
                                <label>
                                    <div class="form-label"><span>Bet amount</span></div>
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
                                </label>
                            </div>
                            <div class="command-type btnToggle">
								<button class="btn-bet bet-red" data-color="red">
									<div class="bet-chance">
										<div class="chance-text">
											<span>Red</span>
										</div>
									</div>
								</button>
								<button class="btn-bet bet-blue" data-color="blue">
									<div class="bet-chance">
										<div class="chance-text">
											<span id="blue_persent">Blue</span>
										</div>
									</div>
								</button>
							</div>
                            <button type="button" class="btn btn-green btn-play"><span>Make game</span></button>
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
					<div class="progress-wrap">
						<div class="progress-item left">
							<div class="title">Min sum: <span id="minBet">{{$settings->bomb_min_bet}}</span> <svg class="icon icon-coin"><use xlink:href="/img/symbols.svg#icon-coin"></use></svg></div>
							<div class="title">Max sum: <span id="maxBet">{{$settings->bomb_max_bet}}</span> <svg class="icon icon-coin"><use xlink:href="/img/symbols.svg#icon-coin"></use></svg></div>
						</div>
					</div>
					<div class="game-area__wrap">
						<div class="game-area">
							<div class="game-area-content">
								<div class="coinflip-games">
                                    <div class="yours">
										<div class="line">
											<span>You game</span>
										</div>
                                        <div class="scroll">
                                            @auth
                                            @foreach($new as $rl)
                                            @if($u->unique_id == $rl['unique_id'])
                                            <div class="game-coin flip_{{$rl['id']}}">
                                                <div class="top">
                                                    <div class="left">
                                                        <div class="players block">
															<div class="user">
																<div class="ava user-link" data-id="{{$rl['unique_id']}}">
																   <img src="{{$rl['avatar']}}">
																</div>
																<div class="info">
																	<div class="name user-link" data-id="{{$rl['unique_id']}}">{{$rl['username']}}</div>
																</div>
															</div>
														</div>
                                                    </div>
                                                    <div class="center">
														<div class="vs">
															<svg class="icon icon-bomb"><use xlink:href="/img/symbols.svg#icon-bomb"></use></svg>
														</div>
														<div class="arrow"></div>
														<div class="fixed-height">
															<div class="slider">
																<ul></ul>
															</div>
														</div>
                                                    </div>
                                                    <div class="right">
                                                        <div class="players block">
                                                            <div class="user">
                                                                <div class="ava">
                                                                   <img src="/img/no_avatar.jpg">
                                                                </div>
                                                                <div class="info">
                                                                    <div class="name">Expect opponent</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="bottom">
													<div class="info block">
														<div class="bank">
															<span class="type">Bank:</span>
														</div>
														<span class="val"><span>{{$rl['bank']}}</span> <svg class="icon icon-coin {{$rl['balType']}}"><use xlink:href="/img/symbols.svg#icon-coin"></use></svg></span>
													</div>
                                                </div>
                                            </div>
                                            @endif
                                            @endforeach
                                            @endauth
                                        </div>
                                    </div>
                                    <div class="actives">
										<div class="line">
											<span>All games</span>
										</div>
                                        <div class="scroll">
                                            @foreach($new as $rl)
                                            <div class="game-coin flip_{{$rl['id']}}">
                                                <div class="top">
                                                    <div class="left">
                                                        <div class="players block">
															<div class="user">
																<div class="ava user-link" data-id="{{$rl['unique_id']}}">
																   <img src="{{$rl['avatar']}}">
																</div>
																<div class="info">
																	<div class="name user-link" data-id="{{$rl['unique_id']}}">{{$rl['username']}}</div>
																</div>
															</div>
														</div>
                                                    </div>
                                                    <div class="center">
														<div class="vs">
															<svg class="icon icon-bomb"><use xlink:href="/img/symbols.svg#icon-bomb"></use></svg>
                                                   		</div>
														<div class="arrow"></div>
														<div class="fixed-height">
															<div class="slider">
																<ul></ul>
															</div>
														</div>
                                                    </div>
                                                    <div class="right">
                                                        <div class="players block">
                                                            <div class="user">
                                                                <div class="buttons">
																	<button type="button" class="btn btn-red btn-join" data-id="{{$rl['id']}}" data-color="red"><span>Red</span></button>
																	<button type="button" class="btn btn-primary btn-join" data-id="{{$rl['id']}}" data-color="blue"><span>Blue</span></button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="bottom">
													<div class="info block">
														<div class="bank">
															<span class="type">Bank:</span>
														</div>
														<span class="val"><span>{{$rl['bank']}}</span> <svg class="icon icon-coin {{$rl['balType']}}"><use xlink:href="/img/symbols.svg#icon-coin"></use></svg></span>
													</div>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
								</div>
							</div>
						</div>
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
						<div class="th">Participants</div>
						<div class="th"></div>
						<div class="th">Winner</div>
						<div class="th">Bank</div>
					</div>
				</div>
			</div>
			<div class="table-stats-wrap" style="min-height: 530px; max-height: 100%;">
				<div class="table-wrap" style="transform: translateY(0px);">
					<table class="table">
						<tbody>
							@foreach($ended as $end)
							<tr>
								<td class="username">
									<button type="button" class="btn btn-link" data-id="{{ \App\User::getUser($end->user1)->unique_id }}">
										<span class="sanitize-user">
											<div class="sanitize-avatar"><img src="{{ \App\User::getUser($end->user1)->avatar }}" alt=""></div>
											<span class="sanitize-name">{{ \App\User::getUser($end->user1)->username }}</span>
										</span>
									</button>
								</td>
								<td class="username">
									<button type="button" class="btn btn-link" data-id="{{ \App\User::getUser($end->user2)->unique_id }}">
										<span class="sanitize-user">
											<div class="sanitize-avatar"><img src="{{ \App\User::getUser($end->user2)->avatar }}" alt=""></div>
											<span class="sanitize-name">{{ \App\User::getUser($end->user2)->username }}</span>
										</span>
									</button>
								</td>
								<td class="username">
									<button type="button" class="btn btn-link" data-id="{{ \App\User::getUser($end->winner_id)->unique_id }}">
										<span class="sanitize-user">
											<div class="sanitize-avatar"><img src="{{ \App\User::getUser($end->winner_id)->avatar }}" alt="" style="border: solid 1px #4986f5;"></div>
											<span class="sanitize-name">{{ \App\User::getUser($end->winner_id)->username }}</span>
										</span>
									</button>
								</td>
								<td>
									<div class="bet-number">
										<span class="bet-wrap">
											<span>{{$end->winner_sum}}</span>
											<svg class="icon icon-coin {{$end->balType}}">
												<use xlink:href="/img/symbols.svg#icon-coin"></use>
											</svg>
										</span>
									</div>
								</td>
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