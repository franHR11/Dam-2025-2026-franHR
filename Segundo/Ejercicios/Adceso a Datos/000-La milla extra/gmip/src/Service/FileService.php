<?php

declare(strict_types=1);

namespace GMIP\Service;

class FileService
{
    public static function writeBinaryLog(string $path, string $data): void
    {
        $fh = fopen($path, 'ab');
        if (!$fh) throw new \RuntimeException('No se puede abrir el fichero para escritura: ' . $path);
        fwrite($fh, $data);
        fclose($fh);
    }

    public static function readBinaryChunk(string $path, int $offset, int $length): string
    {
        $fh = fopen($path, 'rb');
        if (!$fh) throw new \RuntimeException('No se puede abrir el fichero para lectura: ' . $path);
        fseek($fh, $offset);
        $chunk = fread($fh, $length);
        fclose($fh);
        return $chunk ?: '';
    }

    public static function move(string $src, string $dest): void
    {
        if (!@rename($src, $dest)) {
            throw new \RuntimeException('Movimiento de fichero fallido');
        }
    }

    public static function copy(string $src, string $dest): void
    {
        if (!@copy($src, $dest)) {
            throw new \RuntimeException('Copia de fichero fallida');
        }
    }
}