<?php

declare(strict_types=1);

use DoWStarterTheme\Helpers\View as v;

$items = v::get('items');
?>

<?php if (count($items) > 1) : ?>
	<ul class="social-links-menu">
		<?php foreach ($items as $item) : ?>
			<li class="social-links-item">
				<a class="link" href="<?php echo $item['link']['url']; ?>" target="<?php echo $item['link']['target']; ?>" aria-label="<?php echo $item['link']['title']; ?>">
					<i class="fab fa-<?php echo esc_attr($item['icon']); ?>"></i>
				</a>
			</li>
		<?php endforeach; ?>
	</ul>
<?php endif; ?>
