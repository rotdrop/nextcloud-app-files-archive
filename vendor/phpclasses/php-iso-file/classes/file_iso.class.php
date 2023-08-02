<?php
	/**
	 * \class CISOFile
	 * \brief Main acces to an ISO-9660 image file
	 * \see http://www.ecma-international.org/publications/files/ECMA-ST/Ecma-119.pdf
	 * \author Christian SCHROETTER, (http://www.quatilfait.fr)
	 * \example examples/iso_files_test.php
	 * \example examples/iso_base_test.php
	 * \example examples/BootCatalog_test.php
	 * \copyright Creative Commons (BY NC SA)
	 * The holder allows the use of the original in non-commercial work and the creation of derived works provided they are distributed in a manner identical to the one that governs the original work license.
	*/
	class CISOFile
	{
		/**
		 * \protected
		 * \param resource $file
		 * \brief The resource's file pointer to an ISO-9660 image file
		*/
		protected $file = NULL;
		/**
		 * \protected
		 * \param array $descriptors
		 * \brief The array of descritors found
		 * \see CDescriptor
		 * \see CVolumeDescriptor
		 * \see CTerminatorDescriptor
		 * \see CPartitionDescriptor
		 * \see CBootDescriptor
		*/
		protected $descriptors = array();

		/**
		 * \fn public function Open($filepath)
		 * \param string $filepath The path to the ISO-9660 image file
		 * \brief Open a file
		 * \see $file
		 * \return true OR false
		*/
		public function Open($filepath)
		{
			$this->file = fopen($filepath, 'r');
			return $this->IsValid();
		}
		/**
		 * \fn public function Close()
		 * \brief Close a previously openned file and clear vars.
		 * \see $file
		 * \see Open
		 * \return nothing
		*/
		public function Close()
		{
			if($this->file != NULL)
				fclose($this->file);

			$this->file = NULL;
			$this->descriptors = array();
		}
		/**
		 * \fn public function IsValid()
		 * \brief CreCheck that the resource file pointer is valid
		 * \see $file
		 * \see Open
		 * \see Close
		 * \return boolean true OR false
		*/
		public function IsValid()
		{
			return ($this->file !== false && $this->file != NULL);
		}
		/**
		 * \fn public function ISOInit()
		 * \brief Read the descriptors present in the ISO-9660 image file.
		 * \see $descriptors
		 * \return boolean true OR false
		*/
		public function ISOInit()
		{
			if(!$this->Seek(16 * 2048, SEEK_SET))
				return false;

			while(($desc = CDescriptor::ReadDescriptor($this)) != NULL) {
				$this->descriptors[$desc->GetType()] = $desc;
				if($desc->GetType() == TERMINATOR_DESC)
					break;
			}

			return count($this->descriptors) > 0;
		}
		/**
		 * \fn public function Seek($seek, $method)
		 * \param int $seek The length to seek
		 * \param int $method The seek method (from begin (SEEK_SET), from current (SEEK_CUR))
		 * \brief Move the file pointer position
		 * \see $file
		 * \return boolean true OR false
		*/
		public function Seek($seek, $method)
		{
			if(!$this->IsValid())
				return false;

			return (fseek($this->file, $seek, $method) != -1);
		}
		/**
		 * \fn public function Read($read)
		 * \param int $read	The amount of data to read
		 * \brief Read datas from file
		 * \see $file
		 * \return boolean/string false OR the datas read
		*/
		public function Read($read)
		{
			if(!$this->IsValid())
				return false;

			return fread($this->file, $read);
		}
		/**
		 * \fn public function GetDescriptor($type)
		 * \param int $type The type of descriptor to get
		 * \brief Get a descriptor by it's type
		 * \see $descriptors
		 * \see BOOT_RECORD_DESC
		 * \see PRIMARY_VOLUME_DESC
		 * \see SUPPLEMENTARY_VOLUME_DESC
		 * \see PARTITION_VOLUME_DESC
		 * \see TERMINATOR_DESC
		 * \see CVolumeDescriptor
		 * \see CTerminatorDescriptor
		 * \see CPartitionDescriptor
		 * \see CBootDescriptor
		 * \see CDescriptor
		 * \return CDescriptor The $type descriptor OR NULL
		*/
		public function GetDescriptor($type)
		{
			if(!isset($this->descriptors[$type]))
				return NULL;

			return $this->descriptors[$type];
		}
		/**
		 * \fn public function GetDescriptorCount()
		 * \brief Get the number of descriptors found
		 * \see $descriptors
		 * \see ISOInit
		 * \return int The number of descriptors
		*/
		public function GetDescriptorCount()
		{
			return count($this->descriptors);
		}
		/**
		 * \fn public function GetDescriptorAt($index)
		 * \brief Get the descriptors at index $index
		 * \see $descriptors
		 * \see GetDescriptorCount
		 * \see ISOInit
		 * \return CDescriptor The descriptors at index $index OR NULL
		*/
		public function GetDescriptorAt($index)
		{
			$current = 0;
			foreach($this->descriptors as $desc)
			{
				if($current == $index)
					return $desc;

				$current++;
			}

			return NULL;
		}
	}
?>