<?php

namespace App\Http\Controllers;

use App\Models\Book;
use ZipArchive;

class BookExportController extends Controller
{
    public function export()
    {
        $books = Book::with(['publisher', 'authors'])->get();
        
        $tempDir = sys_get_temp_dir() . '/excel_' . uniqid();
        mkdir($tempDir);
        mkdir($tempDir . '/_rels');
        mkdir($tempDir . '/docProps');
        mkdir($tempDir . '/xl');
        mkdir($tempDir . '/xl/_rels');
        mkdir($tempDir . '/xl/worksheets');
        
        // [Content_Types].xml
        file_put_contents($tempDir . '/[Content_Types].xml', $this->getContentTypes());
        
        // _rels/.rels
        file_put_contents($tempDir . '/_rels/.rels', $this->getRels());
        
        // docProps/app.xml
        file_put_contents($tempDir . '/docProps/app.xml', $this->getApp());
        
        // docProps/core.xml
        file_put_contents($tempDir . '/docProps/core.xml', $this->getCore());
        
        // xl/workbook.xml
        file_put_contents($tempDir . '/xl/workbook.xml', $this->getWorkbook());
        
        // xl/_rels/workbook.xml.rels
        file_put_contents($tempDir . '/xl/_rels/workbook.xml.rels', $this->getWorkbookRels());
        
        // xl/styles.xml (ESTILOS AQUÍ)
        file_put_contents($tempDir . '/xl/styles.xml', $this->getStyles());
        
        // xl/worksheets/sheet1.xml (DATOS AQUÍ)
        file_put_contents($tempDir . '/xl/worksheets/sheet1.xml', $this->getSheet($books));
        
        // Crear ZIP
        $zipFile = sys_get_temp_dir() . '/livros_' . uniqid() . '.xlsx';
        $zip = new ZipArchive();
        $zip->open($zipFile, ZipArchive::CREATE | ZipArchive::OVERWRITE);
        
        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($tempDir),
            \RecursiveIteratorIterator::LEAVES_ONLY
        );
        
        foreach ($files as $file) {
            if (!$file->isDir()) {
                $filePath = $file->getRealPath();
                $relativePath = substr($filePath, strlen($tempDir) + 1);
                $zip->addFile($filePath, $relativePath);
            }
        }
        
        $zip->close();
        
        // Limpiar archivos temporales
        $this->deleteDirectory($tempDir);
        
        // Descargar
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="livros.xlsx"');
        header('Cache-Control: max-age=0');
        
        readfile($zipFile);
        unlink($zipFile);
        exit;
    }
    
    private function getContentTypes()
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">
    <Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>
    <Default Extension="xml" ContentType="application/xml"/>
    <Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>
    <Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>
    <Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>
    <Override PartName="/docProps/core.xml" ContentType="application/vnd.openxmlformats-package.core-properties+xml"/>
    <Override PartName="/docProps/app.xml" ContentType="application/vnd.openxmlformats-officedocument.extended-properties+xml"/>
</Types>';
    }
    
    private function getRels()
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
    <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>
    <Relationship Id="rId2" Type="http://schemas.openxmlformats.org/package/2006/relationships/metadata/core-properties" Target="docProps/core.xml"/>
    <Relationship Id="rId3" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/extended-properties" Target="docProps/app.xml"/>
</Relationships>';
    }
    
    private function getApp()
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Properties xmlns="http://schemas.openxmlformats.org/officeDocument/2006/extended-properties">
    <Application>Microsoft Excel</Application>
</Properties>';
    }
    
    private function getCore()
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<cp:coreProperties xmlns:cp="http://schemas.openxmlformats.org/package/2006/metadata/core-properties" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:dcterms="http://purl.org/dc/terms/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
    <dc:creator>Sistema</dc:creator>
    <dcterms:created xsi:type="dcterms:W3CDTF">' . date('Y-m-d\TH:i:s\Z') . '</dcterms:created>
</cp:coreProperties>';
    }
    
    private function getWorkbook()
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">
    <sheets>
        <sheet name="Livros" sheetId="1" r:id="rId1"/>
    </sheets>
</workbook>';
    }
    
    private function getWorkbookRels()
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
    <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>
    <Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>
</Relationships>';
    }
    
    private function getStyles()
    {
        // Aquí defines los estilos: negrita, fondo amarillo, wrap text, centrado
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">
    <fonts count="2">
        <font>
            <sz val="11"/>
            <name val="Calibri"/>
        </font>
        <font>
            <b/>
            <sz val="12"/>
            <name val="Calibri"/>
        </font>
    </fonts>
    <fills count="3">
        <fill>
            <patternFill patternType="none"/>
        </fill>
        <fill>
            <patternFill patternType="gray125"/>
        </fill>
        <fill>
            <patternFill patternType="solid">
                <fgColor rgb="FFFFFF00"/>
            </patternFill>
        </fill>
    </fills>
    <borders count="1">
        <border>
            <left/>
            <right/>
            <top/>
            <bottom/>
            <diagonal/>
        </border>
    </borders>
    <cellXfs count="4">
        <xf numFmtId="0" fontId="0" fillId="0" borderId="0"/>
        <xf numFmtId="0" fontId="1" fillId="2" borderId="0" applyFont="1" applyFill="1" applyAlignment="1">
            <alignment horizontal="center" vertical="center"/>
        </xf>
        <xf numFmtId="0" fontId="0" fillId="0" borderId="0" applyAlignment="1">
            <alignment wrapText="1" vertical="top"/>
        </xf>
        <xf numFmtId="0" fontId="0" fillId="0" borderId="0" applyAlignment="1">
            <alignment horizontal="center" vertical="center"/>
        </xf>
    </cellXfs>
</styleSheet>';
    }
    
    private function getSheet($books)
    {
        $xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">
    <cols>
        <col min="1" max="1" width="20" customWidth="1"/>
        <col min="2" max="2" width="35" customWidth="1"/>
        <col min="3" max="3" width="25" customWidth="1"/>
        <col min="4" max="4" width="40" customWidth="1"/>
        <col min="5" max="5" width="50" customWidth="1"/>
        <col min="6" max="6" width="15" customWidth="1"/>
    </cols>
    <sheetData>';
        
        // Cabecera (fila 1) con estilo s="1" (negrita + amarillo + centrado)
        $xml .= '<row r="1" ht="25" customHeight="1">';
        $headers = ['ISBN', 'Nome', 'Editora', 'Autores', 'Bibliografia', 'Preço'];
        $col = 'A';
        foreach ($headers as $header) {
            $xml .= '<c r="' . $col . '1" s="1" t="inlineStr"><is><t>' . htmlspecialchars($header) . '</t></is></c>';
            $col++;
        }
        $xml .= '</row>';
        
        // Datos (sin estilo, s="0" por defecto)
        $row = 2;
        foreach ($books as $book) {
            $authors = $book->authors->pluck('name')->implode(', ');
            
            // Calcular altura dinámica basada en el contenido de bibliografia
            $bibliografiaLength = mb_strlen($book->bibliography ?? '');
            $rowHeight = max(30, min(150, ceil($bibliografiaLength / 80) * 20));
            
            $xml .= '<row r="' . $row . '" ht="' . $rowHeight . '" customHeight="1">';
            
            $data = [
                $book->isbn,
                $book->name,
                $book->publisher->name ?? '',
                $authors,
                $book->bibliography,
                number_format($book->price, 2, ',', '.'),
            ];
            
            $col = 'A';
            $colIndex = 0;
            foreach ($data as $value) {
                // s="2" (wrap text) para Bibliografia (índice 4)
                // s="3" (centrado) para las demás columnas
                $style = ($colIndex === 4) ? 's="2"' : 's="3"';
                $xml .= '<c r="' . $col . $row . '" ' . $style . ' t="inlineStr"><is><t>' . htmlspecialchars($value) . '</t></is></c>';
                $col++;
                $colIndex++;
            }
            
            $xml .= '</row>';
            $row++;
        }
        
        $xml .= '</sheetData>
</worksheet>';
        
        return $xml;
    }
    
    private function deleteDirectory($dir)
    {
        if (!file_exists($dir)) return;
        
        $files = array_diff(scandir($dir), ['.', '..']);
        foreach ($files as $file) {
            $path = $dir . '/' . $file;
            is_dir($path) ? $this->deleteDirectory($path) : unlink($path);
        }
        rmdir($dir);
    }
}