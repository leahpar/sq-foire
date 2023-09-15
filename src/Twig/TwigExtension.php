<?php

namespace App\Twig;

use App\Entity\Reference;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class TwigExtension extends AbstractExtension
{


    public function getFilters(): array
    {
        return [
            new TwigFilter('qrcode',  [$this, 'qrcode'], ['is_safe' => ['html']]),
        ];
    }
    
    public function qrCode(?string $value = null): ?string
    {
        $options = [
            'version' => 3, // https://www.qrcode.com/en/about/version.html
            //'versionMin' => 5,
            //'versionMax' => 10,
            'eccLevel' => QRCode::ECC_M,
            //'outputType' => QRCode::OUTPUT_MARKUP_SVG,
            'imageTransparent' => true,
        ];

        $qrcode = new QRCode(new QROptions($options));
        return $qrcode->render($value);
    }

}
