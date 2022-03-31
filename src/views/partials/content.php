<?php

declare(strict_types=1);

?>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?> itemscope itemtype="https://schema.org/Article">
	<header class="entry-header">
		<h1 itemprop="name"><?php the_title(); ?></h1>
	</header>

	<div class="entry-content" itemprop="text">
		<?php the_content(); ?>
	</div>
</article>
