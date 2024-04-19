let RS_Icon_Search = new (function() {
	let r = this;

	// Called after this file has loaded
	r.init = function() {
	};

	// Get an array of icon search elements on the page
	r.get_search_elements = function() {
		return document.querySelectorAll('.rs-icon-search');
	};

	// Call init after this file has loaded
	r.init();

	return r;
})();