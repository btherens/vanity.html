# vanity.html

vanity.html is a simple and lightweight profile website. The setup is intended to represent a bare-bones mvc framework supporting a single page multi-view web application. Scripts and stylesheets are embedded with the html output.

visit `/pdf` path of the website for a PDF version of the page.

## Screenshot
<table style="width:100%">
  <tr>
    <td><img src="https://raw.githubusercontent.com/btherens/vanity.html/main/asset/light-mode.png" ></td>
    <td><img src="https://raw.githubusercontent.com/btherens/vanity.html/main/asset/dark-mode.png" ></td>
  </tr>
  <tr>
    <td>light mode</td>
    <td>dark mode</td>
  </tr>
</table>

## Compatibility
| component | tested |
|----------|:-------------:|
| client   | Chromium and Webkit browsers |
| server   | Apache/2.4.46 (Unix) <br> PHP 7.4, 8.0, 8.1 |
| database | MySQL 8.0.23, MariaDB 10.4.13 |

## Basic usage

1. checkout this repository to the root of your web host's public path.
2. create the mysql database  
connect to your MySQL environment using an account with enough access to provision new databases and users. example:
```
/* create the database */
CREATE DATABASE `vanitydb`;  
/* create the new user (replace PASSWORD) */  
CREATE USER 'vanityphpaccess'@'localhost' identified by 'PASSWORD';  
/* give access to this specific database only */  
GRANT ALL PRIVILEGES ON vanitydb.* TO 'vanityphpaccess'@'localhost';  
/* reload access tables */  
FLUSH PRIVILEGES;  
```

take note of your deployment requirements! Only use user host @'localhost' if your mysql instance can accept users provisioned to that host.

3. create a config.php document at the root of your checked out repo and fill in required variables (see `~/config.php.sample` in repository for details)  
4. exiftool and wkhtmltopdf binaries are required for pdf printing at yoururl/pdf

## Files
- config.php: **required variables for script execution (see sample)**
- .htaccess: server rules route requests to index.php
- index.php: receive requests from web at this file (redirected here by .htaccess)
- __utilities__: MVC framework and other support classes
  - bootstrap.php: route handling for MVC requests
  - bmail.php: simple class for sending email
  - db.php: static class for database connection
  - inlineasset.php: serve static text files from asset/ directory to output buffer
  - modal.php: static class for sending modal dialogs to client
  - startup.php: startup commands to be run before routes are called
  - uinterface.php: simple classes that extend DOMDocument objects with more complex definitions. rich interaction with validvar objects
  - uivar.php: class designed to sit between model and controller and handle data validation and sanitization
  - vanityPrint.sh: shell script to render web page, using wkhtmltopdf for rendering and exiftool for properties
  - view.php: view methods
- __asset__: vector and images required for site operation
  - avatar.jpg: image will be featured in the profile sidebar (bring your own!)
- __controllers__: controllers are called by web request and generate output as response
  - controller.php: base controller class
  - canvascontroller.php: call dependent views and combine into single page view
  - profilecontroller.php: profile info
  - skillcontroller.php: list of skills
  - methodcontroller.php: list of methods/tools
  - workcontroller.php: list of employment details
  - educationcontroller.php: list of educational programs
- __models__: models work get and set values from database
  - model.php: base model class - query database and table generation
  - canvasmodel.php
  - profilemodel.php
  - skillmodel.php
  - methodmodel.php
  - workmodel.php
  - educationmodel.php
- __views__: views used to populat output response
  - __canvas__
    - index.php
  - __education__
    - index.php
  - __method__
    - index.php
  - __profile__
    - index.php
  - __skill__
    - index.php
  - __work__
    - index.php
- __css__: style files define look and feel
  - variable.css: basic colors and font scaling
  - responsive.css: responsive layout
  - font.css: text formats
  - element.css: non-text formats
  - layout.css: application layout, backgrounds, borders, drop shadows
  - icon.css: support for inline svg look, alignment, and scaling
  - modal.css: modal dialog look and feel, show/hide, component of modal.js (disabled in release)
  - active.css: visual accents, component of active.js (disabled in release)
- __js__: style files define look and feel
  - base.js: basic support for script environment, launch callback
  - ajax.js: functions for server-client communication
  - modal.js: modal dialog - generate / call / dismiss / accept input _(disabled in release)_
  - active.js: library of functions that enhance input forms in script-enabled environments _(disabled in release)_
- __includes__: simple reference files that link client-side libraries together (html/js/css)
  - head.php: the head element of html response. includes site metadata
  - script.php: reference any necessary js libraries
  - style.php: reference any css libraries. altered css is served to pdf printer to ensure color variables are properly used

## notes
unaffiliated content used in readme.md  
>Moriareviews.com, 2021, www.moriareviews.com/rongulator/wp-content/uploads/Hobbit-1977-4.jpg. Accessed 2 Mar. 2021.

## License
Copyright (C) 2021 Brian Therens

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU Lesser General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU Lesser General Public License
along with this program.  If not, see <https://www.gnu.org/licenses/lgpl>.
