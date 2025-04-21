<?php
	/**
	 * \class CBootCatalogEntry
	 * \brief The EL TORITO Initial/Default Entry
	 * \see http://download.intel.com/support/motherboards/desktop/sb/specscdrom.pdf
	 * \author Christian SCHROETTER, (http://www.quatilfait.fr)
	 * \example examples/BootCatalogEntry_test.php
	 * \copyright Creative Commons (BY NC SA)
	 * The holder allows the use of the original in non-commercial work and the creation of derived works provided they are distributed in a manner identical to the one that governs the original work license.
	*/
	class CBootCatalogEntry
	{
		/**
		 * \public
		 * \param int $indicator (MUST be 0x88)
		*/
		var $indicator;
		/**
		 * \public
		 * \param int $mediaType
		 * \brief the type of media emulated
		 * 0 No Emulation
		 * 1 1.2 meg diskette
		 * 2 1.44 meg diskette
		 * 3 2.88 meg diskette
		 * 4 Hard Disk (drive 80)
		*/
		var $mediaType;
		/**
		 * \public
		 * \param int $loadSegment
		 * \brief loaded segment.
		 * This is the load segment for the initial boot image
		*/
		var $loadSegment;
		/**
		 * \public
		 * \param int $systemType
		 * \brief The type of system
		 * This must be a copy of byte 5 (System Type) from the "Partition Table" found in the boot image
		*/
		var $systemType;
		/**
		 * \public
		 * \param int $unused
		*/
		var $unused;
		/**
		 * \public
		 * \param int $sectorCount
		 * This is the number of segment loaded by the BIOS
		 * But this value is confusing because the microcode store in the entry can load mores block
		 * \warning ONE sector of the boot image is equal to 512 bytes (not the standart 2048 bytes of a Optical media LBA)
		*/
		var $sectorCount;
		/**
		 * \public
		 * \param int $loadRDA
		 * \brief The location of the boot image
		 * This is the start address of the virtual disk.
		 * \warning Optical media use Relative/Logical block addressing
		*/
		var $loadRDA;

		/**
		 * \fn public function IsValid()
		 * \brief Check is this Initial/Default Entry is 'valid'
		 * Check all the member's of the "Initial/Default Entry"
		 * The \a $indicator value (also correct the \a $loadSegment value)
		 * \return boolean TRUE OR FALSE
		 * \see $indicator
		 * \see $loadSegment
		*/
		public function IsValid()
		{
			if($this->indicator != 0x88)
				return false;

			// set default load segment is not set...
			if($this->loadSegment == 0)
				$this->loadSegment = 0x7c0;

			return true;
		}
		/**
		 * \fn static public function BootMediaTypeToName($mediaType)
		 * \param int $mediaType The media identifier number.
		 * \see $mediaType
		 * \brief Convert mediaType to string
		 * Convert the media identifier (int) to a human readable string.
		 * \return string The readable media identifier
		 * \see $mediaType
		*/
		static public function BootMediaTypeToName($mediaType)
		{
			switch($mediaType)
			{
			case 0: return 'No Emulation';
			case 1: return '1.2 meg diskette';
			case 2: return '1.44 meg diskette';
			case 3: return '2.88 meg diskette';
			case 4: return 'Hard Disk (drive 80)';
			}

			return 'ID inconnu: ' . $mediaType;
		}
		/**
		 * \fn static public function SystemTypeToName($systemType)
		 * \param int $systemType The system type number.
		 * \see $systemType
		 * \brief Convert systemType to string
		 * Convert the system identifier (int) to a human readable string.
		 * \return string The readable system identifier
		*/
		static public function SystemTypeToName($systemType)
		{
			switch($systemType)
			{
			case 0x00: return 'Vide';
			case 0x01: return 'FAT12';
			case 0x02: return 'XENIX root';
			case 0x03: return 'XENIX /usr';
			case 0x04: return 'FAT16 <32Mio (adressage CHS)';
			case 0x05: return 'Étendue';
			case 0x06: return 'FAT16';
			case 0x07: return 'NTFS (et son prédécesseur HPFS)';
			case 0x08: return 'AIX, voir JFS';
			case 0x09: return 'AIX bootable';
			case 0x0a: return 'OS/2 Boot Manager';
			case 0x0b: return 'Win95 OSR2 FAT32 (adressage CHS)';
			case 0x0c: return 'Win95 OSR2 FAT32 (adressage LBA, appelée aussi FAT32X )';
			case 0x0e: return 'Win95 FAT16 (adressage LBA)';
			case 0x0f: return 'Étendue (adressage LBA)';
			case 0x10: return 'OPUS';
			case 0x11: return 'Hidden FAT12';
			case 0x12: return 'Compaq diagnostic';
			case 0x14: return 'Hidden FAT16 <32M';
			case 0x16: return 'Hidden FAT16';
			case 0x17: return 'Hidden HPFS/NTFS';
			case 0x18: return 'AST SmartSleep';
			case 0x1b: return 'Hidden Win95 FAT32';
			case 0x1c: return 'Hidden Win95 FAT32 (LBA)';
			case 0x1e: return 'Hidden Win95 FAT16 (LBA)';
			case 0x24: return 'NEC DOS';
			case 0x2f: return 'Smart File System';
			case 0x30: return 'AROS RDB';
			case 0x39: return 'Plan 9';
			case 0x3c: return 'PartitionMagic Recoverable Partition (PqRP)';
			case 0x40: return 'Venix4 80286';
			case 0x41: return 'PPC PReP Boot';
			case 0x42: return 'SFS';
			case 0x4d: return 'QNX4.x';
			case 0x4e: return 'QNX4.x 2nde partition';
			case 0x4f: return 'QNX4.x 3e partition';
			case 0x50: return 'OnTrack DM';
			case 0x51: return 'OnTrack DM6 Aux';
			case 0x52: return 'CP/M';
			case 0x53: return 'OnTrack DM6 Aux';
			case 0x54: return 'OnTrackDM6';
			case 0x55: return 'EZ-Drive';
			case 0x56: return 'Golden Bow';
			case 0x5c: return 'Priam Edisk';
			case 0x61: return 'SpeedStor';
			case 0x63: return 'GNU HURD or Sys';
			case 0x64: return 'Novell Netware';
			case 0x65: return 'Novell Netware';
			case 0x70: return 'DiskSecure Mult';
			case 0x75: return 'PC/IX';
			case 0x80: return 'Ancien Minix';
			case 0x81: return 'Minix / ancien Linux';
			case 0x82: return 'Swap Linux / pool ZFS';
			case 0x83: return 'Ce type de partition est utilisé par les systèmes de fichiers ext2, ext3, ext4, ReiserFS et JFS';
			case 0x84: return 'OS/2 hidden C:';
			case 0x85: return 'Linux étendu';
			case 0x86: return 'NTFS volume set';
			case 0x87: return 'NTFS volume set';
			case 0x8e: return 'Linux LVM';
			case 0x93: return 'Amoeba';
			case 0x94: return 'Amoeba BBT';
			case 0x9d: return 'SDF';
			case 0x9f: return 'BSD/OS';
			case 0xa0: return 'IBM Thinkpad hi';
			case 0xa5: return 'FreeBSD';
			case 0xa6: return 'OpenBSD';
			case 0xa7: return 'NeXTSTEP';
			case 0xa8: return 'Darwin UFS';
			case 0xa9: return 'NetBSD';
			case 0xab: return 'Darwin boot';
			case 0xaf: return 'HFS+';
			case 0xb7: return 'BSDI fs';
			case 0xb8: return 'BSDI swap';
			case 0xbb: return 'Boot Wizard hid / Acronis Hidden';
			case 0xbc: return 'Acronis Secure Zone';
			case 0xbe: return 'Solaris boot';
			case 0xc0: return 'Fichier CrashDump.sys CTOS-III PC (CTOS = système d\'exploitation x86 [i386]';
			case 0xc1: return 'DRDOS/sec (FAT-';
			case 0xc4: return 'DRDOS/sec (FAT-';
			case 0xc6: return 'DRDOS/sec (FAT-';
			case 0xc7: return 'Syrinx';
			case 0xcd: return 'Système de fichiers (disque système ou disque de données) CTOS-III PC';
			case 0xda: return 'Non-FS data';
			case 0xdb: return 'CP/M / CTOS /.';
			case 0xde: return 'Dell Utility';
			case 0xdf: return 'BootIt';
			case 0xe1: return 'DOS access';
			case 0xe3: return 'DOS lecture seule';
			case 0xe4: return 'SpeedStor';
			case 0xeb: return 'BeOS file system';
			case 0xee: return 'EFI GPT1';
			case 0xef: return 'EFI (FAT-12/16/';
			case 0xf0: return 'Linux/PA-RISC b';
			case 0xf1: return 'SpeedStor';
			case 0xf4: return 'SpeedStor';
			case 0xf2: return 'DOS secondaire';
			case 0xf7: return 'MVTFS';
			case 0xfd: return 'Linux raid auto';
			case 0xfe: return 'LANstep';
			case 0xff: return 'BBT';
			}

			return 'System inconnu: ' . $systemType;
		}
	}
?>