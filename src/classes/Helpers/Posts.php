<?php

declare(strict_types=1);

namespace DoWStarterTheme\Helpers;

/**
 * Posts helper class
 *
 * @phpstan-type Args array{
 *   limit?: int,
 *   post_type?: string,
 *   taxonomy?: string,
 *   query?: bool,
 * }
 */
class Posts
{
	/**
	 * Return WP_Query object with related posts
	 *
	 * @param  Args $args Arguments.
	 * @return \WP_Query|array<mixed>
	 */
	public static function related(array $args = [])
	{
		$defaults = [
			'limit' => 3,
			'post_type' => 'post',
			'taxonomy' => 'category',
			'query' => false,
		];

		$argsToPass = array_diff_key($args, $defaults);
		$args = wp_parse_args($args, $defaults);

		$currentPostId = get_the_ID();

		$queryArgs = [
			'posts_per_page' => $args['limit'],
			'post_type' => $args['post_type'],
		];

		$queryArgs['post__not_in'] = [ $currentPostId ];
		$queryArgs['orderby'] = 'rand';

		$terms = $currentPostId !== false ? get_the_terms($currentPostId, $args['taxonomy']) : false;

		if ($terms !== false && ! is_wp_error($terms)) {
			$queryArgs['tax_query'] = [
				[
					'taxonomy' => $args['taxonomy'],
					'field' => 'id',
					'terms' => wp_list_pluck($terms, 'term_id'),
				],
			];

			$takenPosts = get_posts($queryArgs);
			$numberOfPosts = count($takenPosts);

			if ($numberOfPosts < $args['limit']) {
				unset($queryArgs['tax_query']);

				if (count($takenPosts) > 0) {
					$missingPosts = $args['limit'] - $numberOfPosts;
					$postNotIn = wp_list_pluck($takenPosts, 'ID');
					$postNotIn[] = $currentPostId;
					$queryArgs['posts_per_page'] = $missingPosts;
					$queryArgs['post__not_in'] = $postNotIn;

					$randomPosts = get_posts($queryArgs);

					$relatedPosts = $postNotIn;

					if (count($randomPosts) > 0) {
						$relatedPosts = array_merge($postNotIn, wp_list_pluck($randomPosts, 'ID'));
					}

					unset($queryArgs['post__not_in']);

					$queryArgs['orderby'] = 'post__in';
					$queryArgs['post__in'] = $relatedPosts;
				}
			}
		}

		$queryArgs['posts_per_page'] = $args['limit'];

		$queryArgs = array_merge($queryArgs, $argsToPass);

		return $args['query'] === true ? new \WP_Query($queryArgs) : $queryArgs;
	}
}
