<?php
/**
* Kacho: File-based PHP5 caching library
*
* @license http://opensource.org/licenses/LGPL-3.0 GNU Lesser General Public License, version 3.0
* @author Kaloyan K. Tsvetkov <kalotan@kaloyan.info>
* @link https://github.com/kktsvetkov/kacho/
*/

/**
* Kacho: File-based PHP5 caching library
*/
class Kacho
{
	/**
	* @var string filename where the cache is stored
	*/
	protected $filename = null;

	/**
	* @var string
	*/
	protected $chgrp;

	/**
	* @var string
	*/
	protected $chmod = 02775;

	/**
	* Constructor
	*
	* @param string $filename
	* @param array $options
	*/
	public function __construct($filename, array $options = array())
	{
		$this->filename = $filename;

		$options += array(
			'chgrp' => $this->chgrp,
			'chmod' => $this->chmod
			);
		$this->chgrp = $options['chgrp'];
		$this->chmod = $options['chmod'];
	}

	/**
	* Static shortcut method (for piping it with {@link Kacho::read()}
	* and {@link Kacho::write()}, e.g. Kachoo:open('x')->write(23);)
	*
	* @param string $filename
	* @param array $options
	* @return Kacho
	*/
	public static function open($filename, array $options = array())
	{
		return new Kacho($filename, $options);
	}

	/**
	* Reads the stored data; if there is nothing then it returns FALSE
	* @return mixed|FALSE
	*/
	public function read()
	{
		if (!file_exists($this->filename))
		{
			return false;
		}

		include($this->filename);
		$c = 'kacho_' . md5($this->filename);
		return isset($$c) ? $$c : false;
	}

	/**
	* Writes data in the file.
	*
	* @param mixed $data
	* @param integer $expire the time-to-live (TTL) of the cached data
	* @return boolean
	*/
	public function write($data, $expire = 3600)
	{
		$tmp_name = tempnam(dirname($this->filename), basename($this->filename));
		$md5 = md5($this->filename);

		file_put_contents(
			$tmp_name,
			'<?php if(' . intval(time() + $expire) . '>time())$kacho_'
				. $md5 . '=' . var_export($data, 1)
				. ';',
			LOCK_EX
			);
		if (file_exists($this->filename))
		{
			unlink($this->filename);
		}

		rename($tmp_name, $this->filename);

		if ($this->chgrp)
		{
			chgrp($this->filename, $this->chgrp);
		}

		if ($this->chmod)
		{
			chmod($this->filename, $this->chmod);
		}

		// compare the original data and the stored one
		//
		return $data == $this->read();
	}
}
