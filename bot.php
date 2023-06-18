<?php
date_default_timezone_set('Asia/Makassar');
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
    $key = '';                                                }

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
// membuat URL request
$urlcek = 'https://api.tokovoucher.id/member?member_code=' . $member_code . '&signature=' . $signature;
// melakukan request ke API
$response = file_get_contents($urlcek);

// mengambil data JSON dari response
$data = json_decode($response, true);
if ($data['status'] != 1) {
    die("Error !! : " . $data['error_msg'] ."\n");
}
// mengecek status response
if ($data['status'] == 1) {
    // jika sukses, menampilkan saldo member
   echo "Nama : "  .$data['data']['nama'] ."\n";
   echo "Saldo : RP " .number_format( $data['data']['saldo'], 0, ',', '.') . "\n";
} else {
    // jika gagal, menampilkan pesan error
    echo "Terjadi kesalahan: " . $data['error_msg'] . "\n";
}
// Membuat URL untuk melakukan request ke API
$url1 = "https://api.tokovoucher.id/member/produk/category/list?member_code=$member_code&signature=$signature";

// Melakukan request GET ke API dan menyimpan responsenya dalam variabel $response
$response1 = file_get_contents($url1);

// Mengubah JSON menjadi array asosiatif PHP
$data1 = json_decode($response1, true);

// Menampilkan semua kategori yang tersedia
$categories = array();
foreach ($data1['data'] as $item) {
    if (!in_array($item['nama'], $categories)) {
        $categories[] = $item['nama'];
    }
}

echo "Pilih Kategori:\n";
for ($i = 0; $i < count($categories); $i++) {
    echo $i+1 . ". " . $categories[$i] . "\n";
}
$categoryIndex = readline("Masukkan nomor kategori: ");

// Memilih ID kategori produk berdasarkan nomor kategori yang dipilih
$selectedCategory = $categories[$categoryIndex-1];
$idCategory = "";
foreach ($data1['data'] as $item) {
    if ($item['nama'] == $selectedCategory) {
        $idCategory = $item['id'];
        break;
    }
}

// Membuat URL baru dengan ID kategori produk yang dipilih
$url2 = "https://api.tokovoucher.id/member/produk/operator/list?member_code=$member_code&signature=$signature&id=$idCategory";

// Melakukan request GET ke API dan menyimpan responsenya dalam variabel $response
$response2 = file_get_contents($url2);
// Mengubah JSON menjadi array asosiatif PHP
$data = json_decode($response2, true);

// Menampilkan semua operator yang sesuai dengan kategori produk yang dipilih
$operators = $data['data'];
echo "Pilih Operator:\n";
for ($i = 0; $i < count($operators); $i++) {
    echo $i+1 . ". " . $operators[$i]['nama'] . "\n";
}
$operatorIndex = readline("Masukkan nomor operator: ");

// Memilih operator berdasarkan nomor operator yang dipilih
$selectedOperator = $operators[$operatorIndex-1];

// Menampilkan data operator yang dipilih
echo "ID Operator: " . $selectedOperator['id'] . "\n";
echo "Nama Operator: " . $selectedOperator['nama'] . "\n";
//echo "Keterangan: " . $selectedOperator['keterangan'] . "\n";
$id_operator = $selectedOperator['id'];

// Parameter endpoint
$endpoint1 = 'https://api.tokovoucher.id/member/produk/jenis/list';

// Buat URL endpoint
$url3 = $endpoint1 . '?member_code=' . $member_code . '&signature=' . $signature . '&id=' . $id_operator;

// Ambil response dari API
$response3 = file_get_contents($url3);

// Decode response menjadi array associative
$data = json_decode($response3, true);

// Cek apakah response berhasil atau gagal
if ($data['status'] == 0) {
  // Response gagal
  echo 'Error: ' . $data['error_msg'];
} else {
  // Response sukses
  $produk_list = $data['data'];

$jenisproduc = $data['data'];
echo "Pilih Operator:\n";
for ($i = 0; $i < count($jenisproduc); $i++) {
    echo $i+1 . ". " . $jenisproduc[$i]['nama'] . "\n";
}
$jenisproducIndex = readline("Masukkan Nomor jenis: ");

// Memilih operator berdasarkan nomor operator yang dipilih
$selectedjenisproduc = $jenisproduc[$jenisproducIndex-1];

// Menampilkan data operator yang dipilih
echo "ID Operator: " . $selectedjenisproduc['id'] . "\n";
echo "Nama Operator: " . $selectedjenisproduc['nama'] . "\n";
$id_jenis_produk = $selectedjenisproduc['id'];

}
$endpoint2 = 'https://api.tokovoucher.id/member/produk/list';

// Buat URL dengan parameter yang diberikan
$url4 = $endpoint2 . "?member_code=$member_code&signature=$signature&id_jenis=$id_jenis_produk";

// Lakukan request ke API menggunakan cURL
$ch = curl_init($url4);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);
// Cek apakah response berhasil diterima
if ($response === false) {
    die("Error accessing API: " . curl_error($ch));
}

// Konversi response dari JSON menjadi array asosiatif
$data = json_decode($response, true);

$produk_list = $data['data'];

$listproduc = $data['data'];
echo "Pilih Operator:\n";
for ($i = 0; $i < count($listproduc); $i++) {
    echo $i+1 . ". " . $listproduc[$i]['nama_produk'] ." RP : " . number_format($listproduc[$i]['price'], 0, ',', '.')  . "\n";
    
}
$listproducIndex = readline("Masukkan nomor operator: ");

// Memilih operator berdasarkan nomor operator yang dipilih
$selectedlistproduc = $listproduc[$listproducIndex-1];

// Menampilkan data operator yang dipilih
echo "Kode : " . $selectedlistproduc['code'] . "\n";
echo "Product :  " . $selectedlistproduc['nama_produk'] . "\n";
echo "Harga: RP " . number_format($selectedlistproduc['price'], 0, ',', '.') . "\n";
$status = ($selectedlistproduc["status"] == 1) ? 'Tersedia' : 'Gangguan !!';
echo "Status   : " . $status . "\n";
$produk = $selectedlistproduc['code'];

//$secret = $kunci;
$ref_id = "REF" .date('YmdHis') ."WAYAN";
echo $ref_id ."\n";
echo "Input tujuan: ";
$tujuan = trim(fgets(STDIN));

echo "Input server_id (optional): ";
$server_id = trim(fgets(STDIN));

$urltrx = "https://api.tokovoucher.id/v1/transaksi?ref_id={$ref_id}&produk={$produk}&tujuan={$tujuan}&secret={$secret}&member_code={$member_code}&server_id={$server_id}";
$responsetrx = file_get_contents($urltrx);
$data = json_decode($responsetrx, true);

//simpan respon transaksi ke history.json
if (isset($data['status']) && in_array($data['status'], ['sukses', 'pending'])) {
    $history_file = 'history.json';
    if (file_exists($history_file)) {
        $history_data = file_get_contents($history_file);
        $history_arr = json_decode($history_data, true);
    } else {
        $history_arr = array();
    }

    array_push($history_arr, $data);

    file_put_contents($history_file, json_encode($history_arr));
}

// check the status
if($data['status'] == 'sukses'){
    // handle success response
    echo "Success! Transaction ID: ".$data['trx_id'];
}
elseif($data['status'] == 'pending'){
    // handle pending response
    echo "Transaksi  pending TRX ID : ".$data['trx_id'];
}
elseif($data['status'] == 'gagal'){
    // handle failed response
    echo "Transaction failed. Error message: ".$data['sn'];
}
else{
    // handle other errors
    echo "Error: ".$data['error_msg'];
}

?>
