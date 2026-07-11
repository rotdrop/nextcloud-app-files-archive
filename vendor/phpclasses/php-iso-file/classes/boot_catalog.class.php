<?php
	/**
	 * \class CBootCatalog
	 * \brief The EL TORITO validation's entry
	 * \see http://download.intel.com/support/motherboards/desktop/sb/specscdrom.pdf
	 * \author Christian SCHROETTER, (http://www.quatilfait.fr)
	 * \example examples/BootCatalog_test.php
	 * \copyright Creative Commons (BY NC SA)
	 * The holder allows the use of the original in non-commercial work and the creation of derived works provided they are distributed in a manner identical to the one that governs the original work license.
	*/
	class CBootCatalog
	{
		/**
		 * \public
		 * \param int $header (MUST be 0x01)
		*/
		var $header;
		/**
		 * \public
		 * \param int $platformID
		 * \see PlatformIDToName
		 * \brief The platform identifier
		 * 
		 * 0 = 80x86
		 * 1 = Power PC
		 * 2 = Mac
		 * 
		*/
		var $platformID;
		/**
		 * \public
		 * \param int $reserved (MUST be 0)
		*/
		var $reserved;
		/**
		 * \public
		 * \param string $manufacturerID
		 * \brief The manufacturer/developer of the CD-ROM.
		 * 
		 * This is intended to identify the manufacturer/developer of the CD-ROM.
		 * 
		*/
		var $manufacturerID;
		/**
		 * \public
		 * \param int $checksum
		 * \brief Check the entry validation integrity
		 * 
		 * This sum of all the words in this record should be 0.
		 * 
		*/
		var $checksum;
		/**
		 * \public
		 * \param int $key1 (MUST be 0x55)
		*/
		var $key1;
		/**
		 * \public
		 * \param int $key2 (MUST be 0xAA)
		*/
		var $key2;

		/**
		 * \fn static public function PlatformIDToName($platformID)
		 * \param int $platformID The platform identifier number.
		 * \see $platformID
		 * \brief Convert platformID to string
		 * Convert the platform identifier (int) to a human readable string.
		 * \return string The readable platform identifier
		*/
		static public function PlatformIDToName($platformID)
		{
			switch($platformID)
			{
			case 0:	return '80x86';
			case 1:	return 'Power PC';
			case 2:	return 'Mac';
			}

			return 'ID inconnu: ' . $platformID;
		}
		/**
		 * \fn public function IsValid()
		 * \brief Check is this validation entry is 'valid'
		 * Check all the member's of the "validation entry"
		 * The \a $header, \a $key1 and \a $key2 param and also the \a $cheksum value
		 * \see $header
		 * \see $checksum
		 * \see $key1
		 * \see $key2
		 * \todo Implemente the checksum validation algo...
		 * \return boolean true OR false
		*/
		public function IsValid()
		{
			if($this->header != 0x01 || $this->key1 != 0x55 || $this->key2 != 0xaa) {
				return false;
			}
/*
			$word = 0;
			$len = strlen($this->manufacturerID);
			for($i = 0 ; $i < $len ; $i += 2) {
				$word += (int)hexdec(dechex(ord($this->manufacturerID[$i + 0])) . dechex(ord($this->manufacturerID[$i + 1])));
			}
*/
			return true;
		}
	}
?>