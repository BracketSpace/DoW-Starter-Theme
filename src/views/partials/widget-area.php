<?php
/**
 * @var \DoWStarterTheme\View\View $this
 */

declare(strict_types=1);

?>

<div class="widget-area" id="<?php $this->attr('id'); ?>">
	<?php dynamic_sidebar($this->get('id')); ?>
</div>
