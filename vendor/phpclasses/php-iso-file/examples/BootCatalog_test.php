<?php
	define('ISO_LIB_PATH', '/data/ftp/autre/php_iso_lib/classes/');

	include_once (ISO_LIB_PATH . 'iso_includes.php');

	$isoFile = new CISOFile();
	if(!$isoFile->Open('../isos/debian-live-6.0.1-i386-kde-desktop.iso') || !$isoFile->ISOInit())
	{
		die('Une erreur est survenue lors de l\'ouverture du fichier ISO...' . "\n");
	}
	else
	{
		$Pdesc = $isoFile->GetDescriptor(PRIMARY_VOLUME_DESC);
		if(!$Pdesc) {
			die('Impossible de localiser le "Primary Descriptor"...' . "\n");
		}

		$Bdesc = $isoFile->GetDescriptor(BOOT_RECORD_DESC);
		if($Bdesc) {

			$bootCat = $Bdesc->LoadBootCatalog($isoFile, $Pdesc->iBlockSize);
			if($bootCat != NULL) {

				echo 'Information de boot' . "\n";
				echo "\t" . 'Platform: ' . CBootCatalog::PlatformIDToName($bootCat->platformID) . "\n";
				echo "\t" . 'Manufacturer: ' . $bootCat->manufacturerID . "\n";
				echo "\n";

				$count = $Bdesc->GetBootCatalogEntryCount($isoFile, $Pdesc->iBlockSize);
				for($i = 0 ; $i < $count ; $i++) {

					$bootCatDefaultEntry = $Bdesc->LoadBootCatalogEntry($isoFile, $Pdesc->iBlockSize, $i);
					echo "\t" . 'Boot entry ' . ($i + 1) . ': ' . "\n";
					if($bootCatDefaultEntry) {

						echo "\t\t" . 'Boot media type: ' . CBootCatalogEntry::BootMediaTypeToName($bootCatDefaultEntry->mediaType) . "\n";
						echo "\t\t" . 'Loaded segment: 0x' . dechex($bootCatDefaultEntry->loadSegment) . "\n";
						echo "\t\t" . 'System type: ' . CBootCatalogEntry::SystemTypeToName($bootCatDefaultEntry->systemType) . "\n";
						echo "\t\t" . 'Location (LBA): ' . $bootCatDefaultEntry->loadRDA . "\n";
						echo "\t\t" . 'Sector count: ' . $bootCatDefaultEntry->sectorCount . "\n";
					}
					else
						echo "\t\t" . 'Invalide...' . "\n";
				}
			} else {

				echo 'L\'image ISO n\'est pas bootable...' . "\n\n";
			}
		}
		else
		{
			echo 'L\'image ISO n\'est pas bootable...' . "\n\n";
		}
	}
?>