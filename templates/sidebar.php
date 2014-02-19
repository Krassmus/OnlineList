<script>
window.setInterval(function () {
    jQuery.ajax({
        'url': STUDIP.ABSOLUTE_URI_STUDIP + "plugins.php/onlinelist/sidebar_users",
        'success': function (data) {
            jQuery("#online_users").html(data);
        }
    });
}, 5000);

window.setTimeout(function () { window.location.reload(); }, 1000 * 60 * 25);

jQuery(".actions [data-chaturl]").live("click", function () {
    var chaturl = jQuery(this).data("chaturl");
    navigator.mozSocial.openChatWindow(chaturl);
    return false;
});
STUDIP.OnlineList = {
    askToAddContact: function (username, name) {
        var name = jQuery(name).text();
        name = name.substr(0, name.indexOf("("));
        jQuery("#add_user_question .name").text(name);
        jQuery("#add_user_question").slideDown();
    },
    dontAddContact: function () {
        jQuery("#add_user_question").slideUp();
        jQuery("input[name=new_contact]").val("");
        jQuery("#new_contact_1").val("");
    },
    addContact: function () {
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
    },
    openNotificationWindow: function () {
        navigator.mozSocial.openPanel(
            STUDIP.ABSOLUTE_URI_STUDIP + "plugins.php/onlinelist/notifications", 
            15
        );
        navigator.mozSocial.getWorker().port.onmessage = function(e) {
            jQuery("#debug_window").text("hey!");
        }
        navigator.mozSocial.getWorker().port.postMessage({
            topic: "jsupdater.register",
            data: {}
        });
    }
};
//STUDIP.jsupdate_enable = false;
jQuery(function () {
    jQuery("#notification_marker").bind("click", STUDIP.OnlineList.openNotificationWindow);
});
</script>
<style>
    body {
        background-color: #E7EBF1;
        background-image: none;
    }
    #content ul {
        margin: 0px;
        padding: 0px;
    }
    #content ul > li {
        padding: 5px;
        border-radius: 9px;
        border: solid white 3px;
        box-shadow: 0px 0px 4px lightgrey;
        background-color: #eeeeee;
        display: block;
        width: calc(100% - 26px);
        margin: 3px;
    }
    #content .avatar {
        background-position: center center;
        background-repeat: no-repeat;
        background-size: 100% auto;
        height: 38px;
        width: 38px;
        display: inline-block;
        float: left;
    }
    #content .name {
        text-align: center;
    }
    #content .actions {
        text-align: center;
    }
    
    #new_contact_1 {
        color: white;
        background-color: #899AB9;
        width: calc(100% - 36px);
        border: none;
    }
    
    #add_user_question button.button {
        min-width: 70px;
    }
</style>
<div id="content">
    <div style="text-align: center; background-color: #899AB9; color: #eeeeee; padding: 2px; padding-top: 0px; border-bottom: #1E3E70 1px solid;">
        <? $notifications = PersonalNotifications::getMyNotifications() ?>
        <? $lastvisit = (int) UserConfig::get($GLOBALS['user']->id)->getValue('NOTIFICATIONS_SEEN_LAST_DATE') ?>
        <? foreach ($notifications as $notification) {
            if ($notification['mkdate'] > $lastvisit) {
                $alert = true;
            }
        } ?>
        <a id="notification_marker" style="display: inline-block; cursor: pointer;"><?= count($notifications) ?></a>
        <div class="list below" id="notification_list" style="display: none;">
            <ul>
            </ul>
        </div>
        
        <?= $quicksearch->render() ?>
        <div id="add_user_question" style="display: none; border-top: #aaaaaa solid 1px; font-size: 0.8em;">
            <a class="name"></a><?= _(" als Kontakt hinzufügen?") ?>
            <div>
                <?= \Studip\Button::createAccept(_("Ja"), "", array('onclick' => "STUDIP.OnlineList.addContact();")) ?><?= \Studip\Button::createCancel(_("Nein"), "", array('onclick' => "STUDIP.OnlineList.dontAddContact();")) ?>
            </div>
        </div>
    </div>
    <ul id="online_users">
        <?= $this->render_partial("_sidebar_users.php", compact('contacts', 'actions')) ?>
    </ul>
</div>


<div id="debug_window">blubb</div>