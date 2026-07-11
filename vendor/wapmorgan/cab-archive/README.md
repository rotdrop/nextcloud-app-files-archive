# CabArchive

**CabArchive** is reader of CAB (Microsoft Cabinet files).

[![Composer package](http://composer.network/badge/wapmorgan/cab-archive)](https://packagist.org/packages/wapmorgan/cab-archive)
[![Total Downloads](https://poser.pugx.org/wapmorgan/cab-archive/downloads)](https://packagist.org/packages/wapmorgan/cab-archive)
[![Latest Stable Version](https://poser.pugx.org/wapmorgan/cab-archive/v/stable)](https://packagist.org/packages/wapmorgan/cab-archive)
[![License](https://poser.pugx.org/wapmorgan/cab-archive/license)](https://packagist.org/packages/wapmorgan/cab-archive)
[![Latest Unstable Version](https://poser.pugx.org/wapmorgan/cab-archive/v/unstable)](https://packagist.org/packages/wapmorgan/cab-archive)

**Precaution!** Right now library provide functionality to extract files only on PHP 7.0.22+, 7.1.8+, 7.2.0 due to bug in previous versions.

# Usage
Firstly, you need to create CabArchive instance:

```php
$cab = new CabArchive('123.cab');

```
After that you can get list of files in archive:

```php
var_dump($cab->getFileNames());
```

After that you can get all information about one file in archive:

```php
var_dump($cab->getFileData('README.md'));
```

## CabArchive API
All list of properties and methods of `CabArchive` is listed below.

- `$filesCount` - number of files in Cab-archive
- `__construct($filename)` - creates new instance from file, stream or socket
- `getCabHeader()` - returns header of Cab-archive as array
- `hasPreviousCab()` - checks that this cab has previous Cab in set
- `getPreviousCab()` - returns name of previous Cab
- `hasNextCab()` - checks that this cab has next Cab in set
- `getNextCab()` - returns name of next Cab
- `getSetId()` - returns set id (identical for all cab-archives from one set)
- `getInSetNumber()` - returns number of cab in set
- `getFileNames(): array` - retrives list of files from archive
- `getFileData($filename): object` - returns additional info of file as object.
- `getFileAttributes($filename): array` - returns list of file attributes. All available attributes:
    - `CabArchive::ATTRIB_READONLY`
    - `CabArchive::ATTRIB_HIDDEN`
    - `CabArchive::ATTRIB_SYSTEM`
    - `CabArchive::ATTRIB_EXEC`
- `getFileContent($filename): string` - returns raw content of file.
- `extract($output, array $files = []): bool|int` - _in development now_

### getFileNames
```php
array getFileNames()
```
This method returns an array of file names compressed in cab.

### getFileData
```php
object getFileData($filename)
```
This method returns an object with following fields:

- **size** - uncompressed size in bytes
- **packedSize** - compressed size in bytes
- **unixtime** - date&time of modification in unixtime format
- **isCompressed** - is file compressed as _boolean_

### getFileContent
```php
string getFileContent($filename)
```
This method returns raw file content of `$filename`. 
Supports extracting from cab's without compression and with MSZip compression. LZX compression is not supported.

Works correctly only on PHP:

- 7.0.22+
- 7.1.8+
- 7.2.0

### extract($outputDirectory, array $files = [])
```php
int|bool extract($outputDirectory, array $files = [])
```
This method extracts passed files or the whole cab to `$outputDirectory`.

Supports extracting from cab's without compression and with MSZip compression. LZX compression is not supported.

Works correctly only on PHP:

- 7.0.22+
- 7.1.8+
- 7.2.0

## CAB Format
- .CAB-files format is described at https://msdn.microsoft.com/en-us/library/bb417343.aspx
- MSZIP compression (based on Deflate) is described at http://interoperability.blob.core.windows.net/files/MS-MCI/[MS-MCI].pdf
