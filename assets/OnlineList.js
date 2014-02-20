STUDIP.OnlineList = {
    startSidebarPanel: function () {
        var data = {
            // currently required
            "name": STUDIP.UNI_NAME_CLEAN,
            "iconURL": STUDIP.ASSETS_URL + "/images/icons/16/blue/seminar.png",
            "icon32URL": STUDIP.ASSETS_URL + "/images/icons/32/blue/seminar.png",
            "icon64URL": STUDIP.ASSETS_URL + "/images/icons/64/blue/seminar.png",

            // at least one of these must be defined
            "workerURL": STUDIP.ABSOLUTE_URI_STUDIP + "plugins.php/OnlineList/worker",
            "sidebarURL": STUDIP.ABSOLUTE_URI_STUDIP + "plugins.php/OnlineList/sidebar",
            //"shareURL": STUDIP.ABSOLUTE_URI_STUDIP+"plugins.php/OnlineList/share?url=%{url}",

            // status buttons are scheduled for Firefox 26 or 27
            //"statusURL": STUDIP.ABSOLUTE_URI_STUDIP + "/plugins.php/OnlineList/status",

            // social bookmarks are available in Firefox 26
            //"markURL": baseurl+"/mark.html?url=%{url}",
            // icons should be 32x32 pixels
            //"markedIcon": baseurl+"/unchecked.jpg",
            //"unmarkedIcon": baseurl+"/checked.jpg",

            // should be available for display purposes
            "description": "A list of my buddies being online in Stud.IP",
            "author": "Rasmus Fuhse, data-quest",
            "homepageURL": "https://www.studip.de",

            // optional
            "version": "0.1"
        };
        var event = new CustomEvent("ActivateSocialFeature");
        this.setAttribute("data-service", JSON.stringify(data));
        this.dispatchEvent(event);
        return false;
    }
};

jQuery(function () {
    jQuery("#nav_onlinelist").bind("click", STUDIP.OnlineList.startSidebarPanel);
});