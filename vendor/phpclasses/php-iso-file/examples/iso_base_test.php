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
		$pDesc = $isoFile->GetDescriptor(PRIMARY_VOLUME_DESC);
		if($pDesc == NULL) {
			$pDesc = $isoFile->GetDescriptor(SUPPLEMENTARY_VOLUME_DESC);
			if($pDesc == NULL) {
				die('Pas de "Primary" ou "supplementary" descriptor trouvés...' . "\n");
			}
		}

		$dCount = $isoFile->GetDescriptorCount();
		echo '"Descriptor(s)" présent dans le l\'image: ' . $dCount . "\n";
		for($i = 0 ; $i < $dCount ; $i++)
		{
			$desc = $isoFile->GetDescriptorAt($i);
			echo "\t" . '"Descriptor" ' . ($i + 1) . ': ' . $desc->GetName() . "\n";

			if($desc->GetType() == PRIMARY_VOLUME_DESC || $desc->GetType() == SUPPLEMENTARY_VOLUME_DESC)
			{
				echo "\t\t" . 'Crée le: ' . $desc->dtCreation . "\n";
				echo "\t\t" . 'Modifé le: ' . $desc->dtModification . "\n";
				echo "\t\t" . 'Expire le: ' . $desc->dtExpiration . "\n";
				echo "\t\t" . 'Valable a partir le: ' . $desc->dtEffective . "\n";
			}
			else if($desc->GetType() == BOOT_RECORD_DESC)
			{
				echo "\t\t" . 'Identifiant du système de boot: ' . $desc->BootSysId . "\n";
				echo "\t\t" . 'Identifiant de boot: ' . $desc->BootId . "\n";

				$bootCat = $desc->LoadBootCatalog($isoFile, $pDesc->iBlockSize);
				if($bootCat)
				{
				}

				$bceCount = $desc->GetBootCatalogEntryCount($isoFile, $pDesc->iBlockSize);
				echo "\t\t" . 'Nombre de d\'entrée dans le "Boot Catalog": ' . $bceCount . "\n";
				for($i = 0 ; $i < $bceCount ; $i++)
				{
					// CBootCatalogEntry
					$bceEntry = $desc->LoadBootCatalogEntry($isoFile, $pDesc->iBlockSize, $i);
					if($bceEntry)
					{
						// affichage d'info sur le "BootCatalog".
					}
				}
			}
			else if($desc->GetType() == PARTITION_VOLUME_DESC)
			{
				echo "\t\t" . 'Système ID: ' . $desc->SystemID . "\n";
				echo "\t\t" . 'Partition ID: ' . $desc->VolPartitionID . "\n";
				echo "\t\t" . 'Emplacement de le Partition (LBA): ' . $desc->VolPartitionLocation . "\n";
				echo "\t\t" . 'Taille de la Partition: ' . $desc->VolPartitionSize . "\n";
			}
			else if($desc->GetType() == TERMINATOR_DESC)
			{
				echo "\t\t" . 'Fin des "descriptors"...' . "\n";
			}
		}
	}
?>