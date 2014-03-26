<script>
/*window.setInterval(function () {
    jQuery.ajax({
        'url': STUDIP.ABSOLUTE_URI_STUDIP + "plugins.php/onlinelist/sidebar_users",
        'success': function (data) {
            jQuery("#online_users").html(data);
        }
    });
}, 5000);*/

jQuery(".actions [data-chaturl]").live("click", function () {
    var chaturl = jQuery(this).data("chaturl");
    navigator.mozSocial.openChatWindow(chaturl);
    return false;
});

function debugWorker(topic, data) {
    jQuery("<li>").text(topic).append(jQuery("<p>").text(typeof data === "string" ? "(string)" + data : JSON.stringify(data))).appendTo("#debug_window");
}

STUDIP.OnlineList = {
    updateUsers: function (data) {
        if (data.userlist) {
            jQuery("#online_users").html(data.userlist);
        }
        if (typeof data.notregistered !== "undefined") {
            jQuery("input[name=login_ticket]").val() || jQuery("input[name=login_ticket]").val(data.ticket);
            jQuery("input[name=security_token]").val() || jQuery("input[name=security_token]").val(data.CSRFProtectionToken);
            jQuery("#login").show();
            jQuery("#online_users").hide();
        }
    },
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
        
    },
    registerAtWorker: function () {
        navigator.mozSocial.getWorker().port.onmessage = function (e) {
            var topic = e.data.topic;
            var data = e.data.data;
            if (topic === "jsupdater.data") {
                STUDIP.JSUpdater.processUpdate(typeof data === "string" ? JSON.parse(data) : data);
            }
        };
        navigator.mozSocial.getWorker().port.onerror = function (e) {
            var topic = e.data.topic;
            var data = e.data.data;
            jQuery("#error_window").text(topic);
        }
        
        navigator.mozSocial.getWorker().port.postMessage({
            topic: "jsupdater.register",
            data: {}
        });
    }
};
STUDIP.jsupdate_enable = false;
jQuery(function () {
    jQuery("#notification_marker").bind("click", STUDIP.OnlineList.openNotificationWindow);
    STUDIP.OnlineList.registerAtWorker();
});
</script>
<style>
    body {
        background-color: #E7EBF1;
        background-image: none;
    }
    #topbar {
        text-align: center;
        background-color: #899AB9;
        color: #eeeeee;
        padding: 2px;
        padding-top: 0px;
        border-bottom: #1E3E70 1px solid;
        position: fixed;
        top: 0px;
        width: 100%;
        box-shadow: 0 0 15px rgba(0, 0, 0, 0.3);
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
    
    #login {
        margin-top: 180px;
    }
    #loginform > * {
        text-align: center;
        width: 100px;
    }
    #loginform input {
        width: 80%;
    }
</style>
<div id="content">
    <div id="topbar">
        <? $notifications = PersonalNotifications::getMyNotifications() ?>
        <? $lastvisit = (int) UserConfig::get($GLOBALS['user']->id)->getValue('NOTIFICATIONS_SEEN_LAST_DATE') ?>
        <? foreach ($notifications as $notification) {
            if ($notification['mkdate'] > $lastvisit) {
                $alert = true;
            }
        } ?>
        <? $lastvisit = (int) UserConfig::get($GLOBALS['user']->id)->getValue('NOTIFICATIONS_SEEN_LAST_DATE') ?>
        <a id="notification_marker" style="display: inline-block; cursor: pointer;" data-lastvisit="<?= $lastvisit ?>"><?= count($notifications) ?></a>
        <div class="list below" id="notification_list" style="display: none;">
            <ul>
            </ul>
        </div>
        <? if (PersonalNotifications::isAudioActivated()) : ?>
        <audio id="audio_notification" preload="none">
            <source src="<?= Assets::url('sounds/blubb.ogg') ?>" type="audio/ogg">
            <source src="<?= Assets::url('sounds/blubb.mp3') ?>" type="audio/mpeg">
        </audio>
        <? endif ?>
        
        <?= $quicksearch->render() ?>
        <div id="add_user_question" style="display: none; border-top: #aaaaaa solid 1px; font-size: 0.8em;">
            <a class="name"></a><?= _(" als Kontakt hinzufügen?") ?>
            <div>
                <?= \Studip\Button::createAccept(_("Ja"), "", array('onclick' => "STUDIP.OnlineList.addContact();")) ?><?= \Studip\Button::createCancel(_("Nein"), "", array('onclick' => "STUDIP.OnlineList.dontAddContact();")) ?>
            </div>
        </div>
    </div>
    <div style="min-height: 28px;"></div>
    <ul id="online_users"<?= $GLOBALS['user']->id === "nobody" ? ' style="display: none;"' : "" ?>>
        <?= $this->render_partial("_sidebar_users.php", compact('contacts', 'actions')) ?>
    </ul>
    <div id="login"<?= $GLOBALS['user']->id !== "nobody" ? ' style="display: none;"' : "" ?>>
        <?= $this->render_partial("_login.php")  ?>
    </div>
</div>


<ul id="debug_window"></ul>
<div id="error_window" style='color: red'></div>
