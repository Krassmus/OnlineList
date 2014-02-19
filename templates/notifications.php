<style>
    body {
        height: 400px;
        background-color: #f8f8f8;
        background-image: none;
    }
    #notification_list > ul {
        list-style: none;
        padding: 0px;
        margin: 0px;
    }
    #notification_list > ul > li {
        background-color: #eeeeee;
        color: #333333;
        padding: 4px;
        line-height: 20px;
        transition-property: color;
        -moz-transition-property: background-color;
        transition-duration: 500ms;
        border-bottom: 1px solid grey;
    }
    #notification_list > ul > li:last-child {
        border-bottom: none;
    }
    #notification_list > ul > li:hover {
        background-color: white;
        transition-property: background-color;
        transition-duration: 500ms;
    }
    #notification_list > ul > li:after {
        display: table;
        content: "";
        line-height: 0;
        clear: both;
    }
    #notification_list > ul > li .options {
        float: right;
    }
    #notification_list .more {
        font-size: 0.8em;
        font-weight: bold;
        text-align: center;
    }
</style>
<script>
window.setInterval(function () {
    if (jQuery("#notification_list > ul > li").length === 0) {
        jQuery("#no_notifications").show();
    } else {
        jQuery("#no_notifications").hide();
    }
    jQuery("body").css("height", jQuery("#notification_container").height() + "px");
}, 500);
</script>

<div id="notification_container">
    <div id="notification_list">
        <ul>
            <? foreach ($notifications as $notification) : ?>
            <?= $template = $this->render_partial("_notification.php", compact('notification')); ?>
            <? endforeach ?>
        </ul>
    </div>
    <div id="no_notifications" style="<?= count($notifications) === 0 ? "" : "display: none; " ?>text-align: center; padding: 40px;"><?= _("Keine Benachrichtigungen") ?></div>
</div>

<div id="notification_marker" style="display: none;"></div>