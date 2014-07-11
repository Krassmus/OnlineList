<?php

if (!interface_exists("Hook")) {
    interface Hook {
        static public function getHookDescription();
    }
}

class ActionNavigationHook extends Navigation implements Hook {
    
    protected $postvars = array();
    
    public function setPostVariable($varname, $value) {
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

If you want to add an action that is corresponding to a POST-request and
not a simple GET-request, you can call #addPostVariables(name, value)
and the icon should be wrapped in a form-tag with hidden input 
fields and an XSRF-token, so your action is a valid POST-request.
        ";
    }
}

class DisplayOnlineUserActionHook extends ActionNavigationHook {}

