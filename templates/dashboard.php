<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$bakery_ordering_pastry = plugins_url( 'assets/images/croissants.png', dirname( __FILE__ ) );
?>
<div class="bo-dashboard">
	<aside class="bo-sidebar">
		<a class="bo-brand" href="<?php echo esc_url( add_query_arg( 'bakery_dashboard', '1', home_url( '/' ) ) ); ?>" aria-label="Hos Bakery dashboard"><span class="bo-wheat" aria-hidden="true">❧</span><span class="bo-wordmark">Hos Bakery</span><span class="bo-tagline">WHOLESALE ORDERING</span></a>
		<nav class="bo-nav" aria-label="Bakery navigation">
		<?php foreach ( array( 'Dashboard' => 'home', 'My Orders' => 'calendar', 'Standing Orders' => 'layers', 'Products' => 'croissant', 'Order History' => 'history', 'Account' => 'user' ) as $label => $icon ) : ?>
			<?php if ( 'Products' === $label ) : ?><a class="bo-nav-item" href="<?php echo esc_url( add_query_arg( array( 'bakery_dashboard' => '1', 'view' => 'products' ), home_url( '/' ) ) ); ?>"><?php echo bakery_ordering_icon( $icon ); ?><span><?php echo esc_html( $label ); ?></span></a><?php else : ?><button type="button" class="bo-nav-item<?php echo 'Dashboard' === $label ? ' is-active' : ''; ?>" <?php echo 'Dashboard' === $label ? 'aria-current="page"' : 'data-bo-preview'; ?>><?php echo bakery_ordering_icon( $icon ); ?><span><?php echo esc_html( $label ); ?></span></button><?php endif; ?>
		<?php endforeach; ?>
		</nav>
		<div class="bo-help"><?php echo bakery_ordering_icon( 'help' ); ?><h2>Need Help?</h2><p>Contact our bakery team.</p><button type="button" data-bo-preview><?php echo bakery_ordering_icon( 'mail' ); ?> Send a Message</button></div>
	</aside>
	<main class="bo-main">
		<header class="bo-header">
			<img class="bo-hero-pastry" src="<?php echo esc_url( $bakery_ordering_pastry ); ?>" alt="">
			<button class="bo-account" type="button" data-bo-preview><?php echo bakery_ordering_icon( 'user' ); ?> Main Street Café <span aria-hidden="true">⌄</span></button>
			<div class="bo-greeting"><p>Welcome back,</p><h1>Main Street Café</h1><p class="bo-intro">Manage your bakery orders, update deliveries, and view your schedule.</p></div>
		</header>
		<div class="bo-top-grid">
			<section class="bo-card bo-next" aria-labelledby="bo-next-title">
				<div class="bo-card-heading"><h2 id="bo-next-title"><?php echo bakery_ordering_icon( 'truck' ); ?>Next Delivery</h2><span class="bo-badge bo-editable">Editable until Mon, Nov 10 at 2:00 PM</span></div>
				<div class="bo-next-body"><div><h3>Tuesday, Nov 11</h3><p>3 products <span>•</span> 44 total items</p></div><img src="<?php echo esc_url( $bakery_ordering_pastry ); ?>" alt="Golden butter croissants"></div>
				<div class="bo-buttons"><button type="button" class="bo-button bo-primary" data-bo-preview><?php echo bakery_ordering_icon( 'edit' ); ?>Edit This Order</button><button type="button" class="bo-button" data-bo-preview><?php echo bakery_ordering_icon( 'calendar' ); ?>Skip This Delivery</button></div>
			</section>
			<section class="bo-card bo-standing" aria-labelledby="bo-standing-title">
				<div class="bo-card-heading"><h2 id="bo-standing-title"><?php echo bakery_ordering_icon( 'layers' ); ?>Your Standing Order</h2><span class="bo-badge bo-peach">Every Tuesday</span></div>
				<ul class="bo-products">
				<?php foreach ( array( array( 'Croissants', '24', 'croissant' ), array( 'Cinnamon Rolls', '12', 'roll' ), array( 'Sourdough Loaves', '8', 'bread' ) ) as $product ) : ?>
					<li><span class="bo-product-art bo-art-<?php echo esc_attr( $product[2] ); ?>" aria-hidden="true"><img src="<?php echo esc_url( plugins_url( 'assets/images/' . array( 'croissant' => 'croissants.png', 'roll' => 'cinnamon-roll.png', 'bread' => 'sourdough.png' )[ $product[2] ], dirname( __FILE__ ) ) ); ?>" alt=""></span><span><?php echo esc_html( $product[0] ); ?></span><strong><?php echo esc_html( $product[1] ); ?></strong></li>
				<?php endforeach; ?>
				</ul><button type="button" class="bo-button bo-full" data-bo-preview><?php echo bakery_ordering_icon( 'edit' ); ?>Edit Standing Order</button>
			</section>
		</div>
		<div class="bo-bottom-grid">
			<section class="bo-card bo-upcoming" aria-labelledby="bo-upcoming-title"><div class="bo-card-heading"><h2 id="bo-upcoming-title"><?php echo bakery_ordering_icon( 'calendar' ); ?>Upcoming Deliveries</h2><button class="bo-text-button" type="button" data-bo-preview>View Calendar <?php echo bakery_ordering_icon( 'arrow' ); ?></button></div>
			<div class="bo-deliveries"><?php foreach ( array( 'Tue, Nov 11', 'Tue, Nov 18', 'Tue, Nov 25' ) as $index => $date ) : ?><button class="bo-delivery" type="button" data-bo-preview><?php echo bakery_ordering_icon( 'calendar' ); ?><strong><?php echo esc_html( $date ); ?></strong><?php if ( 0 === $index ) : ?><span class="bo-badge">Next Delivery</span><?php endif; ?><span class="bo-delivery-count">3 products <span>•</span> 44 items</span><?php echo bakery_ordering_icon( 'chevron' ); ?></button><?php endforeach; ?></div></section>
			<section class="bo-card bo-quick" aria-labelledby="bo-quick-title"><div class="bo-card-heading"><h2 id="bo-quick-title"><?php echo bakery_ordering_icon( 'bolt' ); ?>Quick Actions</h2></div><div class="bo-actions"><?php foreach ( array( 'Add a One-Time Order' => 'cart', 'Pause Deliveries' => 'pause', 'View Order History' => 'history' ) as $label => $icon ) : ?><button type="button" data-bo-preview><?php echo bakery_ordering_icon( $icon ); ?><span><?php echo esc_html( $label ); ?></span><?php echo bakery_ordering_icon( 'chevron' ); ?></button><?php endforeach; ?></div></section>
		</div>
		<p class="bo-preview-note">Design preview · Sample account, dates, and orders</p>
		<div class="bo-toast" role="status" aria-live="polite" hidden>This is a design preview. This feature will be added in the next phase.</div>
	</main>
</div>
