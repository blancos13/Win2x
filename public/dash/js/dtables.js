"use strict";
var KTDatatablesData = function() {

	var initTable1 = function() {
		var table = $('#dtable');

		// begin first table
		table.DataTable({
			responsive: true,
			searchDelay: 500
		});
	};
	var initTable2 = function() {
		var table = $('#dtable2');

		// begin first table
		table.DataTable({
			responsive: true,
			searchDelay: 500
		});
	};

	return {

		//main function to initiate the module
		init: function() {
			initTable1();
			initTable2();
		},

	};

}();

jQuery(document).ready(function() {
	KTDatatablesData.init();
});