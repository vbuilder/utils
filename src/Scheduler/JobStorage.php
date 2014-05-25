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

namespace vBuilder\Scheduler;

use vBuilder,
	vBuilder\Utils\FileSystem,
	Nette,
	Nette\Utils\Finder;

/**
 * Job storage
 *
 * @author Adam Staněk (velbloud)
 * @since May 25, 2014
 */
class JobStorage extends Nette\Object {

	const FILENAME_PREFIX = 'job.';
	const FILENAME_SUFFIX = '.php';

	/** @var vBuilder\IO\IMetadataFileStorage */
	private $fileStorage;

	/** @var string */
	private $dir;

	/**
	 * @param string path to directory
	 * @throws Nette\DirectoryNotFoundException if directory does not exist
	 */
	public function __construct($dir) {
		$this->setDirectory($dir);
	}

	/**
	 * @param string path to directory
	 * @return self
	 */
	public function setDirectory($dir) {
		$this->dir = $dir;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getDirectory() {
		return $this->dir;
	}

	/**
	 * Return file storage
	 *
	 * @return vBuilder\IO\IMetadataFileStorage
	 */
	public function getFileStorage() {
		if(!isset($this->fileStorage)) {
			$this->fileStorage = new vBuilder\IO\PhpFileStorage;
		}

		return $this->fileStorage;
	}

	/**
	 * Creates new job with code and metadata
	 *
	 * @param string name
	 * @param array metadata
	 * @param string php code
	 * @return string|FALSE absolute path to job file or FALSE on failure
	 */
	public function createJob($name, array $metadata, $phpCode) {

		FileSystem::createDirIfNotExists($this->dir);

		$path = $this->dir . '/' . self::FILENAME_PREFIX . $name . '.' . md5($phpCode) . self::FILENAME_SUFFIX;
		$result = $this->getFileStorage()->write($path, $metadata, "<?php\n" . $phpCode);

		return $result === FALSE ? FALSE : $path;
	}

	/**
	 * Returns array of all job scripts
	 *
	 * @return array
	 */
	public function getJobs() {
		$jobs = array();

		if(is_dir($this->dir)) {
			$files = Finder::findFiles(self::FILENAME_PREFIX . '*' . self::FILENAME_SUFFIX)->in($this->dir);
			foreach($files as $fileInfo) {
				$name = substr($fileInfo->getFilename(), strlen(self::FILENAME_PREFIX), 0 - strlen(self::FILENAME_SUFFIX));
				$name = preg_replace('#\\..*#', '', $name);

				if(!isset($jobs[$name]))
					$jobs[$name] = array($fileInfo->getPathname());
				else
					$jobs[$name][] = $fileInfo->getPathname();
			}
		}

		return $jobs;
	}

}