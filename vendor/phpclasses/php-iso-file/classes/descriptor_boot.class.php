<?php
	/**
	 * \class CBootDescriptor
	 * \brief An ISO-9660 "Boot Descriptors"
	 * \see http://www.ecma-international.org/publications/files/ECMA-ST/Ecma-119.pdf
	 * \author Christian SCHROETTER, (http://www.quatilfait.fr)
	 *  - 1 Volume Descriptor Type numerical value
	 *  - 2 to 6 Standard Identifier CD001
	 *  - 7 Volume Descriptor Version numerical value
	 *  - 8 to 39 Boot System Identifier a-characters
	 *  - 40 to 71 Boot Identifier a-characters
	 *  - 72 to 2 048 Boot System Use not specified
	 * \example examples/iso_base_test.php
	 * \see CDescriptor
	 * \see BOOT_RECORD_DESC
	 * \copyright Creative Commons (BY NC SA)
	 * The holder allows the use of the original in non-commercial work and the creation of derived works provided they are distributed in a manner identical to the one that governs the original work license.
	*/
	class CBootDescriptor extends CDescriptor
	{
		/**
		 * \public
		 * \param string $BootSysId
		 * \brief Boot System Identifier
		 * Specify an identification of a system which can recognize and act upon the content of the Boot Identifier and Boot System Use fields in the Boot Record
		*/
		var $BootSysId = '';
		/**
		 * \public
		 * \param string $BootId
		 * \brief Boot Identifier
		 * An identification of the boot system specified in the Boot System Use field of the Boot Record.
		*/
		var $BootId = '';
		/**
		 * \public
		 * \param array $SysUse
		 * \brief Boot System Use
		 * Reserved for boot system use. Its content is not specified
		*/
		var $SysUse = NULL;

		/**
		 * \fn public function __construct()
		 * \brief Init the class and set the name
		*/
		public function __construct()
		{
			$this->name = 'Boot volume descriptor';
		}
		/**
		 * \fn public function Create($desc, $isoFile, $offset)
		 * \param CDescriptor $desc[in|out] the descriptor to load data to
		 * \param CISOFile $isoFile[in] The actual ISO File to read from
		 * \param int $offset[in] The data offset within the actual buffer
		 * \brief Load the "Boot Descriptors"
		 * \return boolean true OR false
		 * \see CDescriptor
		 * \see $bytes
		 * \see BOOT_RECORD_DESC
		*/
		public function Create(&$desc, &$isoFile, &$offset)
		{
			if(!parent::Create($desc, $isoFile, $offset))
				return false;

			$this->BootSysId = CBuffer::GetString($this->bytes, 32, $offset);
			$this->BootId = CBuffer::GetString($this->bytes, 32, $offset);
			$this->BootCatalogLocation = CBuffer::ReadLSB($this->bytes, 4, $offset);

			// free some space...
			unset($this->bytes);

			return true;
		}
		/**
		 * \fn public function LoadBootCatalog($isoFile, $Blocksize)
		 * \param CISOFile $isoFile[in] The actual ISO File to read from
		 * \param int $Blocksize[in] The size of one LB
		 * \brief Load the "Boot Catalog"
		 * \return CBootCatalog OT NULL
		 * \see CBootCatalog
		*/
		public function LoadBootCatalog(&$isoFile, $Blocksize)
		{
			if(!$isoFile->IsValid())
				return NULL;

			$location = $this->BootCatalogLocation * $Blocksize;
			if(!$isoFile->Seek($location, SEEK_SET))
				return NULL;

			$string = $isoFile->Read(2048);
			if($string === false)
				return NULL;

			$offset = 1;
			$bytes = unpack('C*', $string);

			$bootCat = new CBootCatalog();
			$bootCat->header = $bytes[$offset++];
			$bootCat->platformID = $bytes[$offset++];
			$bootCat->reserved = CBuffer::GetBytes($bytes, 2, $offset);
			$bootCat->manufacturerID = CBuffer::GetString($bytes, 24, $offset);
			$bootCat->checksum = CBuffer::GetBytes($bytes, 2, $offset);
			$bootCat->key1 = $bytes[$offset++];
			$bootCat->key2 = $bytes[$offset++];

			if(!$bootCat->IsValid()) {
				return NULL;
			}

			return $bootCat;
		}
		/**
		 * \fn public function GetBootCatalogEntryCount($isoFile, $Blocksize)
		 * \param CISOFile $isoFile[in] The actual ISO File to read from
		 * \param int $Blocksize[in] The size of one LB
		 * \brief Get the number of "Boot Catalog Entry"
		 * \return int 0 OR higher
		 * \see CBootCatalogEntry
		*/
		public function GetBootCatalogEntryCount(&$isoFile, $Blocksize)
		{
			if(!$isoFile->IsValid())
				return 0;

			$location = $this->BootCatalogLocation * $Blocksize;
			if(!$isoFile->Seek($location, SEEK_SET))
				return 0;

			$string = $isoFile->Read(2048);
			if($string === false)
				return NULL;

			$offset = 1 + 0x20;
			$bytes = unpack('C*', $string);

			$count = 0;
			while($bytes[$offset] == 0x88)
			{
				$offset += 0x20;
				$count++;
			}

			return $count;
		}
		/**
		 * \fn public function LoadBootCatalogEntry($isoFile, $Blocksize, $entry_index)
		 * \param CISOFile $isoFile[in] The actual ISO File to read from
		 * \param int $Blocksize[in] The size of one LB
		 * \param int $entry_index[in] The "Boot Catalog Entry" index to load. (0 for default entry)
		 * \brief Return a "Boot Catalog Entry"
		 * \return object CBootCatalogEntry OR NULL
		 * \see CBootCatalogEntry
		*/
		public function LoadBootCatalogEntry(&$isoFile, $Blocksize, $entry_index)
		{
			if(!$isoFile->IsValid())
				return NULL;

			$location = $this->BootCatalogLocation * $Blocksize;
			if(!$isoFile->Seek($location, SEEK_SET))
				return NULL;

			$string = $isoFile->Read(2048);
			if($string === false)
				return NULL;

			$offset = 1 + 0x20 + ($entry_index * 0x20);
			$bytes = unpack('C*', $string);

			$bootCatEntry = new CBootCatalogEntry();
			$bootCatEntry->indicator = $bytes[$offset++];
			$bootCatEntry->mediaType = $bytes[$offset++];
			$bootCatEntry->loadSegment = CBuffer::ReadLSB($bytes, 2, $offset);
			$bootCatEntry->systemType = $bytes[$offset++];
			$bootCatEntry->unused = $bytes[$offset++];
			$bootCatEntry->sectorCount = CBuffer::ReadLSB($bytes, 2, $offset);
			$bootCatEntry->loadRDA = CBuffer::ReadLSB($bytes, 4, $offset);

			if(!$bootCatEntry->IsValid())
				return NULL;

			return $bootCatEntry;
		}
	}
?>