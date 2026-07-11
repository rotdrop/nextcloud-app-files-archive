<?php
	define('ISO_LIB_PATH', '/data/ftp/autre/php_iso_lib/classes/');

	include_once (ISO_LIB_PATH . 'iso_includes.php');

	system('clear');

	function EchoFDD(&$fileDirDesc, $tab)
	{
		if(!$fileDirDesc->IsDirectory())
			return;

		global $isoFile;
		global $usedDesc;

		$tab_str = '';
		for($i = 0 ; $i < $tab ; $i++)
			$tab_str .= "\t";

		$extents = $fileDirDesc->LoadExtents($isoFile, $usedDesc->iBlockSize, true);
		if($extents)
		{
			foreach($extents as $extent)
			{
				if(!$extent->IsThis() && !$extent->IsParent())
				{
					if($extent->IsDirectory() === true)
					{
						echo $tab_str . '* ' . $extent->strd_FileId . "\n";
						EchoFDD($extent, $tab + 1);
					}
					else
					{
						echo $tab_str . $extent->strd_FileId . "\n";
					}
				}
			}
		}
	}
	function EchoPT(&$ptRec, $tab)
	{
		global $isoFile;
		global $usedDesc;

		$tab_str = '';
		for($i = 0 ; $i < $tab ; $i++)
			$tab_str .= "\t";

		echo $tab_str . $ptRec->strd_DirId . "\n";
		$tab_str .= "\t";

		$extents = $ptRec->LoadExtents($isoFile, $usedDesc->iBlockSize, true);
		if($extents)
		{
			foreach($extents as $extent)
			{
				if(!$extent->IsThis() && !$extent->IsParent())
				{
					if($extent->IsDirectory() === true)
					{
						echo $tab_str . '* ' . $extent->strd_FileId . "\n";
						EchoFDD($extent, $tab + 1);
					}
					else
					{
						echo $tab_str . $extent->strd_FileId . "\n";
					}
				}
			}
		}
	}

	$isoFile = new CISOFile();
	if(!$isoFile->Open('../isos/debian-live-6.0.1-i386-kde-desktop.iso') || !$isoFile->ISOInit())
	{
		die('Une erreur est survenue lors de l\'ouverture du fichier ISO...' . "\n");
	}
	else
	{
		$usedDesc = $isoFile->GetDescriptor(SUPPLEMENTARY_VOLUME_DESC);
		if(!$usedDesc) {

			$usedDesc = $isoFile->GetDescriptor(PRIMARY_VOLUME_DESC);
			if(!$usedDesc) {

				die('Pas de "Primary" ou "supplementary" descriptor trouvés...' . "\n");
			}
		}

		echo 'Affichage en liste (l, défaut) ou en arbre (a): ';
		$sel = trim(strtolower(fgets(STDIN)));
		if($sel == 'a')
		{
			$pathTable = $usedDesc->LoadMPathTable($isoFile);
			foreach($pathTable as $ptRec)
			{
				if($ptRec->ParentDirNum == 1)
				{
					EchoPT($ptRec, 0);
				}
			}
		}
		else
		{
			$pathTable = $usedDesc->LoadMPathTable($isoFile);
			foreach($pathTable as $ptRec)
			{
				$fPath = $ptRec->GetFullPath($pathTable, $dbg);
				echo 'Nom: ' . $ptRec->strd_DirId . "\n";
				echo "\t" . 'Chemin: ' . $fPath . "\n";
				echo "\t" . 'Position: ' . $ptRec->Location . ' (LBA)' . "\n";
				echo "\t" . 'Étendu: ' . $ptRec->ExtAttrLen . ' (LBA)' . "\n";
			}
		}

	}
?>