
STUDIP.OnlineList.sharedWorker = null;

STUDIP.OnlineList.initSharedWorker = function () {
    STUDIP.OnlineList.sharedWorker = new SharedWorker();
};

  
        /***** used by the sidebar *****/

STUDIP.OnlineList.askToAddContact = function (username, name) {
    var name = jQuery(name).text();
    name = name.substr(0, name.indexOf("("));
    jQuery("#add_user_question .name").text(name);
    jQuery("#add_user_question").slideDown();
};
STUDIP.OnlineList.dontAddContact = function () {
    jQuery("#add_user_question").slideUp();
    jQuery("input[name=new_contact]").val("");
    jQuery("#new_contact_1").val("");
};
STUDIP.OnlineList.addContact = function () {
    jQuery.ajax({
        'url': STUDIP.ABSOLUTE_URI_STUDIP + "plugins.php/onlinelist/add_contact",
        'data': {
            'username': jQuery("input[name=new_contact]").val()
        },
        'type': "post",
        'success': function () {
            jQuery("#add_user_question").slideUp();
            jQuery("input[name=new_contact]").val("");
            jQuery("#new_contact_1").val("");
        }
    });
};
STUDIP.OnlineList.openNotificationWindow = function () {
    navigator.mozSocial.openPanel(
        STUDIP.ABSOLUTE_URI_STUDIP + "plugins.php/onlinelist/notifications", 
        15
    );
};