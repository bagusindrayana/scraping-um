<?php

use Illuminate\Support\Facades\Route;
use GuzzleHttp\Client;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return "hasil data dari proses scraping website https://universitasmulia.ac.id/. gunakan parameter ?page untuk menampilkan data selanjutnya";
});

Route::get('/{cat}', function ($cat) {
    $url = "https://universitasmulia.ac.id/category/$cat".((request()->page && request()->page > 1)?"/page/".request()->page:"");
    $httpClient = new Client();
    $response = $httpClient->get($url);
    $htmlString = (string) $response->getBody();
    //add this line to suppress any warnings
    libxml_use_internal_errors(true);
    $doc = new DOMDocument();
    $doc->loadHTML($htmlString);
    $xpath = new DOMXPath($doc);
    $judul_beritas = $xpath->evaluate('//main[contains(@class,"content")]//article[contains(@class,"post-entry")]//header//h2//a');
    $gambar_beritas = $xpath->evaluate('//main[contains(@class,"content")]//article[contains(@class,"post-entry")]//div[@class="blog-meta"]//a//img/@src');
    $isi_beritas = $xpath->evaluate('//main[contains(@class,"content")]//article[contains(@class,"post-entry")]//div[@class="entry-content"]');
    $tanggal_berita = $xpath->evaluate('//main[contains(@class,"content")]//article[contains(@class,"post-entry")]//time[contains(@class,"date-container")]');

    $beritas = [];
    
    foreach ($judul_beritas as $index => $judul_berita) {
        
        $beritas[] = [
            'judul_berita' => $judul_berita->textContent,
            'gambar_berita'=>@$gambar_beritas[$index]->textContent,
            'isi_berita'=>@$isi_beritas[$index]->textContent,
            'tanggal_berita'=>@$tanggal_berita[$index]->textContent
            
        ];
        
    }

    return json_encode($beritas);
});
