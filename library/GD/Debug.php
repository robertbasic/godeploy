<?php

/**
 * GoDeploy deployment application
 * Copyright (C) 2011 the authors listed in AUTHORS file
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.

 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.

 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @copyright 2011 GoDeploy
 * @author See AUTHORS file
 * @link http://www.godeploy.com/
 */

/**
 * Debugging/logging class
 *
 * @author james
 */
class GD_Debug
{
	/**
	 * @var resource File handle to an open file
	 */
	private static $_fh;

	/**
	 * @var int Current debug level
	 */
	private static $_current_debug_level;

	const DEBUG_NONE = 0;
	const DEBUG_BASIC = 1;
	const DEBUG_FULL = 2;

	/**
	 * Set the current debug level or default. If debugging enabled then open
	 * the file handle and allow logging.
	 */
	private static function initialise()
	{
		try
		{
			self::$_current_debug_level = GD_Config::get("debug_level");
		}
		catch(Zend_Db_Adapter_Exception $ex)
		{
			self::$_current_debug_level = self::DEBUG_NONE;
		}

		if(!self::$_current_debug_level || self::$_current_debug_level == "0")
		{
			return false;
		}

		if(!self::$_fh)
		{
			$logfile = GD_Config::get('logfile');
			self::$_fh = @fopen($logfile, "a");

			if(!self::$_fh) return false;
		}
		return true;
	}

	/**
	 * Log some debug
	 *
	 * @param string $text String to output to debugging log
	 * @param int $level Debugging level to show this debug on
	 * @param bool $append_newline Append a \n newline on the end (default true)
	 * @param bool $prepend_timestamp Prepend timestamp (default true)
	 */
	public static function Log($text, $level, $append_newline = true, $prepend_timestamp = true)
	{
		if(!self::initialise()) return false;

		if(self::$_current_debug_level >= $level)
		{
			$dbg_txt = $prepend_timestamp ? "[" . date("Y-m-d H:i:s") . "]: " : "";
			$dbg_txt .= $text;
			$dbg_txt .= $append_newline ? "\n" : "";

			fwrite(self::$_fh, $dbg_txt);
		}
	}

	public static function StartDeploymentLog($deployment_id)
	{
		self::Log("===============================================================================", self::DEBUG_BASIC, true, false);
		self::Log("Deployment ID " . $deployment_id . " started - " . date("Y-m-d H:i:s"), self::DEBUG_BASIC, true, false);
		self::Log("===============================================================================", self::DEBUG_BASIC, true, false);
	}

	public static function EndDeploymentLog($deployment_id)
	{
		self::Log("===============================================================================", self::DEBUG_BASIC, true, false);
	}
}
