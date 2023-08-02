<?php
	/**
	 * \class CBuffer
	 * \brief Helper to access stream information. Use internaly by CIsoXXX classes.
	 * \author Christian SCHROETTER, (http://www.quatilfait.fr)
	 * \copyright Creative Commons (BY NC SA)
	 * The holder allows the use of the original in non-commercial work and the creation of derived works provided they are distributed in a manner identical to the one that governs the original work license.
	*/
	class CBuffer
	{
		/**
		 * \fn static public function Align($num, $align)
		 * \param int $num The number to align
		 * \param int $align The alignement
		 * \brief Align a number
		*/
		static public function Align($num, $align)
		{
			$tmp = (int)($num / $align);
			if((int)($num % $align) > 0)
				$tmp++;

			return $tmp * $align;
		}
		/**
		 * \fn static public function GetString($buffer, $length, $offset = 0, $supplementary)
		 * \param array $buffer The data to read from
		 * \param int $length The string length to read
		 * \param int $offset The actual position in the buffer
		 * \param boolean $supplementary Is this a string from a primary (8 bits for 1 char) or supplementary (16 bits for 1 char) volume descriptor
		 * \brief Read a string from the buffer
		 * \return string a String
		*/
		static public function GetString(&$buffer, $length, &$offset = 0, $supplementary = false)
		{
			$string = '';
			for($i = $offset ; $i < ($offset + $length) ; $i++) {
				$string .= chr($buffer[$i]);
			}

			if($supplementary)
				$string = mb_convert_encoding($string, 'UTF-8', 'UTF-16');

			$offset += $length;
			return $string;
		}
		/**
		 * \fn static public function ReadAString($buffer, $length, $offset = 0, $supplementary = false)
		 * \param array $buffer The data to read from
		 * \param int $length The string length to read
		 * \param int $offset The actual position in the buffer
		 * \param boolean $supplementary Is this a string from a primary (8 bits for 1 char) or supplementary (16 bits for 1 char) volume descriptor
		 * \brief Read an a-string from the buffer
		 * \return string a A-String
		 * \see GetString
		*/
		static public function ReadAString(&$buffer, $length, &$offset = 0, $supplementary = false)
		{
			return CBuffer::GetString($buffer, $length, $offset);
		}
		/**
		 * \fn static public function ReadDString($buffer, $length, $offset = 0, $supplementary)
		 * \param array $buffer The data to read from
		 * \param int $length The string length to read
		 * \param int $offset The actual position in the buffer
		 * \param boolean $supplementary Is this a string from a primary (8 bits for 1 char) or supplementary (16 bits for 1 char) volume descriptor
		 * \brief Read a d-string from the buffer
		 * \return string a D-String
		 * \see GetString
		*/
		static public function ReadDString(&$buffer, $length, &$offset = 0, $supplementary = false)
		{
			return CBuffer::GetString($buffer, $length, $offset, $supplementary);
		}
		/**
		 * \fn static public function GetBytes($buffer, $length, $offset)
		 * \param array $buffer The data to read from
		 * \param int $length The string length to read
		 * \param int $offset The actual position in the buffer
		 * \brief Read datas from the buffer
		 * \return string the packed datas.
		*/
		static public function GetBytes(&$buffer, $length, &$offset = 0)
		{
			$datas = '';
			for($i = $offset ; $i < ($offset + $length) ; $i++) {
				$datas .= $buffer[$i];
			}

			$offset += $length;
			return $datas;
		}
		/**
		 * \fn static public function ReadBBO($buffer, $length, $offset)
		 * \param array $buffer The data to read from
		 * \param int $length The string length to read
		 * \param int $offset The actual position in the buffer
		 * \brief Read a number written in BBO (Bost Byte Order) (ex: a 4 BYTES number require 8 BYTES, 4 for LSM mode and 4 for MSB)
		 * \return int The BBO number OR -1 on error
		*/
		static public function ReadBBO(&$buffer, $length, &$offset = 0)
		{
			$N1 = $N2 = 0;
			$Len = $length / 2;
			for($i = 0 ; $i < $Len ; $i++)
			{
				$N1 += $buffer[$offset + ($Len - 1 - $i)];
				$N2 += $buffer[$offset + $Len + $i];

				if(($i + 1) < $Len)
				{
					$N1 = $N1 << 8;
					$N2 = $N2 << 8;
				}
			}

			if($N1 != $N2) {
				return -1;
			}

			$offset += $length;
			return $N1;
		}
		/**
		 * \fn static public function ReadLSB($buffer, $length, $offset = 0)
		 * \param array $buffer The data to read from
		 * \param int $length The string length to read
		 * \param int $offset The actual position in the buffer
		 * \brief Read a number written in LSB mode ("Less Signifient Bit" first)
		 * \return int The LSB number
		*/
		static public function ReadLSB(&$buffer, $length, &$offset = 0)
		{
			$LSB = 0;
			for($i = 0 ; $i < $length ; $i++)
			{
				$LSB += $buffer[$offset + ($length - 1) - $i];

				if(($i + 1) < $length) {
					$LSB = $LSB << 8;
				}
			}

			$offset += $length;
			return $LSB;
		}
		/**
		 * \fn static public function ReadMSB($buffer, $length, $offset = 0)
		 * \param array $buffer The data to read from
		 * \param int $length The string length to read
		 * \param int $offset The actual position in the buffer
		 * \brief Read a number written in MSB mode ("Most Signifient Bit" first)
		 * \return int The MSB number
		*/
		static public function ReadMSB(&$buffer, $length, &$offset = 0)
		{
			$MSB = 0;
			for($i = 0 ; $i < $length ; $i++)
			{
				$MSB += $buffer[$offset + $i];

				if(($i + 1) < $length) {
					$MSB = $MSB << 8;
				}
			}

			$offset += $length;
			return $MSB;
		}
		/**
		 * \fn static public function ReadInt16($buffer, $offset = 0)
		 * \param array $buffer The data to read from
		 * \param int $offset The actual position in the buffer
		 * \brief Read a word (16 bits number)
		 * \return int The 16 bits number
		*/
		static public function ReadInt16(&$buffer, &$offset = 0)
		{
			$output = 0;

			$output += $buffer[$offset + 0] << 8;
			$output += $buffer[$offset + 1];

			$offset += 2;
			return $output;
		}
		/**
		 * \fn static public function ReadInt32($buffer, $offset = 0)
		 * \param array $buffer The data to read from
		 * \param int $offset The actual position in the buffer
		 * \brief Read a DWORD (32 bits number)
		 * \return int The 32 bits number
		*/
		static public function ReadInt32(&$buffer, &$offset = 0)
		{
			$output = 0;

			$output += $buffer[$offset + 0] << 24;
			$output += $buffer[$offset + 1] << 16;
			$output += $buffer[$offset + 2] << 8;
			$output += $buffer[$offset + 3];

			$offset += 4;
			return $output;
		}
	}
?>