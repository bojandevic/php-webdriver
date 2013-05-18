<?php

/*
  Copyright 2011 3e software house & interactive agency

  Licensed under the Apache License, Version 2.0 (the "License");
  you may not use this file except in compliance with the License.
  You may obtain a copy of the License at

  http://www.apache.org/licenses/LICENSE-2.0

  Unless required by applicable law or agreed to in writing, software
  distributed under the License is distributed on an "AS IS" BASIS,
  WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
  See the License for the specific language governing permissions and
  limitations under the License.
 */

class WebDriverException extends Exception
{
	const NO_SUCH_ELEMENT 	        = 7;
	const NO_SUCH_FRAME 	           = 8;
	const UNKNOWN_COMMAND 	        = 9;
	const STALE_ELEMENT_REFERENCE   = 10;
	const ELEMENT_NOT_VISIBLE       = 11;
	const INVALID_ELEMENT_STATE     = 12;
	const UNKNOWN_ERROR             = 13;
	const ELEMENT_IS_NOT_SELECTABLE = 15;
	const JAVA_SCRIPT_ERROR         = 17;
	const XPATH_LOOKUP_ERROR        = 19;
	const NO_SUCH_WINDOW            = 23;
	const INVALID_COOKIE_DOMAIN     = 24;
	const UNABLE_TO_SET_COOKIE      = 25;
	const TIMEOUT                   = 28;

	public static $messages =
		array('NO_SUCH_ELEMENT'           => 'An element could not be located on the page using the given search parameters.',
				'NO_SUCH_FRAME'             => 'A request to switch to a frame could not be satisfied because the frame could not be found.',
				'UNKNOWN_COMMAND'           => 'The requested resource could not be found, or a request was received using an HTTP method that is not supported by the mapped resource.',
				'STALE_ELEMENT_REFERENCE'   => 'An element command failed because the referenced element is no longer attached to the DOM.',
				'ELEMENT_NOT_VISIBLE'       => 'An element command could not be completed because the element is not visible on the page.',
				'INVALID_ELEMENT_STATE'     => 'An element command could not be completed because the element is in an invalid state (e.g. attempting to click a disabled element).',
				'UNKNOWN_ERROR'             => 'An unknown server-side error occurred while processing the command.',
				'ELEMENT_IS_NOT_SELECTABLE' => 'An attempt was made to select an element that cannot be selected.',
				'JAVA_SCRIPT_ERROR'         => 'An error occurred while executing user supplied JavaScript.',
				'XPATH_LOOKUP_ERROR'        => 'An error occurred while searching for an element by XPath.',
				'NO_SUCH_WINDOW'            => 'A request to switch to a different window could not be satisfied because the window could not be found.',
				'INVALID_COOKIE_DOMAIN'     => 'An illegal attempt was made to set a cookie under a different domain than the current page.',
				'UNABLE_TO_SET_COOKIE'      => 'A request to set a cookie\'s value could not be satisfied.',
				'TIMEOUT'                   => 'A command did not complete before its timeout expired.');

	public function __construct($message, $code, $previous = null) {
		parent::__construct($message, $code);
	}
}
?>
