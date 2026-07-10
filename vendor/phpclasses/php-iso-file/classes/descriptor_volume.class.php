<?php
	/**
	 * \class CVolumeDescriptor
	 * \brief An ISO-9660 "Primary/Supplementary Volume Descriptors"
	 * \see http://www.ecma-international.org/publications/files/ECMA-ST/Ecma-119.pdf
	 * \author Christian SCHROETTER, (http://www.quatilfait.fr)
	 *  - PRIMARY
	 *  - 1 Volume Descriptor Type numerical value
	 *  - 2 to 6 Standard Identifier CD001
	 *  - 7 Volume Descriptor Version numerical value
	 *  - 8 to 2048 (Reserved for future standardization) (00) bytes
	 *  - 8 Unused Field (00) byte
	 *  - 9 to 40 System Identifier a-characters
	 *  - 41 to 72 Volume Identifier d-characters
	 *  - 73 to 80 Unused Field (00) bytes
	 *  - 81 to 88 Volume Space Size numerical value
	 *  - 89 to 120 Unused Field (00) bytes
	 *  - 121 to 124 Volume Set Size numerical value
	 *  - 125 to 128 Volume Sequence Number numerical value
	 *  - 129 to 132 Logical Block Size numerical value
	 *  - 133 to 140 Path Table Size numerical value
	 *  - 141 to 144 Location of Occurrence of Type L Path Table numerical value
	 *  - 145 to 148 Location of Optional Occurrence of Type L Path Table numerical value
	 *  - 149 to 152 Location of Occurrence of Type M Path Table numerical value
	 *  - 153 to 156 Location of Optional Occurrence of Type M Path Table numerical value
	 *  - 157 to 190 Directory Record for Root Directory 34 bytes
	 *  - 191 to 318 Volume Set Identifier d-characters
	 *  - 319 to 446 Publisher Identifier a-characters
	 *  - 447 to 574 Data Preparer Identifier a-characters
	 *  - 575 to 702 Application Identifier a-characters
	 *  - 703 to 739 Copyright File Identifier d-characters, SEPARATOR 1, SEPARATOR 2
	 *  - 740 to 776 Abstract File Identifier d-characters, SEPARATOR 1, SEPARATOR 2
	 *  - 777 to 813 Bibliographic File Identifier d-characters, SEPARATOR 1, SEPARATOR 2
	 *  - 814 to 830 Volume Creation Date and Time Digit(s), numerical value
	 *  - 831 to 847 Volume Modification Date and Time Digit(s), numerical value
	 *  - 848 to 864 Volume Expiration Date and Time Digit(s), numerical value
	 *  - 865 to 881 Volume Effective Date and Time Digit(s), numerical value
	 *  - 882 File Structure Version numerical value
	 *  - 883 (Reserved for future standardization) (00) byte
	 *  - 884 to 1395 Application Use not specified
	 *  - 1396 to 2048 (Reserved for future standardization) (00) bytes
	 *  - SUPPLEMENTARY
	 *  - 1 Volume Descriptor Type numerical value
	 *  - 2 to 6 Standard Identifier CD001
	 *  - 7 Volume Descriptor Version numerical value
	 *  - 8 Volume Flags 8 bits
	 *  - 9 to 40 System Identifier a-characters
	 *  - 41 to 72 Volume Identifier d-characters
	 *  - 73 to 80 Unused Field (00) bytes
	 *  - 81 to 88 Volume Space Size numerical value
	 *  - 89 to 120 Escape Sequences 32 bytes
	 *  - 121 to 124 Volume Set Size numerical value
	 *  - 125 to 128 Volume Sequence Number numerical value
	 *  - 129 to 132 Logical Block Size numerical value
	 *  - 133 to 140 Path Table Size numerical value
	 *  - 141 to 144 Location of Occurrence of Type L Path Table numerical value
	 *  - 145 to 148 Location of Optional Occurrence of Type L Path Table numerical value
	 *  - 149 to 152 Location of Occurrence of Type M Path Table numerical value
	 *  - 153 to 156 Location of Optional Occurrence of Type M Path Table numerical value
	 *  - 157 to 190 Directory Record for Root Directory 34 bytes
	 *  - 191 to 318 Volume Set Identifier d-characters
	 *  - 319 to 446 Publisher Identifier a-characters
	 *  - 447 to 574 Data Preparer Identifier a-characters
	 *  - 575 to 702 Application Identifier a-characters
	 *  - 703 to 739 Copyright File Identifier d-characters, SEPARATOR 1, SEPARATOR 2
	 *  - 740 to 776 Abstract File Identifier d-characters, SEPARATOR 1, SEPARATOR 2
	 *  - 777 to 813 Bibliographic File Identifier d-characters, SEPARATOR 1, SEPARATOR 2
	 *  - 814 to 830 Volume Creation Date and Time Digit(s), numerical value
	 *  - 831 to 847 Volume Modification Date and Time Digit(s), numerical value
	 *  - 848 to 864 Volume Expiration Date and Time Digit(s), numerical value
	 *  - 865 to 881 Volume Effective Date and Time Digit(s), numerical value
	 *  - 882 File Structure Version numerical value
	 *  - 883 (Reserved for future standardization) (00) byte
	 *  - 884 to 1395 Application Use not specified
	 *  - 1396 to 2048 (Reserved for future standardization) (00) bytes
	 * \example examples/iso_base_test.php
	 * \see CDescriptor
	 * \see PRIMARY_VOLUME_DESC
	 * \see SUPPLEMENTARY_VOLUME_DESC
	 * \copyright Creative Commons (BY NC SA)
	 * The holder allows the use of the original in non-commercial work and the creation of derived works provided they are distributed in a manner identical to the one that governs the original work license.
	*/
	class CVolumeDescriptor extends CDescriptor
	{
		/**
		 * \fn public function __construct()
		 * \brief Init the class and set the name
		*/
		public function __construct()
		{
			$this->name = 'Primary or Supplementary volume descriptor';
		}
		/**
		 * \fn public function Create($desc, $isoFile, $offset)
		 * \param CDescriptor $desc[in|out] the descriptor to load data to
		 * \param CISOFile $isoFile[in] The actual ISO File to read from
		 * \param int &$offset[in|out] The data offset within the actual buffer
		 * \brief Load the "Primary/Supplementary Descriptors"
		 * \return boolean true OR false
		 * \see CDescriptor
		 * \see $bytes
		 * \see PRIMARY_VOLUME_DESC
		 * \see SUPPLEMENTARY_VOLUME_DESC
		*/
		public function Create(&$desc, &$isoFile, &$offset)
		{
			if(!parent::Create($desc, $isoFile, $offset))
				return false;

			if($this->VolDescType == SUPPLEMENTARY_VOLUME_DESC)
				$this->name = 'Supplementary volume descriptor';
			else
				$this->name = 'Primary volume descriptor';

			$this->Unused1OrFlag = $this->bytes[$offset++];

			$this->stra_SystemId = CBuffer::ReadAString($this->bytes, 32, $offset, ($this->VolDescType == SUPPLEMENTARY_VOLUME_DESC));
			$this->strd_VolumeId = CBuffer::ReadDString($this->bytes, 32, $offset, ($this->VolDescType == SUPPLEMENTARY_VOLUME_DESC));

			$this->byUnused2 = CBuffer::GetBytes($this->bytes, 8, $offset);

			$this->i_bbo_VolSpaceSize = CBuffer::ReadBBO($this->bytes, 8, $offset);

			$this->EscapeSequences = CBuffer::GetBytes($this->bytes, 32, $offset);

			$this->iVolSetSize = CBuffer::ReadBBO($this->bytes, 4, $offset);
			$this->iVolSeqNum = CBuffer::ReadBBO($this->bytes, 4, $offset);
			$this->iBlockSize = CBuffer::ReadBBO($this->bytes, 4, $offset, true);
			$this->iPathTableSize = CBuffer::ReadBBO($this->bytes, 8, $offset);

			$this->i_lsb_LPathTablePos = CBuffer::ReadLSB($this->bytes, 4, $offset);
			$this->i_lsb_OptLPathTablePos = CBuffer::ReadLSB($this->bytes, 4, $offset);
			$this->i_msb_MPathTablePos = CBuffer::ReadMSB($this->bytes, 4, $offset);
			$this->i_msb_OptMPathTablePos = CBuffer::ReadMSB($this->bytes, 4, $offset);

			$this->byRootDirRec = new CFileDirDescriptors();
			$this->byRootDirRec->Init($this->bytes, $offset, ($this->VolDescType == SUPPLEMENTARY_VOLUME_DESC));

			$this->strd_VolSetId = CBuffer::ReadDString($this->bytes, 128, $offset, ($this->VolDescType == SUPPLEMENTARY_VOLUME_DESC));
			$this->stra_PublisherId = CBuffer::ReadAString($this->bytes, 128, $offset, ($this->VolDescType == SUPPLEMENTARY_VOLUME_DESC));
			$this->stra_PreparerId = CBuffer::ReadAString($this->bytes, 128, $offset, ($this->VolDescType == SUPPLEMENTARY_VOLUME_DESC));
			$this->stra_AppId = CBuffer::ReadAString($this->bytes, 128, $offset, ($this->VolDescType == SUPPLEMENTARY_VOLUME_DESC));

			$this->strd_CopyrightFileId = CBuffer::ReadDString($this->bytes, 37, $offset, ($this->VolDescType == SUPPLEMENTARY_VOLUME_DESC));
			$this->strd_AbstractFileId = CBuffer::ReadDString($this->bytes, 37, $offset, ($this->VolDescType == SUPPLEMENTARY_VOLUME_DESC));

			$this->strd_BibliographicFileId = CBuffer::ReadDString($this->bytes, 37, $offset, ($this->VolDescType == SUPPLEMENTARY_VOLUME_DESC));

			$this->dtCreation = new CIsoDate();
			$this->dtCreation->Init17($this->bytes, $offset);

			$this->dtModification = new CIsoDate();
			$this->dtModification->Init17($this->bytes, $offset);

			$this->dtExpiration = new CIsoDate();
			$this->dtExpiration->Init17($this->bytes, $offset);

			$this->dtEffective = new CIsoDate();
			$this->dtEffective->Init17($this->bytes, $offset);

			$this->byFileStructureVersion = $this->bytes[$offset++];

			$this->stra_SystemId = trim($this->stra_SystemId);
			$this->strd_VolumeId = trim($this->strd_VolumeId);
			$this->strd_VolSetId = trim($this->strd_VolSetId);
			$this->stra_PublisherId = trim($this->stra_PublisherId);
			$this->stra_PreparerId = trim($this->stra_PreparerId);
			$this->stra_AppId = trim($this->stra_AppId);
			$this->strd_CopyrightFileId = trim($this->strd_CopyrightFileId);
			$this->strd_AbstractFileId = trim($this->strd_AbstractFileId);
			$this->strd_BibliographicFileId = trim($this->strd_BibliographicFileId);

			// free some space...
			unset($this->bytes);

			return true;
		}
		/**
		 * \fn public function IsMPathTable()
		 * \brief Tell if a "M Path Table" is present
		 * \return boolean true OR false
		 * \see $i_msb_MPathTablePos
		*/
		public function IsMPathTable()
		{
			return ($this->i_msb_MPathTablePos != 0);
		}
		/**
		 * \fn public function IsMPathTable()
		 * \brief Tell if a "L Path Table" is present
		 * \return boolean true OR false
		 * \see $i_msb_LPathTablePos
		*/
		public function IsLPathTable()
		{
			return ($this->i_msb_LPathTablePos != 0);
		}
		/**
		 * \fn public function LoadMPathTable($isoFile)
		 * \param CISOFile &$isoFile The actual ISO File to read from
		 * \brief Load the "M Path Table"
		 * \return array NULL OR CPathTableRecord[]
		 * \warning L-Table are LSB-first and the values in the M-Table are MSB-first
		 * \see LoadGenPathTable
		**/
		public function LoadMPathTable(&$isoFile)
		{
			return $this->LoadGenPathTable($isoFile, $this->i_msb_MPathTablePos);
		}
		/**
		 * \fn public function LoadLPathTable($isoFile)
		 * \param CISOFile $isoFile The actual ISO File to read from
		 * \brief Load the "L Path Table"
		 * \return array NULL OR CPathTableRecord[]
		 * \warning L-Table are LSB-first and the values in the M-Table are MSB-first
		 * \see LoadGenPathTable
		**/
		public function LoadLPathTable(&$isoFile)
		{
			return $this->LoadGenPathTable($isoFile, $this->i_lsb_LPathTablePos);
		}
		/**
		 * \fn public function LoadGenPathTable(&$isoFile, $iPathTablePos)
		 * \param CISOFile $isoFile The actual ISO File to read from
		 * \param int $iPathTablePos The location of the path table
		 * \brief Load the "L Path Table" OR "M Path Table"
		 * \return array NULL OR CPathTableRecord[]
		 * \warning L-Table are LSB-first and the values in the M-Table are MSB-first
		 * \see LoadLPathTable
		 * \see LoadMPathTable
		**/
		protected function LoadGenPathTable(&$isoFile, $iPathTablePos)
		{
			if($iPathTablePos == 0 || $this->iBlockSize == 0) {
				return NULL;
			}

			if(!$isoFile->Seek($iPathTablePos * $this->iBlockSize, SEEK_SET))
				return NULL;

			$iPathTableSize = CBuffer::Align($this->iPathTableSize, $this->iBlockSize);

			$string = $isoFile->Read($iPathTableSize);
			$bytes = unpack('C*', $string);

			$pathTable = array();

			$offset = $DirNum = 1;
			$ptRec = new CPathTableRecord();
			$bres = $ptRec->Init($bytes, $offset, ($this->VolDescType == SUPPLEMENTARY_VOLUME_DESC));
			while($bres === true)
			{
				$ptRec->SetDirectoryNumber($DirNum);
				$ptRec->LoadExtents($isoFile, $this->iBlockSize, true);

				$pathTable[$DirNum] = $ptRec;
				$DirNum++;

				$ptRec = new CPathTableRecord();
				$bres = $ptRec->Init($bytes, $offset, ($this->VolDescType == SUPPLEMENTARY_VOLUME_DESC));
			}

			return $pathTable;
		}
	}
?>