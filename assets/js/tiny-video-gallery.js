document.onreadystatechange = function () {
	if (document.readyState == "interactive") {
		const filterButtons =
			document.querySelector("#tpvg_filter-btns").children;
		const items = document.querySelector(".tpvg_gallery-items").children;
		const galleryItems = document.querySelectorAll(".tpvg_gallery-item");

		if (filterButtons) {
			for (let i = 0; i < filterButtons.length; i++) {
				filterButtons[i].addEventListener("click", function () {
					for (let j = 0; j < filterButtons.length; j++) {
						filterButtons[j].classList.remove("active");
					}
					this.classList.add("active");
					const target = this.getAttribute("data-target");

					for (let k = 0; k < items.length; k++) {
						items[k].style.display = "none";
						var cats = items[k].getAttribute("data-id").split(",");
						if (jQuery.inArray(target, cats) !== -1) {
							items[k].style.display = "block";
						}
						if (target == "all") {
							items[k].style.display = "block";
						}
					}
				});
			}
		}

		if (filterButtons) {
			galleryItems.forEach(function (item) {
				var configObject = {
					sourceUrl: item.getAttribute("data-video"),
					triggerElement: "#" + item.getAttribute("id"),
					progressCallback: function () {
						console.log("Callback Invoked.");
					},
				};

				var videoBuild = new YoutubeOverlayModule(configObject);
				videoBuild.activateDeployment();
			});
		}
	}
};
