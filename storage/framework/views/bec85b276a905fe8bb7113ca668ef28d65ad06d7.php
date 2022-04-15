

<?php $__env->startSection('content'); ?>
<link rel="stylesheet" href="/css/wheel.css">
<script type="text/javascript" src="/js/wheel.js"></script>
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
							<div class="button-group__wrap">
								<div class="button-group__content wheel btnToggle">
									<button class="btn btn-black btn-light" data-color="black"><span>x2</span></button>
									<button class="btn btn-red btn-light" data-color="red"><span>x3</span></button>
									<button class="btn btn-green btn-light" data-color="green"><span>x5</span></button>
									<button class="btn btn-yellow btn-light" data-color="yellow"><span>x50</span></button>
								</div>
								<span class="button-group-label"><span>Multiplier</span></span>
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
				<div class="game_Wheel">
					<div class="progress-wrap">
						<div class="progress-item left">
							<div class="title">Min sum: <span id="minBet"><?php echo e($settings->wheel_min_bet); ?></span> <svg class="icon icon-coin"><use xlink:href="/img/symbols.svg#icon-coin"></use></svg></div>
							<div class="title">Max sum: <span id="maxBet"><?php echo e($settings->wheel_max_bet); ?></span> <svg class="icon icon-coin"><use xlink:href="/img/symbols.svg#icon-coin"></use></svg></div>
						</div>
						<div class="progress-item right">
							<div class="title">Game #<span id="gameId"><?php echo e($game->id); ?></span></div>
						</div>
					</div>
					<div class="wheel-game">
						<div class="wheel-content">
							<?php if($game->status == 2): ?>
							<script>
								setTimeout(() => {
									$('.wheel-game .wheel-img').css({
										'transition' : '-webkit-transform <?php echo e($coldwn); ?>s cubic-bezier(0.32, 0.64, 0.45, 1)',
										'transform' : 'rotate(<?php echo e($rotate2); ?>deg)'
									}); 
								}, 1);
							</script>
							<?php endif; ?>
							<div class="wheel-img" style="transform: rotate(<?php echo e($rotate); ?>deg);"><img src="/img/wheel.png" alt=""></div>
							<div class="arrow">
								<svg class="icon"><use xlink:href="/img/symbols.svg#icon-picker"></use></svg>
							</div>
							<div class="time">
								<div class="block">
									<div class="title">To start</div>
									<div class="value"><?php echo e($time[0]); ?>:<?php echo e($time[1]); ?></div>
								</div>
							</div>
						</div>
					</div>
					<div class="history_wrapper">
						<div class="history_history">
							<?php $__currentLoopData = $history; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $l): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
							<div class="item history_item history_<?php echo e($l->winner_color); ?> checkGame" data-hash="<?php echo e($l->hash); ?>"></div>
							<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
						</div>
					</div>
					<div class="hash">
						<span class="title">HASH:</span> <span class="text"><?php echo e($game->hash); ?></span>
					</div>
				</div>
				<?php if(auth()->guard()->guest()): ?>
				<div class="game-sign">
					<div class="game-sign-wrap">
						<div class="game-sign-block auth-buttons">
							To play, you must be authorized
							<button type="button" class="btn" data-toggle="modal" data-target="#signinModal">Log In</button>
						</div>
					</div>
				</div>
				<?php endif; ?>
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
						<div class="th">Color</div>
					</div>
				</div>
			</div>
			<div class="table-stats-wrap" style="min-height: 530px; max-height: 100%;">
				<div class="table-wrap" style="transform: translateY(0px);">
					<table class="table">
						<tbody>
							<?php $__currentLoopData = $bets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bet): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
							<tr data-userid="<?php echo e($bet->user_id); ?>">
								<td class="username">
									<button type="button" class="btn btn-link" data-id="<?php echo e($bet->unique_id); ?>">
										<span class="sanitize-user">
											<div class="sanitize-avatar"><img src="<?php echo e($bet->avatar); ?>" alt=""></div>
											<span class="sanitize-name"><?php echo e($bet->username); ?></span>
										</span>
									</button>
								</td>
								<td>
									<div class="bet-number">
										<span class="bet-wrap">
											<span><?php echo e($bet->sum); ?></span>
											<svg class="icon icon-coin <?php echo e($bet->balance); ?>">
												<use xlink:href="/img/symbols.svg#icon-coin"></use>
											</svg>
										</span>
									</div>
								</td>
								<td><span class="bet-type bet_<?php echo e($bet->color); ?>"><?php echo e($bet->color == 'black' ? 'x2' : ($bet->color == 'red' ? 'x3' : ($bet->color == 'green' ? 'x5' : 'x50'))); ?></span></td>
							</tr>
							<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php /* /var/www/html/resources/views/pages/wheel.blade.php */ ?>