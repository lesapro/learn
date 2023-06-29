<?php

namespace Fovoso\Catalog\Plugin;

class SetName
{
    private \Magento\Framework\App\RequestInterface $request;

    public function __construct(\Magento\Framework\App\RequestInterface $request)
    {
        $this->request = $request;
    }

    const CATEGORIES_ID = [56, 57, 58, 66, 63, 81, 82, 130, 134, 371, 83, 84, 85, 86, 164, 247, 248, 297, 731, 739, 1694, 1524, 1525, 1526, 1700, 3017, 1696, 1698, 1715, 1940, 1697, 1699, 1702, 1701, 1708, 1726, 1918, 1733, 1933, 2157, 1957, 1987, 2047, 2077, 2107,356, 357, 358, 376, 575, 576, 1527, 621, 622, 625, 627, 632, 691, 733, 734, 735, 742, 744, 740, 741, 743, 838, 1510, 1511, 1528, 1529, 1533, 1535, 1540, 1530, 1532, 1534, 1537, 1538, 1536, 1516, 1517, 45, 46, 54, 94, 97, 99, 108, 192, 364, 369, 250, 595, 597, 6001, 55, 100, 118, 123, 190, 705, 191, 276, 494, 495, 704, 708, 709, 718, 706, 707, 714, 715, 716, 721, 251, 252, 316, 595, 596, 603, 605, 615, 732, 597, 598, 599, 604, 606, 609, 610, 607, 608, 2342, 612, 613, 616, 600, 710, 711, 712, 713, 717, 719, 720,
        194, 195, 219, 227, 241, 242, 2134, 244, 311, 312, 757, 75, 76, 77, 93, 366, 80, 105, 128, 129, 361, 289, 379];

    public function afterProductAttribute($subject, $result, $product, $attributeHtml, $attributeName) {
        $a = 1;
        if ($this->request->getRequestString() === '') {
            if ($attributeName === 'name') {
                foreach ($product->getCategoryIds() as $categoryId) {
                    if (in_array($categoryId, self::CATEGORIES_ID)) {
                        return $result;
                    }
                }
                return '';
            }
        }

        return $result;
    }
}
