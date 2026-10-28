<?php
/**
 * LunarMC: textura de skin para el visor 3D (PNG sin renderizar).
 * Fuente principal: perfil oficial de Mojang + textures.minecraft.net.
 * Respaldo: textura cruda de Minotar. Caché privado de 30 minutos.
 * No acepta URLs del visitante: evita SSRF.
 */
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (empty($_SESSION['usuario_id'])) {
    http_response_code(401);
    exit;
}
$nick = (string) ($_SESSION['nick'] ?? '');
session_write_close();

if (preg_match('/^[A-Za-z0-9_]{3,16}$/D', $nick) !== 1) {
    http_response_code(400);
    exit;
}

/** Descarga con límites de tiempo, tamaño y HTTPS verificado. */
function lunarFetch(string $url, int $maxBytes = 1048576): ?string
{
    if (!str_starts_with($url, 'https://')) {
        return null;
    }
    if (function_exists('curl_init')) {
        $ch = curl_init($url);
        if ($ch === false) return null;
        $body = '';
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => false,
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_TIMEOUT => 22,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_USERAGENT => 'LunarMC/1.0 (Minecraft skin preview)',
            CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_WRITEFUNCTION => static function ($handle, string $chunk) use (&$body, $maxBytes): int {
                if (strlen($body) + strlen($chunk) > $maxBytes) return 0;
                $body .= $chunk;
                return strlen($chunk);
            },
        ]);
        $ok = curl_exec($ch);
        $status = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return $ok !== false && $status === 200 ? $body : null;
    }
    // Respaldo para XAMPP si cURL está deshabilitado.
    if (!filter_var(ini_get('allow_url_fopen'), FILTER_VALIDATE_BOOLEAN)) return null;
    $ctx = stream_context_create(['http' => [
        'timeout' => 8,
        'follow_location' => 0,
        'ignore_errors' => true,
        'header' => "User-Agent: LunarMC/1.0\r\n",
    ]]);
    $bytes = @file_get_contents($url, false, $ctx, 0, $maxBytes + 1);
    $status = $http_response_header[0] ?? '';
    return is_string($bytes) && strlen($bytes) <= $maxBytes
        && preg_match('/^HTTP\/\S+ 200\b/', $status) === 1 ? $bytes : null;
}

function lunarJson(string $url): ?array
{
    $raw = lunarFetch($url, 65536);
    if ($raw === null) return null;
    $json = json_decode($raw, true);
    return is_array($json) ? $json : null;
}

function lunarSkinUrl(string $nick): ?string
{
    $lookupUrls = [
        'https://api.mojang.com/users/profiles/minecraft/' . rawurlencode($nick),
        'https://api.minecraftservices.com/minecraft/profile/lookup/name/' . rawurlencode($nick),
    ];
    $uuid = null;
    foreach ($lookupUrls as $lookup) {
        $data = lunarJson($lookup);
        $candidate = (string) ($data['id'] ?? '');
        if (preg_match('/^[a-f0-9]{32}$/iD', $candidate)) {
            $uuid = strtolower($candidate);
            break;
        }
    }
    if ($uuid === null) return null;

    $profile = lunarJson('https://sessionserver.mojang.com/session/minecraft/profile/' . $uuid);
    foreach (($profile['properties'] ?? []) as $property) {
        if (($property['name'] ?? '') !== 'textures' || !is_string($property['value'] ?? null)) continue;
        $decoded = base64_decode($property['value'], true);
        if ($decoded === false) continue;
        $data = json_decode($decoded, true);
        $url = (string) ($data['textures']['SKIN']['url'] ?? '');
        // Solo se permiten texturas oficiales con hash, nunca URLs arbitrarias.
        if (preg_match('~^https?://textures\.minecraft\.net/texture/([a-f0-9]{32,128})$~iD', $url, $m)) {
            return 'https://textures.minecraft.net/texture/' . $m[1];
        }
    }
    return null;
}

function lunarValidSkin(string $bytes): bool
{
    if (strlen($bytes) < 24 || substr($bytes, 0, 8) !== "\x89PNG\r\n\x1a\n") return false;
    $width = unpack('N', substr($bytes, 16, 4))[1];
    $height = unpack('N', substr($bytes, 20, 4))[1];
    // PNG estándar (64x64 o 64x32) y variantes HD del mismo formato.
    return $width >= 64 && $width <= 1024 && $width % 64 === 0
        && ($height === $width || $height * 2 === $width);
}

$cacheDir = rtrim(sys_get_temp_dir(), '/\\') . DIRECTORY_SEPARATOR . 'lunarmc_skin_cache';
if (!is_dir($cacheDir)) @mkdir($cacheDir, 0700, true);
$cacheFile = $cacheDir . DIRECTORY_SEPARATOR . hash('sha256', strtolower($nick)) . '.png';
$cached = is_file($cacheFile) ? @file_get_contents($cacheFile) : false;
$cachedValid = is_string($cached) && lunarValidSkin($cached);
$fresh = $cachedValid && (time() - (int) filemtime($cacheFile) < 86400);
$bytes = $fresh ? $cached : null;

// Prioridad a la skin original guardada en LunarMC. Útil si las APIs
// externas están caídas o devuelven Steve por error.
// Guardar el PNG original de la cuenta en skins/NICK.png (64x64).
$localFile = __DIR__ . '/skins/' . $nick . '.png';
if (is_file($localFile)) {
    $localBytes = @file_get_contents($localFile);
    if (is_string($localBytes) && lunarValidSkin($localBytes)) {
        $bytes = $localBytes;
    }
}


if ($bytes === null) {
    $skinUrl = lunarSkinUrl($nick);
    $downloaded = $skinUrl !== null ? lunarFetch($skinUrl) : null;
    if ($downloaded === null || !lunarValidSkin($downloaded)) {
        // Minotar entrega el PNG crudo, no la imagen plana del cuerpo.
        $downloaded = lunarFetch('https://minotar.net/skin/' . rawurlencode($nick));
    }
    if ($downloaded !== null && lunarValidSkin($downloaded)) {
        $bytes = $downloaded;
        if (is_dir($cacheDir) && is_writable($cacheDir)) {
            @file_put_contents($cacheFile, $bytes, LOCK_EX);
        }
    } elseif ($cachedValid) {
        // Una caída de la API no reemplaza la skin por Steve o Alex.
        $bytes = $cached;
    }
}

if (!is_string($bytes) || !lunarValidSkin($bytes)) {
    error_log('LunarMC: skin no disponible para ' . $nick);
    http_response_code(503);
    header('Content-Type: text/plain; charset=utf-8');
    exit('Skin no disponible temporalmente');
}
header('Content-Type: image/png');
header('Cache-Control: private, no-cache');
header('X-Content-Type-Options: nosniff');
echo $bytes;
