<?php

interface HookClass 
{
    /**
     * This should return a string that contains information on how and when and
     * where the hookclass is called. Also it should tell if the object of the 
     * class usually contains any starting information and if it expected to serve
     * some information back to the place from where the hook was/will be called.
     * So this method more or less returns a documentation of the hook and all
     * its circumstances. Don't be afraid to write some few lines more. That's okay.
     */
    public function getDescription();
}

class ActionNavigation extends Navigation implements HookClass {
    
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
    
    public function getDescription() 
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

class OnlineUserAction extends ActionNavigation {}

class HookCenter 
{
    static protected $hooks = array(); //the hooks and registered callables
    
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
    static public function run($hookclass = null, $instance = null) 
    {
        if ($instance === null or !is_a($instance, $hookclass)) {
            $instance = new $hookclass();
        }
        if (!is_a($instance, "HookClass")) {
            throw new Exception(sprintf("%s is not implementing the HookClass interface.", $hookclass));
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