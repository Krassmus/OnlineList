<script>
window.setInterval(function () {
    jQuery.ajax({
        'url': STUDIP.ABSOLUTE_URI_STUDIP + "plugins.php/onlinelist/sidebar_users",
        'success': function (data) {
            console.log(data);
            jQuery("#online_users").html(data);
        }
    });
}, 5000);
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
</style>
<div id="content">
    <div style="text-align: center; margin: 2px;">
        <?= $quicksearch->render() ?>
    </div>
    <ul id="online_users">
        <?= $this->render_partial("_sidebar_users.php", compact('contacts')) ?>
    </ul>
</div>
