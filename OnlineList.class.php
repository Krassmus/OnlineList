<?php

require_once 'lib/messaging.inc.php';
if (!class_exists("HookCenter") && file_exists($STUDIP_BASE_PATH."/lib/classes/HookCenter.class.php")) {
    include_once "lib/classes/HookCenter.class.php";
}
include_once dirname(__file__)."/lib/ActionNavigationHook.class.php";
include_once dirname(__file__)."/lib/AddToSocialWorkerHook.class.php";

class OnlineList extends StudIPPlugin implements SystemPlugin {
    
    public function __construct() {
        parent::__construct();
        PageLayout::addScript($this->getPluginURL()."/assets/OnlineList.js");
        PageLayout::addStylesheet($this->getPluginURL()."/assets/OnlineList.css");
        
        $activator = new Navigation(_("OnlineListe"), "#");
        $activator->setImage(Assets::image_path("icons/lightblue/group2.svg"));
        PageLayout::addHeadElement("script", array(), "STUDIP.UNI_NAME_CLEAN = '".htmlReady($GLOBALS['UNI_NAME_CLEAN'])."'");
        Navigation::addItem("/onlinelist", $activator);
        
        if (UpdateInformation::isCollecting() && Request::get("page") === "plugins.php/onlinelist/worker") {
            $data = array();
            
            if ($GLOBALS['user']->id !== "nobody") {
                $contacts = $this->getOnlineContacts();
                $actions = $actions = $this->getUserActions();
                if (class_exists("HookCenter")) {
                    $actions = HookCenter::run("DisplayOnlineUserActionHook", $actions);
                } else {
                    NotificationCenter::postNotification("DisplayOnlineUserActionHook", $actions);
                }

                $template = $this->getTemplate("_sidebar_users.php", null);
                $template->set_attribute('contacts', $contacts);
                $template->set_attribute('actions', $actions);
                $data['userlist'] = $template->render();
            } else {
                $data['notregistered'] = array(
                    'ticket' => Seminar_Session::get_ticket(),
                    'CSRFProtectionToken' => CSRFProtection::token()
                );
            }
            
            UpdateInformation::setInformation("OnlineList.updateUsers", $data);
        }
    }
    
    public function sidebar_action() {
        $contacts = $this->getOnlineContacts();
        
        $quicksearch = new QuickSearch("new_contact", new StandardSearch("username"));
        $quicksearch->fireJSFunctionOnSelect("STUDIP.OnlineList.askToAddContact");
        
        $actions = $this->getUserActions();
        if (class_exists("HookCenter")) {
            $actions = HookCenter::run("DisplayOnlineUserActionHook", $actions);
        } else {
            NotificationCenter::postNotification("DisplayOnlineUserActionHook", $actions);
        }
            
        $template = $this->getTemplate("sidebar.php", $this->getTemplate("emptylayout.php", null));
        $template->set_attribute('contacts', $contacts);
        $template->set_attribute('quicksearch', $quicksearch);
        $template->set_attribute('actions', $actions);
        echo $template->render();
    }
    
    public function getUserActions() {
        $navigation = new DisplayOnlineUserActionHook("onlinelist");
        //Die voreingestellten Aktionen der Nutzer
        $nav = new DisplayOnlineUserActionHook(_("Nachricht verfassen"), URLHelper::getURL("sms_send.php", array('rec_uname' => ':username')));
        $nav->setImage(Assets::image_path("icons/16/blue/mail.png"), array('title' => _("Nachricht verfassen")));
        $navigation->addSubNavigation("messaging", $nav);

        $nav = new DisplayOnlineUserActionHook(_("anblubbern"), URLHelper::getURL("plugins.php/blubber/streams/global?mention=:username", array('mention' => ':username')));
        $nav->setImage(Assets::image_path("icons/16/blue/blubber.png"), array(
            'title' => _("anblubbern"),
            'data-chaturl' => URLHelper::getURL("plugins.php/onlinelist/privateblubber", array('username' => ":username"))
        ));
        $navigation->addSubNavigation("blubber", $nav);
        return $navigation;
    }
    
    public function worker_action() {
        header("Content-Type: text/javascript");
        $template = $this->getTemplate("js_worker.php", null);
        echo $template->render();
    }
    
    protected function getOnlineContacts() {
        $query = "SELECT auth_user_md5.user_id, auth_user_md5.username, CONCAT(auth_user_md5.Vorname, ' ', auth_user_md5.Nachname) AS name, (UNIX_TIMESTAMP() - user_online.last_lifesign) AS inactive_seconds
                 FROM user_online
                    INNER JOIN auth_user_md5 ON (auth_user_md5.user_id = user_online.user_id)
                    INNER JOIN contact ON (contact.user_id = auth_user_md5.user_id)
                    LEFT JOIN user_visibility ON (user_visibility.user_id = user_online.user_id)
                 WHERE user_online.last_lifesign > (UNIX_TIMESTAMP() - 10 * 60) 
                    AND user_online.user_id <> :me
                    AND contact.owner_id = :me
                    AND " . get_vis_query('auth_user_md5', 'online') . " > 0
                 ORDER BY Nachname ASC, Vorname ASC";
        $statement = DBManager::get()->prepare($query);
        $statement->execute(array(
            'me' => $GLOBALS['user']->id
        ));
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function privateblubber_action() {
        $user = new User(get_userid(Request::get("username")));
        PageLayout::setTitle($user['Vorname']." ".$user['Nachname']);
        PageLayout::removeHeadElement("link", array('rel' => 'shortcut icon'));
        PageLayout::addHeadElement("link", array('rel' => 'shortcut icon', 'href' => Assets::image_path("icons/32/black/blubber.png")));
        PageLayout::addHeadElement("script", array('src' => $GLOBALS['ABSOLUTE_URI_STUDIP']."plugins_packages/core/Blubber/assets/javascripts/blubber.js"), "");
        
        $template = $this->getTemplate("privateblubber.php", $this->getTemplate("emptylayout.php", null));
        echo $template->render();
    }
    
    public function add_contact_action() {
        if (!Request::isPost()) {
            throw new Exception(_("Kein Zugriff �ber GET"));
        }
        $username = Request::username('username');

        Contact::import(array(
                'owner_id' => User::findCurrent()->id,
                'user_id' => User::findByUsername($username)->id)
        )->store();
    }
    
    public function notifications_action() {
        $notifications = PersonalNotifications::getMyNotifications();
        $template = $this->getTemplate("notifications.php", $this->getTemplate("emptylayout.php", null));
        $template->set_attribute('notifications', $notifications);
        echo $template->render();
    }
    
    protected function getTemplate($template_file_name, $layout = "without_infobox") {
        if (!$this->template_factory) {
            $this->template_factory = new Flexi_TemplateFactory(dirname(__file__)."/templates");
        }
        $template = $this->template_factory->open($template_file_name);
        if ($layout) {
            if (!PageLayout::getTitle()) {
                if (method_exists($this, "getDisplayName")) {
                    PageLayout::setTitle($this->getDisplayName());
                } else {
                    PageLayout::setTitle(get_class($this));
                }
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