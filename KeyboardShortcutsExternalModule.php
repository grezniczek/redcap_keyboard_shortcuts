<?php namespace DE\RUB\KeyboardShortcutsExternalModule;

use ExternalModules\AbstractExternalModule;

class KeyboardShortcutsExternalModule extends AbstractExternalModule
{

    function redcap_every_page_top($project_id) {

        $this->includeCSS("css/keyboard-shortcut.css");

        $debug = $this->getSystemSetting("debug-mode") == true;
        $showIndicator = $this->getSystemSetting("show-indicator") == true;
        if ($project_id) {
            $hideIndicator = $this->getProjectSetting("project-hide-indicator") == true;
            if ($hideIndicator) $showIndicator = false;
        }

        $emBaseLink = $this->IsDevelopmentFramework() ? 
            (APP_PATH_WEBROOT_PARENT . "external_modules/") : 
            (trim(APP_PATH_WEBROOT_FULL, "/") . APP_PATH_WEBROOT . "ExternalModules/");

        // Transfer data to the JavaScript implementation.
        ?>
        <script>
            if (typeof window.ExternalModules == 'undefined') {
                window.ExternalModules = {};
            };
            window.ExternalModules.KeyboardShortcutsEM_DTO = {
                debug: <?= json_encode($debug) ?>,
                indicator: <?= json_encode($showIndicator) ?>,
                emBase: <?= json_encode($emBaseLink) ?>,
                pid: <?= json_encode($project_id) ?>
            };
        </script>
        <?php
        $this->includeScript("js/keyboard-shortcuts.js");
    }



    private function includeScript($file, $inline = false) {
        if ($inline) {
            $script = file_get_contents(__DIR__ . "/$file");
            echo "<script type=\"text/javascript\">\n{$script}\n</script>";
        }
        else {
            echo '<script type="text/javascript" src="' . $this->framework->getUrl($file) . '"></script>';
        }
    }

    /**
     * Includes a CSS file (either in-line or as a separate resource).
     * @param string $name The path of the CSS file relative to the module folder.
     * @param bool $inline Determines whether the styles will be inlined or loaded as a separate resource.
     */
    private function includeCSS($name, $inline = false) {
        
        if ($inline) {
            $css = file_get_contents(__DIR__ ."/{$name}");
            echo "<style>\n{$css}\n</style>\n";
        }
        else {
            $css = $this->framework->getUrl($name);
            $name = md5($name);
            echo "<script type=\"text/javascript\">
                    (function() {
                        var id = 'emcCSS{$name}'
                        if (!document.getElementById(id)) {
                            var head = document.getElementsByTagName('head')[0]
                            var link = document.createElement('link')
                            link.id = id
                            link.rel = 'stylesheet'
                            link.type = 'text/css'
                            link.href = '{$css}'
                            link.media = 'all'
                            head.appendChild(link)
                        }
                    })();
                </script>";
        }
    }

    private function IsDevelopmentFramework() {
        return strpos($this->getUrl("dummy.php"), "/external_modules/?prefix=") !== false;
    }
}
