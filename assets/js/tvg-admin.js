jQuery(document).ready(function ($) {
	var importBtn = $("#tvg_yt_sync");
	var loadingModal = $("#tvg_yt_stat");
	var ytSyncComplete = false;

	var syncData = function () {
		$.ajax({
			type: "get",
			url: ajaxurl,
			data: {
				action: "tvg_yt_sync",
			},
			success: function (resp) {
				if (resp.success) {
					if (resp.data.data) {
						console.log(resp.data.data);
						var requests = [];
						for (const [key, value] of Object.entries(
							resp.data.data
						)) {
							// console.log(`${value.id}: ${value.title}`);
							requests.push(syncInsert(value.id, value.title));
							// setTimeout(function () {
							// 	syncInsert(value.id, value.title);
							// }, key * 1000);
						}

						$.when.apply(undefined, requests).done(function () {
							console.log(arguments); //array of responses [0][data, status, xhrObj],[1][data, status, xhrObj]...
							ytSyncComplete = true;
							// loadingModal.hide();
						});

						// location.reload();
					} else {
						ytSyncComplete = true;
					}
				} else {
					ytSyncComplete = true;
					loadingModal.hide();
				}
				// console.log(data);
			},
			error: function () {
				ytSyncComplete = true;
			},
		});

		if (!ytSyncComplete) setTimeout(syncStat, 500);
	};

	var syncInsert = function (id, title) {
		$.ajax({
			type: "post",
			url: ajaxurl,
			data: {
				action: "tvg_yt_insert",
				id: id,
				title: title,
			},
			success: function (resp) {
				console.log(resp);
			},
		});
	};

	var syncStat = function () {
		$.ajax({
			type: "get",
			url: ajaxurl,
			data: {
				action: "tvg_yt_sync_stat",
			},
			success: function (data) {
				$("#tvg_yt_stat_summary").html(data.data);
				if (!ytSyncComplete) {
					setTimeout(syncStat, 500);
				}
			},
			error: function () {
				if (!ytSyncComplete) {
					setTimeout(syncStat, 500);
				}
			},
		});
	};

	// importBtn.on("click", function (e) {
	// 	e.stopImmediatePropagation();
	// 	e.preventDefault();
	// 	loadingModal.show();
	// 	syncData();
	// });
});
