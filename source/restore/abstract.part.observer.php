<?php
/**
 * Akeeba Restore
 * An AJAX-powered archive extraction library for JPA, JPS and ZIP archives
 *
 * @package   restore
 * @copyright Copyright (c)2008-2020 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

/**
 * Descendants of this class can be used in the unarchiver's observer methods (attach, detach and notify)
 *
 * @author Nicholas
 *
 */
abstract class AKAbstractPartObserver
{
	abstract public function update($object, $message);
}

