<?php
echo file_get_contents("https://sites.cascais.pt/menu-cascais/participa/prd");



$contentBannerImagesSections = App\Http\Controllers\PublicContentManagerController::getSections("banner_images");

$headerImages = collect($contentBannerImagesSections)->where('code', '=', 'banner_slideshow')->first();
$headerImages = json_decode(collect($headerImages->section_parameters ?? [])->first()->value ?? "{}");

$firstLogo = ONE::getSiteConfiguration("file_logo_first","/images/demo/LogoEmpatia-l-02.png");

$headerImage = ONE::getSiteConfiguration("file_homepage_image","/images/demo/workplace-1245776_1920_grey_blured.jpg");
?>

<div class="container-fluid background-image" style="min-height: 70vh; background-image:url('@if(isset($headerImages) && !collect($headerImages)->isEmpty()) {!! action('FilesController@download', [$headerImages[0]->id, $headerImages[0]->code, 1, "w" => 1280]) !!} @else {{ $headerImage }} @endif ');">

</div>