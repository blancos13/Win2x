<?php if(Auth::user() && $u->ban): ?>
<!DOCTYPE html>
<html>
<head>
    <title><?php echo e($settings->title); ?></title>
    <meta charset="utf-8">
    <meta content="ie=edge" http-equiv="x-ua-compatible">
    <meta content="width=device-width, initial-scale=1" name="viewport">
    <link href="/favicon.ico" rel="shortcut icon">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css" integrity="sha384-mzrmE5qonljUremFsqc01SB46JvROS7bZs3IO2EmfFsd15uHvIt+Y8vEf7N7fWAU" crossorigin="anonymous">
    <link href="css/pre.css" rel="stylesheet">
</head>
<body>
	<div class="logo">
		<img src="/img/logo.png" alt="">
		<span class="title">You are banned!</span>
		<span class="text"><?php echo e($u->ban_reason); ?></span>
		<?php if($settings->vk_url): ?><a href="<?php echo e($settings->vk_url); ?>" class="vk" target="_blank"><span>Go to group </span><i class="fab fa-facebook"></i></a><?php endif; ?>
	</div>
</body>
<?php else: ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=no">
    <meta name="description" content="">
	<meta name="google-site-verification" content="Dw7rGDwaUU56zqD9Va1kCi00pHSFxtiWLv6S2ndmAq0" />
    <title><?php echo e($settings->title); ?></title>
    <link rel="stylesheet" href="/css/main.css?v=2">
    <link rel="stylesheet" href="/css/icon.css">
    <link rel="stylesheet" href="/css/notify.css">
    <link rel="stylesheet" href="/css/animation.css">
    <link rel="stylesheet" href="/css/media.css">
    <script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
    <?php echo NoCaptcha::renderJs(); ?>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/socket.io/2.1.1/socket.io.js"></script>
    <script type="text/javascript" src="/js/perfect-scrollbar.min.js"></script>
    <script type="text/javascript" src="/js/wnoty.js"></script>
    <?php if(Auth::user() and $u->is_admin == 1 || $u->is_moder == 1): ?>
    <script type="text/javascript" src="/js/moderatorOptions.js"></script>
    <?php endif; ?>
	<script>
		<?php if(auth()->guard()->check()): ?>
		const USER_ID = '<?php echo e($u->unique_id); ?>';
		const youtuber = '<?php echo e($u->is_youtuber); ?>';
		const admin = '<?php echo e($u->is_admin); ?>';
		const moder = '<?php echo e($u->is_moder); ?>';
		<?php else: ?>
		const USER_ID = null;
		const youtuber = null;
		const admin = null;
		const moder = null;
		<?php endif; ?>
		const settings = <?php echo json_encode($gws); ?>;
		const btcrate = <?php echo e($btcr); ?>;
		const rates = <?php echo $rates; ?>;
	</script>
</head>

<body>
    <div class="wrapper">
        <div class="page">
            <div class="header">
                <div class="header-inner">
                    <div class="header-block">
                        <a class="logo" href="/">
					   		<img src="/img/logo.png" alt="">
                        </a>
                        <?php if(auth()->guard()->check()): ?>
                        <div class="top-nav-wrapper">
                            <button class="opener">
                                <div class="bar"></div>
                                <div class="bar"></div>
                                <div class="bar"></div>
                            </button>
                            <ul class="top-nav">
                                <li>
                                    <a class="<?php echo e(Request::is('affiliate') ? 'isActive' : ''); ?>" href="/affiliate">
                                        <svg class="icon icon-affiliate">
                                            <use xlink:href="/img/symbols.svg#icon-affiliate"></use>
                                        </svg>
                                        <span>Referrals</span>
                                    </a>
                                </li>
                                <li>
                                    <a class="<?php echo e(Request::is('free') ? 'isActive' : ''); ?>" href="/free">
                                        <svg class="icon icon-faucet">
                                            <use xlink:href="/img/symbols.svg#icon-faucet"></use>
                                        </svg>
                                        <span>Free coins</span>
                                    </a>
                                </li>
                                <li>
                                    <a data-toggle="modal" data-target="#promoModal">
                                        <svg class="icon icon-promo">
                                            <use xlink:href="/img/symbols.svg#icon-promo"></use>
                                        </svg>
                                        <span>Promocode</span>
                                    </a>
                                </li>
                                <li>
                                    <div class="toggle">
                                        <button class="btn">
                                            <svg class="icon icon-faq">
                                                <use xlink:href="/img/symbols.svg#icon-faq"></use>
                                            </svg>
                                            <span>Support</span>
                                            <svg class="icon icon-down">
                                                <use xlink:href="/img/symbols.svg#icon-down"></use>
                                            </svg>
                                        </button>
                                        <ul class="">
                                            <li>
                                                <a class="" href="/faq">
                                                    <svg class="icon icon-faq">
                                                        <use xlink:href="/img/symbols.svg#icon-faq"></use>
                                                    </svg>
                                                    <span>FAQ</span>
                                                </a>
                                            </li>
                                            <?php if($settings->vk_support_link): ?>
                                            <li>
                                                <a href="<?php echo e($settings->vk_support_link); ?>" target="_blank">
                                                    <svg class="icon icon-support">
                                                        <use xlink:href="/img/symbols.svg#icon-support"></use>
                                                    </svg>
                                                    Write in support
                                                </a>
                                            </li>
                                            <?php endif; ?>
                                        </ul>
                                    </div>
                                </li>
                                <?php if(Auth::check() && $u->is_admin): ?>
                                <li>
                                    <a href="/admin">
                                        <svg class="icon icon-fairness">
                                            <use xlink:href="/img/symbols.svg#icon-fairness"></use>
                                        </svg>
                                        <span>Dashboard</span>
                                    </a>
                                </li>
                                <?php endif; ?>
                            </ul>
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php if(auth()->guard()->guest()): ?>
                    <div class="auth-buttons">
						<button type="button" class="btn btn-light" data-toggle="modal" data-target="#signinModal">Log In</button>
                       	<button type="button" class="btn" data-toggle="modal" data-target="#signupModal">Sign up</button>
                    </div>
                    <?php else: ?>
					<div class="deposit-wrap">
						<div class="bottom-start rounded dropdown">
							<button type="button" aria-haspopup="true" aria-expanded="false" class="dropdown-toggle btn btn-secondary" data-toggle="dropdown">
								<div class="selected balance">
									<svg class="icon icon-coin">
										<use xlink:href="/img/symbols.svg#icon-coin"></use>
									</svg>
								</div>
								<div class="opener">
									<svg class="icon icon-down">
										<use xlink:href="/img/symbols.svg#icon-down"></use>
									</svg>
								</div>
							</button>
							<div tabindex="-1" role="menu" aria-hidden="true" class="dropdown-menu" x-placement="bottom-start">
								<button type="button" data-id="balance" tabindex="0" role="menuitem" class="dropdown-item">
									<div class="balance-item balance">
										<svg class="icon icon-coin">
											<use xlink:href="/img/symbols.svg#icon-coin"></use>
										</svg><span>Money</span>
										<div class="value" id="balance_bal"><?php echo e($u->balance); ?></div>
									</div>
								</button>
								<button type="button" data-id="bonus" tabindex="0" role="menuitem" class="dropdown-item">
									<div class="balance-item bonus">
										<svg class="icon icon-coin">
											<use xlink:href="/img/symbols.svg#icon-coin"></use>
										</svg><span>Bonus</span>
										<div class="value" id="bonus_bal"><?php echo e($u->bonus); ?></div>
									</div>
								</button>
							</div>
						</div>
						<div class="deposit-block">
							<div class="select-field"><span id="balance">0</span></div>
						</div>
					</div>
               		<?php endif; ?>
                </div>
            </div>
            <div class="left-sidebar">
                <a class="logo" href="/">
                	<img src="/img/logo.png" alt="">
                </a>
                <ul class="side-nav">
                    ​ <!--<li class="<?php echo e(Request::is('bomb') ? 'current' : ''); ?>">
                        <a class="" href="/bomb">
                            <svg class="icon">
                                <use xlink:href="/img/symbols.svg#icon-bombnav"></use>
                            </svg>
                            <div class="side-nav-tooltip">Bomb</div>
                        </a>
                    </li>-->
                    <li class="<?php echo e(Request::is('crash') ? 'current' : ''); ?>">
                        <a class="" href="/crash">
                            <svg class="icon">
                                <use xlink:href="/img/symbols.svg#icon-crash"></use>
                            </svg>
                            <div class="side-nav-tooltip">Crash</div>
                        </a>
                    </li>
                    <li class="<?php echo e(Request::is('/') ? 'current' : '' || Request::is('jackpot/history') ? 'current' : ''  || Request::is('jackpot/history/*') ? 'current' : ''); ?>">
                        <a class="" href="/">
                            <svg class="icon">
                                <use xlink:href="/img/symbols.svg#icon-jackpot"></use>
                            </svg>
                            <div class="side-nav-tooltip">Jackpot</div>
                        </a>
                    </li>
                    <li class="<?php echo e(Request::is('wheel') ? 'current' : '' || Request::is('wheel/history') ? 'current' : ''); ?>">
                        <a class="" href="/wheel">
                            <svg class="icon">
                                <use xlink:href="/img/symbols.svg#icon-roulette"></use>
                            </svg>
                            <div class="side-nav-tooltip">Wheel</div>
                        </a>
                    </li>
                    <li class="<?php echo e(Request::is('coinflip') ? 'current' : ''); ?>">
                        <a class="" href="/coinflip">
                            <svg class="icon">
                                <use xlink:href="/img/symbols.svg#icon-flip"></use>
                            </svg>
                            <div class="side-nav-tooltip">PvP</div>
                        </a>
                    </li>
                    <li class="<?php echo e(Request::is('battle') ? 'current' : ''); ?>">
                        <a class="" href="/battle">
                            <svg class="icon">
                                <use xlink:href="/img/symbols.svg#icon-battle"></use>
                            </svg>
                            <div class="side-nav-tooltip">Battle</div>
                        </a>
                    </li>
                    <li class="<?php echo e(Request::is('dice') ? 'current' : ''); ?>">
                        <a class="" href="/dice">
                            <svg class="icon">
                                <use xlink:href="/img/symbols.svg#icon-dice"></use>
                            </svg>
                            <div class="side-nav-tooltip">Dice</div>
                        </a>
                    </li>
                </ul>
            </div>
            <div class="main-content">
                <div class="main-content-top">
                    <?php echo $__env->yieldContent('content'); ?>
                </div>
                <div class="main-content-footer">
                    <div class="footer-counters">
                        <div class="container">
                            <div class="row">
                                <div class="col col-3 col-md-6">
                                    <div class="counter-block">
                                        <div class="counter-num"><?php echo e($stats['countUsers']); ?></div>
                                        <div class="counter-text">Total users</div>
                                    </div>
                                </div>
                                <div class="col col-3 col-md-6">
                                    <div class="counter-block">
                                        <div class="counter-num"><?php echo e($stats['countUsersToday']); ?></div>
                                        <div class="counter-text">Total users today</div>
                                    </div>
                                </div>
                                <div class="col col-3 col-md-6">
                                    <div class="counter-block">
                                        <div class="counter-num"><?php echo e($stats['totalGames']); ?></div>
                                        <div class="counter-text">Played games</div>
                                    </div>
                                </div>
                                <div class="col col-3 col-md-6">
                                    <div class="counter-block">
                                        <div class="counter-num white">
                                            <div class="bet-number"><span class="bet-wrap"><span><?php echo e($stats['totalWithdraw']); ?></span>
                                                <svg class="icon icon-coin">
                                                    <use xlink:href="/img/symbols.svg#icon-coin"></use>
                                                </svg>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="counter-text">Withdrawn</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="footer">
                        <div class="container">
                            <div class="row">
                                <div class="col col-7">
                                    <ul class="footer-nav">
                                        <li><a class="" data-toggle="modal" data-target="#tosModal">Terms of use</a></li>
                                        <?php if($settings->vk_url): ?>
                                        <li>
                                            <a href="<?php echo e($settings->vk_url); ?>" target="_blank">
                                                <svg class="icon icon-vk">
                                                    <use xlink:href="/img/symbols.svg#icon-vk"></use>
                                                </svg><?php echo e($settings->sitename); ?>

                                            </a>
                                        </li>
                                        <?php endif; ?>
                                    </ul>
                                </div>
                                <div class="col col-5">
                                    <div class="copyright">
                                        <div class="footer-logo"><img src="/img/logo.png" alt=""></div>
                                        <div class="text">© 2019 <?php echo e($settings->sitename); ?>

                                            <br> All rights reserved</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="right-sidebar">
                <div class="sidebar-container">
                	<?php if(auth()->guard()->check()): ?>
					<div class="tabs-nav">
						<div class="item current">
							<svg class="icon icon-conversations"><use xlink:href="/img/symbols.svg#icon-conversations"></use></svg>
							<span>Chat</span>
						</div>
						<div class="item">
							<svg class="icon icon-person"><use xlink:href="/img/symbols.svg#icon-person"></use></svg>
							<span>Profile</span>
						</div>
					</div>
                   	<?php endif; ?>
                    <div class="chat tab current">
                        <div class="chat-params">
                            <div class="item">
                                <div class="chat-online">Online: <span>0</span></div>
                            </div>
                            <div class="item">
                                <?php if(Auth::user() and $u->is_admin): ?>
                                <div class="toggle">
                                	<a class="toggle-btn" data-toggle="tooltip" data-placement="top" title="Admin Mode">
										<svg class="icon">
											<use xlink:href="/img/symbols.svg#icon-sheriff"></use>
										</svg>
									</a>
                                </div>
                                <?php endif; ?>
                                <?php if(Auth::user() and $u->is_admin || $u->is_moder): ?>
                                <div class="list">
                                	<button class="banned-btn" data-toggle="tooltip" data-placement="top" title="Banned users">
										<svg class="icon">
											<use xlink:href="/img/symbols.svg#icon-ban"></use>
										</svg>
									</button>
                                </div>
                                <div class="clear">
                                	<button class="clear-btn clearChat" data-toggle="tooltip" data-placement="top" title="Clear chat">
										<svg class="icon">
											<use xlink:href="/img/symbols.svg#icon-clear"></use>
										</svg>
									</button>
                                </div>
                                <?php endif; ?>
                                <?php if(auth()->guard()->check()): ?>
                                <div class="share">
                                	<button class="share-btn shareToChat" data-toggle="tooltip" data-placement="top" title="Share balance">
										<svg class="icon">
											<use xlink:href="/img/symbols.svg#icon-coin"></use>
										</svg>
									</button>
                                </div>
                                <?php endif; ?>
                                <button class="close-btn">
                                    <svg class="icon icon-close">
                                        <use xlink:href="/img/symbols.svg#icon-close"></use>
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <div class="chat-conversation">
                            <div class="scrollbar-container chat-conversation-inner ps">
                                <?php if($messages != 0): ?> <?php $__currentLoopData = $messages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sms): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="message-block user_<?php echo e($sms['unique_id']); ?>" id="chatm_<?php echo e($sms['time2']); ?>">
                                    <div class="message-avatar <?php echo e($sms['admin'] ? 'isAdmin' : ''); ?><?php echo e($sms['moder'] ? 'isModerator' : ''); ?>"><img src="<?php echo e($sms['avatar']); ?>" alt=""></div>
                                    <div class="message-content">
                                        <div>
                                            <button class="user-link" type="button" data-id="<?php echo e($sms['unique_id']); ?>">
                                                <span class="sanitize-name">
                                                	<span class="sanitize-text"><?php if($sms['admin']): ?><span class="admin-badge isAdmin" data-toggle="tooltip" data-placement="top" title="Администратор"><span class=""><svg class="icon icon-a"><use xlink:href="/img/symbols.svg#icon-a"></use></svg></span></span> Администратор <?php elseif($sms['moder']): ?><span class="admin-badge isModerator" data-toggle="tooltip" data-placement="top" title="Модератор"><span class=""><svg class="icon icon-m"><use xlink:href="/img/symbols.svg#icon-m"></use></svg></span></span> <?php echo e($sms['username']); ?> <?php elseif($sms['youtuber']): ?><span class="admin-badge isYouTuber" data-toggle="tooltip" data-placement="top" title="YouTuber"><span class=""><svg class="icon icon-y"><use xlink:href="/img/symbols.svg#icon-y"></use></svg></span></span> <?php echo e($sms['username']); ?> <?php else: ?> <?php echo e($sms['username']); ?> <?php endif; ?><span>&nbsp;</span></span>
                                                </span>
                                            </button>
                                            <div class="message-text"><?php echo $sms['messages']; ?></div>
                                        </div>
                                    </div>
                                    <?php if(Auth::user() and $u->is_admin || $u->is_moder): ?>
									<div class="delete">
										<button type="button" class="btn btn-light" onclick="chatdelet(<?php echo e($sms['time2']); ?>)">
											<svg class="icon">
												<use xlink:href="/img/symbols.svg#icon-close"></use>
											</svg><span>Delete</span>
										</button>
										<?php if(!$sms['admin']): ?>
										<?php if(!$sms['ban']): ?>
										<button type="button" class="btn btn-light btnBan" data-name="<?php echo e($sms['username']); ?>" data-id="<?php echo e($sms['unique_id']); ?>">
											<svg class="icon">
												<use xlink:href="/img/symbols.svg#icon-ban"></use>
											</svg><span>Ban</span>
										</button>
										<?php else: ?>
										<button type="button" class="btn btn-light btnUnBan" data-name="<?php echo e($sms['username']); ?>" data-id="<?php echo e($sms['unique_id']); ?>">
											<svg class="icon">
												<use xlink:href="/img/symbols.svg#icon-ban"></use>
											</svg><span>Unban</span>
										</button>
										<?php endif; ?>
										<?php endif; ?>
									</div>
                               		<?php endif; ?>
                                </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?> <?php endif; ?>
                            </div>
                        </div>
                        <?php if(auth()->guard()->guest()): ?>
                        <div class="chat-empty-block">You must be logged in to chat</div>
                        <?php else: ?>
                        	<input type="hidden" id="optional" value="0">
							<?php if($u->banchat): ?>
							<div class="chat-ban-block">
								<div class="title">Chat blocked!</div>
								<button type="button" class="btn btn-light unbanMe">
									<span>Unlock ( -0.50 <svg class="icon icon-coin balance"><use xlink:href="/img/symbols.svg#icon-coin"></use></svg> )</span>
								</button>
							</div>
							<?php else: ?>
							<div class="chat-message-input">
								<div class="chat-textarea">
									<div class="chat-editable" contenteditable="true"></div>
								</div>
								<div class="chat-controls">
									<button class="item" id="smilesBlock" data-toggle="popover" data-placement="top">
										<svg class="icon icon-smile">
											<use xlink:href="/img/symbols.svg#icon-smile"></use>
										</svg>
									</button>
									<button type="submit" class="item sendMessage">
										<svg class="icon icon-send">
											<use xlink:href="/img/symbols.svg#icon-send"></use>
										</svg>
									</button>
								</div>
							</div>
							<?php endif; ?>
                        <?php endif; ?>
                    </div>
					<div class="user-profile tab">
						<?php if(auth()->guard()->check()): ?>
						<div class="user-block">
							<div class="user-avatar">
								<button class="close-btn">
									<svg class="icon icon-close">
										<use xlink:href="/img/symbols.svg#icon-close"></use>
									</svg>
								</button>
								<div class="avatar"><img src="<?php echo e($u->avatar); ?>" alt=""></div>
							</div>
							<div class="user-name">
								<div class="nickname"><?php echo e($u->username); ?></div>
							</div>
						</div>
						<ul class="profile-nav">
							<li>
								<a class="" href="/profile/history">
									<div class="item-icon">
										<svg class="icon icon-history">
											<use xlink:href="/img/symbols.svg#icon-history"></use>
										</svg>
									</div><span>History</span>
								</a>
							</li>
						</ul>
						<a href="/logout" class="btn btn-logout">
							<div class="item-icon">
								<svg class="icon icon-logout">
									<use xlink:href="/img/symbols.svg#icon-logout"></use>
								</svg>
							</div><span>Logout</span>
						</a>
						<?php endif; ?>
					</div>
                </div>
            </div>
			<div class="mobile-nav-component">
				<?php if(auth()->guard()->check()): ?>
				<div class="pull-out other">
					<button class="close-btn">
						<svg class="icon icon-close">
							<use xlink:href="/img/symbols.svg#icon-close"></use>
						</svg>
					</button>
					<ul class="pull-out-nav">
						<li>
							<a href="/affiliate">
								<svg class="icon icon-affiliate">
									<use xlink:href="/img/symbols.svg#icon-affiliate"></use>
								</svg>Referrals
							</a>
						</li>
						<li>
							<a href="/faq">
								<svg class="icon icon-faq">
									<use xlink:href="/img/symbols.svg#icon-faq"></use>
								</svg>FAQ
							</a>
						</li>
						<?php if($settings->vk_support_url): ?>
						<li>
							<a href="<?php echo e($settings->vk_support_url); ?>" target="_blank">
								<svg class="icon icon-support">
									<use xlink:href="/img/symbols.svg#icon-support"></use>
								</svg>Support
							</a>
						</li>
						<?php endif; ?>
						<li>
							<a href="/free">
								<svg class="icon icon-faucet">
									<use xlink:href="/img/symbols.svg#icon-faucet"></use>
								</svg>Free coins
							</a>
						</li>
						<li>
							<a data-toggle="modal" data-target="#promoModal">
								<svg class="icon icon-promo">
									<use xlink:href="/img/symbols.svg#icon-promo"></use>
								</svg>Promocode
							</a>
						</li>
						<?php if(Auth::check() && $u->is_admin): ?>
						<li>
							<a href="/admin">
								<svg class="icon icon-fairness">
									<use xlink:href="/img/symbols.svg#icon-fairness"></use>
								</svg>Dashboard
							</a>
						</li>
						<?php endif; ?>
					</ul>
				</div>
				<?php endif; ?>
				<div class="pull-out game">
					<button class="close-btn">
						<svg class="icon icon-close">
							<use xlink:href="/img/symbols.svg#icon-close"></use>
						</svg>
					</button>
					<ul class="pull-out-nav">
						<li>
							<a href="/crash">
								<svg class="icon">
									<use xlink:href="/img/symbols.svg#icon-crash"></use>
								</svg>Crash
							</a>
						</li>
						<li>
							<a href="/">
								<svg class="icon">
									<use xlink:href="/img/symbols.svg#icon-jackpot"></use>
								</svg>Jackpot
							</a>
						</li>
						<li>
							<a href="/wheel">
								<svg class="icon">
									<use xlink:href="/img/symbols.svg#icon-roulette"></use>
								</svg>Wheel
							</a>
						</li>
						<li>
							<a href="/coinflip">
								<svg class="icon">
									<use xlink:href="/img/symbols.svg#icon-flip"></use>
								</svg>PvP
							</a>
						</li>
						<li>
							<a href="/battle">
								<svg class="icon">
									<use xlink:href="/img/symbols.svg#icon-battle"></use>
								</svg>Battle
							</a>
						</li>
						<li>
							<a href="/dice">
								<svg class="icon">
                                	<use xlink:href="/img/symbols.svg#icon-dice"></use>
								</svg>Dice
							</a>
						</li>
					</ul>
				</div>
				<div class="mobile-nav-menu-wrapper">
					<ul class="mobile-nav-menu">
						<li>
							<button id="gamesMenu">
								<svg class="icon icon-gamepad">
									<use xlink:href="/img/symbols.svg#icon-gamepad"></use>
								</svg>Games
							</button>
						</li>
						<li>
							<button id="chatMenu">
								<svg class="icon icon-conversations">
									<use xlink:href="/img/symbols.svg#icon-conversations"></use>
								</svg>Chat
							</button>
						</li>
						<?php if(auth()->guard()->check()): ?>
						<li>
							<button id="profileMenu">
								<svg class="icon icon-person">
									<use xlink:href="/img/symbols.svg#icon-person"></use>
								</svg>Profile
							</button>
						</li>
						<li>
							<button id="otherMenu">
								<svg class="icon icon-more">
									<use xlink:href="/img/symbols.svg#icon-more"></use>
								</svg><span>More</span>
							</button>
						</li>
						<?php endif; ?>
					</ul>
				</div>
			</div>
        </div>
    </div>
    <script type="text/javascript" src="/js/main.js?v=2"></script>
    <?php if(auth()->guard()->check()): ?>
	<div class="modal fade" id="walletModal" tabindex="-1" role="dialog" aria-labelledby="walletModalLabel" aria-hidden="true">
		<div class="modal-dialog deposit-modal modal-dialog-centered" role="document">
			<div class="modal-content">
				<button class="modal-close" data-dismiss="modal" aria-label="Close">
					<svg class="icon icon-close">
						<use xlink:href="/img/symbols.svg#icon-close"></use>
					</svg>
				</button>
				<div class="deposit-modal-component">
					<div class="wrap">
						<div class="tabs">
							<button type="button" class="btn btn-tab isActive">Deposite</button>
							<button type="button" class="btn btn-tab">Withdraw</button>
						</div>
						<div class="deposit-section tab active" data-type="deposite">
							<form action="/pay" method="post" id="payment">
								<div class="form-row">
									<label>
										<div class="form-label">Refill amount ($)</div>
										<div class="form-field">
											<div class="input-valid">
												<input class="input-field input-with-icon" name="amount" placeholder="Min sum: <?php echo e($settings->min_dep); ?>$">
												<div class="input-icon">
													<svg class="icon icon-coin">
														<use xlink:href="/img/symbols.svg#icon-coin"></use>
													</svg>
												</div>
												<div class="valid inline"></div>
											</div>
										</div>
									</label>
								</div>
								<div class="form-row">
									<div class="form-label">Payment method</div>
									<div class="select-payment">
										<input type="hidden" name="type" value="" id="depositType">
										<div class="bottom-start dropdown">
											<button type="button" aria-haspopup="true" aria-expanded="false" class="dropdown-toggle btn btn-secondary" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
												Chosen method
												<div class="opener">
													<svg class="icon icon-down"><use xlink:href="/img/symbols.svg#icon-down"></use></svg>
												</div>
											</button>
											<div tabindex="-1" role="menu" aria-hidden="true" class="dropdown-menu" x-placement="bottom-start" data-placement="bottom-start">
												<button type="button" data-id="5" tabindex="0" role="menuitem" class="dropdown-item" data-system="perfectmoney">
													<div class="image"><img src="/img/wallets/perfectmoney.png" alt="perfectmoney"></div><span>PerfectMoney</span>
												</button>
												<button type="button" data-id="4" tabindex="0" role="menuitem" class="dropdown-item" data-system="coinpayments">
													<div class="image"><img src="/img/wallets/coinpayments.png" alt="coinpayments"></div><span>CoinPayments</span>
												</button>
											</div>
										</div>
									</div>
								</div>
								<button type="submit" class="btn btn-green">Pay</button>
							</form>
						</div>
						<div class="deposit-section tab" data-type="withdraw">
							<div class="form-row">
								<label>
									<div class="form-label">Available for withdraw ($)</div>
									<div class="form-field">
										<div class="input-valid">
											<input class="input-field input-with-icon" value="<?php echo e($u->requery); ?>" readonly>
											<div class="input-icon">
												<svg class="icon icon-coin">
													<use xlink:href="/img/symbols.svg#icon-coin"></use>
												</svg>
											</div>
										</div>
									</div>
								</label>
							</div>
							<div class="form-row">
								<div class="form-label">Withdraw method</div>
								<div class="select-payment">
									<input type="hidden" name="type" value="" id="withdrawType">
									<div class="bottom-start dropdown">
										<button type="button" aria-haspopup="true" aria-expanded="false" class="dropdown-toggle btn btn-secondary" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
											Chosen method
											<div class="opener">
												<svg class="icon icon-down"><use xlink:href="/img/symbols.svg#icon-down"></use></svg>
											</div>
										</button>
										<div tabindex="-1" role="menu" aria-hidden="true" class="dropdown-menu" x-placement="bottom-start" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(0px, 46px, 0px);" data-placement="bottom-start">
											<button type="button" tabindex="0" role="menuitem" class="dropdown-item" data-system="perfectmoney"><span>PerfectMoney</span></button>
											<button type="button" tabindex="0" role="menuitem" class="dropdown-item" data-system="BTC"><span>Bitcoin (BTC)</span></button>
											

											<button type="button" tabindex="0" role="menuitem" class="dropdown-item" data-system="DOGE"><span>Dogecoin (DOGE)</span></button>
											<button type="button" tabindex="0" role="menuitem" class="dropdown-item" data-system="ETH"><span>Ether (ETH)</span></button>

					
											<button type="button" tabindex="0" role="menuitem" class="dropdown-item" data-system="LTC"><span>Litecoin (LTC)</span></button>
											<button type="button" tabindex="0" role="menuitem" class="dropdown-item" data-system="LTCT"><span>Litecoin Testnet (LTCT)</span></button>
											

						
											<button type="button" tabindex="0" role="menuitem" class="dropdown-item" data-system="TRX"><span>TRON (TRX)</span></button>
											<button type="button" tabindex="0" role="menuitem" class="dropdown-item" data-system="USDT"><span>Tether USD (Omni Layer) (USDT)</span></button>
						
											

										</div>
									</div>
								</div>
							</div>
							<div class="form-row">
								<label>
									<div class="form-label">Enter your wallet number</div>
									<div class="form-field">
										<div class="input-valid">
											<input class="input-field" name="purse" placeholder="" value="" id="numwallet" required>
										</div>
									</div>
								</label>
							</div>
							<div class="form-row">
								<label>
									<div class="form-label">Withdraw amount ($)</div>
									<div class="form-field">
										<div class="input-valid">
											<input class="input-field input-with-icon" name="amount" value="" id="valwithdraw" placeholder="Enter amount" required>
											<div class="input-icon">
												<svg class="icon icon-coin">
													<use xlink:href="/img/symbols.svg#icon-coin"></use>
												</svg>
											</div>
										</div>
									</div>
								</label>
							</div>
							<div class="form-row">
								<div class="com-row">
									Fees: <span>0%</span>
								</div>
							</div>
							<div class="form-row">
								<div>
									Converting: <span id="withdrawGive">0</span>
								</div>
							</div>
							<button type="submit" disabled="" class="btn btn-green" id="submitwithdraw">Withdraw (<span id="totalwithdraw">0</span> $)</button>
							<div class="checkbox-block">
								<label>I confirm the accuracy of the details
								<input name="agree" type="checkbox" id="withdraw-checkbox" value=""><span class="checkmark"></span></label>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="modal fade" id="exchangeModal" tabindex="-1" role="dialog" aria-labelledby="exchangeModalLabel" aria-hidden="true">
		<div class="modal-dialog faucet-demo-modal modal-dialog-centered" role="document">
			<div class="modal-content">
				<button class="modal-close" data-dismiss="modal" aria-label="Close">
					<svg class="icon icon-close">
						<use xlink:href="/img/symbols.svg#icon-close"></use>
					</svg>
				</button>
				<div class="faucet-container">
					<h3 class="faucet-caption"><span>Bonus exchange</span></h3>
					<div class="caption-line"><span class="span"><svg class="icon icon-coin"><use xlink:href="/img/symbols.svg#icon-coin"></use></svg></span></div>
					<div class="faucet-modal-form">
						<div class="faucet-reload"><span>Min sum</span> <span><?php echo e($settings->exchange_min); ?></span> <svg class="icon icon-coin bonus"><use xlink:href="/img/symbols.svg#icon-coin"></use></svg></div>
					</div>
					<div class="faucet-modal-form">
						<div class="faucet-reload"><span>Rate</span> <span>0.0000000<?php echo e($settings->exchange_curs); ?></span> <svg class="icon icon-coin bonus"><use xlink:href="/img/symbols.svg#icon-coin"></use></svg> = <span>0.00000001</span> <svg class="icon icon-coin balance"><use xlink:href="/img/symbols.svg#icon-coin"></use></svg></div>
					</div>
					<div class="form-row">
						<label>
							<div class="form-label">Exchange amount</div>
							<div class="form-field">
								<div class="input-valid">
									<input class="input-field input-with-icon" name="amount" placeholder="Enter amount" id="exSum">
									<div class="input-icon">
										<svg class="icon icon-coin">
											<use xlink:href="/img/symbols.svg#icon-coin"></use>
										</svg>
									</div>
									<div class="valid inline"></div>
								</div>
							</div>
						</label>
					</div>
					<div class="faucet-modal-form">
						<div class="faucet-amount">
							<div class="faucet-reload"><span>You'll get:</span> <span id="exTotal">0</span> <svg class="icon icon-coin balance"><use xlink:href="/img/symbols.svg#icon-coin"></use></svg></div>
						</div>
						<button type="button" class="btn btn-green exchangeBonus"><span>Exchange</span></button>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="modal fade" id="promoModal" tabindex="-1" role="dialog" aria-labelledby="promoModalLabel" aria-hidden="true">
		<div class="modal-dialog faucet-demo-modal modal-dialog-centered" role="document">
			<div class="modal-content">
				<button class="modal-close" data-dismiss="modal" aria-label="Close">
					<svg class="icon icon-close">
						<use xlink:href="/img/symbols.svg#icon-close"></use>
					</svg>
				</button>
				<div class="faucet-container">
					<h3 class="faucet-caption"><span>Activation of promocodes</span></h3>
					<div class="caption-line"><span class="span"><svg class="icon icon-coin"><use xlink:href="/img/symbols.svg#icon-coin"></use></svg></span></div>
					<div class="form-row">
						<label>
							<div class="form-field">
								<div class="input-valid">
									<input class="input-field input-with-icon" name="promo" placeholder="Enter promocode" id="promoInput">
									<div class="input-icon">
										<svg class="icon icon-promo">
											<use xlink:href="/img/symbols.svg#icon-promo"></use>
										</svg>
									</div>
								</div>
							</div>
						</label>
					</div>
					<div class="faucet-modal-form">
						<button type="button" class="btn btn-green activatePromo"><span>Activate</span></button>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="modal fade" id="captchaModal" tabindex="-1" role="dialog" aria-labelledby="captchaModalLabel" aria-hidden="true">
		<div class="modal-dialog captcha-need-modal modal-dialog-centered" role="document">
			<div class="modal-content">
				<div class="captcha-need-modal-container">
					<div class="caption">Confirm that you are not a robot!</div>
					<div class="form">
						<div class="label">Click "I am not a robot" to continue!</div>
						<div class="captcha">
							<div hl="ru">
								<div>
									<div style="width: 304px; height: 78px;">
										<?php echo NoCaptcha::display(['data-callback' => 'recaptchaCallback']); ?>

									</div>
								</div>
							</div>
						</div>
						<button type="button" disabled="" class="btn" id="submitBonus">Continue</button>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php if($u->is_admin == 1 || $u->is_moder == 1): ?>
	<div class="modal fade" id="bannedModal" tabindex="-1" role="dialog" aria-labelledby="bannedModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-lg modal-dialog-centered" role="document">
			<div class="modal-content">
				<button class="modal-close" data-dismiss="modal" aria-label="Close">
					<svg class="icon icon-close">
						<use xlink:href="/img/symbols.svg#icon-close"></use>
					</svg>
				</button>
				<div class="faucet-container">
					<h3 class="faucet-caption"><span>Blocked users</span></h3>
					<h3 class="faucet-caption"><div id="unbanName"></div></h3>
					<div class="caption-line"><span class="span"><svg class="icon"><use xlink:href="/img/symbols.svg#icon-ban"></use></svg></span></div>
					<div class="form-row">
						<div class="table-heading">
							<div class="thead">
								<div class="tr">
									<div class="th">User</div>
									<div class="th">End of blocking</div>
									<div class="th">Reason</div>
									<div class="th">Actions</div>
								</div>
							</div>
						</div>
						<div class="table-ban-wrap" style="max-height: 100%;">
							<div class="table-wrap" style="transform: translateY(0px);">
								<table class="table">
									<tbody id="bannedList">
									</tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="modal fade" id="banModal" tabindex="-1" role="dialog" aria-labelledby="banModalLabel" aria-hidden="true">
		<div class="modal-dialog faucet-demo-modal modal-dialog-centered" role="document">
			<div class="modal-content">
				<button class="modal-close" data-dismiss="modal" aria-label="Close">
					<svg class="icon icon-close">
						<use xlink:href="/img/symbols.svg#icon-close"></use>
					</svg>
				</button>
				<div class="faucet-container">
					<h3 class="faucet-caption"><span>Block chat user</span></h3>
					<h3 class="faucet-caption"><div id="banName"></div></h3>
					<div class="caption-line"><span class="span"><svg class="icon"><use xlink:href="/img/symbols.svg#icon-ban"></use></svg></span></div>
					<div class="form-row">
						<input type="hidden" name="user_ban_id">
						<label>
							<div class="form-label">Ban time in minutes</div>
							<div class="form-field">
								<div class="input-valid">
									<input class="input-field input-with-icon" name="time" placeholder="Time" id="banTime">
									<div class="input-icon">
										<svg class="icon">
											<use xlink:href="/img/symbols.svg#icon-time"></use>
										</svg>
									</div>
								</div>
							</div>
						</label>
					</div>
					<div class="form-row">
						<input type="hidden" name="user_ban_id">
						<label>
							<div class="form-label">Reason ban</div>
							<div class="form-field">
								<div class="input-valid">
									<input class="input-field input-with-icon" name="reason" placeholder="Reason" id="banReason">
									<div class="input-icon">
										<svg class="icon">
											<use xlink:href="/img/symbols.svg#icon-edit"></use>
										</svg>
									</div>
								</div>
							</div>
						</label>
					</div>
					<div class="form-row">
						<button type="button" class="btn btn-green banThis"><span>Ban</span></button>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="modal fade" id="unbanModal" tabindex="-1" role="dialog" aria-labelledby="unbanModalLabel" aria-hidden="true">
		<div class="modal-dialog faucet-demo-modal modal-dialog-centered" role="document">
			<div class="modal-content">
				<button class="modal-close" data-dismiss="modal" aria-label="Close">
					<svg class="icon icon-close">
						<use xlink:href="/img/symbols.svg#icon-close"></use>
					</svg>
				</button>
				<div class="faucet-container">
					<h3 class="faucet-caption"><span>Unlock chat user</span></h3>
					<h3 class="faucet-caption"><div id="unbanName"></div></h3>
					<div class="caption-line"><span class="span"><svg class="icon"><use xlink:href="/img/symbols.svg#icon-ban"></use></svg></span></div>
					<div class="form-row">
						<input type="hidden" name="user_unban_id">
						<button type="button" class="btn btn-green unbanThis"><span>Unban</span></button>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php endif; ?>
	<?php endif; ?>
	<?php if(auth()->guard()->guest()): ?>
	<div class="modal fade" id="confirmAgeModal" tabindex="-1" role="dialog" aria-labelledby="confirmAgeModalLabel" aria-hidden="true">
		<div class="modal-dialog confirm-age-modal modal-dialog-centered" role="document">
			<div class="modal-content">
				<button class="modal-close" data-dismiss="modal" aria-label="Close">
					<svg class="icon icon-close">
						<use xlink:href="/img/symbols.svg#icon-close"></use>
					</svg>
				</button>
				<div class="confirm-age-modal-container">
					<div class="wrap">
						<div class="head"><img src="/img/logo.png" alt=""></div>
						<div class="body">
							<div class="wrap">
								<div class="buttons auth-buttons">
									<button type="button" class="btn" data-toggle="modal" data-target="#signinModal">Log In</button>
								</div>
								<div class="disclaimer">By entering the site, you accept the terms
									<br>
									<button class="button-link" data-toggle="modal" data-target="#tosModal">license agreement</button> and confirm that you are 18 years old</div>
								<div class="leave-link"><a href="https://google.com" rel="nofollow">Leave site</a></div>
								<div class="info">*Site services - are simulators (simulators), allowing to get psycho-emotional satisfaction without any risks for the user, and therefore, site services are related to attractions.</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php endif; ?>
	<div class="modal fade" id="tosModal" tabindex="-1" role="dialog" aria-labelledby="tosModalLabel" aria-hidden="true">
		<div class="modal-dialog tos-modal modal-dialog-centered" role="document">
			<div class="modal-content">
				<button class="modal-close" data-dismiss="modal" aria-label="Close">
					<svg class="icon icon-close">
						<use xlink:href="/img/symbols.svg#icon-close"></use>
					</svg>
				</button>
				<div class="tos-modal-container">
					<div class="scrollbar-container tos-modal-block ps">
						<h2>General provisions</h2>
						<p>1.1. This agreement (hereinafter the «Agreement») governs the procedure and conditions for the provision of services by the site «<?php echo e($settings->domain); ?>», hereinafter referred to as the «Organizer”, and is addressed to an individual who wants to receive the services of this website (hereinafter «Participant».)</p>
						<p>1.2. The organizer and the participant recognize the order and form of the conclusion of this agreement equivalent in terms of legal force to an agreement concluded in writing.</p>
						<p>1.3. The terms of this agreement are accepted by the participants in full and without any reservations, by joining the agreement in the form in which it is set out on the site «<?php echo e($settings->domain); ?>»</p>
						<h2>Terms and Definitions</h2>
						<p>2.1. The subject of this Agreement is the provision by the organizer of the party services for the organization of leisure and recreation in the game «<?php echo e($settings->domain); ?>» in accordance with the terms of this Agreement. Such services include, in particular, the following: services for the purchase and sale of gaming equipment (<?php echo e($settings->domain); ?>), keeping records of relevant information: movements on the gaming account, providing measures for identifying and security of participants, developing software integrated into the playground and external applications, informational and other services necessary for organizing the game and serving the participant in its process at the organizer's site.</p>
						<p>2.2. The game as a whole, as well as any of its elements or any associated external gaming application, created exclusively for entertainment. The participant acknowledges that all activities in the game on the playground are for him entertainment. The participant agrees that, depending on the characteristics of his account, the degree of his participation in the game will be available in varying degrees.</p>
						<p>2.3. The participant agrees that he is personally responsible for all actions performed with the game equipment (<?php echo e($settings->domain); ?>): buying, selling, entering and withdrawing, as well as for playing actions on the playing field: creating, buying and selling , operations with all game elements and other game attributes and objects used for gameplay.</p>
						<p>2.4. The participant acknowledges that the degree and possibility of participation in entertainment on the Games server are the main qualities of the service rendered to him.</p>
						<h2>Rights and obligations of the parties</h2>
						<p>3.1 Rights and obligations of the participant.</p>
						<p>3.1.1. Only persons who have attained civil capacity under the laws of the country of their residence can take part in the game «<?php echo e($settings->domain); ?>». All consequences of non-fulfillment of this condition are borne by the participant.</p>
						<p>3.1.2. The degree and method of participation in the game are determined by the participant himself, but cannot contradict this Agreement and the rules of the playground.</p>
						<p>3.1.2. Member must:</p>
						<p>3.1.2.1. truthfully provide information about yourself when registering and upon the Organizer's first request to provide reliable information about your identity, allowing you to identify him as the owner of the account in the game;</p>
						<p>3.1.2.2. not to use the game to perform any actions that are contrary to international law and the laws of the country - the Participant’s residence;</p>
						<p>3.1.2.3. not to use undocumented features (bugs) and errors of the game software and immediately report to the Organizer about them, as well as about the persons using these errors;</p>
						<p>3.1.2.4. do not use external programs of any kind to gain advantages in the game;</p>
						<p>3.1.2.5. not to use for advertising your affiliate links, as well as the resource containing it, mailing lists and other types of communication to persons who have not expressed their consent to receive them (spam);</p>
						<p>3.1.2.6. does not have the right to restrict the access of other participants or other persons to the Game, must respectfully and correctly treat the participants of the game, as well as the Organizer, his partners and employees, not interfere with the work of the latter;</p>
						<p>3.1.2.7. not to deceive the Organizer and the participants of the game;</p>
						<p>3.1.2.8. not to use profanity and insults in any form;</p>
						<p>3.1.2.9. Do not defame the actions of other players and the Administration;</p>
						<p>3.1.2.10. not to threaten violence and physical violence to anyone;</p>
						<p>3.1.2.11. not to distribute materials that promote rejection or hatred of any race, religion, culture, nation, people, language, politics, state, ideology, or social movement;</p>
						<p>3.1.2.12. not to advertise pornography, drugs and resources containing such information;</p>
						<p>3.1.2.13. not to use actions, terminology or jargon to disguise the violation of the obligations of the participant;</p>
						<p>3.1.2.14. independently take care of the necessary measures of computer and other security, keep secret and not transfer to another person or another participant their identification data: login, account password, etc., prevent unauthorized access to the mailbox specified in the profile of the participant account. The entire risk of adverse consequences of disclosing this data is borne by the participant, since the participant agrees that the gaming platform’s information security system excludes the transfer of the login, password and identification information of the participant’s account to third parties;</p>
						<p>3.1.2.15. to bear personal responsibility for maintaining their financial transactions and operations; the Organizer is not responsible for the financial actions between players in transferring game equipment and game currency, as well as other game attributes.</p>
						<p>3.1.2.16. notify the organizer in writing of their claims and complaints first through the “Support” page.</p>
						<p>3.1.2.17. Regularly independently get acquainted with the news of the game, as well as with changes in this Agreement and in the rules of the game on the playing court.</p>
						<p>3.1.2.18. do not create additional accounts (multi-accounts). Such actions will result in account blocking or its cancellation.</p>
						<p>3.1.2.19. It is forbidden to sell / transfer accounts</p>
						<p>3.1.2.20. Prohibited "collusion" of groups of persons in order to obtain benefits for the participants / non-parties to the collusion</p>
						<p>3.1.2.21. "Collusion" - they are cartel collusion, criminal conspiracy, cooperative. This term defines a group of persons who, through cooperation, try to get a benefit on the site. In the case of the discovery of these, all participants face a ban and zeroing, as well as the penalty imposed by the administrators.</p>
						<h3> Rights and Obligations of the Organizer</h3>
						<p>4.1.1. The organizer must:</p>
						<p>4.1.1.1. provide free of charge to the participant access to the playground and to participate in the game. The participant pays for Internet access at his own expense and bears other expenses associated with this action.</p>
						<p>4.1.1.2. keep records of gaming equipment (<?php echo e($settings->domain); ?>) on the gaming account of the participant.</p>
						<p>4.1.1.3. regularly improve the hardware and software complex, but does not guarantee that the game software does not contain errors, and the hardware will not go out of working parameters and will function smoothly.</p>
						<p>4.1.1.4. Observe confidentiality regarding the personal data of the participant in accordance with paragraph 6 of this agreement.</p>
						<p>4.1.1.5. Receipt by the user may be limited by the administration at its discretion.</p>
						<p>4.1.1.6. Any person legally owning game equipment (<?php echo e($settings->domain); ?>) is paid a sum of money due to the exchange rate value (<?php echo e($settings->domain); ?>), minus the cost of the operation.</p>
						<p>4.1.2. The organizer has the right:</p>
						<p>4.1.2.2. provide the participant with additional paid services, the list of which, as well as the procedure and conditions for the use of which are determined by this agreement, the rules of the playground and other announcements of the organizer. In this case, the organizer has the right at any time to change the number and amount of paid services offered, their cost, name, type and effect of use.</p>
						<p>4.1.2.3. suspend the present agreement and disconnect the participant from participation in the game at the time of the investigation on suspicion of the participant in violation of this Agreement and the rules of the playing court.</p>
						<p>4.1.2.4. exclude the participant from the game, if it determines that the participant has violated this agreement or the rules established on the playing court, in order of 5.10 of this agreement.</p>
						<p>4.1.2.5. partially or completely interrupt the provision of services without warning the participant during the reconstruction, repair and maintenance work on the site.</p>
						<p>4.1.2.6. The organizer is not responsible for the improper functioning of the game software. The participant uses the software as is "AS IS". If the organizer determines that there was a malfunction (error) in the work of the site during the game, the results that occurred during the incorrect operation of the software can be canceled or adjusted at the discretion of the organizer. The participant agrees not to appeal to the organizer about the quality, quantity, order and timing of the gaming opportunities and services provided to him.</p>
						<p>Guarantees and liability 5.1. The organizer does not guarantee continuous and uninterrupted access to the playground and its services in the event of technical problems and / or unforeseen circumstances, including: inadequate work or non-functioning of Internet providers, information servers, banking and payment systems, as well as third-party misconduct individuals. The organizer will make every effort to prevent failures, but is not responsible for temporary technical failures and interruptions in the work of the Game, regardless of the reasons for such failures.</p>
						<p>5.2. The participant fully agrees that the organizer cannot be held liable for losses of the participant that have arisen due to illegal actions of third parties aimed at violating the security system of electronic equipment and game databases, or as a result of interruptions, suspension or termination of channels and networks independent of the organizer. communications used to interact with the participant, as well as illegal or unreasonable actions of payment systems, as well as third parties.</p>
						<p>5.3. The Organizer is not responsible for losses incurred as a result of the use or non-use by the participant of information about the Game, the game rules and the Game itself and is not responsible for losses or other harm caused to the participant due to his unqualified actions and ignorance of the game rules or his mistakes in the calculations;</p>
						<p>5.4. The participant agrees that he uses the playground of his own free will and at his own risk. The organizer does not give the participant any guarantee that he will benefit from or participate in the game. The degree of participation in the game is determined by the participant.</p>
						<p>5.5. The organizer is not responsible to the participant for the actions of other participants.</p>
						<p>5.6. In case of disputes and disagreements on the playing court, the decision of the organizer is final, and the participant fully agrees with him. All disputes and controversies arising out of or in connection with this Agreement shall be resolved through negotiations. In case of failure to reach agreement through negotiations, disputes, disagreements and claims arising from this Agreement shall be resolved in accordance with the current legislation of the Russian Federation.</p>
						<p>5.7. The organizer does not bear the tax burden for the Participant. The participant undertakes to independently include possible income received in the tax return in accordance with the laws of the country of his residence.</p>
						<p>5.8. The organizer may make changes to this Agreement, the rules of the playground and other documents unilaterally. In case of making changes to the documents, the Organizer places the latest versions of documents on the website of the playground. All changes take effect from the time of posting. Member has the right to terminate this Agreement within 3 days, if he does not agree with the changes. In this case, the termination of the Agreement is made in accordance with paragraph 5.9 of this Agreement. The Participant is obliged to regularly visit the official website of the Game in order to familiarize himself with official documents and news.</p>
						<p>5.9. The participant has the right to terminate this Agreement unilaterally without saving the game account. In this case, all costs associated with participation in the game, the participant is not compensated and will not be returned.</p>
						<p>5.10. The Organizer has the right to terminate this Agreement unilaterally, as well as to perform other actions that limit the possibilities in the Game, in relation to the participant or group of participants who are accomplices to the violations of the terms of this Agreement. At the same time, all game attributes, game inventory (<?php echo e($settings->domain); ?>) in the account and in the game account of a participant or a group of participants, as well as all expenses are not refundable and are not reimbursed, unless the Organizer deems at his discretion It is advisable to compensate for the costs of the participant or group of participants.</p>
						<p>5.11. The organizer and the Participant are relieved from liability in case of occurrence of force majeure circumstances (force majeure circumstances), which include, but are not limited to: natural disasters, wars, fire (fires), floods, explosions, terrorism, riots, civil unrest, acts of government or regulatory authority, hacker attacks, absences, non-functioning or malfunctioning of power supply, Internet service providers, communication networks or other systems, networks and services. The party in which such circumstances arose should, within a reasonable time and in an accessible manner, inform the other party of such circumstances.</p>
						<h2>Privacy</h2>
						<p>6.1. The confidentiality condition applies to information that the Organizer can receive about the Participant during his stay on the Game website and which can be correlated with this particular user. The organizer automatically receives and writes technical information from your browser to the server logs: IP address, address of the requested page, etc. The organizer can write cookies to the user's computer and subsequently use them. The Organizer guarantees that the data provided by the participant when registering at the Game will be used by the Organizer only within the Game.</p>
						<p>6.2. The organizer has the right to transfer personal information about the Participant to third parties only in the following cases:</p>
						<p>6.2.1. The participant expressed a desire to disclose this information;</p>
						<p>6.2.2. Without this, the Participant cannot use the desired product or service, in particular - information about names (nicknames), game attributes - may be available to other participants;</p>
						<p>6.2.3. This is required by international law and / or authorities in compliance with the legal procedure;</p>
						<p>6.2.4. The participant violates this Agreement and the rules of the playground.</p>
						<h2>Other provisions</h2>
						<p>7.1. The invalidity of a part or clause (sub-clause) of this agreement does not entail the invalidity of all other parts and clauses (sub-clauses).</p>
						<p>7.2. The term of this Agreement is set for the entire period of the playing field, that is, for an indefinite period, and does not imply the end date of this agreement.</p>
						<p>7.3. By registering and being on the playing court, the participant acknowledges that he has read, understood and fully accepts the terms of this Agreement, as well as the rules of the game and other official documents.</p>
						<p>7.4. Prohibited the use of temporary (one-time) mail, for the use of such account will be removed and measures will be taken. One-time mail is determined by the site administration. This definition fits the mail delivered to the purchased domains, the purchased domains are determined by the site administration.</p>
						<p>7.4.1. It is forbidden to register more than one account through the site. Such actions will result in account blocking</p>
						<p>7.4.2. Artificial balancing of balance with the help of scripts is strictly prohibited. The participant to be noticed will be blocked</p>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="modal fade" id="fairModal" tabindex="-1" role="dialog" aria-labelledby="tosModalLabel" aria-hidden="true">
		<div class="modal-dialog fair-modal modal-dialog-centered" role="document">
			<div class="modal-content">
				<button class="modal-close" data-dismiss="modal" aria-label="Close">
					<svg class="icon icon-close">
						<use xlink:href="/img/symbols.svg#icon-close"></use>
					</svg>
				</button>
				<div class="fair-modal__container">
					<h1><span>Fair game</span></h1><span>Our fair play system ensures that we cannot manipulate the outcome of the game. <br><br> Just as you cut a deck in a real casino. This implementation gives you complete peace of mind during the game, knowing that we cannot “adjust” the bets in our favor.<br><br></span>
					<div class="collapse-component">
						<div class="form-field">
							<div class="input-valid">
								<input class="input-field input-with-icon" name="hash" id="gameHash" placeholder="Enter hash">
								<div class="input-icon">
									<svg class="icon icon-coin">
										<use xlink:href="/img/symbols.svg#icon-fairness"></use>
									</svg>
								</div>
							</div>
						</div>
					</div>
					<button type="button" class="btn btn-rotate checkHash"><span>Check</span></button>
					<div class="fair-table" style="display: none;">
						<table class="table">
							<thead>
								<tr>
									<th><span># Game</span></th>
									<th><span>Generated number</span></th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td id="gameRound"></td>
									<td id="gameNumber"></td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="modal fade" id="userModal" tabindex="-1" role="dialog" aria-labelledby="userModalLabel" aria-hidden="true">
		<div class="modal-dialog user-modal modal-dialog-centered" role="document">
			<div class="modal-content">
				<button class="modal-close" data-dismiss="modal" aria-label="Close">
					<svg class="icon icon-close">
						<use xlink:href="/img/symbols.svg#icon-close"></use>
					</svg>
				</button>
				<div class="user-modal__container"></div>
			</div>
		</div>
	</div>
	<div class="modal fade" id="signinModal" tabindex="-1" role="dialog" aria-labelledby="signinModalLabel" aria-hidden="true">
		<div class="modal-dialog auth-modal modal-dialog-centered" role="document">
			<div class="modal-content">
				<button class="modal-close" data-dismiss="modal" aria-label="Close">
					<svg class="icon icon-close">
						<use xlink:href="/img/symbols.svg#icon-close"></use>
					</svg>
				</button>
				<div class="auth-modal__container">
					<h3 class="caption"><span>Log In</span></h3>
					<div class="auth-form">
						<div class="caption-line"><span>By soc. network</span></div>
						<div class="social-auth">
							<div class="line">
								<a href="/auth/vkontakte" class="btn btn-vk">VK</a>
								<a href="/auth/facebook" class="btn btn-fb">FB</a>
							</div>
							<a href="/auth/google" class="btn btn-gl">Google</a>
						</div>
						<div class="caption-line"><span>By login and password</span></div>
						<form method="POST" action="<?php echo e(route('login')); ?>">
							<div class="form-row">
								<div class="form-field">
									<div class="input-valid">
										<?php if($errors->any()): ?> <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
										<div class="valid inline top visible"><?php echo e($error); ?></div>
										<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?> <?php endif; ?>
									</div>
								</div>
							</div>
							<div class="form-row">
								<div class="form-field">
									<div class="input-valid">
										<input class="input-field" autocomplete="new-password" type="text" name="user_id" placeholder="Username" value="<?php echo e(old('user_id')); ?>" required autofocus>
									</div>
								</div>
							</div>
							<div class="form-row">
								<div class="form-field">
									<div class="input-valid">
										<input class="input-field" autocomplete="new-password" type="password" name="password" placeholder="Password" required>
									</div>
								</div>
							</div>
							<div class="form-row">
								<div class="form-field">
									<div class="input-valid">
										<?php echo NoCaptcha::display(['data-callback' => 'recaptchaCallback']); ?>

									</div>
								</div>
							</div>
							<button type="submit" class="btn btn-auth"><span>Log In</span></button>
						</form>
						<div class="change-form">
							<button type="button" class="btn" data-dismiss="modal" data-toggle="modal" data-target="#signupModal"><span>Create account</span></button>
							<div class="or"></div>
							<button type="button" class="btn" data-dismiss="modal" data-toggle="modal" data-target="#forgotModal"><span>Forgot password?</span></button>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="modal fade" id="signupModal" tabindex="-1" role="dialog" aria-labelledby="signupModalLabel" aria-hidden="true">
		<div class="modal-dialog auth-modal modal-dialog-centered" role="document">
			<div class="modal-content">
				<button class="modal-close" data-dismiss="modal" aria-label="Close">
					<svg class="icon icon-close">
						<use xlink:href="/img/symbols.svg#icon-close"></use>
					</svg>
				</button>
				<div class="auth-modal__container">
					<h3 class="caption"><span>Register on site</span></h3>
					<div class="auth-form">
						<div class="caption-line"><span>By soc. network</span></div>
						<div class="social-auth">
							<div class="line">
								<a href="/auth/vkontakte" class="btn btn-vk">VK</a>
								<a href="/auth/facebook" class="btn btn-fb">FB</a>
							</div>
							<a href="/auth/google" class="btn btn-gl">Google</a>
						</div>
						<div class="caption-line"><span>By login and password</span></div>
						<form method="POST" action="<?php echo e(route('register')); ?>">
							<div class="form-row">
								<div class="form-field">
									<div class="input-valid">
										<?php if($errors->any()): ?> <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
										<div class="valid inline top visible"><?php echo e($error); ?></div>
										<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?> <?php endif; ?>
									</div>
								</div>
							</div>
							<div class="form-row">
								<div class="form-field">
									<div class="input-valid">
										<input class="input-field" autocomplete="new-password" type="text" name="username" placeholder="Name" value="<?php echo e(old('username')); ?>" required autofocus>
									</div>
								</div>
							</div>
							<div class="form-row">
								<div class="form-field">
									<div class="input-valid">
										<input class="input-field" autocomplete="new-password" type="text" name="user_id" placeholder="Username" value="<?php echo e(old('user_id')); ?>" required>
									</div>
								</div>
							</div>
							<div class="form-row">
								<div class="form-field">
									<div class="input-valid">
										<input class="input-field" autocomplete="new-password" type="email" name="email" placeholder="E-mail" value="<?php echo e(old('email')); ?>" required>
									</div>
								</div>
							</div>
							<div class="form-row">
								<div class="form-field">
									<div class="input-valid">
										<input class="input-field" autocomplete="new-password" type="password" name="password" placeholder="Password" value="" required>
									</div>
								</div>
							</div>
							<div class="form-row">
								<div class="form-field">
									<div class="input-valid">
										<input class="input-field" autocomplete="new-password" type="password" name="password_confirmation" placeholder="Confirm password" value="" required>
									</div>
								</div>
							</div>
							<div class="form-row">
								<div class="form-field">
									<div class="input-valid">
										<?php echo NoCaptcha::display(['data-callback' => 'recaptchaCallback']); ?>

									</div>
								</div>
							</div>
							<button type="submit" class="btn btn-auth"><span>Register</span></button>
						</form>
						<div class="change-form">
							<button type="button" class="btn" data-dismiss="modal" data-toggle="modal" data-target="#signinModal"><span>Already registered?</span></button>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="modal fade" id="forgotModal" tabindex="-1" role="dialog" aria-labelledby="forgotModalLabel" aria-hidden="true">
		<div class="modal-dialog auth-modal modal-dialog-centered" role="document">
			<div class="modal-content">
				<button class="modal-close">
					<svg class="icon icon-close">
						<use xlink:href="/img/symbols.svg#icon-close"></use>
					</svg>
				</button>
				<div class="auth-modal__container">
					<h3 class="caption"><span>Reset password</span></h3>
					<div class="auth-form">
						<form method="POST" action="<?php echo e(route('password.email')); ?>">
							<div class="form-row">
								<div class="form-field">
									<div class="input-valid">
										<?php if($errors->any()): ?> <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
										<div class="valid inline top visible"><?php echo e($error); ?></div>
										<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?> <?php endif; ?>
									</div>
								</div>
							</div>
							<div class="form-row">
								<div class="form-field">
									<div class="input-valid">
										<input class="input-field" autocomplete="new-password" type="email" name="email" placeholder="You email" value="">
										<div class="valid inline top-right"></div>
									</div>
								</div>
							</div>
							<div class="form-row">
								<div class="form-field">
									<div class="input-valid">
										<?php echo NoCaptcha::display(['data-callback' => 'recaptchaCallback']); ?>

									</div>
								</div>
							</div>
							<button type="submit" class="btn btn-auth"><span>Send</span></button>
						</form>
						<div class="change-form">
							<button type="button" class="btn" data-dismiss="modal" data-toggle="modal" data-target="#signupModal"><span>Create account</span></button>
							<div class="or"></div>
							<button type="button" class="btn" data-dismiss="modal" data-toggle="modal" data-target="#signinModal"><span>Remember?</span></button>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php if(session('token')): ?>
    <script>
		$(document).ready(function() {
			setTimeout(function () {
				$('#confirmAgeModal').modal('hide');
				$('#resetModal').modal('show');
			}, 1000);
		});
	</script>
	<div class="modal fade" id="resetModal" tabindex="-1" role="dialog" aria-labelledby="resetModalLabel" aria-hidden="true">
		<div class="modal-dialog auth-modal modal-dialog-centered" role="document">
			<div class="modal-content">
				<button class="modal-close">
					<svg class="icon icon-close">
						<use xlink:href="/img/symbols.svg#icon-close"></use>
					</svg>
				</button>
				<div class="auth-modal__container">
					<h3 class="caption"><span>Update password</span></h3>
					<div class="auth-form">
						<form method="POST" action="<?php echo e(route('password.update')); ?>">
							<input type="hidden" name="token" value="<?php echo e(session('token')); ?>">
							<div class="form-row">
								<div class="form-field">
									<div class="input-valid">
										<?php if($errors->any()): ?> <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
										<div class="valid inline top visible"><?php echo e($error); ?></div>
										<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?> <?php endif; ?>
									</div>
								</div>
							</div>
							<div class="form-row">
								<div class="form-field">
									<div class="input-valid">
										<input class="input-field" autocomplete="new-password" type="email" name="email" placeholder="You email" value="<?php echo e($email ?? old('email')); ?>">
										<div class="valid inline top-right"></div>
									</div>
								</div>
							</div>
							<div class="form-row">
								<div class="form-field">
									<div class="input-valid">
										<input class="input-field" autocomplete="new-password" type="password" name="password" placeholder="Password" value="" required>
									</div>
								</div>
							</div>
							<div class="form-row">
								<div class="form-field">
									<div class="input-valid">
										<input class="input-field" autocomplete="new-password" type="password" name="password_confirmation" placeholder="Confirm password" value="" required>
									</div>
								</div>
							</div>
							<button type="submit" class="btn btn-auth"><span>Recover</span></button>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
    <?php endif; ?>
<?php if(session('error')): ?>
	<script>
	$.notify({
		type: 'error',
		message: "<?php echo e(session('error')); ?>"
	});
	</script>
<?php elseif(session('success')): ?>
	<script>
	$.notify({
		type: 'success',
		message: "<?php echo e(session('success')); ?>"
	});
	</script>
<?php endif; ?>
</body>
</html>
<?php endif; ?>
<?php /* /var/www/html/resources/views/layout.blade.php */ ?>