<?php
	/**
	 * \class CIsoDate
	 * \brief An ISO-9660 date
	 * \see http://www.ecma-international.org/publications/files/ECMA-ST/Ecma-119.pdf
	 * \author Christian SCHROETTER, (http://www.quatilfait.fr)
	 * \example examples/iso_base_test.php
	 * \copyright Creative Commons (BY NC SA)
	 * The holder allows the use of the original in non-commercial work and the creation of derived works provided they are distributed in a manner identical to the one that governs the original work license.
	*/
	class CIsoDate
	{
		/**
		 * \public
		 * \param int $year
		 * \brief The year
		*/
		var $year;
		/**
		 * \public
		 * \param int $month
		 * \brief The month
		*/
		var $month;
		/**
		 * \public
		 * \param int $day
		 * \brief The day
		*/
		var $day;
		/**
		 * \public
		 * \param int $hour
		 * \brief The hour
		*/
		var $hour;
		/**
		 * \public
		 * \param int $min
		 * \brief The minute
		*/
		var $min;
		/**
		 * \public
		 * \param int $sec
		 * \brief The second
		*/
		var $sec;
		/**
		 * \public
		 * \param int $DST
		 * \brief The Day Saving Time
		*/
		var $DST;

		/**
		 * \fn public function Init7($buffer, $offset)
		 * \param array $buffer The data to read from
		 * \param int $offset The actual position in the buffer
		 * \brief Create from a "7 bytes" date
		 * \see CFileDirDescriptors
		 * \return nothing
		*/
		public function Init7(&$buffer, &$offset)
		{
			$this->year = 1900 + $buffer[$offset + 0];
			$this->month = $buffer[$offset + 1];
			$this->day = $buffer[$offset + 2];
			$this->hour = $buffer[$offset + 3];
			$this->min = $buffer[$offset + 4];
			$this->sec = $buffer[$offset + 5];
			$this->DST = $buffer[$offset + 6];

			$offset += 7;
		}
		/**
		 * \fn public function Init17($buffer, $offset)
		 * \param array $buffer The data to read from
		 * \param int $offset The actual position in the buffer
		 * \brief Create from a "17 bytes" date
		 * \see CFileDirDescriptors
		 * \return nothing
		*/
		public function Init17(&$buffer, &$offset)
		{
			$Date = CBuffer::GetString($buffer, 16, $offset);

			$this->year = substr($Date, 0, 4);
			$this->month = substr($Date, 4, 2);
			$this->day = substr($Date, 6, 2);
			$this->hour = substr($Date, 8, 2);
			$this->min = substr($Date, 10, 2);
			$this->sec = substr($Date, 12, 2);
			$this->ms = substr($Date, 14, 2);
			$DST = $buffer[16];
		
			$offset += 1;
		}
		/**
		 * \fn public function __toString()
		 * \brief Display a human readable string representing the date
		 * \see CVolumeDescriptor
		 * \overload Magical method
		 * \return string The date
		*/
		public function __toString()
		{
			return $this->day . '-' . $this->month . '-' . $this->year . ' ' . $this->hour . ':' . $this->min . ':' . $this->sec;
		}
	}
?>