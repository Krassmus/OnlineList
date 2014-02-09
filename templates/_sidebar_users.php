        <? if (count($contacts)) : ?>
        <? foreach ($contacts as $contact) : ?>
        <li>
            <div class="avatar" style="background-image: url('<?= Avatar::getAvatar($contact['user_id'])->getURL(Avatar::MEDIUM) ?>')"></div>
            <div class="name"><a href="<?= URLHelper::getLink("dispatch.php/profile", array('username' => $contact['username'])) ?>" target="_blank"><?= htmlReady($contact['name']) ?></a></div>
            <div class="actions">
                <a href="<?= URLHelper::getLink("sms_send.php", array('rec_uname' => $contact['username'])) ?>" target="_blank" title="<?= _("Nachricht verfassen") ?>">
                    <?= Assets::img("icons/16/blue/mail") ?>
                </a>
                <a href="<?= URLHelper::getLink("plugins.php/blubber/streams/global", array('mention' => $contact['username'])) ?>" target="_blank" title="<?= _("anblubbern") ?>">
                    <?= Assets::img("icons/16/blue/blubber") ?>
                </a>
            </div>
            <div style="clear: both;"></div>
        </li>
        <? endforeach ?>
        <? else : ?>
        <li><?= _("Keine Kontakte online") ?></li>
        <? endif ?>