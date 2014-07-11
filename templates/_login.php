<form name="loginform" id="loginform">
    <input type="hidden" name="login_ticket" value="<?= Seminar_Session::get_ticket() ?>">
    <?= CSRFProtection::tokenTag() ?>
    <input type="hidden" name="resolution"  value="">
    <input type="hidden" name="device_pixel_ratio" value="1">
    
    <div><?= _("Melden Sie sich an bei Stud.IP") ?></div>
    <div><input type="text" name="loginname" id="login_loginname" placeholder="<?= _("Nutzername") ?>"></div>
    <div><input type="password" name="password" id="login_password"></div>
    <?= Studip\Button::create(_('Anmelden'), _('Login')); ?>
</form>

<script type="text/javascript" language="javascript">
//<![CDATA[
jQuery(function () {
    jQuery('form[name=loginform]').submit(function () {
        jQuery('input[name=resolution]', this).val( screen.width + 'x' + screen.height );
        jQuery('input[name=device_pixel_ratio]').val(window.devicePixelRatio || 1);
        return;
    });
    jQuery("#login_loginname").focus();
});
// -->
</script>