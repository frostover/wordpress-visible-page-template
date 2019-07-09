( function( wp ) {
	let registerPlugin = wp.plugins.registerPlugin;
	let PluginSidebar = wp.editPost.PluginPostStatusInfo;
	let el = wp.element.createElement;

	registerPlugin( 'wpengine-show-page-template', {
		render: function() {
			return el( PluginSidebar,
				{
					name: 'wpengine-show-page-template',
					title: 'WPEngine Show Template',
				},
				el( 'div',
					{ className: 'plugin-sidebar-content' },
					el( 'span', { class: 'wpengine-show-page-template-label' }, 'Template' ),
					el( 'span', { class: 'wpengine-show-page-template-value' }, wp_template )
				)
			);
		}
	} );
} )( window.wp );