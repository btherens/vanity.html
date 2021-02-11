<!DOCTYPE html>
<html>
	<?php
		include 'includes/head.php';
		include 'includes/style.php';
		include 'includes/script.php';
	?>

	<body>
		<nav id="sidebar-main" class="sidebar" ><br>
			<a class="sidebar-btn" onclick="sidebar_toggle(false);" title="close menu" >close menu</a>
			<div class="sidebar-title">
				<span><a href="/"><b>rss</b></a></span>
			</div>
			<div class="sidebar-menu-list">
				<a <?php echo ($active == 'Feeds') ? null : 'class="activebar" href="/feeds"'  ?> onclick="sidebar_toggle(false);" >feeds</a>
				</hr>
				<a <?php echo ($active == 'Account') ? null : 'class="activebar" href="/account"' ?> onclick="sidebar_toggle(false);" >account</a>
			</div>
			<div class="userpanel">
				<div class="profile-pic"><img height="56" src="/asset/profile/avatar.svg" /></div>
				<div class="text">
					<a><?php echo Account::username(); ?></a>
					<div class="role">builder</div>
					<div class="linkrow">
						<a href="/account" >account</a> | <a href="/account/logout" >logout</a>
					</div>
				</div>
			</div>
		</nav>
		<header class="sidebar-header">
			<a class="sidebar-hamburgerbtn" onclick="sidebar_toggle(true);" title="open menu" >☰</a>
			<span><a href="/"><b>rss</b></a></span>
		</header>
		<div id="sidebar-overlay" class="overlay" onclick="sidebar_toggle(false);" title="close menu"></div>

		<div class="content">
			<?php echo $pagecontent; ?>
		</div>
		<div class="footer">
			
			<p>© <?php echo date('Y'); ?> skyhold.app</p>
		</div>
		<div id="modal-container" class="modal<?php echo isset($modalcontent) ? ' show' : ''; ?>" onclick="modal_toggle(false);" >
			<div id="modalform-content" class="form" onclick="event.stopPropagation();" >
				<?php echo isset($modalcontent) ? $modalcontent : ''; ?>
			</div>
		</div>
	</body>
</html>
