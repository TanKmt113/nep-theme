<?php

/**
 * PHP port of the Astro `NEP_DATA` (demo/placeholder content).
 * Consumed by the seeder (app/seed.php) to populate WordPress on first run.
 * Replace imagery + pricing with real data, then remove the seeder.
 */

namespace App;

function nep_seed_data(): array
{
    return [
        'process' => [
            ['n' => '01', 'title' => 'Khảo sát', 'desc' => 'Đo đạc thực tế tại không gian của bạn.', 'icon' => 'ruler'],
            ['n' => '02', 'title' => 'Tư vấn',   'desc' => 'Gợi ý chất liệu, màu sắc và kiểu dáng phù hợp.', 'icon' => 'messages-square'],
            ['n' => '03', 'title' => 'Thiết kế', 'desc' => 'Phối cảnh 3D để bạn hình dung trước.', 'icon' => 'pen-tool'],
            ['n' => '04', 'title' => 'Thi công', 'desc' => 'Sản xuất và lắp đặt bởi đội ngũ chuyên nghiệp.', 'icon' => 'hammer'],
            ['n' => '05', 'title' => 'Bàn giao', 'desc' => 'Nghiệm thu và vệ sinh sạch sẽ.', 'icon' => 'package-check'],
            ['n' => '06', 'title' => 'Bảo hành', 'desc' => 'Đồng hành dài hạn, hỗ trợ nhanh chóng.', 'icon' => 'shield-check'],
        ],

        'features' => [
            ['title' => 'Thi công tận nơi',     'desc' => 'Đo đạc và lắp đặt tại nhà trên toàn quốc.', 'icon' => 'truck'],
            ['title' => 'Bảo hành dài hạn',     'desc' => 'Cam kết bảo hành lên đến 24 tháng.', 'icon' => 'shield-check'],
            ['title' => 'Chất liệu cao cấp',    'desc' => 'Vải nhập khẩu, an toàn và bền màu.', 'icon' => 'sparkles'],
            ['title' => 'Đội ngũ chuyên nghiệp', 'desc' => 'Hơn 10 năm kinh nghiệm trong nghề.', 'icon' => 'users'],
        ],

        'categories' => [
            ['name' => 'Rèm vải',      'count' => 48, 'icon' => 'blinds',            'img' => 'https://images.unsplash.com/photo-1505693416388-ac5ce068fe85?w=900&q=80'],
            ['name' => 'Rèm cầu vồng', 'count' => 32, 'icon' => 'rainbow',           'img' => 'https://images.unsplash.com/photo-1631679706909-1844bbd07221?w=900&q=80'],
            ['name' => 'Rèm cuốn',     'count' => 26, 'icon' => 'scroll',            'img' => 'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?w=900&q=80'],
            ['name' => 'Rèm Roman',    'count' => 21, 'icon' => 'layout-panel-top',  'img' => 'https://images.unsplash.com/photo-1616627561839-074385245ff6?w=900&q=80'],
            ['name' => 'Rèm gỗ',       'count' => 18, 'icon' => 'trees',             'img' => 'https://images.unsplash.com/photo-1540574163026-643ea20ade25?w=900&q=80'],
            ['name' => 'Rèm sáo nhôm', 'count' => 15, 'icon' => 'align-justify',     'img' => 'https://images.unsplash.com/photo-1513161455079-7dc1de15ef3e?w=900&q=80'],
            ['name' => 'Rèm tổ ong',   'count' => 12, 'icon' => 'hexagon',           'img' => 'https://images.unsplash.com/photo-1493809842364-78817add7ffb?w=900&q=80'],
        ],

        'products' => [
            ['name' => 'Rèm vải Linen Bali',    'cat' => 'Rèm vải',      'material' => 'Linen',     'color' => 'Be',     'color_hex' => '#E4DACB', 'price' => 'Từ 1.200.000đ', 'price_val' => 1200000, 'desc' => 'Linen tự nhiên, buông mềm, lọc sáng dịu.', 'badge' => 'new', 'img' => 'https://images.unsplash.com/photo-1505693416388-ac5ce068fe85?w=900&q=80'],
            ['name' => 'Rèm cầu vồng Aurora',   'cat' => 'Rèm cầu vồng', 'material' => 'Polyester', 'color' => 'Xám',    'color_hex' => '#807D72', 'price' => 'Từ 850.000đ',   'price_val' => 850000,  'desc' => 'Điều chỉnh ánh sáng vô cấp, hiện đại.', 'badge' => 'hot', 'img' => 'https://images.unsplash.com/photo-1631679706909-1844bbd07221?w=900&q=80'],
            ['name' => 'Rèm cuốn Sàigon',       'cat' => 'Rèm cuốn',     'material' => 'Blackout',  'color' => 'Trắng',  'color_hex' => '#FFFFFF', 'price' => 'Từ 620.000đ',   'price_val' => 620000,  'desc' => 'Cản sáng tối ưu, gọn gàng cho văn phòng.', 'badge' => '', 'img' => 'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?w=900&q=80'],
            ['name' => 'Rèm Roman Hội An',      'cat' => 'Rèm Roman',    'material' => 'Cotton',    'color' => 'Nâu gỗ', 'color_hex' => '#8C6F52', 'price' => 'Từ 1.450.000đ', 'price_val' => 1450000, 'desc' => 'Xếp lớp thanh lịch, ấm cúng kiểu cổ điển.', 'badge' => '', 'img' => 'https://images.unsplash.com/photo-1616627561839-074385245ff6?w=900&q=80'],
            ['name' => 'Rèm gỗ Tre Việt',       'cat' => 'Rèm gỗ',       'material' => 'Gỗ tre',    'color' => 'Nâu',    'color_hex' => '#6A5440', 'price' => 'Từ 1.680.000đ', 'price_val' => 1680000, 'desc' => 'Nan gỗ tự nhiên, mộc mạc và bền bỉ.', 'badge' => 'new', 'img' => 'https://images.unsplash.com/photo-1540574163026-643ea20ade25?w=900&q=80'],
            ['name' => 'Rèm sáo nhôm Metro',    'cat' => 'Rèm sáo nhôm', 'material' => 'Nhôm',      'color' => 'Xám',    'color_hex' => '#807D72', 'price' => 'Từ 540.000đ',   'price_val' => 540000,  'desc' => 'Lá nhôm mảnh, dễ vệ sinh, phong cách tối giản.', 'badge' => '', 'img' => 'https://images.unsplash.com/photo-1513161455079-7dc1de15ef3e?w=900&q=80'],
            ['name' => 'Rèm tổ ong Cloud',      'cat' => 'Rèm tổ ong',   'material' => 'Polyester', 'color' => 'Kem',    'color_hex' => '#F8F6F3', 'price' => 'Từ 980.000đ',   'price_val' => 980000,  'desc' => 'Cách nhiệt tổ ong, tiết kiệm năng lượng.', 'badge' => 'hot', 'img' => 'https://images.unsplash.com/photo-1493809842364-78817add7ffb?w=900&q=80'],
            ['name' => 'Rèm vải Velvet Noir',   'cat' => 'Rèm vải',      'material' => 'Nhung',     'color' => 'Olive',  'color_hex' => '#6E764F', 'price' => 'Từ 1.950.000đ', 'price_val' => 1950000, 'desc' => 'Nhung dày sang trọng, cản sáng và cách âm.', 'badge' => '', 'img' => 'https://images.unsplash.com/photo-1604014237800-1c9102c219da?w=900&q=80'],
        ],

        'projects' => [
            ['name' => 'Căn hộ The Marq',       'place' => 'Quận 1, TP.HCM',  'span' => 2, 'year' => '2025', 'area' => '120 m²', 'type' => 'Căn hộ cao cấp', 'scope' => ['Rèm vải Linen', 'Rèm cuốn blackout', 'Rèm Roman'], 'desc' => 'Căn hộ 3 phòng ngủ tại The Marq được khoác lên mình hệ rèm vải linen tông be ấm, kết hợp rèm cuốn blackout cho phòng ngủ.', 'img' => 'https://images.unsplash.com/photo-1524758631624-e2822e304c36?w=900&q=80'],
            ['name' => 'Biệt thự Thảo Điền',    'place' => 'TP. Thủ Đức',     'span' => 1, 'year' => '2024', 'area' => '320 m²', 'type' => 'Biệt thự', 'scope' => ['Rèm gỗ tự nhiên', 'Rèm vải nhung', 'Rèm tổ ong'], 'desc' => 'Biệt thự sân vườn dùng rèm gỗ tre cho không gian tiếp khách và rèm nhung olive cho phòng master.', 'img' => 'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?w=900&q=80'],
            ['name' => 'Văn phòng Dreamplex',   'place' => 'Quận 3, TP.HCM',  'span' => 1, 'year' => '2024', 'area' => '650 m²', 'type' => 'Văn phòng', 'scope' => ['Rèm cuốn', 'Rèm cầu vồng', 'Thêu đồng phục'], 'desc' => 'Không gian làm việc chung trang bị rèm cầu vồng điều sáng vô cấp cho phòng họp.', 'img' => 'https://images.unsplash.com/photo-1631679706909-1844bbd07221?w=900&q=80'],
            ['name' => 'Penthouse Landmark 81', 'place' => 'Bình Thạnh',      'span' => 2, 'year' => '2025', 'area' => '240 m²', 'type' => 'Penthouse', 'scope' => ['Rèm vải tự động', 'Rèm sáo nhôm', 'Rèm Roman'], 'desc' => 'Penthouse tầng cao ứng dụng hệ rèm vải điều khiển tự động theo khung giờ.', 'img' => 'https://images.unsplash.com/photo-1513161455079-7dc1de15ef3e?w=900&q=80'],
        ],
    ];
}
