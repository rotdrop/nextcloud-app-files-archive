<?php
	/**
	 * \brief File is hidden
	 * 
	 * If set to ZERO, shall mean that the existence of the file shall be made known to the
	 * user upon an inquiry by the user.
	 * If set to ONE, shall mean that the existence of the file need not be made known to
	 * the user.
	 * 
	 * \copyright Creative Commons (BY NC SA)
	 * The holder allows the use of the original in non-commercial work and the creation of derived works provided they are distributed in a manner identical to the one that governs the original work license.
	*/
	define('FILE_MODE_HIDDEN', 0x01);
	/**
	 * \brief File is a directory
	 * 
	 * If set to ZERO, shall mean that the Directory Record does not identify a directory.
	 * If set to ONE, shall mean that the Directory Record identifies a directory.
	 * 
	*/
	define('FILE_MODE_DIRECTORY', 0x02);
	/**
	 * \brief File isassociated
	 * 
	 * If set to ZERO, shall mean that the file is not an Associated File.
	 * If set to ONE, shall mean that the file is an Associated File.
	 * 
	*/
	define('FILE_MODE_ASSOCIATED', 0x04);
	/**
	 * \brief File is file info are recorded
	 * 
	 * If set to ZERO, shall mean that the structure of the information in the file is not
	 * specified by the Record Format field of any associated Extended Attribute Record (see 9.5.8).
	 * If set to ONE, shall mean that the structure of the information in the file has a
	 * record format specified by a number other than zero in the Record Format Field of
	 * the Extended Attribute Record (see 9.5.8).
	 * 
	*/
	define('FILE_MODE_RECORD', 0x08);
	/**
	 * \brief File is protected
	 * 
	 * If set to ZERO, shall mean that
	 * - an Owner Identification and a Group Identification are not specified for the file (see 9.5.1 and 9.5.2);
	 * - any user may read or execute the file (see 9.5.3). If set to ONE, shall mean that
	 * - an Owner Identification and a Group Identification are specified for the file (see 9.5.1 and 9.5.2);
	 * - at least one of the even-numbered bits or bit 0 in the Permissions field of the associated Extended Attribute Record is set to ONE (see 9.5.3).
	 * 
	*/
	define('FILE_MODE_PROTECTED', 0x10);
	/**
	 * \brief File has multi extent
	 * 
	 * If set to ZERO, shall mean that this is the final Directory Record for the file.
	 * If set to ONE, shall mean that this is not the final Directory Record for the file.
	 * 
	*/
	define('FILE_MODE_MULTI_EXTENT', 0x80);

	/**
	 * \class CFileDirDescriptors
	 * \see http://www.ecma-international.org/publications/files/ECMA-ST/Ecma-119.pdf
	 * \author Christian SCHROETTER, (http://www.quatilfait.fr)
	 * \brief An ISO-9660 "Directory Record"
	 * 
	 * - 1 Length of Directory Record (LEN-DR) numerical value
	 * - 2 Extended Attribute Record Length numerical value
	 * - 3 to 10 Location of Extent numerical value
	 * - 11 to 18 Data Length numerical value
	 * - 19 to 25 Recording Date and Time numerical values
	 * - 26 File Flags 8 bits
	 * - 27 File Unit Size numerical value
	 * - 28 Interleave Gap Size numerical value
	 * - 29 to 32 Volume Sequence Number numerical value
	 * - 33 Length of File Identifier (LEN_FI) numerical value
	 * - 34 to (33+LEN_FI) File Identifier d-characters, d1-characters, SEPARATOR 1, SEPARATOR 2, (00) or (01) byte 
	 * - (34 + LEN_FI) Padding Field (00) byte 
	 * - (LEN_DR - LEN_SU + 1) to LEN_DR System Use LEN_SU bytes
	 * 
	 * \example examples/iso_base_test.php
	*/
	class CFileDirDescriptors
	{
		/**
		 * \public
		 * \param int $DirRecLen
		 * \brief The length of the "Directory Record"
		*/
		var $DirRecLen;
		/**
		 * \public
		 * \param int $ExtAttrRecLen
		 * \brief The length of the "Directory Record" extended attribut record
		*/
		var $ExtAttrRecLen;
		/**
		 * \public
		 * \param int $Location
		 * \brief Location of extents
		*/
		var $Location;
		/**
		 * \public
		 * \param int $DataLen
		 * \brief The length of the data (the content for a file, the "child file & folder for a directory...
		*/
		var $DataLen;
		/**
		 * \public
		 * \param CIsoDate $isoRecDate
		 * \brief The recording date
		*/
		var $isoRecDate;
		/**
		 * \public
		 * \param int $FileFlags
		 * \brief File (or folder) flags.
		*/
		var $FileFlags;
		/**
		 * \public
		 * \param int $FileUnitSize
		 * \brief The File Unit Size
		*/
		var $FileUnitSize;
		/**
		 * \public
		 * \param int $InterleaveGapSize
		 * \brief The Interleave Gap Size
		*/
		var $InterleaveGapSize;
		/**
		 * \public
		 * \param int $VolSeqNum
		 * \brief The ordinal number of the volume in the Volume Set
		*/
		var $VolSeqNum;
		/**
		 * \public
		 * \param int $FileIdLen
		 * \brief The length of the file identifier
		*/
		var $FileIdLen;
		/**
		 * \public
		 * \param string $strd_FileId
		 * \brief The file identifier
		*/
		var $strd_FileId;

		/**
		 * \fn public function Init($buffer, $offset, $supplementary)
		 * \param array $bytes The datas
		 * \param int $offset The offset within the datas
		 * \param boolean $supplementary Is this a "Path Table Record" from a primary (8 bits for 1 char) or supplementary (16 bits for 1 char) volume descriptor
		 * \brief Load the "Directory Record" from buffer
		 * \return true OR false
		 * \see CVolumeDescriptor
		*/
		public function Init(&$buffer, &$offset, $supplementary)
		{
			$tmp = $offset;

			$this->DirRecLen = $buffer[$tmp++];
			if($this->DirRecLen == 0) {
				return false;
			}

			$this->ExtAttrRecLen = $buffer[$tmp++];

			$this->Location = CBuffer::ReadBBO($buffer, 8, $tmp);
			$this->DataLen = CBuffer::ReadBBO($buffer, 8, $tmp);

			$this->isoRecDate = new CIsoDate();
			$this->isoRecDate->Init7($buffer, $tmp);

			$this->FileFlags = $buffer[$tmp++];
			$this->FileUnitSize = $buffer[$tmp++];
			$this->InterleaveGapSize = $buffer[$tmp++];

			$this->VolSeqNum = CBuffer::ReadBBO($buffer, 4, $tmp);

			$this->FileIdLen = $buffer[$tmp++];

			if($this->FileIdLen == 1 && $buffer[$tmp] == 0) {
				$this->strd_FileId = '.';
				$tmp++;
			} else if($this->FileIdLen == 1 && $buffer[$tmp] == 1) {
				$this->strd_FileId = '..';
				$tmp++;
			} else {
				$this->strd_FileId = CBuffer::ReadDString($buffer, $this->FileIdLen, $tmp, $supplementary);

				$pos = strpos($this->strd_FileId, ';1');
				if($pos !== false && $pos == strlen($this->strd_FileId) - 2) {
					$this->strd_FileId = substr($this->strd_FileId, 0, strlen($this->strd_FileId) - 2);
				}
			}

			$offset += $this->DirRecLen;
			return true;
		}
		/**
		 * \fn public function IsHidden()
		 * \brief Test if the "Directory Record" is hidden
		 * \return boolean true OR false
		 * \see FILE_MODE_HIDDEN
		*/
		public function IsHidden()		{return ( ($this->FileFlags & FILE_MODE_HIDDEN) == FILE_MODE_HIDDEN);}
		/**
		 * \fn public function IsDirectory()
		 * \brief Test if the "Directory Record" is directory
		 * return boolean true OR false
		 * \see FILE_MODE_DIRECTORY
		*/
		public function IsDirectory()	{return ( ($this->FileFlags & FILE_MODE_DIRECTORY) == FILE_MODE_DIRECTORY);}
		/**
		 * \fn public function IsAssociated()
		 * \brief Test if the "Directory Record" is associated
		 * \return boolean true OR false
		 * \see FILE_MODE_ASSOCIATED
		*/
		public function IsAssociated()	{return ( ($this->FileFlags & FILE_MODE_ASSOCIATED) == FILE_MODE_ASSOCIATED);}
		/**
		 * \fn public function IsRecord()
		 * \brief Test if the "Directory Record" is record
		 * \return boolean true OR false
		 * \see FILE_MODE_RECORD
		*/
		public function IsRecord()		{return ( ($this->FileFlags & FILE_MODE_RECORD) == FILE_MODE_RECORD);}
		/**
		 * \fn public function IsProtected()
		 * \brief Test if the "Directory Record" is protected
		 * \return boolean true OR false
		 * \see FILE_MODE_PROTECTED
		*/
		public function IsProtected()	{return ( ($this->FileFlags & FILE_MODE_PROTECTED) == FILE_MODE_PROTECTED);}
		/**
		 * \fn public function IsMultiExtent()
		 * \brief Test if the "Directory Record" is a multi-extent
		 * \return boolean true OR false
		 * \see FILE_MODE_MULTI_EXTENT
		*/
		public function IsMultiExtent()	{return ( ($this->FileFlags & FILE_MODE_MULTI_EXTENT) == FILE_MODE_MULTI_EXTENT);}
		/**
		 * \fn public function IsThis()
		 * \brief Test if the "Directory Record" is a "node" to itself
		 * \return boolean true OR false
		*/
		public function IsThis()
		{
			if($this->FileIdLen > 1) {
				return false;
			}

			return ($this->strd_FileId == '.');
		}
		/**
		 * \fn public function IsParent()
		 * \brief Test if the "Directory Record" is a "node" to its parent
		 * \return boolean true OR false
		*/
		public function IsParent()
		{
			if($this->FileIdLen > 1) {
				return false;
			}

			return ($this->strd_FileId == '..');
		}

		/**
		 * \fn public function LoadExtents($isoFile, $BlockSize, $supplementary)
		 * \param CISOFile $isoFile The actual ISO File to read from
		 * \param int $BlockSize The size of 1 LB (Logical Block)
		 * \param boolean $supplementary Is this a "Path Table Record" from a primary (8 bits for 1 char) or supplementary (16 bits for 1 char) volume descriptor
		 * \brief Load the "File Directory Descriptors"(extents) from ISO file
		 * \see LoadExtents_st
		 * \return true OR false
		*/
		public function LoadExtents(&$isoFile, $BlockSize, $supplementary)
		{
			return CFileDirDescriptors::LoadExtents_st($isoFile, $BlockSize, $this->Location, $supplementary);
		}

		/**
		 * \fn static public function CFileDirDescriptors::LoadExtents_st($isoFile, $BlockSize, $Location, $supplementary)
		 * \brief Load the "File Directory Descriptors"(extents) from ISO file
		 * \param CISOFile $isoFile The actual ISO File to read from
		 * \param int $BlockSize The size of 1 LB (Logical Block)
		 * \param int $Location The location of the extent.
		 * \param boolean $supplementary Is this a "Path Table Record" from a primary (8 bits for 1 char) or supplementary (16 bits for 1 char) volume descriptor
		 * \return true OR false
		*/
		static public function LoadExtents_st(&$isoFile, $BlockSize, $Location, $supplementary)
		{
			if(!$isoFile->Seek($Location * $BlockSize, SEEK_SET))
				return false;

			$string = $isoFile->read(4096);
			if($string === false)
				return false;;

			$bytes = unpack('C*', $string);

			$offset = 1;
			$fdDesc = new CFileDirDescriptors();
			while($fdDesc->Init($bytes, $offset, $supplementary) !== false)
			{
				$Extents[] = $fdDesc;
				$fdDesc = new CFileDirDescriptors();
			}

			return $Extents;
		}
	}
?>