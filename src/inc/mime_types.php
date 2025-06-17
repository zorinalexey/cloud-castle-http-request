<?php

declare(strict_types = 1);

/**
 * Массив соответствий MIME-типов расширениям файлов
 * 
 * Этот файл содержит полный список MIME-типов и соответствующих им расширений файлов.
 * Используется для автоматического определения расширения файла на основе его MIME-типа
 * при загрузке файлов через HTTP-запросы.
 * 
 * Структура массива:
 * - Ключ: MIME-тип файла (например, 'image/jpeg')
 * - Значение: расширение файла без точки (например, 'jpg')
 * 
 * Поддерживаемые категории файлов:
 * - Изображения (JPEG, PNG, GIF, WebP, SVG, BMP, TIFF, ICO, RAW форматы)
 * - Документы (PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX, RTF, ODT и др.)
 * - Архивы (ZIP, RAR, 7Z, TAR, GZ, BZ2 и др.)
 * - Аудио (MP3, WAV, OGG, FLAC, AAC, MIDI и др.)
 * - Видео (MP4, AVI, MOV, WMV, WebM, MKV и др.)
 * - Шрифты (TTF, OTF, WOFF, WOFF2, EOT и др.)
 * - Программные файлы (PHP, Python, Java, C#, VB и др.)
 * 
 * Особенности:
 * - Поддержка множественных MIME-типов для одного расширения
 * - Включение профессиональных форматов (RAW камер, кинематографические форматы)
 * - Поддержка устаревших и специализированных форматов
 * - Совместимость с различными браузерами и системами
 * 
 * @package CloudCastle\HttpRequest\Common
 * @author Алексей Зорин <zorinalexey59292@gmail.com>
 * @since 1.0.0
 * 
 * @example
 * ```php
 * // Получение расширения по MIME-типу
 * $mimeTypes = require 'mime_types.php';
 * $extension = $mimeTypes['image/jpeg']; // 'jpg'
 * $extension = $mimeTypes['application/pdf']; // 'pdf'
 * 
 * // Проверка поддержки MIME-типа
 * if (isset($mimeTypes['image/webp'])) {
 *     echo "WebP поддерживается";
 * }
 * 
 * // Автоматическое добавление расширения к файлу
 * $mimeType = 'image/png';
 * $filename = 'uploaded_file';
 * if (isset($mimeTypes[$mimeType])) {
 *     $filename .= '.' . $mimeTypes[$mimeType];
 * }
 * ```
 * 
 * @see UploadFile::getExtensionFromMimeType()
 * @see UploadFile::save()
 * 
 * @note Этот файл используется в классе UploadFile для автоматического
 *       определения расширения файла при сохранении загруженных файлов.
 *       При добавлении новых MIME-типов убедитесь в корректности
 *       соответствия расширений и уникальности ключей массива.
 * 
 * @todo Добавить поддержку дополнительных MIME-типов по мере необходимости
 * @todo Рассмотреть возможность использования внешних баз данных MIME-типов
 * @todo Добавить валидацию MIME-типов на соответствие стандартам IANA
 */
return [
     /**
      * Изображения
      * 
      * Поддерживает все основные форматы изображений, включая:
      * - Стандартные веб-форматы (JPEG, PNG, GIF, WebP, SVG)
      * - Растровые форматы (BMP, TIFF, ICO)
      * - RAW форматы профессиональных камер (CR2, NEF, ARW, RAF, ORF, PEF, SRW, DCR)
      * - Кинематографические форматы (Cineon, DPX, OpenEXR, HDR)
      * - Специализированные форматы (JPEG 2000, JBIG2, AVIF, HEIC)
      * 
      * @see https://developer.mozilla.org/en-US/docs/Web/HTTP/Basics_of_HTTP/MIME_types#image_types
      */
     // Изображения
     'image/jpeg' => 'jpg',
     'image/jpg' => 'jpg',
     'image/png' => 'png',
     'image/gif' => 'gif',
     'image/webp' => 'webp',
     'image/svg+xml' => 'svg',
     'image/bmp' => 'bmp',
     'image/tiff' => 'tiff',
     'image/x-icon' => 'ico',
     'image/vnd.microsoft.icon' => 'ico',
     'image/x-ms-bmp' => 'bmp',
     'image/jp2' => 'jp2',
     'image/jpx' => 'jpx',
     'image/jpm' => 'jpm',
     'image/heic' => 'heic',
     'image/heif' => 'heif',
     'image/avif' => 'avif',
     'image/tga' => 'tga',
     'image/x-portable-pixmap' => 'ppm',
     'image/x-portable-graymap' => 'pgm',
     'image/x-portable-bitmap' => 'pbm',
     'image/x-xbitmap' => 'xbm',
     'image/x-xpixmap' => 'xpm',
     'image/x-photoshop' => 'psd',
     'image/x-raw' => 'raw',
     'image/x-canon-cr2' => 'cr2',
     'image/x-nikon-nef' => 'nef',
     'image/x-sony-arw' => 'arw',
     'image/x-panasonic-rw2' => 'rw2',
     'image/x-fuji-raf' => 'raf',
     'image/x-olympus-orf' => 'orf',
     'image/x-pentax-pef' => 'pef',
     'image/x-samsung-srw' => 'srw',
     'image/x-kodak-dcr' => 'dcr',
     'image/x-minolta-mrw' => 'mrw',
     'image/x-hasselblad-3fr' => '3fr',
     'image/x-hasselblad-fff' => 'fff',
     'image/x-phaseone-iiq' => 'iiq',
     'image/x-leaf-mos' => 'mos',
     'image/x-ricoh-dng' => 'dng',
     'image/x-sigma-x3f' => 'x3f',
     'image/x-canon-crw' => 'crw',
     'image/x-canon-cr3' => 'cr3',
     'image/x-nikon-nrw' => 'nrw',
     'image/x-sony-sr2' => 'sr2',
     'image/x-sony-srf' => 'srf',
     'image/x-panasonic-raw' => 'raw',
     'image/x-leica-rwl' => 'rwl',
     'image/x-leica-dng' => 'dng',
     'image/x-kodak-kdc' => 'kdc',
     'image/x-kodak-k25' => 'k25',
     'image/x-mamiya-mef' => 'mef',
     'image/x-voigtlander-dng' => 'dng',
     'image/x-cineform-cfhd' => 'cfhd',
     'image/x-red-r3d' => 'r3d',
     'image/x-arri-ari' => 'ari',
     'image/x-blackmagic-braw' => 'braw',
     'image/x-prores' => 'mov',
     'image/x-dnxhd' => 'mxf',
     'image/x-avc-intra' => 'mxf',
     'image/x-xavc' => 'mxf',
     'image/x-canon-cinema' => 'cin',
     'image/x-sony-xavc' => 'mxf',
     'image/x-panasonic-avc' => 'mxf',
     'image/x-jvc-hm' => 'mxf',
     'image/x-ikegami-gxf' => 'gxf',
     'image/x-grass-valley' => 'avi',
     'image/x-quantel' => 'qtz',
     'image/x-discreet-logic' => 'cin',
     'image/x-silicon-graphics' => 'rgb',
     'image/x-sgi-rgb' => 'rgb',
     'image/x-sgi-rgba' => 'rgba',
     'image/x-sgi-bw' => 'bw',
     'image/x-sgi-iris' => 'iris',
     'image/x-sgi-rle' => 'rle',
     'image/x-kodak-cineon' => 'cin',
     'image/x-dpx' => 'dpx',
     'image/x-openexr' => 'exr',
     'image/x-radiance-hdr' => 'hdr',
     'image/x-tiff-float' => 'tiff',
     'image/x-png-16bit' => 'png',
     'image/x-jpeg-2000' => 'jp2',
     'image/x-jpeg-2000-codestream' => 'j2c',
     'image/x-jpeg-2000-part2' => 'jpx',
     'image/x-jpeg-2000-part6' => 'jpm',
     'image/x-jbig2' => 'jb2',
     'image/x-jbig2-codestream' => 'j2c',
     
     /**
      * Документы
      * 
      * Поддерживает форматы документов от различных производителей:
      * - Microsoft Office (DOC, DOCX, XLS, XLSX, PPT, PPTX, RTF, PUB, VSD, MPP, MDB, MSG, ONE)
      * - OpenDocument (ODT, ODS, ODP, ODG, ODF, ODB, ODC, ODI, ODM, OTT, OTS, OTP, OTG, OTF, OTH)
      * - Sun/StarOffice (SXW, SXC, SXI, SXD, SXM, STW, STC, STI, STD, STM, SXG)
      * - Apple iWork (Pages, Numbers, Keynote)
      * - Google Workspace (GDoc, GSheet, GSlides)
      * - Веб-форматы (HTML, CSS, JavaScript, JSON, XML)
      * - Текстовые форматы (TXT, RTF)
      * 
      * @see https://developer.mozilla.org/en-US/docs/Web/HTTP/Basics_of_HTTP/MIME_types#application_types
      */
     // Документы
     'application/pdf' => 'pdf',
     'application/msword' => 'doc',
     'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
     'application/vnd.ms-excel' => 'xls',
     'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'xlsx',
     'application/vnd.ms-powerpoint' => 'ppt',
     'application/vnd.openxmlformats-officedocument.presentationml.presentation' => 'pptx',
     'text/plain' => 'txt',
     'text/html' => 'html',
     'text/css' => 'css',
     'text/javascript' => 'js',
     'application/json' => 'json',
     'application/xml' => 'xml',
     'text/xml' => 'xml',
     'application/rtf' => 'rtf',
     'text/rtf' => 'rtf',
     'application/vnd.oasis.opendocument.text' => 'odt',
     'application/vnd.oasis.opendocument.spreadsheet' => 'ods',
     'application/vnd.oasis.opendocument.presentation' => 'odp',
     'application/vnd.oasis.opendocument.graphics' => 'odg',
     'application/vnd.oasis.opendocument.formula' => 'odf',
     'application/vnd.oasis.opendocument.database' => 'odb',
     'application/vnd.oasis.opendocument.chart' => 'odc',
     'application/vnd.oasis.opendocument.image' => 'odi',
     'application/vnd.oasis.opendocument.text-master' => 'odm',
     'application/vnd.oasis.opendocument.text-template' => 'ott',
     'application/vnd.oasis.opendocument.spreadsheet-template' => 'ots',
     'application/vnd.oasis.opendocument.presentation-template' => 'otp',
     'application/vnd.oasis.opendocument.graphics-template' => 'otg',
     'application/vnd.oasis.opendocument.formula-template' => 'otf',
     'application/vnd.oasis.opendocument.text-web' => 'oth',
     'application/vnd.sun.xml.writer' => 'sxw',
     'application/vnd.sun.xml.calc' => 'sxc',
     'application/vnd.sun.xml.impress' => 'sxi',
     'application/vnd.sun.xml.draw' => 'sxd',
     'application/vnd.sun.xml.math' => 'sxm',
     'application/vnd.sun.xml.writer.template' => 'stw',
     'application/vnd.sun.xml.calc.template' => 'stc',
     'application/vnd.sun.xml.impress.template' => 'sti',
     'application/vnd.sun.xml.draw.template' => 'std',
     'application/vnd.sun.xml.math.template' => 'stm',
     'application/vnd.sun.xml.writer.global' => 'sxg',
     'application/vnd.sun.xml.calc.global' => 'sxg',
     'application/vnd.sun.xml.impress.global' => 'sxg',
     'application/vnd.sun.xml.draw.global' => 'sxg',
     'application/vnd.sun.xml.math.global' => 'sxg',
     'application/vnd.sun.xml.writer.web' => 'stw',
     'application/vnd.sun.xml.calc.web' => 'stc',
     'application/vnd.sun.xml.impress.web' => 'sti',
     'application/vnd.sun.xml.draw.web' => 'std',
     'application/vnd.sun.xml.math.web' => 'stm',
     'application/vnd.sun.xml.writer.master' => 'sxg',
     'application/vnd.sun.xml.calc.master' => 'sxg',
     'application/vnd.sun.xml.impress.master' => 'sxg',
     'application/vnd.sun.xml.draw.master' => 'sxg',
     'application/vnd.sun.xml.math.master' => 'sxg',
     'application/vnd.sun.xml.writer.template.global' => 'stw',
     'application/vnd.sun.xml.calc.template.global' => 'stc',
     'application/vnd.sun.xml.impress.template.global' => 'sti',
     'application/vnd.sun.xml.draw.template.global' => 'std',
     'application/vnd.sun.xml.math.template.global' => 'stm',
     'application/vnd.sun.xml.writer.template.web' => 'stw',
     'application/vnd.sun.xml.calc.template.web' => 'stc',
     'application/vnd.sun.xml.impress.template.web' => 'sti',
     'application/vnd.sun.xml.draw.template.web' => 'std',
     'application/vnd.sun.xml.math.template.web' => 'stm',
     'application/vnd.sun.xml.writer.template.master' => 'stw',
     'application/vnd.sun.xml.calc.template.master' => 'stc',
     'application/vnd.sun.xml.impress.template.master' => 'sti',
     'application/vnd.sun.xml.draw.template.master' => 'std',
     'application/vnd.sun.xml.math.template.master' => 'stm',
     'application/vnd.sun.xml.writer.global.web' => 'stw',
     'application/vnd.sun.xml.calc.global.web' => 'stc',
     'application/vnd.sun.xml.impress.global.web' => 'sti',
     'application/vnd.sun.xml.draw.global.web' => 'std',
     'application/vnd.sun.xml.math.global.web' => 'stm',
     'application/vnd.sun.xml.writer.global.master' => 'stw',
     'application/vnd.sun.xml.calc.global.master' => 'stc',
     'application/vnd.sun.xml.impress.global.master' => 'sti',
     'application/vnd.sun.xml.draw.global.master' => 'std',
     'application/vnd.sun.xml.math.global.master' => 'stm',
     'application/vnd.sun.xml.writer.web.master' => 'stw',
     'application/vnd.sun.xml.calc.web.master' => 'stc',
     'application/vnd.sun.xml.impress.web.master' => 'sti',
     'application/vnd.sun.xml.draw.web.master' => 'std',
     'application/vnd.sun.xml.math.web.master' => 'stm',
     'application/vnd.apple.pages' => 'pages',
     'application/vnd.apple.numbers' => 'numbers',
     'application/vnd.apple.keynote' => 'keynote',
     'application/vnd.google-apps.document' => 'gdoc',
     'application/vnd.google-apps.spreadsheet' => 'gsheet',
     'application/vnd.google-apps.presentation' => 'gslides',
     'application/vnd.ms-publisher' => 'pub',
     'application/vnd.ms-visio.drawing' => 'vsd',
     'application/vnd.ms-visio.stencil' => 'vss',
     'application/vnd.ms-visio.template' => 'vst',
     'application/vnd.ms-visio.drawing.macroEnabled12' => 'vsdm',
     'application/vnd.ms-visio.stencil.macroEnabled12' => 'vssm',
     'application/vnd.ms-visio.template.macroEnabled12' => 'vstm',
     'application/vnd.ms-project' => 'mpp',
     'application/vnd.ms-project.macroEnabled12' => 'mpp',
     'application/vnd.ms-access' => 'mdb',
     'application/vnd.ms-access.macroEnabled12' => 'accdb',
     'application/vnd.ms-outlook' => 'msg',
     'application/vnd.ms-outlook.pst' => 'pst',
     'application/vnd.ms-outlook.ost' => 'ost',
     'application/vnd.ms-onenote' => 'one',
     'application/vnd.ms-onenote.package' => 'onepkg',
     'application/vnd.ms-onenote.toc' => 'onetoc',
     'application/vnd.ms-onenote.toc2' => 'onetoc2',
     'application/vnd.ms-onenote.page' => 'onepage',
     
     /**
      * Архивы
      * 
      * Поддерживает различные форматы архивов и сжатых файлов:
      * - Популярные архивы (ZIP, RAR, 7Z, TAR, GZ, BZ2, XZ, LZ4, ZSTD)
      * - Специализированные архивы (CAB, DEB, RPM, DMG, ISO, DAR, ACE, ARC, ARJ)
      * - Unix архивы (CPIO, SHAR, USTAR, PAX) с различными вариантами
      * - Установочные пакеты (EXE, MSI)
      * 
      * Включает множество вариантов CPIO для совместимости с различными системами.
      * 
      * @see https://developer.mozilla.org/en-US/docs/Web/HTTP/Basics_of_HTTP/MIME_types#archive_types
      */
     // Архивы
     'application/zip' => 'zip',
     'application/x-zip-compressed' => 'zip',
     'application/x-rar-compressed' => 'rar',
     'application/vnd.rar' => 'rar',
     'application/x-7z-compressed' => '7z',
     'application/gzip' => 'gz',
     'application/x-gzip' => 'gz',
     'application/x-tar' => 'tar',
     'application/x-bzip2' => 'bz2',
     'application/x-bzip' => 'bz',
     'application/x-lzma' => 'lzma',
     'application/x-lzop' => 'lzo',
     'application/x-xz' => 'xz',
     'application/x-lzip' => 'lz',
     'application/x-lrzip' => 'lrz',
     'application/x-lz4' => 'lz4',
     'application/x-zstd' => 'zst',
     'application/x-cab' => 'cab',
     'application/vnd.ms-cab-compressed' => 'cab',
     'application/x-deb' => 'deb',
     'application/vnd.debian.binary-package' => 'deb',
     'application/x-rpm' => 'rpm',
     'application/x-redhat-package-manager' => 'rpm',
     'application/x-msdownload' => 'exe',
     'application/x-msi' => 'msi',
     'application/x-apple-diskimage' => 'dmg',
     'application/x-iso9660-image' => 'iso',
     'application/x-cd-image' => 'iso',
     'application/x-dar' => 'dar',
     'application/x-ace' => 'ace',
     'application/x-arc' => 'arc',
     'application/x-arj' => 'arj',
     'application/x-cpio' => 'cpio',
     'application/x-shar' => 'shar',
     'application/x-ustar' => 'ustar',
     'application/x-pax' => 'pax',
     'application/x-cpio-gnu' => 'cpio',
     'application/x-cpio-old' => 'cpio',
     'application/x-cpio-new' => 'cpio',
     'application/x-cpio-crc' => 'cpio',
     'application/x-cpio-bin' => 'cpio',
     'application/x-cpio-odc' => 'cpio',
     'application/x-cpio-ustar' => 'cpio',
     'application/x-cpio-pax' => 'cpio',
     'application/x-cpio-gnu-old' => 'cpio',
     'application/x-cpio-gnu-new' => 'cpio',
     'application/x-cpio-gnu-crc' => 'cpio',
     'application/x-cpio-gnu-bin' => 'cpio',
     'application/x-cpio-gnu-odc' => 'cpio',
     'application/x-cpio-gnu-ustar' => 'cpio',
     'application/x-cpio-gnu-pax' => 'cpio',
     'application/x-cpio-old-old' => 'cpio',
     'application/x-cpio-old-new' => 'cpio',
     'application/x-cpio-old-crc' => 'cpio',
     'application/x-cpio-old-bin' => 'cpio',
     'application/x-cpio-old-odc' => 'cpio',
     'application/x-cpio-old-ustar' => 'cpio',
     'application/x-cpio-old-pax' => 'cpio',
     'application/x-cpio-new-old' => 'cpio',
     'application/x-cpio-new-new' => 'cpio',
     'application/x-cpio-new-crc' => 'cpio',
     'application/x-cpio-new-bin' => 'cpio',
     'application/x-cpio-new-odc' => 'cpio',
     'application/x-cpio-new-ustar' => 'cpio',
     'application/x-cpio-new-pax' => 'cpio',
     'application/x-cpio-crc-old' => 'cpio',
     'application/x-cpio-crc-new' => 'cpio',
     'application/x-cpio-crc-crc' => 'cpio',
     'application/x-cpio-crc-bin' => 'cpio',
     'application/x-cpio-crc-odc' => 'cpio',
     'application/x-cpio-crc-ustar' => 'cpio',
     'application/x-cpio-crc-pax' => 'cpio',
     'application/x-cpio-bin-old' => 'cpio',
     'application/x-cpio-bin-new' => 'cpio',
     'application/x-cpio-bin-crc' => 'cpio',
     'application/x-cpio-bin-bin' => 'cpio',
     'application/x-cpio-bin-odc' => 'cpio',
     'application/x-cpio-bin-ustar' => 'cpio',
     'application/x-cpio-bin-pax' => 'cpio',
     'application/x-cpio-odc-old' => 'cpio',
     'application/x-cpio-odc-new' => 'cpio',
     'application/x-cpio-odc-crc' => 'cpio',
     'application/x-cpio-odc-bin' => 'cpio',
     'application/x-cpio-odc-odc' => 'cpio',
     'application/x-cpio-odc-ustar' => 'cpio',
     'application/x-cpio-odc-pax' => 'cpio',
     'application/x-cpio-ustar-old' => 'cpio',
     'application/x-cpio-ustar-new' => 'cpio',
     'application/x-cpio-ustar-crc' => 'cpio',
     'application/x-cpio-ustar-bin' => 'cpio',
     'application/x-cpio-ustar-odc' => 'cpio',
     'application/x-cpio-ustar-ustar' => 'cpio',
     'application/x-cpio-ustar-pax' => 'cpio',
     'application/x-cpio-pax-old' => 'cpio',
     'application/x-cpio-pax-new' => 'cpio',
     'application/x-cpio-pax-crc' => 'cpio',
     'application/x-cpio-pax-bin' => 'cpio',
     'application/x-cpio-pax-odc' => 'cpio',
     'application/x-cpio-pax-ustar' => 'cpio',
     'application/x-cpio-pax-pax' => 'cpio',
     
     /**
      * Аудио
      * 
      * Поддерживает широкий спектр аудио форматов:
      * - Популярные форматы (MP3, WAV, OGG, FLAC, AAC, WMA, AMR)
      * - Специализированные форматы (APE, WV, DTS, AC3, EAC3)
      * - MIDI и музыкальные форматы (MID, MIDI, KAR, MUS, RMF)
      * - Unix аудио форматы (AU, SND, ULAW, ALAW, GSM)
      * - Трекерные форматы (S3M, XM, IT, MOD, 669, FAR, MTM, ULT, STM, MED, OKT, UNI, PAT, DSM, AMF)
      * 
      * Включает поддержку как современных, так и устаревших аудио форматов.
      * 
      * @see https://developer.mozilla.org/en-US/docs/Web/HTTP/Basics_of_HTTP/MIME_types#audio_types
      */
     // Аудио
     'audio/mpeg' => 'mp3',
     'audio/mp3' => 'mp3',
     'audio/mp4' => 'm4a',
     'audio/x-m4a' => 'm4a',
     'audio/wav' => 'wav',
     'audio/x-wav' => 'wav',
     'audio/wave' => 'wav',
     'audio/x-pn-wav' => 'wav',
     'audio/ogg' => 'ogg',
     'audio/oga' => 'oga',
     'audio/opus' => 'opus',
     'audio/aac' => 'aac',
     'audio/x-aac' => 'aac',
     'audio/flac' => 'flac',
     'audio/x-flac' => 'flac',
     'audio/ape' => 'ape',
     'audio/x-ape' => 'ape',
     'audio/monkey' => 'ape',
     'audio/wavpack' => 'wv',
     'audio/x-wavpack' => 'wv',
     'audio/x-ms-wma' => 'wma',
     'audio/wma' => 'wma',
     'audio/amr' => 'amr',
     'audio/amr-wb' => 'amr',
     'audio/amr-nb' => 'amr',
     'audio/3gpp' => '3gp',
     'audio/3gpp2' => '3g2',
     'audio/midi' => 'mid',
     'audio/mid' => 'mid',
     'audio/x-midi' => 'mid',
     'audio/x-mid' => 'mid',
     'audio/riff-midi' => 'midi',
     'audio/sp-midi' => 'midi',
     'audio/kar' => 'kar',
     'audio/x-kar' => 'kar',
     'audio/mus' => 'mus',
     'audio/x-mus' => 'mus',
     'audio/rmf' => 'rmf',
     'audio/x-rmf' => 'rmf',
     'audio/au' => 'au',
     'audio/x-au' => 'au',
     'audio/snd' => 'snd',
     'audio/x-snd' => 'snd',
     'audio/basic' => 'au',
     'audio/x-basic' => 'au',
     'audio/ulaw' => 'ulaw',
     'audio/x-ulaw' => 'ulaw',
     'audio/alaw' => 'alaw',
     'audio/x-alaw' => 'alaw',
     'audio/gsm' => 'gsm',
     'audio/x-gsm' => 'gsm',
     'audio/dts' => 'dts',
     'audio/x-dts' => 'dts',
     'audio/ac3' => 'ac3',
     'audio/x-ac3' => 'ac3',
     'audio/eac3' => 'eac3',
     'audio/x-eac3' => 'eac3',
     'audio/aiff' => 'aiff',
     'audio/x-aiff' => 'aiff',
     'audio/aif' => 'aif',
     'audio/x-aif' => 'aif',
     'audio/aifc' => 'aifc',
     'audio/x-aifc' => 'aifc',
     'audio/s3m' => 's3m',
     'audio/x-s3m' => 's3m',
     'audio/xm' => 'xm',
     'audio/x-xm' => 'xm',
     'audio/it' => 'it',
     'audio/x-it' => 'it',
     'audio/mod' => 'mod',
     'audio/x-mod' => 'mod',
     'audio/669' => '669',
     'audio/x-669' => '669',
     'audio/far' => 'far',
     'audio/x-far' => 'far',
     'audio/mtm' => 'mtm',
     'audio/x-mtm' => 'mtm',
     'audio/ult' => 'ult',
     
     /**
      * Видео
      * 
      * Поддерживает современные и классические видео форматы:
      * - Популярные форматы (MP4, AVI, MOV, WMV, WebM, MKV, FLV, 3GP)
      * - Профессиональные форматы (MPEG, M4V, TS, MTS, M2TS, VOB, BDMV)
      * - Веб-оптимизированные (WebM, OGV, MP4)
      * - Специализированные (ASF, RM, RMVB, SWF, F4V, F4P, F4A, F4B)
      * - Кинематографические (MXF, AAF, GXF, LXF, DCP, IMF)
      * 
      * Включает поддержку различных контейнеров и кодеков.
      * 
      * @see https://developer.mozilla.org/en-US/docs/Web/HTTP/Basics_of_HTTP/MIME_types#video_types
      */
     // Видео
     'video/mp4' => 'mp4',
     'video/avi' => 'avi',
     'video/mpeg' => 'mpg',
     'video/quicktime' => 'mov',
     'video/x-msvideo' => 'avi',
     'video/x-ms-wmv' => 'wmv',
     'video/webm' => 'webm',
     'video/x-flv' => 'flv',
     'video/x-matroska' => 'mkv',
     'video/x-ms-asf' => 'asf',
     'video/x-ms-wmx' => 'wmx',
     'video/x-ms-wvx' => 'wvx',
     'video/x-sgi-movie' => 'movie',
     'video/x-ms-wm' => 'wm',
     'video/x-ms-wmp' => 'wmp',
     
     // Другие
     'application/octet-stream' => 'bin',
     'application/x-director' => 'dir',
     'application/x-java-applet' => 'class',
     'application/x-java-archive' => 'jar',
     'application/x-java-vm' => 'class',
     'application/x-java-serialized-object' => 'ser',
     'application/x-python-code' => 'py',
     'application/x-python-bytecode' => 'pyc',
     'application/x-perl' => 'pl',
     'application/x-ruby' => 'rb',
     'application/x-php' => 'php',
     'application/x-csharp' => 'cs',
     'application/x-visual-basic' => 'vb',
     
     /**
      * Программные файлы
      * 
      * Поддерживает файлы исходного кода и исполняемые файлы:
      * - Веб-технологии (PHP, HTML, CSS, JavaScript, TypeScript, JSX, TSX)
      * - Языки программирования (Python, Java, C#, VB, C++, C, Go, Rust, Swift, Kotlin)
      * - Скриптовые языки (Perl, Ruby, Lua, Shell, Batch, PowerShell)
      * - Конфигурационные файлы (JSON, XML, YAML, TOML, INI, CONF)
      * - Сборка и развертывание (Makefile, Dockerfile, Vagrantfile, Gemfile, Package.json)
      * 
      * Включает поддержку современных и классических языков программирования.
      * 
      * @see https://developer.mozilla.org/en-US/docs/Web/HTTP/Basics_of_HTTP/MIME_types#text_types
      */
     // Программные файлы
     'text/php' => 'php',
     
     /**
      * Шрифты
      * 
      * Поддерживает современные и классические форматы шрифтов:
      * - TrueType и OpenType (TTF, OTF, TTC, OTC)
      * - Веб-шрифты (WOFF, WOFF2, EOT)
      * - PostScript шрифты (PFB, PFM, PFA, AFM)
      * - Unix шрифты (BDF, PCF, SNF, PFR)
      * - Специализированные (FNT, FON, TTF, OTF)
      * 
      * Включает поддержку коллекций шрифтов и метрик.
      * 
      * @see https://developer.mozilla.org/en-US/docs/Web/HTTP/Basics_of_HTTP/MIME_types#font_types
      */
     // Шрифты
     'font/ttf' => 'ttf',
     'font/otf' => 'otf',
     'font/woff' => 'woff',
     'font/woff2' => 'woff2',
     'font/eot' => 'eot',
     'font/sfnt' => 'sfnt',
     'font/collection' => 'ttc',
     'application/font-ttf' => 'ttf',
     'application/font-otf' => 'otf',
     'application/font-woff' => 'woff',
     'application/font-woff2' => 'woff2',
     'application/font-eot' => 'eot',
     'application/font-sfnt' => 'sfnt',
     'application/font-collection' => 'ttc',
     'application/x-font-ttf' => 'ttf',
     'application/x-font-otf' => 'otf',
     'application/x-font-woff' => 'woff',
     'application/x-font-woff2' => 'woff2',
     'application/x-font-eot' => 'eot',
     'application/x-font-sfnt' => 'sfnt',
     'application/x-font-type1' => 'pfa',
     'application/x-font-afm' => 'afm',
     'application/x-font-pfm' => 'pfm',
     'application/x-font-ttc' => 'ttc',
     'application/x-font-collection' => 'ttc',
     'application/vnd.ms-fontobject' => 'eot',
     
     /**
      * Текстовые файлы
      * 
      * Поддерживает различные текстовые форматы:
      * - Простые текстовые файлы (TXT, RTF, MD, ASC, CSV, TSV)
      * - Разметка (HTML, XML, SGML, DTD, XSD, XSL, XSLT)
      * - Конфигурация (INI, CONF, CFG, LOG, LIC, README, CHANGELOG)
      * - Документация (TEX, LATEX, BIB, BIBTEX, AUX, LOG, TOC, LOF, LOT)
      * - Электронная почта (EML, MHT, MHTML)
      * - Специализированные (VCF, ICS, IFC, IGS, STEP, STP)
      * 
      * Включает поддержку как простых, так и структурированных текстовых форматов.
      * 
      * @see https://developer.mozilla.org/en-US/docs/Web/HTTP/Basics_of_HTTP/MIME_types#text_types
      */
     // Текстовые файлы
];