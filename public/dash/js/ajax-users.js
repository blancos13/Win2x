"use strict";
var KTDatatablesDataSourceAjaxServer = function() {

	var initTable1 = function() {
		var table = $('#ajax-users');

		// begin first table
		table.DataTable({
			responsive: true,
			searchDelay: 500,
			processing: true,
			serverSide: true,
			ajax: {
				url: "/admin/usersAjax",
				type: "POST"
			},
			columns: [
				{ data: "id", searchable: true },
				{ data: "username", visible: false, searchable: true },
				{ data: "username", searchable: false,
					render: function (data, type, row) {
						return '<img src="'+ row.avatar +'" style="width:26px;border-radius:50%;margin-right:10px;vertical-align:middle;">' + data;
					}

				},
				{ data: "balance", searchable: false,
					render: function (data, type, row) {
						return data + '$';
					}

				},
				{ data: "bonus", searchable: false,
					render: function (data, type, row) {
						return data + '$';
					}

				},
				{ data: null, searchable: false, orderable: false,
					render: function (data, type, row) {
						if(row.is_admin) return '<span class="kt-font-bold kt-font-danger">Admin</span>';
						if(row.is_moder) return '<span class="kt-font-bold kt-font-success">Moder</span>';
						if(row.is_youtuber) return '<span class="kt-font-bold kt-font-primary">YouTube`r</span>';
						return 'User';
					}

				},
				{ data: "ip", searchable: true, orderable: false,
					render: function (data, type, row) {
						return data;
					}

				},
				{ data: "ban", searchable: false, orderable: true,
					render: function (data, type, row) {
						if(data) return '<span class="kt-badge kt-badge--danger kt-badge--inline kt-badge--pill">Yes</span>';
						return '<span class="kt-badge kt-badge--success kt-badge--inline kt-badge--pill">No</span>';
					}

				},
				{ data: null, searchable: false, orderable: false,
					render: function (data, type, row) {
						return '<a href="/admin/user/'+ row.id +'" class="btn btn-sm btn-clean btn-icon btn-icon-md" title="Edit"><i class="la la-edit"></i></a>';
					}
				}
			]
		});
	};

	return {

		//main function to initiate the module
		init: function() {
			initTable1();
		},

	};

}();

jQuery(document).ready(function() {
	KTDatatablesDataSourceAjaxServer.init();
});