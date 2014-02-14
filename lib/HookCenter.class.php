<?php

/**
 * An interface for all hookclasses that should be handled by the HookCenter.
 * They need a static public method getHookDescription to tell how the hookclass
 * should be used. A derived hookclass can be very simple like
 *     class BulletinBoardQuoteHook extends ArrayObject implements Hook 
 *     {
 *         static public function getHookDescription() 
 *         {
 *             return "Just put strings in the array that are randomly displayed 
 *                      in the bulletin board as famous quotes.";
 *         }
 *     }
 * Or they could be very complex with lots of methods and attributes. It's your 
 * choice. But please write documentation so that everyone else knows how to use
 * the hook.
 * 
 * As a convention derived classes should be named with "Hook" as the last four
 * letters of the name.
 */
interface Hook 
{
    /**
     * This should return a string that contains information on how and when and
     * where the hookclass is called. Also it should tell if the object of the 
     * class usually contains any starting information and if it expected to serve
     * some information back to the place from where the hook was/will be called.
     * So this method more or less returns a documentation of the hook and all
     * its circumstances. Don't be afraid to write some few lines more. That's okay.
     */
    static public function getHookDescription();
}

class ActionNavigationHook extends Navigation implements Hook {
    
    protected $postvars = array();
    
    public function addPostVariable($varname, $value) {
        if ($value !== null) {
            $this->postvars[$varname] = $value;
        } else {
            unset($this->postvars[$varname]);
        }
    }
    
    public function clearPostVariables() 
    {
        $this->postvars = array();
    }
    
    public function getPostVariables() 
    {
        return $this->postvars;
    }
    
    static public function getHookDescription() 
    {
        return "
            This hook is often called in lists at which you can see some icons
            on the right side of the list that are representing actions or links
            to sites in Stud.IP. Just like at the online-page or the participants
            page in the course.
            
            You can think of such an icon as a subnavigation of this ActionNavigation-
            object. So if you want to add an icon/action to the list, just call 
            #addSubNavigation (derived from Navigation class) to add your own 
            ActionNavigation objects. Also you can delete existing subnavigation
            objects or rearrange them - just like in casual navigation.
            
            If you want to add an action that corresponding to a POST-request and
            not a simple GET-request, you can call #addPostVariables(name, value)
            and the icon should be wrapped in a <form>-tag with hidden input 
            fields and an XSRF-token, so your action is a valid POST-request.
        ";
    }
}

class DisplayOnlineUserActionHook extends ActionNavigationHook {}

/**
 * Class to be used to register and run hooks in Stud.IP. The difference to
 * NotificationCenter is that the HookCenter doesn't handle simple events but
 * it serves objects of hookclasses the registered function can interact with.
 * 
 * So if you just want to tell that you created a user, just fire the event with
 * the NotificationCenter, but if you want to display a user and want to give 
 * plugins or any other code the ability to add links or input fields or whatever
 * you should use the HookCenter. The used hookclass gathers the icons, inputfields
 * or whatever you want and you can use the object of type hookclass to display
 * these information to the user-page.
 * 
 * Example for running a hook:
 *     $actions = new DisplayOnlineUserActionHook("onlinelist");
 *     $actions = HookCenter::run("DisplayOnlineUserActionHook", $actions);
 *     foreach ($actions->getSubNavigation() as $action) {
 *         echo '<a href="'.URLHelper::getLink($action->getURL()).'">'.htmlReady($action->getTitle()).'</a> ';
 *     }
 * 
 * Example for registering a callback to a hook (should be run before the the hook is run):
 *     HookCenter::register("DisplayOnlineUserActionHook", function ($navigation) {
 *         $nav = new DisplayOnlineUserActionHook(_("Nachricht verfassen"), URLHelper::getURL("sms_send.php"));
 *         $nav->setImage(Assets::image_path("icons/16/blue/mail.png"), array('title' => _("Nachricht verfassen")));
 *         $navigation->addSubNavigation("messaging", $nav);
 *     });
 * 
 * You can see that the hookclass is the mediator between the registered function
 * and the code that has called the hook. To know as a programmer what exactly the
 * code calling the hook is expecting, you only need to know about the hookclass.
 * Just have a look at the class, at the documentation of the class and/or at 
 * the output of the static method #getHookDescription() of the hookclass that 
 * usually describes what the hook is doing and how the registered function can 
 * interact with the hookclass.
 */
class HookCenter 
{
    static protected $hooks = array(); //the hooks and registered callables
    
    /**
     * Registers a callback function for the case that the given hook is run.
     * @param string $hookclass : name of the hookclass is the name of the hook itself.
     * @param callable $callable : your callback. The first argument of the 
     * callback-function will be an instance of type $hookclass your callback
     * receives from the HookCenter when the hook is run. See documentation of
     * the $hookclass to know how your callback can interact with the hook.
     */
    static public function register($hookclass, $callable) 
    {
        self::$hooks[$hookclass][] = $callable;
    }
    
    /**
     * Runs a hook. If there are any functions registered (via #register) those 
     * functions will get called and an instance of $hookclass will be given to
     * that function. The function can then retrieve information of the instance
     * of $hookclass, just do some stuff or even interact with the class and give
     * information to the instance back. This method returns the instance that
     * has run through all registered functions.
     * @param type $hookclass : the name of the class that defined the behaviour 
     * of thee hook. The class is more or less identic with the hook. An instance 
     * of $hookclass will get passed to all registered functions and those functions
     * can interact with this instance, call public methods and even give 
     * information to the object. The code that has called the Hook (called #run)
     * gets the object back and can retrieve information.
     * @param hookclass $instance : an optional instance of $hookclass. If you want 
     * the instance that is passed to the registered functions not to come freshly
     * out of the constructor you can pass your own instance of $hookclass here 
     * that may contain special information.
     * @return \hookclass : instance of the $hookclass. If you gave in a second 
     * argument the returned object will be exactly that instance, otherwise a 
     * new instance will be created. But in both cases the instance was handled by
     * all registered functions and can contain more information or a different 
     * state than before.
     */
    static public function run($hookclass, $instance = null) 
    {
        if ($instance === null or !is_a($instance, $hookclass)) {
            $instance = new $hookclass();
        }
        if (!is_a($instance, "Hook")) {
            throw new Exception(sprintf("%s is not implementing the Hook-interface.", $hookclass));
        }
        foreach (self::$hooks as $hook => $callables) {
            if ($hook === $hookclass) {
                foreach ($callables as $callable) {
                    if (is_callable($callable)) {
                        call_user_func($callable, $instance);
                    } else {
                        throw new Exception(sprintf("%s is not a callable.", $callable));
                    }
                }
            }
        }
        return $instance;
    }
}