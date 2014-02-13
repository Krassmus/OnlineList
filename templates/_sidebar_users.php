        <? if (count($contacts)) : ?>
        <? foreach ($contacts as $contact) : ?>
        <li>
            <div class="avatar" style="background-image: url('<?= Avatar::getAvatar($contact['user_id'])->getURL(Avatar::MEDIUM) ?>')"></div>
            <div class="name"><a href="<?= URLHelper::getLink("dispatch.php/profile", array('username' => $contact['username'])) ?>" target="_blank"><?= htmlReady($contact['name']) ?></a></div>
            <div class="actions">
                <? foreach ($actions->getSubNavigation() as $action_nav) : ?>
                <? if ($action_nav->getImage()) : ?>
                <? $url = str_replace(array(':user_id',':username','%3Auser_id','%3Ausername'), array($contact['user_id'], $contact['username'],$contact['user_id'], $contact['username']), $action_nav->getURL()) ?>
                <a href="<?= URLHelper::getLink($url) ?>" target="_blank">
                    <?
                    $image = $action_nav->getImage();
                    if ($image['data-chaturl']) {
                        $image['data-chaturl'] = str_replace(array(':user_id',':username','%3Auser_id','%3Ausername'), array($contact['user_id'], $contact['username'],$contact['user_id'], $contact['username']), $image['data-chaturl']);
                    }
                    echo Assets::img($image['src'], array_map("htmlready", $image));
                    ?>
                </a>
                <? endif ?>
                <? endforeach ?>
            </div>
            <div style="clear: both;"></div>
        </li>
        <? endforeach ?>
        <? else : ?>
        <li><?= _("Keine Kontakte online") ?></li>
        <? endif ?>