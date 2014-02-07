<script>
window.setTimeout(function () {
    window.location.reload();
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
</style>
<div id="content">
    <div>
        <?= $quicksearch->render() ?>
    </div>
    <ul>
        <? if (count($contacts)) : ?>
        <? foreach ($contacts as $contact) : ?>
        <li>
            <div class="avatar" style="background-image: url('<?= Avatar::getAvatar($contact['user_id'])->getURL(Avatar::MEDIUM) ?>')"></div>
            <div class="name"><?= htmlReady($contact['name']) ?></div>
            <div style="clear: both;"></div>
        </li>
        <? endforeach ?>
        <? else : ?>
        <li><?= _("Keine Kontakte online") ?></li>
        <? endif ?>
    </ul>
</div>
