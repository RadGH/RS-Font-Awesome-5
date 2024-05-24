(function() {

	// Allow ACF select2 to use html markup in the results
	if ( typeof acf === 'object' ) {
		acf.add_filter('select2_args', function(args) {
			args.templateSelection = function(selection) {
				var $selection = jQuery('<span class="acf-selection"></span>');

				$selection.html(acf.escHtml(selection.text));
				$selection.data('element', selection.element);

				return $selection;
			}

			args.templateResult = function(selection) {
				var $selection = jQuery('<span class="acf-selection"></span>');

				$selection.html(acf.escHtml(selection.text));
				$selection.data('element', selection.element);

				return $selection;
			}

			return args;
		});
	}

})();