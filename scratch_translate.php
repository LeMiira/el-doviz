<?php
$po_file = 'languages/ledoviz-turkish-exchange-rates-en_US.po';
$content = file_get_contents($po_file);

$dict = [
    'Dil / Language' => 'Language',
    'El Döviz' => 'El Doviz',
    'El Döviz Kurları ve Trendler' => 'El Doviz Rates and Trends',
    'Para birimlerini dinamik yükseliş/düşüş okları (trend göstergeleri) ve yüzde değişimleriyle birlikte gösterir. Renkleri ve tasarımı tamamen özelleştirilebilir.' => 'Displays currencies with dynamic up/down arrows (trend indicators) and percentage changes. Colors and design are fully customizable.',
    'Trend Oklarını Göster' => 'Show Trend Arrows',
    'Göster' => 'Show',
    'Gizle' => 'Hide',
    'Yükseliş Rengi (Trend Up)' => 'Trend Up Color',
    'Düşüş Rengi (Trend Down)' => 'Trend Down Color',
    'Sürüm:' => 'Version:',
    'Geliştirici:' => 'Developer:',
    'Lisans:' => 'License:',
    'Veri Kaynağı:' => 'Data Source:',
    'TCMB (T.C. Merkez Bankası)' => 'CBRT (Central Bank of the Republic of Turkey)',
    'Invalid BIST JSON.' => 'Invalid BIST JSON.',
    'Eklentiyi Destekleyin' => 'Support the Plugin',
    'GitHub Sponsors' => 'GitHub Sponsors',
    'Buy Me a Coffee' => 'Buy Me a Coffee',
    'El Döviz Canlı Kur Bandı' => 'El Doviz Live Ticker',
    'El Döviz Gizlilik ve KVKK' => 'El Doviz Privacy & GDPR',
    'El Döviz Kontrol Paneli' => 'El Doviz Dashboard',
    'El Döviz Kurları ve Endeksler' => 'El Doviz Exchange Rates and Indexes',
    'Döviz kurları yüklenemedi.' => 'Exchange rates could not be loaded.',
    'Data source (tcmb).' => 'Data source (tcmb).',
    'Invalid data source.' => 'Invalid data source.',
    'Unexpected HTTP response.' => 'Unexpected HTTP response.',
    'Failed to parse TCMB XML.' => 'Failed to parse TCMB XML.',
    'Unknown source for parsing.' => 'Unknown source for parsing.',
    'El Doviz Footer Ticker' => 'El Doviz Footer Ticker',
    'Shows a scrolling ticker of exchange rates in the footer.' => 'Shows a scrolling ticker of exchange rates in the footer.',
    'Rates unavailable.' => 'Rates unavailable.',
    'Currencies (comma separated)' => 'Currencies (comma separated)',
    'El Doviz Header Ticker' => 'El Doviz Header Ticker',
    'Shows a scrolling ticker of exchange rates in the header.' => 'Shows a scrolling ticker of exchange rates in the header.',
    'Exchange Rates' => 'Exchange Rates',
    'Displays selected Turkish exchange rates.' => 'Displays selected Turkish exchange rates.',
    'Live Ticker' => 'Live Ticker',
    'Displays a scrolling live ticker of selected rates.' => 'Displays a scrolling live ticker of selected rates.',
    'LeDoviz - Turkish Exchange Rates' => 'LeDoviz - Turkish Exchange Rates',
    'https://github.com/LeMiira/Le-Doviz' => 'https://github.com/LeMiira/Le-Doviz',
    'Gutenberg, shortcode, and widget support.' => 'Gutenberg, shortcode, and widget support.',
    'Mira' => 'Mira',
    'https://miiiira.com' => 'https://miiiira.com'
];

foreach ($dict as $tr => $en) {
    // Replace empty msgstr for this specific msgid
    $pattern = '/msgid "' . preg_quote($tr, '/') . '"\nmsgstr ""/m';
    $replacement = 'msgid "' . $tr . '"' . "\n" . 'msgstr "' . $en . '"';
    $content = preg_replace($pattern, $replacement, $content);
}

// Special case for long strings that might be wrapped in the PO file
$content = str_replace(
    'msgid "El Döviz eklentisini beğendiyseniz, geliştirilmesine katkıda bulunmak ve "\n"yeni özelliklerin eklenmesini desteklemek için bağış yapabilirsiniz."\nmsgstr ""',
    'msgid "El Döviz eklentisini beğendiyseniz, geliştirilmesine katkıda bulunmak ve "\n"yeni özelliklerin eklenmesini desteklemek için bağış yapabilirsiniz."\nmsgstr "If you liked the El Doviz plugin, you can donate to contribute to its development and support adding new features."',
    $content
);

file_put_contents($po_file, $content);
echo "PO file updated successfully.\n";
?>
