<?
# Lifter010: TODO
?>
<!DOCTYPE html>
<html class="no-js">
<head>
    <meta charset="WINDOWS-1252">
    <title>
      <?= htmlReady(PageLayout::getTitle()) ?>
    </title>
    <?= PageLayout::getHeadElements() ?>

    <script src="<?= URLHelper::getScriptLink('dispatch.php/localizations/' . $_SESSION['_language']) ?>"></script>

    <script>
      STUDIP.ABSOLUTE_URI_STUDIP = "<?= $GLOBALS['ABSOLUTE_URI_STUDIP'] ?>";
      STUDIP.ASSETS_URL = "<?= $GLOBALS['ASSETS_URL'] ?>";
      String.locale = "<?= htmlReady(strtr($_SESSION['_language'], '_', '-')) ?>";
      <? if (is_object($GLOBALS['perm']) && $GLOBALS['perm']->have_perm('autor') && PersonalNotifications::isActivated()) : ?>
      STUDIP.jsupdate_enable = true;
      <? endif ?>
      STUDIP.URLHelper.parameters = <?= json_encode(studip_utf8encode(URLHelper::getLinkParams())) ?>;
      STUDIP.WYSIWYG = <?= \Config::GetInstance()->getValue('WYSIWYG') ? 'true' : 'false' ?>;
    </script>
</head>

<body id="<?= $body_id ? $body_id : PageLayout::getBodyElementId() ?>">
<?= $content_for_layout ?>
</body>
</html>
