<?php

declare(strict_types=1);

namespace DoWStarterTheme\Widgets;

/**
 * Social Links Widget class
 */
class SocialLinksWidget extends Widget
{
	/**
	 * Returns prepared data for the view.
	 *
	 * @phpcsSuppress SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter
	 *
	 * @param  string       $id   Widget ID for ACF.
	 * @param  array<mixed> $args Widget args.
	 * @return array<mixed>
	 */
	protected function getData(string $id, array $args): array
	{
		return [
			'items' => get_field('links', $id),
		];
	}
}
