<?php

declare(strict_types=1);

namespace DoWStarterTheme\Widgets;

use DoWStarterTheme\View\Factory;
use DoWStarterTheme\Core\Theme;
use Illuminate\Support\Str;

/**
 * Widget class
 */
abstract class Widget extends \WP_Widget
{
	/**
	 * Optional widget description
	 *
	 * @var string
	 */
	protected $description;

	/**
	 * View name
	 *
	 * @var string
	 */
	protected $viewName;

	/**
	 * Widget constructor
	 */
	public function __construct()
	{
		$name = (string)preg_replace(
			'/Widget$/',
			'',
			substr(static::class, (int)strrpos(static::class, '\\') + 1)
		);

		$id = Str::kebab($name);
		$name = Str::title($name);
		$options = [
			'classname' => "widget-{$id}",
		];

		if (isset($this->description)) {
			$options['description'] = $this->description;
		}

		$this->viewName = $id;

		parent::__construct($id, $name, $options);
	}

	/**
	 * Output widget
	 *
	 * @phpcsSuppress SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter
	 *
	 * @param array<mixed> $args     Widget wrapper.
	 * @param array<mixed> $instance The widget options.
	 * @return void
	 */
	public function widget($args, $instance)
	{
		echo $args['before_widget'];

		$widgetId = 'widget_' . $args['widget_id'];

		$title = get_field('title', $widgetId);

		if (is_string($title) && $title !== '') {
			echo $args['before_title'] . get_field('title', $widgetId) . $args['after_title'];
		}

		$factory = Theme::getService(Factory::class);
		$factory->get("widgets.{$this->viewName}", $this->getData($widgetId, $args))->render();

		echo $args['after_widget'];
	}

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
		return [];
	}

	/**
	 * Creates widget form field
	 *
	 * @phpcsSuppress SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter
	 *
	 * @param array<mixed> $instance Widget options.
	 * @return string
	 */
	public function form($instance): string
	{
		return '';
	}
}
