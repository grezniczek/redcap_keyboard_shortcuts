# REDCap Keyboard Shortcuts

A REDCap External Module providing a few keyboard shortcuts.

## Requirements

- REDCap 9.5.0 or newer.

## Installation

- Clone this repo into `<redcap-root>/modules/redcap_keyboard-shortcuts_v<version-number>`, or
- Obtain this module from the Consortium [REDCap Repo](https://redcap.vanderbilt.edu/consortium/modules/index.php) via the Control Center.
- Go to _Control Center > Technical / Developer Tools > External Modules_ and enable REDCap Keyboard Shortcuts.

## Keyboard Shortcuts

Note: When the focus is on any control (input, link, button), the shortcuts are disabled. `Esc` can be used to remove the focus.

Clicking on the indicator icon will display information about the available keyboard shortcuts.

### Global Shortcuts

- **Alt-1** - _REDCap Instance Home_
- **Alt-2** - _My Projects_
- **Alt-0** - _Control Center_
- **Alt-Shift-E** - _External Modules Manager_

### In-Project Shortcuts

- **A** - Add/Edit Records. This goes straight to a new record for projects with auto-numbering enabled, or else to the _Add/Edit Records_ page with focus set to the record id field.
- **C** - Codebook
- **D** - Record Status Dashboard
- **E** - Add/Edit Records, with focus set to the search field.
- **H** - Record Home Page
- **N** - Go to the next record or the first record shown on dashboard.
- **P** - Go to the previous record or the first record shown on dashboard.  
  Order is determined by the currently active dashboard.
- **Shift-D** - Designer
- **Shift-E** - External Modules

## Configuration

### System

- **Debug mode**: Turns on debugg logging to the browswer console
- **Show indicator**: Shows a small icon in the top left corner indicating whether _Keyboard Shortcuts_ are active.

### Project

- **Hide indicator**: Suppresses display of the activity indicator.
- **Disable prev/next**: Do not determine previous/next record. It may be necessary to use this in very large projects for performance reasons.

## Changelog

Version | Description
------- | ------------------
1.0.1   | Added prev/next record navigation. Add/Edit finetuning. Added help dialog. Disabled on surveys.
1.0.0   | Initial release.
