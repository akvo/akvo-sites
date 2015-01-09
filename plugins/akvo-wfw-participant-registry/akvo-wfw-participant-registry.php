<?php
/*
Plugin Name: AKVO - Walking for Water - Participant Registry
Plugin URI: http://URI_Of_Page_Describing_Plugin_and_Updates
Description: Allows for the management of Schools that Register for Walks under the programme by Walking for Water
Version: 1.0.0
Author: Jayawi Perera, Uthpala Sandirigama
Author URI: http://wp.jayawi.com
License: GPL2
*/
/*  Copyright 2013  Jayawi Perera  (email : jayawiperera@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

define('AkvoWfwParticipantRegistry_Plugin_Slug', 'AkvoWfWParticipantRegistry');
define('AkvoWfwParticipantRegistry_Plugin_Dir', dirname(__FILE__));
define('AkvoWfwParticipantRegistry_Plugin_Url', plugins_url('', __FILE__));
define('AkvoWfwParticipantRegistry_Plugin_File', __FILE__);
define('AkvoWfwParticipantRegistry_Plugin_DirFile', basename(dirname(__FILE__)) . '/' . basename(__FILE__));

require_once 'autoloader.php';

Akvo\WfW\ParticipantRegistry\Controller::getInstance()->initialise();