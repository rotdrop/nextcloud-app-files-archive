<?php
	/**
	 * \class CPartitionDescriptor
	 * \see http://www.ecma-international.org/publications/files/ECMA-ST/Ecma-119.pdf
	 * \author Christian SCHROETTER, (http://www.quatilfait.fr)
	 * \brief An ISO-9660 "Partition Volume Descriptors"
	 * 
	 *  - 1 Volume Descriptor Type numerical value
	 *  - 2 to 6 Standard Identifier CD001
	 *  - 7 Volume Descriptor Version numerical value
	 *  - 8 Unused Field (00) byte
	 *  - 9 to 40 System Identifier a-characters
	 *  - 41 to 72 Volume Partition Identifier d-characters
	 *  - 73 to 80 Volume Partition Location numerical value
	 *  - 81 to 88 Volume Partition Size numerical value
	 *  - 89 to 2048 System Use not specified
	 * 
	 * \example examples/iso_base_test.php
	 * \see CDescriptor
	 * \see PARTITION_VOLUME_DESC
	 * \todo Get some ISO images with such kind of descriptor to test/validate...
	 * \copyright Creative Commons (BY NC SA)
	 * The holder allows the use of the original in non-commercial work and the creation of derived works provided they are distributed in a manner identical to the one that governs the original work license.
	*/
	class CPartitionDescriptor extends CDescriptor
	{
		/**
		 * \public
		 * \param int $Unused
		 * \brief Unused parameter
		*/
		var $Unused;
		/**
		 * \public
		 * \param int $SystemID
		 * \brief The "Partition Volume Descriptors"'s System Identifier
		*/
		var $SystemID;
		/**
		 * \public
		 * \param int $VolPartitionID
		 * \brief The "Partition Volume Descriptors"'s Partition Identifier
		*/
		var $VolPartitionID;
		/**
		 * \public
		 * \param int $VolPartitionLocation
		 * \brief The "Partition Volume Descriptors"'s Partition location
		*/
		var $VolPartitionLocation;
		/**
		 * \public
		 * \param int $VolPartitionSize
		 * \brief The "Partition Volume Descriptors"'s Partition size
		*/
		var $VolPartitionSize;

		/**
		 * \fn public function __construct()
		 * \brief Init the class and set the name
		*/
		public function __construct()
		{
			$this->name = 'Partition volume descriptor';
		}
		/**
		 * \fn public function Create($desc, $isoFile, $offset)
		 * \param CDescriptor $desc[in|out] the descriptor to load data to
		 * \param CIsoFie $isoFile[in] The actual ISO File to read from
		 * \param int $offset[in|out] The data offset within the actual buffer
		 * \brief Load the "Partition Volume Descriptors"
		 * \return boolean true OR false
		 * \see CDescriptor
		 * \see $bytes
		 * \see PARTITION_VOLUME_DESC
		*/
		public function Create(&$desc, &$isoFile, &$offset)
		{
			if(!parent::Create($desc, $isoFile, $offset))
				return false;

			$this->Unused = $this->bytes[$offset++];

			$this->SystemID = CBuffer::ReadAString($this->bytes, 32, $offset);
			$this->VolPartitionID = CBuffer::ReadDString($this->bytes, 32, $offset);

			$this->VolPartitionLocation = CBuffer::ReadMSB($this->bytes, 8, $offset);
			$this->VolPartitionSize = CBuffer::ReadMSB($this->bytes, 8, $offset);

			// free some space...
			unset($this->bytes);

			return true;
		}
	}
?>