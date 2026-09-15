<?php

namespace App\Support;

use Illuminate\Http\Response;
use ZipArchive;

final class XlsxExport
{
    /**
     * Build and return a Response containing a native .xlsx file.
     *
     * @param  array<int, array<int|string>>  $headers
     * @param  array<int, array<int|string>>  $rows
     */
    public static function download(array $headers, array $rows, string $filename): Response
    {
        $tempPath = tempnam(sys_get_temp_dir(), 'xlsx_').'.xlsx';

        try {
            $zip = new ZipArchive;

            if ($zip->open($tempPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
                abort(500, 'Could not create XLSX file.');
            }

            $sharedStrings = [];
            $allCells = [];

            // Pre-collect all cell values for shared strings
            $allRows = array_merge([$headers], $rows);
            foreach ($allRows as $row) {
                foreach ($row as $value) {
                    $key = (string) $value;
                    if (isset($sharedStrings[$key])) {
                        continue;
                    }
                    $sharedStrings[$key] = count($sharedStrings);
                }
            }

            $zip->addFromString('[Content_Types].xml', self::contentTypes());
            $zip->addFromString('_rels/.rels', self::rels());
            $zip->addFromString('xl/workbook.xml', self::workbook());
            $zip->addFromString('xl/_rels/workbook.xml.rels', self::workbookRels());
            $zip->addFromString('xl/styles.xml', self::styles());
            $zip->addFromString('xl/sharedStrings.xml', self::sharedStrings($sharedStrings));
            $zip->addFromString('xl/worksheets/sheet1.xml', self::sheet($headers, $rows, $sharedStrings));

            $zip->close();

            $contents = file_get_contents($tempPath);

            return response($contents, 200, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' => 'attachment; filename="'.$filename.'"',
                'Cache-Control' => 'no-cache',
            ]);
        } finally {
            @unlink($tempPath);
        }
    }

    private static function contentTypes(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">
  <Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>
  <Default Extension="xml" ContentType="application/xml"/>
  <Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>
  <Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>
  <Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>
  <Override PartName="/xl/sharedStrings.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sharedStrings+xml"/>
</Types>';
    }

    private static function rels(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
  <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>
</Relationships>';
    }

    private static function workbook(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"
          xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">
  <sheets>
    <sheet name="Sheet1" sheetId="1" r:id="rId1"/>
  </sheets>
</workbook>';
    }

    private static function workbookRels(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
  <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>
  <Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>
  <Relationship Id="rId3" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/sharedStrings" Target="sharedStrings.xml"/>
</Relationships>';
    }

    private static function styles(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">
  <fonts count="1">
    <font>
      <sz val="11"/>
      <name val="Calibri"/>
    </font>
  </fonts>
  <fills count="2">
    <fill><patternFill patternType="none"/></fill>
    <fill><patternFill patternType="gray125"/></fill>
  </fills>
  <borders count="1">
    <border><left/><right/><top/><bottom/><diagonal/></border>
  </borders>
  <cellStyleXfs count="1">
    <xf numFmtId="0" fontId="0" fillId="0" borderId="0"/>
  </cellStyleXfs>
  <cellXfs count="2">
    <xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/>
    <xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0" applyFont="1" applyAlignment="1">
      <alignment wrapText="1"/>
    </xf>
  </cellXfs>
</styleSheet>';
    }

    /**
     * @param  array<string, int>  $strings
     */
    private static function sharedStrings(array $strings): string
    {
        $xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'."\n";
        $xml .= '<sst xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" count="'.count($strings).'" uniqueCount="'.count($strings).'">'."\n";

        foreach ($strings as $text => $_index) {
            $xml .= '<si><t>'.self::xmlEscape($text).'</t></si>'."\n";
        }

        $xml .= '</sst>';

        return $xml;
    }

    /**
     * @param  array<int|string>  $headers
     */
    private static function sheet(array $headers, array $rows, array $sharedStrings): string
    {
        $colCount = count($headers);
        $rowCount = count($rows) + 1; // +1 for header row

        $xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'."\n";
        $xml .= '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'."\n";

        // Column widths
        $xml .= '<cols>';
        for ($col = 1; $col <= $colCount; $col++) {
            $xml .= '<col min="'.$col.'" max="'.$col.'" width="20" customWidth="1"/>';
        }
        $xml .= '</cols>';

        $xml .= '<sheetData>';

        // Header row
        $xml .= '<row r="1">';
        $colLetter = 'A';
        foreach ($headers as $header) {
            $idx = $sharedStrings[(string) $header];
            $xml .= '<c r="'.$colLetter.'1" t="s" s="1"><v>'.$idx.'</v></c>';
            $colLetter++;
        }
        $xml .= '</row>';

        // Data rows
        $rowNum = 2;
        foreach ($rows as $row) {
            $xml .= '<row r="'.$rowNum.'">';
            $colLetter = 'A';
            foreach ($row as $value) {
                if (is_numeric($value) && $value !== '') {
                    $xml .= '<c r="'.$colLetter.$rowNum.'" s="0"><v>'.$value.'</v></c>';
                } else {
                    $key = (string) $value;
                    $idx = $sharedStrings[$key] ?? 0;
                    $xml .= '<c r="'.$colLetter.$rowNum.'" t="s" s="0"><v>'.$idx.'</v></c>';
                }
                $colLetter++;
            }
            $xml .= '</row>';
            $rowNum++;
        }

        $xml .= '</sheetData></worksheet>';

        return $xml;
    }

    private static function xmlEscape(string $value): string
    {
        return htmlspecialchars($value, ENT_XML1 | ENT_QUOTES, 'UTF-8');
    }
}
