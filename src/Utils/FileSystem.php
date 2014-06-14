<?php

/**
 * This file is part of vBuilder Framework (vBuilder FW).
 *
 * Copyright (c) 2011 Adam Staněk <adam.stanek@v3net.cz>
 *
 * For more information visit http://www.vbuilder.cz
 *
 * vBuilder FW is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * vBuilder FW is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with vBuilder FW. If not, see <http://www.gnu.org/licenses/>.
 */

namespace vBuilder\Utils;

use vBuilder,
	Nette,
	InvalidArgumentException;

/**
 * File system routines
 *
 * @package vBuilder.Utils
 *
 * @author Adam Staněk (velbloud)
 * @since Oct 22, 2011
 */
class FileSystem {

	/**
	 * Returns parsed path components without escape characters.
	 *
	 * <code>
	 * FileSystem::getPathComponents('a/b/c'); // returns array('a', 'b', 'c')
	 * FileSystem::getPathComponents('a/b/c/'); // returns array('a', 'b', 'c')
	 * FileSystem::getPathComponents('/a/b/c'); // returns array('', 'a', 'b', 'c')
	 * </code>
	 *
	 * @see FileSystem::joinPathComponents()
	 * @uses Strings::splitWithEscape()
	 *
	 * @param string path
	 * @param char directory separator
	 *
	 * @return string
	 */
	static function getPathComponents($path, $separator = DIRECTORY_SEPARATOR) {
		if($path == '') return array();

		$tokens = Strings::splitWithEscape($path, $separator, $separator == '\\' ? '\\\\' : '\\', false);

		// We need to ignore all empty values except for first
		$components = array();
		foreach($tokens as $t) {
			if($t == '' && count($components)) continue;
			$components[] = $t;
		}

		return $components;
	}

	/**
	 * Joins path components into path string.
	 *
	 * @see FileSystem::joinPathComponents()
	 *
	 * @param string[] path components
	 * @param char directory separator
	 *
	 * @return string
	 */
	static function joinPathComponents(array $pathComponents, $separator = DIRECTORY_SEPARATOR) {
		$path = '';

		foreach($pathComponents as $c) {
			if($c != '')
				$path .= $separator . self::escapePathComponent($c, $separator);
		}

		return (strlen($path) && $pathComponents[0] != '')
			? substr($path, strlen($separator))
			: $path;
	}

	/**
	 * Returns escaped path component.
	 *
	 * @param string path
	 * @param char directory separator
	 *
	 * @return string
	 */
	static function escapePathComponent($pathComponent, $separator = DIRECTORY_SEPARATOR) {
		$escape = $separator == '\\' ? '\\\\' : '\\';
		return str_replace(array($escape, $separator), array($escape . $escape, $escape . $separator), $pathComponent);
	}

	/**
	 * Resolves ../ and ./ components in given path.
	 *
	 * @note Function works only with given path and does not actually check FS.
	 *
	 * <code>
	 * FileSystem::normalizePath('a/b/../c'); // returns 'a/c'
	 * FileSystem::normalizePath(array('a', 'b', '..', 'c'), FALSE); // returns array('a', 'c')
	 * </code>
	 *
	 * @param string|array path
	 * @param char|FALSE directory separator
	 *
	 * @return string|array depending on input
	 */
	static function normalizePath($path, $separator = DIRECTORY_SEPARATOR) {

		if($separator === FALSE && !is_array($path))
			throw new InvalidArgumentException('Path has to be an array of components if $separator === FALSE');

		$tokens = is_array($path) ? $path : self::getPathComponents($path, $separator);

		$out = array();
		foreach($tokens as $i => $token){
			if($token == '.') continue;
			if($token == '..' && count($out) > 0 && end($out) != '..') array_pop($out);
			else $out[] = $token;
		}

		return $separator === FALSE
			? $out
			: self::joinPathComponents($out, $separator);
	}

	/**
	 * Creates relative path.
	 *
	 * @note Function works only with given path and does not actually check FS.
	 *
	 * <code>
	 * FileSystem::getRelativePath('a/b/c', 'a/d'); // returns '../../d'
	 * </code>
	 *
	 * @param string|array from (current path)
	 * @param string|array to (target path)
	 * @param char directory separator
	 * @param bool return path components instead of string?
	 * @return string
	 */
	static function getRelativePath($from, $to, $separator = DIRECTORY_SEPARATOR) {

		// Get components
		if(!is_array($from)) {
			if($separator === FALSE)
				throw new InvalidArgumentException('Path has to be an array of components if $separator === FALSE');

			$from = self::getPathComponents($from, $separator);
		}

		if(!is_array($to)) {
			if($separator === FALSE)
				throw new InvalidArgumentException('Path has to be an array of components if $separator === FALSE');

			$to = self::getPathComponents($to, $separator);
		}

		// Get components of normalized paths
		$from		= self::normalizePath($from, FALSE);
		$to			= self::normalizePath($to, FALSE);

		$matched 	= count($to) == 0 ? 1 : 0;

		foreach($from as $depth => $dir) {

			// Find first non-matching dir
			if(count($to) && $dir === $to[0] && $matched === $depth) {

				// Ignore this directory
				array_shift($to);
				$matched++;

			} elseif($matched) {

				// Get number of remaining dirs to $from
				$remaining = count($from) - $depth + 1;
				if($remaining > 1) {

					// Add traversals up to first matching dir
					$padLength = (count($to) + $remaining - 1) * -1;
					$to = array_pad($to, $padLength, '..');


				} else {
					// If you want your path to start with ./
					// array_unshift($to, '.');
				}

				break;
			}
		}

		return $separator === FALSE
			? $to
			: self::joinPathComponents($to, $separator);
	}

	/**
	 * Creates directory (all of them if necessary) if directory does not exist.
	 *
	 * @param string directory path
	 * @param string creation mode
	 *
	 * @throws Nette\IOException if cannot create directory
	 */
	static function createDirIfNotExists($dirpath, $mode = 0770) {
		if(!is_dir($dirpath)) {
			if(@mkdir($dirpath, $mode, true) === false) // @ - is escalated to exception
				throw new Nette\IOException("Cannot create directory '".$dirpath."'");
		}
	}

	/**
	 * Creates all parent directories in the file path.
	 *
	 * @uses FileSystem::createDirIfNotExists()
	 *
	 * @param string file path
	 *
	 * @throws Nette\IOException if cannot create directory
	 */
	static function createFilePath($filePath) {
		$dirpath = pathinfo($filePath, PATHINFO_DIRNAME);
		self::createDirIfNotExists($dirpath);
	}

	/**
	 * Finds files matching given base path while trying multiple extensions.
	 *
	 * @param string absolute base path (file name path without extension)
	 * @param array of extensions (if empty all possible extensions will be matched)
	 *
	 * @return array of absolute file paths
	 */
	static function findFilesWithBaseName($basePath, $extensions = array()) {
		$pi = pathinfo($basePath);
		$files = array();

		foreach(Nette\Utils\Finder::findFiles($pi['basename'] . '.*')->in($pi['dirname']) as $file) {
			$matched = count($extensions) == 0;
			foreach($extensions as $curr) {
				// Podporovano az od PHP 5.3.6
				// $ext = $file->getExtension();
				$ext = pathinfo($file->getFilename(), PATHINFO_EXTENSION);

				if(mb_strtolower($curr) == mb_strtolower($ext)) {
					$matched = true;
					break;
				}
			}

			if(!$matched) continue;

			$files[] = $file->getPathname();
		}

		return $files;
	}

	/**
	 * Tries to delete all of given files.
	 *
	 * @param array of absolute file paths
	 *
	 * @return int number of deleted files
	 * @throws Nette\IOException if file has been found, but can't unlink it during the permission violation
	 */
	static function tryDeleteFiles(array $files) {
		$success = 0;

		foreach($files as $path) {
			if(file_exists($path)) {
				if(!unlink($path))
					throw new Nette\IOException("Failed to delete " . var_export($path, true));

				$success++;
			}
		}

		return $success;
	}

}
