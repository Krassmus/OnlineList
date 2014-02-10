<?php

class OnlineList extends StudIPPlugin implements SystemPlugin {
    
    public function __construct() {
        parent::__construct();
        PageLayout::addScript($this->getPluginURL()."/assets/OnlineList.js");
        PageLayout::addStylesheet($this->getPluginURL()."/assets/OnlineList.css");
        
        $activator = new Navigation(_("OnlineListe"), "#");
        $activator->setImage(Assets::image_path("header/community.png"));
        Navigation::addItem("/onlinelist", $activator);
        
        //Die voreingestellten Aktionen der Nutzer
        $nav = new Navigation(_("Nachricht verfassen"), URLHelper::getURL("sms_send.php", array('rec_uname' => ':username')));
        $nav->setImage(Assets::image_path("icons/16/blue/mail.png"), array('title' => _("Nachricht verfassen")));
        Navigation::addItem("/onlinelist/messaging", $nav);
        
        $nav = new Navigation(_("anblubbern"), URLHelper::getURL("plugins.php/blubber/streams/global?mention=:username", array('mention' => ':username')));
        $nav->setImage(Assets::image_path("icons/16/blue/blubber.png"), array('title' => _("anblubbern")));
        Navigation::addItem("/onlinelist/blubber", $nav);
    }
    
    public function sidebar_action() {
        $query = "SELECT a.user_id, a.username, CONCAT(Vorname, ' ', Nachname) AS name
                 FROM user_online uo
                    JOIN auth_user_md5 a ON (a.user_id = uo.user_id)
                    LEFT JOIN user_info ON (user_info.user_id = uo.user_id)
                    LEFT JOIN user_visibility ON (user_visibility.user_id = uo.user_id)
                    INNER JOIN contact ON (owner_id = :me AND contact.user_id = a.user_id)
                 WHERE last_lifesign > :last_lifesign 
                    AND uo.user_id <> :me
                    AND " . get_vis_query('a', 'online') . " > 0
                 ORDER BY Nachname ASC, Vorname ASC";
        $statement = DBManager::get()->prepare($query);
        $statement->execute(array(
            'me' => $GLOBALS['user']->id,
            'last_lifesign' => time() - 10 * 60,
        ));
        $contacts = $statement->fetchAll(PDO::FETCH_ASSOC);
        
        $quicksearch = new QuickSearch("new_contact", new StandardSearch("user_id"));
        
        $template = $this->getTemplate("sidebar.php", $this->getTemplate("emptylayout.php", null));
        $template->set_attribute('contacts', $contacts);
        $template->set_attribute('quicksearch', $quicksearch);
        echo $template->render();
    }
    
    public function sidebar_users_action() {
        $query = "SELECT a.user_id, a.username, CONCAT(Vorname, ' ', Nachname) AS name
                 FROM user_online uo
                    JOIN auth_user_md5 a ON (a.user_id = uo.user_id)
                    LEFT JOIN user_info ON (user_info.user_id = uo.user_id)
                    LEFT JOIN user_visibility ON (user_visibility.user_id = uo.user_id)
                    INNER JOIN contact ON (owner_id = :me AND contact.user_id = a.user_id)
                 WHERE last_lifesign > :last_lifesign 
                    AND uo.user_id <> :me
                    AND " . get_vis_query('a', 'online') . " > 0
                 ORDER BY Nachname ASC, Vorname ASC";
        $statement = DBManager::get()->prepare($query);
        $statement->execute(array(
            'me' => $GLOBALS['user']->id,
            'last_lifesign' => time() - 10 * 60,
        ));
        $contacts = $statement->fetchAll(PDO::FETCH_ASSOC);
        
        $template = $this->getTemplate("_sidebar_users.php", null);
        $template->set_attribute('contacts', $contacts);
        echo studip_utf8encode($template->render());
    }
    
    protected function getTemplate($template_file_name, $layout = "without_infobox") {
        if (!$this->template_factory) {
            $this->template_factory = new Flexi_TemplateFactory(dirname(__file__)."/templates");
        }
        $template = $this->template_factory->open($template_file_name);
        if ($layout) {
            if (method_exists($this, "getDisplayName")) {
                PageLayout::setTitle($this->getDisplayName());
            } else {
                PageLayout::setTitle(get_class($this));
            }
            if (is_a($layout, "flexi_template")) {
                $template->set_layout($layout);
            } else {
                $template->set_layout($GLOBALS['template_factory']->open($layout === "without_infobox" ? 'layouts/base_without_infobox' : 'layouts/base'));
            }
        }
        return $template;
    }
}