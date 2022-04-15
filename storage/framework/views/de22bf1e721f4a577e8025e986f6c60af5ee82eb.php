

<?php $__env->startSection('content'); ?>
<?php if($bet): ?>
<script>
	window.bet = parseInt('<?php echo e($bet->price); ?>');
	window.isCashout = false;
	window.withdraw = parseFloat('<?php echo e($bet->withdraw); ?>');
</script>
<?php endif; ?>
<link rel="stylesheet" href="<?php echo e(asset('/css/crash.css')); ?>">
<script src="<?php echo e(asset('/js/chart.min.js')); ?>"></script>
<script type="text/javascript" src="<?php echo e(asset('/js/crash.js')); ?>"></script>
<div class="section game-section">
    <div class="container">
        <div class="game crash-prefix">
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
                                    <div class="form-row">
										<label>
											<div class="form-label"><span>Auto stop</span></div>
											<div class="form-field">
												<div class="input-valid">
													<input class="input-field" value="2.00" id="betout"><div class="input-suffix"><span>2.00</span>&nbsp;x</div>
												</div>
											</div>
										</label>
                                    </div>
                                </label>
                            </div>
                            <button type="button" class="btn btn-green btn-play" style="<?php if(!is_null($bet)): ?> display : none; <?php endif; ?>"><span>Make bet</span></button>
                            <button type="button" class="btn btn-green btn-withdraw" style="<?php if(is_null($bet)): ?> display : none; <?php endif; ?>"><span>Withdraw</span></button>
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
							<div class="title">Min sum: <span id="minBet"><?php echo e($settings->crash_min_bet); ?></span> <svg class="icon icon-coin"><use xlink:href="/img/symbols.svg#icon-coin"></use></svg></div>
							<div class="title">Max sum: <span id="maxBet"><?php echo e($settings->crash_max_bet); ?></span> <svg class="icon icon-coin"><use xlink:href="/img/symbols.svg#icon-coin"></use></svg></div>
						</div>
						<div class="progress-item right">
							<div class="title">Game #<span id="gameId"><?php echo e($game['id']); ?></span></div>
						</div>
					</div>
					<div class="game-area__wrap">
						<div class="game-area">
							<div class="game-area-content">
								<div class="crash__connected">
									<canvas id="crashChart" height="642" width="800" style="width: 100%; height: auto;"></canvas>
									<h2><span id="chartInfo">Loading</span></h2>
								</div>
								<div class="hash">
									<span class="title">HASH:</span> <span class="text"><?php echo e($game['hash']); ?></span>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="game-history__wrap">
					<div class="game-history">
						<?php $__currentLoopData = $history; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
						<div class="item checkGame" data-hash="<?php echo e($m->hash); ?>">
							<div class="item-bet" style="color: <?php echo e($m->color); ?>;">x<?php echo e(number_format($m->multiplier, 2, '.', '')); ?></div>
						</div>
						<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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
						<div class="th">Auto stop</div>
						<div class="th">Win</div>
					</div>
				</div>
			</div>
			<div class="table-stats-wrap" style="min-height: 530px; max-height: 100%;">
				<div class="table-wrap" style="transform: translateY(0px);">
					<table class="table">
						<tbody id="bets">
							<?php $__currentLoopData = $game['bets']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bet): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
							<tr>
								<td class="username">
									<button type="button" class="btn btn-link" data-id="<?php echo e($bet['user']['unique_id']); ?>">
										<span class="sanitize-user">
											<div class="sanitize-avatar"><img src="<?php echo e($bet['user']['avatar']); ?>" alt=""></div>
											<span class="sanitize-name"><?php echo e($bet['user']['username']); ?></span>
										</span>
									</button>
								</td>
								<td>
									<div class="bet-number">
										<span class="bet-wrap">
											<span><?php echo e($bet['price']); ?></span>
											<svg class="icon icon-coin <?php echo e($bet['balType']); ?>">
												<use xlink:href="/img/symbols.svg#icon-coin"></use>
											</svg>
										</span>
									</div>
								</td>
								<td>х<?php echo e($bet['withdraw']); ?></td>
								<td>
									<?php if($bet['status'] == 1): ?>
									<span class="bet-wrap win">
										<span><?php echo e($bet['won']); ?></span>
										<svg class="icon icon-coin">
											<use xlink:href="/img/symbols.svg#icon-coin"></use>
										</svg>
									</span>
									<?php else: ?>
									<span class="bet-wrap wait">
										<svg class="icon">
											<use xlink:href="/img/symbols.svg#icon-time"></use>
										</svg>
									</span>
									<?php endif; ?>
								</td>
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
<?php /* /var/www/html/resources/views/pages/crash.blade.php */ ?>