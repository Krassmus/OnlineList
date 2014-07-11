<?php

if (!interface_exists("Hook")) {
    interface Hook {
        static public function getHookDescription();
    }
}

class AddToSocialWorkerHook implements Hook {
    
    protected $scripts = array();

    public function addJavascript($script) {
        $this->scripts[] = $script;
    }

    public function getScripts() {
        return $this->scripts;
    }
    
    static public function getHookDescription() 
    {
        return "
When you want to add your own javascript to the socialworker, just use this hook.
        ";
    }
}
