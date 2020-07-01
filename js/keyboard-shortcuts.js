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

    var modifier = e.altKey ? 'a' : ''
    modifier += e.ctrlKey ? 'c' : ''
    modifier += e.shiftKey ? 's' : ''
    
    // Alt-1 -> Home
    if (e.code == 'Digit1' && modifier == 'a') {
        location.href = '/index.php'
    }
    // Alt-2 -> My Projects
    if (e.code == 'Digit2' && modifier == 'a') {
        location.href = '/index.php?action=myprojects'
    }
    // Alt-0 -> Control Center
    if (e.code == 'Digit0' && modifier == 'a') {
        location.href = webroot + 'ControlCenter/index.php'
    }
    // Alt-Shift-E -> External Modules
    if (e.code == 'KeyE' && modifier == 'as') {
        location.href = DTO.emBase + 'manager/control_center.php'
    }
    
    // In-project shortcuts
    if (DTO.pid > 0) {
        // A -> Add/Edit Records
        if (e.code == 'KeyA' && modifier == '') {
            if (DTO.recordAutoNumbering) {
                location.href = webroot + 'DataEntry/record_home.php?pid=' + DTO.pid + '&id=1&auto=1&arm=1'
            }
            else {
                location.href = webroot + 'DataEntry/record_home.php?focus=%23inputString&pid=' + DTO.pid
            }
        }

        // C -> Codebook
        if (e.code == 'KeyC' && modifier == '') {
            location.href = webroot + 'Design/data_dictionary_codebook.php?pid=' + DTO.pid
        }

        // D -> Record Status Dashboard
        if (e.code == 'KeyD' && modifier == '') {
            location.href = webroot + 'DataEntry/record_status_dashboard.php?pid=' + DTO.pid
        }

        // E -> Add/Edit Records (with focus on search)
        if (e.code == 'KeyE' && modifier == '') {
            location.href = webroot + 'DataEntry/record_home.php?focus=%23search_query&pid=' + DTO.pid
        }

        // H -> Project Home Page
        if (e.code == 'KeyH' && modifier == '') {
            location.href = webroot + 'index.php?pid=' + DTO.pid
        }

        // Shift-D -> Designer
        if (e.code == 'KeyD' && modifier == 's') {
            location.href = webroot + 'Design/online_designer.php?pid=' + DTO.pid
        }

        // Ctrl-Shift-E -> External Modules
        if (e.code == 'KeyE' && modifier == 'cs') {
            location.href = DTO.emBase + 'manager/project.php?pid=' + DTO.pid
        }

        // Previous/Next while displaying a record
        if (DTO.currentRecord !== null) {
            // N -> Next Record (staying on same page)
            if (e.code == "KeyN" && modifier == '') {
                location.href = replaceUrlParam(location.href, 'id', DTO.nextRecord)
            }
            // P -> Previous Record (staying on same page)
            if (e.code == "KeyP" && modifier == '') {
                location.href = replaceUrlParam(location.href, 'id', DTO.prevRecord)
            }
        }
        // Previous/Next while showing the dashboard
        if (page == 'DataEntry/record_status_dashboard.php') {
            // N -> Next Record = first in list
            if (e.code == "KeyN" && modifier == '') {
                var href = $('#record_status_table tbody tr:first a:first').attr('href')
                location.href = href
            }
            // P -> Previous Record = last in list
            if (e.code == "KeyP" && modifier == '') {
                var href = $('#record_status_table tbody tr:last a:first').attr('href')
                location.href = href
            }
        }
    }
}

/**
 * Replaces a URL parameter value
 * @param {string} url 
 * @param {string} paramName 
 * @param {string} paramValue 
 */
function replaceUrlParam(url, paramName, paramValue)
{
    if (paramValue == null) {
        paramValue = '';
    }
    var pattern = new RegExp('\\b('+paramName+'=).*?(&|#|$)');
    if (url.search(pattern)>=0) {
        return url.replace(pattern,'$1' + paramValue + '$2');
    }
    url = url.replace(/[?#]$/,'');
    return url + (url.indexOf('?')>0 ? '&' : '?') + paramName + '=' + paramValue;
}

function setup() {
    debugLog('KeyboardShortcuts: Initialized', DTO)
    // Create indicator
    if (DTO.indicator) {
        $indicator = $('<div class="keyboard-shortcut-em active"></div>').append('<span class="fa-stack"><i class="fas fa-keyboard fa-stack-1x"></i><i class="fas fa-ban fa-stack-2x inactive-indicator"></i></span>')
        if (page.includes('Plugins/index.php')) {
            $indicator.addClass('on-plugins')
        }
        else if (!(DTO.pid > 0)) {
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

    // Any tasks?
    // Focus?
    var params = new URLSearchParams(location.search)
    var focus = params.get('focus')
    if (focus) {
        debugLog('Putting focus on: ' + focus)
        $(focus).focus()
    }
}


$(function() {
    // @ts-ignore
    webroot = app_path_webroot
    // @ts-ignore
    page = window.page
    setup()
})

})();