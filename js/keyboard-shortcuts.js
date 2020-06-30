// @ts-check
//
// Keyboard Shortcuts EM
//
;(function() {

/** @type {ExternalModules} */
// @ts-ignore
var EM = window.ExternalModules
if (typeof EM == 'undefined') {
    EM = {}
    // @ts-ignore
    window.ExternalModules = EM
}
var DTO = EM.KeyboardShortcutsEM_DTO
var webroot = ''
var page = ''
/** @type {JQuery} */
var $indicator = null

/**
 * Log to the console when in debug mode.
 */
function debugLog() {
    if (DTO.debug) {
        for (var i = 0; i < arguments.length; i++)
            console.log(arguments[i])
    }
}

/**
 * Evaluates keyboard events
 * @param {KeyboardEvent} e 
 */
function keyListener(e) {
    var $focus = $(':focus')
    debugLog('KeyboardShortcuts:', e, $focus.length > 0)
    // Not doing anything if an element (input) has focus
    if ($focus.length) {
        if (e.key == 'Escape') {
            $focus.blur()
        }
        else return
    }
    
    // Alt-1 -> Home
    if (e.altKey && !e.ctrlKey && !e.shiftKey && e.key == '1') {
        location.href = '/index.php'
    }
    // Alt-2 -> My Projects
    if (e.altKey && !e.ctrlKey && !e.shiftKey && e.key == '2') {
        location.href = '/index.php?action=myprojects'
    }
    // Alt-0 -> Control Center
    if (e.altKey && !e.ctrlKey && !e.shiftKey && e.key == '0') {
        location.href = webroot + 'ControlCenter/index.php'
    }
    
    // In-project shortcuts
    if (DTO.pid > 0) {
        // C -> Codebook
        if (e.code == 'KeyC' && !e.altKey && !e.ctrlKey && !e.shiftKey) {
            location.href = webroot + 'Design/data_dictionary_codebook.php?pid=' + DTO.pid
        }

        // D -> Record Status Dashboard
        if (e.code == 'KeyD' && !e.altKey && !e.ctrlKey && !e.shiftKey) {
            location.href = webroot + 'DataEntry/record_status_dashboard.php?pid=' + DTO.pid
        }

        // Shift-D -> Designer
        if (e.code == 'KeyD' && !e.altKey && !e.ctrlKey && e.shiftKey) {
            location.href = webroot + 'Design/online_designer.php?pid=' + DTO.pid
        }

        // Ctrl-Shift-E -> External Modules
        if (e.code == 'KeyE' && !e.altKey && e.ctrlKey && e.shiftKey) {
            location.href = DTO.emBase + 'manager/project.php?pid=' + DTO.pid
        }

    }



}

function setup() {
    debugLog('KeyboardShortcuts: Initialized', DTO)
    // Create indicator
    if (DTO.indicator) {
        $indicator = $('<div class="keyboard-shortcut-em active"></div>').append('<span class="fa-stack"><i class="fas fa-keyboard fa-stack-1x"></i><i class="fas fa-ban fa-stack-2x inactive-indicator"></i></span>')
        if ($('nav.fixed-top:visible()').length) {
            $indicator.addClass('below-nav')
        }
        $('body').append($indicator)
        // Display/hide indicator
        setInterval(function() {
            if ($(':focus').length) {
                $indicator.removeClass('active')
            }
            else {
                $indicator.addClass('active')
            }
        }, 200)
    }
    // Listen for keystrokes
    document.addEventListener('keyup', keyListener)
}


$(function() {
    // @ts-ignore
    webroot = app_path_webroot
    // @ts-ignore
    page = window.page
    setup()
})

})();