<?php
	/**
	 * \class CTerminatorDescriptor
	 * \brief An ISO-9660 "Volume Descriptor Set Terminator"
	 * \see http://www.ecma-international.org/publications/files/ECMA-ST/Ecma-119.pdf
	 * \author Christian SCHROETTER, (http://www.quatilfait.fr)
	 *  - 1 Volume Descriptor Type numerical value
	 *  - 2 to 6 Standard Identifier CD001
	 *  - 7 Volume Descriptor Version numerical value
	 *  - 8 to 2048 (Reserved for future standardization) (00) bytes
	 * \example examples/iso_base_test.php
	 * \see CDescriptor
	 * \see TERMINATOR_DESC
	 * \copyright Creative Commons (BY NC SA)
	 * The holder allows the use of the original in non-commercial work and the creation of derived works provided they are distributed in a manner identical to the one that governs the original work license.
	*/
	class CTerminatorDescriptor extends CDescriptor
	{
		/**
		 * \fn public function __construct()
		 * \brief Init the class and set the name
		*/
		public function __construct()
		{
			$this->name = 'Terminator descriptor';
		}
		/**
		 * \fn public function Create($desc, $isoFile, $offset)
		 * \param CDescriptor $desc[in|out] the descriptor to load data to
		 * \param CISOFile $isoFile[in] The actual ISO File to read from
		 * \param int $offset[in|out] The data offset within the actual buffer
		 * \brief Load the "Volume Descriptor Set Terminator"
		 * \return boolean true OR false
		 * \see CDescriptor
		 * \see $bytes
		 * \see TERMINATOR_DESC
		*/
		public function Create(&$desc, &$isoFile, &$offset)
		{
			if(!parent::Create($desc, $isoFile, $offset))
				return false;

			if($this->VolDescType != TERMINATOR_DESC)
				return false;

			// free some space...
			unset($this->bytes);

			return true;
		}
	}
?>