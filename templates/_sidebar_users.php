        <? if (count($contacts)) : ?>
        <? foreach ($contacts as $contact) : ?>
        <li>
            <div class="avatar" style="background-image: url('<?= Avatar::getAvatar($contact['user_id'])->getURL(Avatar::MEDIUM) ?>')"></div>
            <div class="name"><a href="<?= URLHelper::getLink("dispatch.php/profile", array('username' => $contact['username'])) ?>" target="_blank" title="<?= sprintf(_("Letztes Lebenszeichen vor %s"), $contact['inactive_seconds'] <= 60 ? $contact['inactive_seconds']." Sekunden" : floor($contact['inactive_seconds'] / 60)." Minuten") ?>"><?= htmlReady($contact['name']) ?></a></div>
            <div class="actions">
            <? foreach ($actions->getSubNavigation() as $action_nav) : ?>
                <? if ($action_nav->getImage()) : ?>
                <? $url = str_replace(array(':user_id',':username','%3Auser_id','%3Ausername'), array($contact['user_id'], $contact['username'],$contact['user_id'], $contact['username']), $action_nav->getURL()) ?>
                <? if (count($action_nav->getPostVariables()) > 0) : ?>
                <? $image = $action_nav->getImage() ?>
                <form action="<?= URLHelper::getLink($url) ?>" target="_blank" method="POST" style="display: inline">
                    <?= CSRFProtection::tokenTag() ?>
                    <? foreach ($action_nav->getPostVariables() as $name => $value) : ?>
                    <? $value = str_replace(array(':user_id',':username','%3Auser_id','%3Ausername'), array($contact['user_id'], $contact['username'],$contact['user_id'], $contact['username']), $value) ?>
                    <input type="hidden" name="<?= htmlReady($name) ?>" value="<?= htmlReady($value) ?>">
                    <? endforeach ?>
                    <button type="submit" style="border: none; background: none; padding: 0px; cursor: pointer;" title="<?= htmlReady($action_nav->getTitle()) ?>">
                        <? if ($image['data-chaturl']) {
                            $image['data-chaturl'] = str_replace(array(':user_id',':username','%3Auser_id','%3Ausername'), array($contact['user_id'], $contact['username'],$contact['user_id'], $contact['username']), $image['data-chaturl']);
                        }
                        echo Assets::img($image['src'], array_map("htmlready", $image));
                        ?>
                    </button>
                </form>
                <? else : ?>
                <a href="<?= URLHelper::getLink($url) ?>" target="_blank" title="<?= htmlReady($action_nav->getTitle()) ?>">
                    <?
                    $image = $action_nav->getImage();
                    if ($image['data-chaturl']) {
                        $image['data-chaturl'] = str_replace(array(':user_id',':username','%3Auser_id','%3Ausername'), array($contact['user_id'], $contact['username'],$contact['user_id'], $contact['username']), $image['data-chaturl']);
                    }
                    echo Assets::img($image['src'], array_map("htmlready", $image));
                    ?>
                </a>
                <? endif ?>
                <? endif ?>
            <? endforeach ?>
            </div>
            <div style="clear: both;"></div>
        </li>
        <? endforeach ?>
        <? else : ?>
        <li><?= _("Keine Kontakte online") ?></li>
        <? endif ?>