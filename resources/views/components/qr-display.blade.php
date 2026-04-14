@props(['payload'])

@php
    $options = new \chillerlan\QRCode\QROptions;
    $options->outputType = \chillerlan\QRCode\Common\EccLevel::L;
    $options->outputInterface = \chillerlan\QRCode\Output\QRMarkupSVG::class;
    $options->svgUseCssProperties = false;
    $options->drawLightModules = true;
    $options->addQuietzone = true;
    $options->scale = 10;

    $qrcode = (new \chillerlan\QRCode\QRCode($options))->render($payload);
@endphp

<div {{ $attributes->merge(['class' => 'inline-block']) }}>
    <img src="{{ $qrcode }}" alt="QR Code" class="w-full h-auto rounded-xl">
</div>
