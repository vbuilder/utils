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

namespace vBuilder\IO;

use Nette\Object,
	Nette\Utils\SafeStream,
	Nette\InvalidArgumentException;

/**
 * PHP file storage
 *
 * @package vBuilder.Utils
 *
 * @author Adam Staněk (velbloud)
 * @since May 25, 2014
 */
class PhpFileStorage extends Object implements IMetadataFileStorage {

	/** Revision of job files (1 - 255) */
	const REVISION = 1;

	/** File header */
	const HEAD_START = '<?php //metadata';
	const HEAD_END = '?>';

	/** Maximum size of serialized metadata */
	const META_MAX_SIZE = 4294967295;

	public function __construct() {
		SafeStream::register();
	}

	/**
	 * @param array metadata
	 * @param mixed data
	 * @return bool
	 */
	public function write($file, array $metadata, $data) {
		$bytes = file_put_contents('safe://' . $file, $this->createData($metadata, $data));
		return $bytes !== FALSE;
	}

	/**
	 * @param string file path
	 * @param int flags
	 * @return mixed|FALSE
	 */
	public function read($file, $flags = 3) {
		$result = array();

		$handle = fopen('safe://' . $file, 'rb');
		if(!$handle) return FALSE;

		do {
			$head = self::getHeadStart();
			if(fread($handle, strlen($head)) !== $head)
				break;

			$metaLen = hexdec(fread($handle, self::getLengthChars()));
			$meta = fread($handle, $metaLen);
			if(!$meta) break;

			$meta = @unserialize($meta);
			if(!$meta) break;

			$result = array();

			if($flags & self::METADATA) {
				$result[self::METADATA] = $meta;
			}

			if($flags & self::DATA) {
				fseek($handle, strlen(self::HEAD_END), SEEK_CUR);
				$result[self::DATA] = stream_get_contents($handle);
			}

			fclose($handle);
			return count($result) > 1 ? $result : reset($result);

		} while(FALSE);

		fclose($handle);
		return FALSE;
	}

	/**
	 * Serializes metadata and data into sigle string
	 *
	 * @param array
	 * @param mixed data
	 * @return string
	 * @throws InvalidArgumentException if metadata are too large
	 */
	protected function createData(array $meta, $data) {

		$meta = serialize($meta);
		$metaLen = strlen($meta);

		if($metaLen > static::META_MAX_SIZE)
			throw new InvalidArgumentException("Maximum metadata length exceeded");

		return self::getHeadStart()
			. str_pad(dechex($metaLen), self::getLengthChars(), '0', STR_PAD_LEFT)
			. $meta
			. static::HEAD_END
			. $data;
	}

	/**
	 * Returns first part of file header
	 *
	 * @return string
	 */
	protected static function getHeadStart() {
		return static::HEAD_START
			. '[' .  str_pad(hexdec(static::REVISION), '0', 2, STR_PAD_LEFT) . ']';
	}

	/**
	 * Returns number of characters needed to represent
	 * length of maximum metadata in hexdec
	 *
	 * @return int
	 */
	protected static function getLengthChars() {
		static $lengthChars = 0;
		if($lengthChars == 0) {
			for($n = self::META_MAX_SIZE; $n > 1; $n = $n >> 2) $lengthChars++;
		}

		return $lengthChars;
	}

}