declare module 'config/typography.json' {
	type CSSProps = Record<string, string | number>;

	const typography: {
		defaultStyle?: string;
		headings?: CSSProps;
		textStyles?: Record<string, CSSProps>;
	};

	export default typography;
}
