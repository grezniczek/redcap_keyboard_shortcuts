<?php namespace DE\RUB\KeyboardShortcutsExternalModule;

use DateTime;
use ExternalModules\AbstractExternalModule;

class KeyboardShortcutsExternalModule extends AbstractExternalModule
{
    function redcap_every_page_top($project_id) {

        // Logged in? Is this a survey page? We are not interested in survey pages
        if (!defined("USERID") || strpos(PAGE, "surveys/index.php") !== false) return;

        // print "<pre>"; print_r($GLOBALS["Proj"]); exit;

        // Gather information
        $debug = $this->getSystemSetting("debug-mode") == true;
        $showIndicator = $this->getSystemSetting("show-indicator") == true;
        if ($project_id) {
            $hideIndicator = $this->getProjectSetting("project-hide-indicator") == true;
            if ($hideIndicator) $showIndicator = false;
        }
        $emBaseLink = $this->IsDevelopmentFramework() ? 
        (APP_PATH_WEBROOT_PARENT . "external_modules/") : 
        (trim(APP_PATH_WEBROOT_FULL, "/") . APP_PATH_WEBROOT . "ExternalModules/");

        $recordAutoNumbering = $project_id > 0 && $GLOBALS["Proj"]->project["auto_inc_set"];

        // Do we have a record?
        $currentRecord = $nextRecord = $prevRecord = null;
        $duration = "";
        $determinePrevNext = $project_id > 0 && $this->getProjectSetting("project-disable-prevnextrecord") !== true;

        if ($determinePrevNext && $project_id > 0 && substr(PAGE, 0, 10) == "DataEntry/" && isset($_GET["id"])) {
            $time = -microtime(true);
            $currentRecord = $_GET["id"];
            global $user_rights, $Proj;
            // Determine prev and next record (according to the dashboard)
            $rd_id = \UIState::getUIStateValue($project_id, 'record_status_dashboard', 'rd_id');
            $dashboard = \RecordDashboard::getRecordDashboardSettings($rd_id);
            // Get list of all records
            $recordNames = $recordNamesReal = array_values(\Records::getRecordList(PROJECT_ID, ($user_rights['group_id'] != '' ? $user_rights['group_id'] : $_GET['dag']), true, false, $_GET['arm']));
            // For DDE user, append DDE#
            $isDDE = ($Proj->project['double_data_entry'] && isset($user_rights['double_data']) && $user_rights['double_data'] != 0);
            if ($isDDE) {
                foreach ($recordNamesReal as &$this_record) {
                    $this_record .= "--" . $user_rights['double_data'];
                }
            }
            // Apply filter logic (if defined for a custom dashboard)
            if (trim($dashboard['filter_logic']) != '' && !empty($recordNames)) 
            {
                // Set events
                $events = (is_numeric($_GET['arm']) && isset($Proj->events[$_GET['arm']])) ? array_keys($Proj->events[$_GET['arm']]['events']) : array_keys($Proj->eventInfo);
                // Get record names
                try {
                    $getDataParams = array('project_id'=>PROJECT_ID, 'return_format'=>'array', 'records'=>$recordNamesReal, 'fields'=>$Proj->table_pk,
                                            'events'=>$events, 'filterLogic'=>$dashboard['filter_logic'], 'returnEmptyEvents'=>true);
                    $recordNames = array_keys(\Records::getData($getDataParams));
                } catch (\Exception $e) {
                    $recordNames = array();
                }
            }
            // If using Order Records By feature, then order records by that field's value instead of by record name
            if (!empty($recordNames) && !($dashboard['sort_order'] == 'ASC' && ($dashboard['sort_field_name'] == '' || $dashboard['sort_field_name'] == $Proj->table_pk)))
            {
                // Get all values for the Order Records By field
                $order_id_by_records = \Records::getData('array', $recordNamesReal, $dashboard['sort_field_name'], $dashboard['sort_event_id']);
                // Isolate values only into separate array
                $order_id_by_values = array();
                foreach ($recordNames as $this_record) {
                    $val = "";
                    if (isset($order_id_by_records[$this_record][$dashboard['sort_event_id']][$dashboard['sort_field_name']])) {
                        $val = $order_id_by_records[$this_record][$dashboard['sort_event_id']][$dashboard['sort_field_name']];
                    }
                    $order_id_by_values[$this_record] = strtolower($val); // Make lowercase since we want to do case-insensitive ordering
                    unset($order_id_by_records[$this_record]);
                }
                // Now sort $formStatusValues by values in $order_id_by_values
                $field_type = $Proj->metadata[$dashboard['sort_field_name']]['element_type'];
                $val_type = $Proj->metadata[$dashboard['sort_field_name']]['element_validation_type'];
                $sortFieldIsNumber = (($dashboard['sort_field_name'] == $Proj->table_pk && $Proj->project['auto_inc_set']) 
                                    || $val_type == 'float' || $val_type == 'int' || $field_type == 'calc' || $field_type == 'slider');
                array_multisort($order_id_by_values, ($dashboard['sort_order'] == 'ASC' ? SORT_ASC : SORT_DESC), ($sortFieldIsNumber ? SORT_NUMERIC : SORT_STRING), $recordNames);
                unset($order_id_by_values, $order_id_by_records);
            }
            // No longer need this
            unset($recordNamesReal);
            // Find the current record in the list and calculate previous and next
            $numRecords = count($recordNames);
            if ($numRecords) {
                $currentIndex = array_search($currentRecord, $recordNames);
                if ($currentIndex === false) {
                    // Use last/first for prev/next
                    $nextRecord = $recordNames[0];
                    $prevRecord = $recordNames[$numRecords - 1];
                }
                else {
                    $nextIndex = $currentIndex == ($numRecords - 1) ? 0 : $currentIndex + 1;
                    $nextRecord = $recordNames[$nextIndex];
                    $prevIndex = $currentIndex == 0 ? $numRecords - 1 : $currentIndex - 1;
                    $prevRecord = $recordNames[$prevIndex];
                }
            }
            $time += microtime(true);
            $time = floor($time * 1000);
            $duration = "Prev/Next Record Time: {$time} ms.";
        }


        // Include CSS and JS, and transfer data to the JavaScript implementation
        $this->includeCSS("css/keyboard-shortcuts.css");
        ?>
        <script>
            if (typeof window.ExternalModules == 'undefined') {
                window.ExternalModules = {};
            };
            window.ExternalModules.KeyboardShortcutsEM_DTO = {
                debug: <?= json_encode($debug) ?>,
                indicator: <?= json_encode($showIndicator) ?>,
                emBase: <?= json_encode($emBaseLink) ?>,
                pid: <?= json_encode($project_id) ?>,
                recordAutoNumbering: <?= json_encode($recordAutoNumbering) ?>,
                currentRecord: <?= json_encode($currentRecord) ?>,
                nextRecord: <?= json_encode($nextRecord) ?>,
                prevRecord: <?= json_encode($prevRecord) ?>,
                prevNextDuration: <?= json_encode($duration) ?> 
            };
        </script>
        <?php
        $this->includeScript("js/keyboard-shortcuts.js");

        include "help.php";
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
