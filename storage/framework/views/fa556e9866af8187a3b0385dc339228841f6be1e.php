

<?php $__env->startSection('content'); ?>
<link rel="stylesheet" href="/css/coinflip.css">
<script type="text/javascript" src="/js/coinflip.js"></script>
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
							<div class="title">Min sum: <span id="minBet"><?php echo e($settings->flip_min_bet); ?></span> <svg class="icon icon-coin"><use xlink:href="/img/symbols.svg#icon-coin"></use></svg></div>
							<div class="title">Max sum: <span id="maxBet"><?php echo e($settings->flip_max_bet); ?></span> <svg class="icon icon-coin"><use xlink:href="/img/symbols.svg#icon-coin"></use></svg></div>
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
                                            <?php if(auth()->guard()->check()): ?>
                                            <?php $__currentLoopData = $rooms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rl): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <?php if($u->unique_id == $rl['unique_id']): ?>
                                            <div class="game-coin flip_<?php echo e($rl['id']); ?>">
                                                <div class="top">
                                                    <div class="left">
                                                        <div class="players block">
															<div class="user">
																<div class="ava user-link" data-id="<?php echo e($rl['unique_id']); ?>">
																   <img src="<?php echo e($rl['avatar']); ?>">
																</div>
																<div class="info">
																	<div class="name user-link" data-id="<?php echo e($rl['unique_id']); ?>"><?php echo e($rl['username']); ?></div>
																	<p><?php echo e($rl['heads_from']); ?> - <?php echo e($rl['heads_to']); ?> <svg class="icon"><use xlink:href="/img/symbols.svg#icon-ticket"></use></svg></p>
																</div>
															</div>
														</div>
                                                    </div>
                                                    <div class="center">
														<div class="vs">VS</div>
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
                                                                    <p>0 - 0 <svg class="icon"><use xlink:href="/img/symbols.svg#icon-ticket"></use></svg></p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="bottom">
													<div class="info block">
														<div class="bank">
															<span class="type">Bank:</span>
															<span class="val"><span><?php echo e($rl['bank']); ?></span> <svg class="icon icon-coin <?php echo e($rl['balType']); ?>"><use xlink:href="/img/symbols.svg#icon-coin"></use></svg></span>
														</div>
														<div class="ticket">
															<span class="type">Lucky ticket:</span>
															<span class="val"><span>???</span> <svg class="icon"><use xlink:href="/img/symbols.svg#icon-ticket"></use></svg></span>
														</div>
													</div>
                                                	<div class="hash">
														<span class="title">HASH:</span> <span class="text" id="hash"><?php echo e($rl['hash']); ?></span>
													</div>
                                                </div>
                                            </div>
                                            <?php endif; ?>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="actives">
										<div class="line">
											<span>All games</span>
										</div>
                                        <div class="scroll">
                                            <?php $__currentLoopData = $rooms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rl): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <div class="game-coin flip_<?php echo e($rl['id']); ?>">
                                                <div class="top">
                                                    <div class="left">
                                                        <div class="players block">
															<div class="user">
																<div class="ava user-link" data-id="<?php echo e($rl['unique_id']); ?>">
																   <img src="<?php echo e($rl['avatar']); ?>">
																</div>
																<div class="info">
																	<div class="name user-link" data-id="<?php echo e($rl['unique_id']); ?>"><?php echo e($rl['username']); ?></div>
																	<p><?php echo e($rl['heads_from']); ?> - <?php echo e($rl['heads_to']); ?> <svg class="icon"><use xlink:href="/img/symbols.svg#icon-ticket"></use></svg></p>
																</div>
															</div>
														</div>
                                                    </div>
                                                    <div class="center">
														<div class="vs">VS</div>
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
                                                                <button type="button" class="btn btn-primary btn-join" data-id="<?php echo e($rl['id']); ?>"><span>Join</span></button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="bottom">
													<div class="info block">
														<div class="bank">
															<span class="type">Bank:</span>
															<span class="val"><span><?php echo e($rl['bank']); ?></span> <svg class="icon icon-coin <?php echo e($rl['balType']); ?>"><use xlink:href="/img/symbols.svg#icon-coin"></use></svg></span>
														</div>
														<div class="ticket">
															<span class="type">Lucky ticket:</span>
															<span class="val"><span>???</span> <svg class="icon"><use xlink:href="/img/symbols.svg#icon-ticket"></use></svg></span>
														</div>
													</div>
                                                	<div class="hash">
														<span class="title">HASH:</span> <span class="text" id="hash"><?php echo e($rl['hash']); ?></span>
													</div>
                                                </div>
                                            </div>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </div>
                                    </div>
								</div>
							</div>
						</div>
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
						<div class="th">Participants</div>
						<div class="th"></div>
						<div class="th">Winner</div>
						<div class="th">Bank</div>
						<div class="th">Lucky ticket</div>
						<div class="th">Check</div>
					</div>
				</div>
			</div>
			<div class="table-stats-wrap" style="min-height: 530px; max-height: 100%;">
				<div class="table-wrap" style="transform: translateY(0px);">
					<table class="table">
						<tbody>
							<?php $__currentLoopData = $ended; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $end): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
							<tr>
								<td class="username">
									<button type="button" class="btn btn-link" data-id="<?php echo e(\App\User::getUser($end->heads)->unique_id); ?>">
										<span class="sanitize-user">
											<div class="sanitize-avatar"><img src="<?php echo e(\App\User::getUser($end->heads)->avatar); ?>" alt=""></div>
											<span class="sanitize-name"><?php echo e(\App\User::getUser($end->heads)->username); ?></span>
										</span>
									</button>
								</td>
								<td class="username">
									<button type="button" class="btn btn-link" data-id="<?php echo e(\App\User::getUser($end->tails)->unique_id); ?>">
										<span class="sanitize-user">
											<div class="sanitize-avatar"><img src="<?php echo e(\App\User::getUser($end->tails)->avatar); ?>" alt=""></div>
											<span class="sanitize-name"><?php echo e(\App\User::getUser($end->tails)->username); ?></span>
										</span>
									</button>
								</td>
								<td class="username">
									<button type="button" class="btn btn-link" data-id="<?php echo e(\App\User::getUser($end->winner_id)->unique_id); ?>">
										<span class="sanitize-user">
											<div class="sanitize-avatar"><img src="<?php echo e(\App\User::getUser($end->winner_id)->avatar); ?>" alt="" style="border: solid 1px #4986f5;"></div>
											<span class="sanitize-name"><?php echo e(\App\User::getUser($end->winner_id)->username); ?></span>
										</span>
									</button>
								</td>
								<td>
									<div class="bet-number">
										<span class="bet-wrap">
											<span><?php echo e($end->bank); ?></span>
											<svg class="icon icon-coin <?php echo e($end->balType); ?>">
												<use xlink:href="/img/symbols.svg#icon-coin"></use>
											</svg>
										</span>
									</div>
								</td>
								<td>
									<div class="bet-number">
										<span class="bet-wrap">
											<span><?php echo e($end->winner_ticket); ?></span>
											<svg class="icon">
												<use xlink:href="/img/symbols.svg#icon-ticket"></use>
											</svg>
										</span>
									</div>
								</td>
								<td>
									<button class="btn btn-primary checkGame" data-hash="<?php echo e($end->hash); ?>">Check</button>
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
<?php /* /var/www/html/resources/views/pages/coinflip.blade.php */ ?>