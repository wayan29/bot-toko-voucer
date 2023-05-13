<?php
// Load data from data.json file if exists
if (file_exists('data.json')) {
$data = json_decode(file_get_contents('data.json'), true);
$member_code = $data['member_code'] ?? '';
$signature = $data['signature'] ?? '';
$key = $data['key'] ?? '';
$secret = $key;
} else {
$member_code = '';
$signature = '';
$key = '';

}
// Prompt user to enter values if they are not set
if (!$member_code) {
    echo "Enter member code: ";
    $member_code = trim(fgets(STDIN));
}
if (!$signature) {
echo "Enter signature: ";
    $signature = trim(fgets(STDIN));
}

if (!$key) {
echo "Enter secret key: ";
$key = trim(fgets(STDIN));
}
// Save data to data.json file
$data = [
    'member_code' => $member_code,
    'signature' => $signature,
    'key' => $key,
];
file_put_contents('data.json', json_encode($data, JSON_PRETTY_PRINT));
// membaca data dari file history.json
$data = file_get_contents('history.json');
// decode data JSON menjadi array PHP
$data = json_decode($data, true);

echo "Pilih jenis input Ref Id:\n";
echo "1. Input Manual\n";
echo "2. Dari File history.json\n";
echo "Masukkan pilihan: ";
$selected_option = trim(fgets(STDIN));

if ($selected_option == "1") {
  echo "Masukkan Ref Id: ";
  $ref_id_number = trim(fgets(STDIN));
} elseif ($selected_option == "2") {
  echo "Pilih nomor urut Ref Id yang ingin ditampilkan:\n";
  foreach ($data as $key => $item) {
    echo ($key+1) . ". " . $item['ref_id'] . "\n";
  }
  echo "Masukkan pilihan nomor urut Ref Id: ";
  $selected = trim(fgets(STDIN));
  $ref_id = $data[$selected-1];
  $ref_id_number = $ref_id['ref_id'];
} else {
  echo "Input tidak valid\n";
  exit();
}

// input member code, secret, dan signature
//echo "Masukkan Member Code: ";
//$member_code = trim(fgets(STDIN));
//echo "Masukkan Secret: ";
//$secret = trim(fgets(STDIN));
$signature = md5($member_code . ":" . $secret . ":" . $ref_id_number);

// membuat HTTP request ke API TokoVoucher
$url = "https://api.tokovoucher.id/v1/transaksi/status?ref_id=$ref_id_number&member_code=$member_code&signature=$signature";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$response = curl_exec($ch);
curl_close($ch);

// decode response JSON menjadi array PHP
$data = json_decode($response, true);

// cek status response
if ($data['status'] == 0) {
  die("Error accessing API: " . $data['error_msg']);
} elseif ($data['status'] == "pending") {
  echo "Status transaksi  $ref_id_number adalah PENDING \n";
  echo "Status : " .$data['status'] . "\n";
  echo "SN : " .$data['sn'] . "\n";
} elseif ($data['status'] == "sukses") {
  echo "Status transaksi $ref_id_number adalah SUKSES \n";
  echo "Status : " .$data['status'] . "\n";
  echo "SN : " .$data['sn'] . "\n";
} elseif ($data['status'] == "gagal") {
  echo "Status transaksi $ref_id_number GAGAL dengan message:\n";
  echo $data['sn'] . "\n";
} else {
  echo "Status response tidak valid\n";
  exit();
}
?>
