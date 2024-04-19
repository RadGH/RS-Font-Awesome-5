let RS_Font_Awesome_5 = new (function() {
	let r = this;

	// Only load if one of the following elements is found
	if ( ! document.querySelector('.rs-font-awesome-5-display-list') ) {
		return;
	}

	// Called after this file has loaded
	r.init = function() {

		// Bind event handler to clicking ".rs-icon" to show the copy links
		r.bind_icon_links();

		// Bind event handler to "copy links"
		r.bind_copy_links();

	};

	// Bind event handler to clicking ".rs-icon" to show the copy links
	r.bind_icon_links = function() {
		// Bind click event to .rs-icon without jquery
		document.addEventListener('click', function(event) {
			let icon = event.target.closest('.rs-icon');
			if ( ! icon ) return;

			let make_active = ! icon.classList.contains('rs-icon--active');

			if ( make_active ) {
				// Close all other open icons
				let icons = document.querySelectorAll('.rs-icon--active');
				icons.forEach(function(i) {
					if ( i !== icon ) {
						i.classList.remove('rs-icon--active');
					}
				});

				// Open the clicked icon
				icon.classList.add('rs-icon--active');
			}else{

				// Do not close if clicking on a button or the name area
				if ( event.target.closest('button, .rs-icon--name') ) return;

				// Close the clicked icon
				icon.classList.remove('rs-icon--active');

			}
		});
	};

	// Bind event handler to "copy links".
	// This copies the name, shortcode, or HTML of an icon from the [rs_icons_display] shortcode
	r.bind_copy_links = function() {
		// Bind click event to button.rs-icon--copy-link without jquery
		document.addEventListener('click', function(event) {
			if ( ! event.target.matches('.rs-icon--copy-link') ) return;

			r.copy_link(event.target);
		});
	};

	r.copy_link = function( element ) {
		// Copy the "data-copy" attribute to clipboard by using a hidden <textarea> element
		let text = element.getAttribute('data-copy');
		let textarea = document.createElement('textarea');
		textarea.value = text;
		document.body.appendChild(textarea);
		textarea.select();
		document.execCommand('copy');
		document.body.removeChild(textarea);

		// Store the default button text
		let previous_button_text = element.getAttribute('data-button-text');

		if ( ! previous_button_text ) {
			previous_button_text = element.innerHTML;
			element.setAttribute('data-button-text', previous_button_text);
		}

		// Change the text of the button to "Copied!" temporarily
		element.innerHTML = 'Copied!';

		setTimeout(function() {
			// Change back to previous button text
			element.innerHTML = previous_button_text;
		}, 1000);
	};

	// Call init after this file has loaded
	r.init();

	return r;
})();