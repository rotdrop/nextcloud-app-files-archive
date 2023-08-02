<?php
	define ('NOT_SET_DESC', -1);
	define ('BOOT_RECORD_DESC', 0);
	define ('PRIMARY_VOLUME_DESC', 1);
	define ('SUPPLEMENTARY_VOLUME_DESC', 2);
	define ('PARTITION_VOLUME_DESC', 3);
	define ('TERMINATOR_DESC', 255);

	/**
	 * \class CDescriptor
	 * \brief An ISO-9660 "Volume Descriptors", the base class for all others descriptors.
	 * \see http://www.ecma-international.org/publications/files/ECMA-ST/Ecma-119.pdf
	 * \author Christian SCHROETTER, (http://www.quatilfait.fr)
	 *  - 1 Volume Descriptor Type numerical value
	 *  - 2 to 6 Standard Identifier CD001
	 *  - 7 Volume Descriptor Version numerical value
	 *  - 8 to 2048 (Depends on Volume Descriptor Type) (Depends on Volume Descriptor Type)
	 * \example examples/iso_base_test.php
	 * \copyright Creative Commons (BY NC SA)
	 * The holder allows the use of the original in non-commercial work and the creation of derived works provided they are distributed in a manner identical to the one that governs the original work license.
	*/
	class CDescriptor
	{
		/**
		 * \protected
		 * \param int $VolDescType
		 * \brief The "Volume Descriptors"'s type
		*/
		protected $VolDescType = NOT_SET_DESC;
		/**
		 * \protected
		 * \param array $StdId
		 * \brief The "Volume Descriptors"'s identifier ("CD001")
		*/
		protected $StdId = array();
		/**
		 * \protected
		 * \param int $Version
		 * \brief The "Volume Descriptors"'s version 
		*/
		protected $Version = 0;
		/**
		 * \protected
		 * \param string $name
		 * \brief A human readable string representing the type of the "Volume Descriptors"
		*/
		protected $name = '';
		/**
		 * \protected
		 * \param array $bytes
		 * \brief The block of data for that "Volume Descriptors"
		*/
		protected $bytes = NULL;

		/**
		 * \fn public function ReadDescriptor(&$isoFile)
		 * \param CISOFile $isoFile The actual ISO File to read from
		 * \brief Load the "Volume Descriptors" commons informations and the "sub" volume based on its type
		 * \return object CDescriptor derived OR NULL
		 * \see CVolumeDescriptor
		 * \see CTerminatorDescriptor
		 * \see CPartitionDescriptor
		 * \see CBootDescriptor
		*/
		static public function ReadDescriptor(&$isoFile)
		{
			$offset = 0;
			$desc = new CDescriptor();
			if(!$desc->Load($isoFile, $offset))
				return NULL;

			$descFinal = NULL;
			if($desc->GetType() == BOOT_RECORD_DESC)
			{
				$descFinal = new CBootDescriptor();
				$descFinal->Create($desc, $isoFile, $offset);
			}
			else if($desc->GetType() == PRIMARY_VOLUME_DESC)
			{
				$descFinal = new CVolumeDescriptor();
				$descFinal->Create($desc, $isoFile, $offset);
			}
			else if($desc->GetType() == SUPPLEMENTARY_VOLUME_DESC)
			{
				$descFinal = new CVolumeDescriptor();
				$descFinal->Create($desc, $isoFile, $offset);
			}
			else if($desc->GetType() == PARTITION_VOLUME_DESC)
			{
				$descFinal = new CPartitionDescriptor();
				$descFinal->Create($desc, $isoFile, $offset);
			}
			else if($desc->GetType() == TERMINATOR_DESC)
			{
				$descFinal = new CTerminatorDescriptor();
				$descFinal->Create($desc, $isoFile, $offset);
			}

			return $descFinal;
		}
		/**
		 * \fn public function GetType()
		 * \brief return the "Volume Descriptors"'s type
		 * \return int The "Volume Descriptors" type
		*/
		public function GetType(){return $this->VolDescType;}
		/**
		 * \fn public function GetID()
		 * \brief return the "Volume Descriptors"'s identifier
		 * \return string The "Volume Descriptors" identifier
		*/
		public function GetID(){return $this->StdId;}
		/**
		 * \fn public function GetVersion()
		 * \brief return the "Volume Descriptors"'s version
		 * \return int The "Volume Descriptors" version
		*/
		public function GetVersion(){return $this->Version;}
		/**
		 * \fn public function GetName()
		 * \brief return the "Volume Descriptors"'s name
		 * \return string The "Volume Descriptors" name
		*/
		public function GetName(){return $this->name;}
		/**
		 * \fn public function Create($desc, $isoFile, $offset)
		 * \param CDescriptor $desc The "Volume Descriptors" to clone basic info from
		 * \param CISOFile $isoFile The actual ISO File to read from
		 * \param int $offset The offset within the datas
		 * \brief Clone and complete data (none for the base class)
		 * \return boolean true OR false
		*/
		public function Create(&$desc, &$isoFile, &$offset)
		{
			if($desc->bytes == NULL)
				return false;

			$this->bytes = $desc->bytes;

			$this->VolDescType = $desc->VolDescType;
			$this->StdId = $desc->StdId;
			$this->Version = $desc->Version;

			return true;
		}
		/**
		 * \fn public function Load($isoFile, $offset)
		 * \param CISOFile $isoFile The actual ISO File to read from
		 * \param int $offset The offset within the datas
		 * \brief Load the "Volume Descriptors" from an image file at current pposition
		 * \return boolean true OR false
		*/
		public function Load(&$isoFile, &$offset)
		{
			if(!is_object($isoFile) || get_class($isoFile) != 'CISOFile' || !$isoFile->IsValid())
				return false;

			$string = $isoFile->Read(2048);
			if($string === false)
				return false;

			$this->bytes = unpack('C*', $string);

			$offset = 1;
			$this->VolDescType = $this->bytes[$offset];						$offset += 1;
			$this->StdId = CBuffer::GetString($this->bytes, 5, $offset);
			$this->Version = $this->bytes[$offset];							$offset += 1;

			return true;
		}
	}
?>