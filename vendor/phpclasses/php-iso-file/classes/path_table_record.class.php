<?php
	/**
	 * \class CPathTableRecord
	 * \brief An ISO-9660 Path Table Record
	 * \see http://www.ecma-international.org/publications/files/ECMA-ST/Ecma-119.pdf
	 * \author Christian SCHROETTER, (http://www.quatilfait.fr)
	 * \example examples/iso_files_test.php
	 * \copyright Creative Commons (BY NC SA)
	 * The holder allows the use of the original in non-commercial work and the creation of derived works provided they are distributed in a manner identical to the one that governs the original work license.
	*/
	class CPathTableRecord
	{
		/**
		 * \public
		 * \param int $DirNum
		 * \brief The directory number
		 * \see $ParentDirNum
		*/
		var $DirNum;
		/**
		 * \public
		 * \param int $DirIdLen
		 * \brief The length of the Dir Identifier
		*/
		var $DirIdLen;
		/**
		 * \public
		 * \param int $ExtAttrLen
		 * \brief The length of the extended attributs
		*/
		var $ExtAttrLen;
		/**
		 * \public
		 * \param int $Location The location of this "Path Table Record"
		 * \brief 
		*/
		var $Location;
		/**
		 * \public
		 * \param int $ParentDirNum The parent's directory number
		 * \brief 
		*/
		var $ParentDirNum;
		/**
		 * \public
		 * \param string $strd_DirId
		 * \brief The directory identifier.
		*/
		var $strd_DirId;

		/**
		 * \fn public function SetDirectoryNumber($DirNum)
		 * \param int $DirNum The directory number
		 * \brief Set the directory number
		 * \see $DirNum
		 * \see $ParentDirNum
		*/
		public function SetDirectoryNumber($DirNum)
		{
			$this->DirNum = $DirNum;
		}
		/**
		 * \fn public function Init($bytes, $offset, $supplementary)
		 * \param array $bytes The datas
		 * \param int $offset The offset within the datas
		 * \param boolean $supplementary Is this a "Path Table Record" from a primary (8 bits for 1 char) or supplementary (16 bits for 1 char) volume descriptor
		 * \brief Load the "Path Table Record" from buffer
		 * \see CVolumeDescriptor
		*/
		public function Init(&$bytes, &$offset, $supplementary)
		{
			$offset_tmp = $offset;

			$this->DirIdLen = $bytes[$offset_tmp++];

			if($this->DirIdLen == 0) {
				return false;
			}

			$this->ExtAttrLen = $bytes[$offset_tmp++];
			$this->Location = CBuffer::ReadInt32($bytes, $offset_tmp);
			$this->ParentDirNum = CBuffer::ReadInt16($bytes, $offset_tmp);
			$this->strd_DirId = CBuffer::ReadDString($bytes, $this->DirIdLen, $offset_tmp, $supplementary);

			if($this->DirIdLen % 2 != 0) {
				$offset_tmp++;
			}

			$this->strd_DirId = trim($this->strd_DirId);

			$offset = $offset_tmp;
			return true;
		}
		/**
		 * \fn public function LoadExtents($isoFile, $BlockSize, $supplementary)
		 * \param CISOFile $isoFile The actual ISO File to read from
		 * \param int $BlockSize The size of 1 LB (Logical Block)
		 * \param boolean $supplementary Is this a "Path Table Record" from a primary (8 bits for 1 char) or supplementary (16 bits for 1 char) volume descriptor
		 * \brief Load the "File Directory Descriptors"(extents) from ISO file
		 * \see CFileDirDescriptors
		*/
		public function LoadExtents(&$isoFile, $BlockSize, $supplementary)
		{
			return CFileDirDescriptors::LoadExtents_st($isoFile, $BlockSize, $this->Location, $supplementary);
		}

		/**
		 * \fn public function GetFullPath($pathTable)
		 * \param array $pathTable The "Path Table Record" array (loaded from CVolumeDescriptor::LoadMPathTable(...))
		 * \brief Build the full path of a CPathTableRecord object based on it's parent(s)
		 * \return string The full path
		 * \see CVolumeDescriptor
		 * \see LoadMPathTable
		*/
		public function GetFullPath($pathTable)
		{
			if($this->ParentDirNum == 1) {
				return '/' . $this->strd_DirId;
			}

			$path = $this->strd_DirId;
			$used = $pathTable[$this->ParentDirNum];

			while(1 == 1)
			{
				$path = $used->strd_DirId . '/' . $path;
				if($used->ParentDirNum == 1) {
					break;
				}

				$used = $pathTable[$used->ParentDirNum];
			}

			return $path;
		}
	}
