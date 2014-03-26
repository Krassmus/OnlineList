<style>
    body {
        background-color: #E7EBF1;
        background-image: none;
    }
    textarea.blubber {
        padding: 5px;
        border-radius: 9px;
        border: solid white 3px;
        box-shadow: 0px 0px 4px lightgrey;
        background-color: #eeeeee;
        display: block;
        width: calc(100% - 26px);
        margin: 3px;
    }
</style>
<script>
STUDIP.jsupdate_enable = false;
jQuery(function () { 
    jQuery("textarea.blubber").focus(); 
    STUDIP.Blubber.makeTextareasAutoresizable();
});
</script>


<textarea class="blubber" id="new_posting">


@<?= htmlReady(Request::get("username")) ?>
</textarea>
