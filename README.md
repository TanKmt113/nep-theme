# NẾP — Sage theme

Bản port của website NẾP (Astro + React) sang **Roots Sage 11** (WordPress + Blade).
Phần CSS/design tokens được giữ nguyên; logic React được chuyển sang Blade + PHP + ACF.

## Yêu cầu

- WordPress (khuyến nghị cài qua [Bedrock](https://roots.io/bedrock/))
- PHP ≥ 8.2, Composer, Node ≥ 20
- Plugin **Advanced Custom Fields** (khuyến nghị **ACF Pro** cho Repeater + Options page)
- Plugin **WooCommerce** (sản phẩm dùng `product` của WooCommerce ở chế độ **RFQ — yêu cầu báo giá**)

## Cài đặt

```bash
# 1. Đặt theme này vào thư mục themes
#    Bedrock:  web/app/themes/nep
#    WP thường: wp-content/themes/nep

# 2. Cài Acorn + dependency PHP
composer install

# 3. Cài dependency JS + build assets
npm install
npm run build        # production
# hoặc: npm run dev   # HMR khi phát triển
```

Trong wp-admin:

1. **Appearance → Themes** → kích hoạt **NẾP**.
2. Cài & kích hoạt plugin **Advanced Custom Fields** và **WooCommerce** (chạy wizard WooCommerce 1 lần để tạo trang Shop/Cart/Checkout).
3. Vào **Appearance → Menus**, tạo menu và gán vào vị trí *Primary Navigation*.
4. **Settings → Reading** → "Trang chủ hiển thị" = *Một trang tĩnh*, chọn trang Home (template `front-page.blade.php` sẽ tự áp dụng cho trang chủ).

## Nạp dữ liệu mẫu (tùy chọn)

Port của `NEP_DATA` nằm ở `app/nep-data.php`. Chạy seeder qua WP-CLI:

```bash
wp nep seed            # tạo product_cat, sản phẩm WooCommerce, dự án, cài đặt + tải ảnh demo
wp nep seed --fresh    # xoá nội dung cũ rồi tạo lại
```

Sau khi có nội dung thật, xoá `app/seed.php` + `app/nep-data.php` và bỏ chúng khỏi danh sách require trong `functions.php`.

## Bản đồ cấu trúc (Astro → Sage)

| Astro | Sage |
|---|---|
| `src/layouts/Layout.astro` | `resources/views/layouts/app.blade.php` |
| `src/components/Header.jsx` | `resources/views/sections/header.blade.php` + JS trong `resources/js/app.js` |
| `src/components/Footer.jsx` | `resources/views/sections/footer.blade.php` |
| `src/components/ui/*` | `resources/views/components/*.blade.php` |
| `src/components/Icon.jsx` (lucide-react) | `components/icon.blade.php` + lucide (CDN/npm) trong `app.js` |
| `src/screens/Home.jsx` | `resources/views/front-page.blade.php` (+ `sections/home/*`) |
| `src/screens/Listing.jsx` | `archive-product.blade.php` + `taxonomy-product_cat.blade.php` (lọc server-side) |
| `src/screens/Detail.jsx` | `single-product.blade.php` (WooCommerce, RFQ) |
| `src/screens/ProjectDetail.jsx` | `single-du-an.blade.php` |
| `src/lib/data.js` (`NEP_DATA`) | WooCommerce `product`/`product_cat` + CPT `du_an`/`catalog` + ACF (`app/fields.php`) |
| `src/styles/*` | `resources/css/*` (giữ nguyên) |

## Dữ liệu

| NEP_DATA | WordPress |
|---|---|
| `products[]` | WooCommerce `product` + ACF: material, color, color_hex, badge (giá thật ở `_regular_price`) |
| `categories[]` | Taxonomy `product_cat` (WooCommerce) + ACF: img, icon |
| `projects[]` | CPT `du_an` + ACF: place, year, area, type, span, scope[], gallery |
| `process[]`, `features[]` | ACF Options (Cài đặt NẾP) |
| `hotline`, `slogan`, … | ACF Options — đọc qua `App\nep('key')` |

## WooCommerce — chế độ RFQ (yêu cầu báo giá)

Cấu hình ở [app/woocommerce.php](app/woocommerce.php):

- Sản phẩm **không thể mua** (`woocommerce_is_purchasable` = false) → ẩn nút "Thêm vào giỏ".
- **Ẩn toàn bộ giá** hiển thị (loop + single).
- `cart`/`checkout` bị **chuyển hướng về trang chủ**.
- Mỗi sản phẩm có nút **"Yêu cầu báo giá"** → tới trang Liên hệ kèm `?sp=<id>`; form
  tự điền sẵn tên sản phẩm và gửi kèm trong email.

Muốn bật bán hàng thật trở lại: gỡ/khoá các filter trong file trên (giữ giá hiển thị +
cho `is_purchasable` mặc định) và bỏ chuyển hướng cart/checkout.

> Giá thật vẫn được lưu ở `_regular_price` (seeder nạp từ `price_val`) để admin dùng nội bộ — chỉ ẩn ở front-end.

## Trang nội dung (Page Templates)

Các trang biên tập dùng **Page Template**. Với mỗi trang: vào **Pages → Add New**, đặt
slug đúng như dưới, rồi ở mục **Page Attributes → Template** chọn template tương ứng:

| Trang | Slug | Template |
|---|---|---|
| Giới thiệu | `gioi-thieu` | *Giới thiệu (About)* |
| Xưởng thêu | `xuong-theu` | *Xưởng thêu (Embroidery)* |
| Liên hệ | `lien-he` | *Liên hệ (Contact)* |
| Bộ sưu tập | `bo-suu-tap` | *Bộ sưu tập (Catalogue / Lookbook)* |

> Slug phải khớp vì header/footer/nút CTA trỏ tới `home_url('/gioi-thieu')`, `/xuong-theu`, `/lien-he`.

Nội dung biên tập (câu chuyện, đội ngũ, mốc thời gian, dịch vụ thêu, lookbook) hiện
được nhúng trực tiếp trong các template — sửa ngay trong file Blade, hoặc chuyển sang
ACF nếu cần khách tự sửa.

### Form liên hệ

Form trên trang Liên hệ POST tới `admin-post.php` và được xử lý ở [app/contact.php](app/contact.php)
— validate nonce, `wp_mail()` tới email admin, rồi redirect kèm `?sent=1`. Đổi sang
Contact Form 7 / WPForms nếu cần chống spam, lưu trữ, autoresponder.

## Đã port

✅ Trang chủ · Sản phẩm WooCommerce RFQ (archive-product + taxonomy-product_cat + lọc) ·
Chi tiết sản phẩm (single-product, nút "Yêu cầu báo giá" + gallery) · Chi tiết dự án ·
Giới thiệu · Xưởng thêu · Liên hệ (+form prefill sản phẩm) · Bộ sưu tập · single Catalog ·
components: button, badge, eyebrow, icon, container, product-card, stat, input, select.

## Còn lại (tùy nhu cầu)

- **Testimonials slider** (Home gốc có) + **CatalogueViewer** (lật catalog dạng PDF) — cần JS, chưa port.
- Chuyển nội dung tĩnh (About/Embroidery/Lookbook) sang **ACF** nếu muốn quản trị qua admin.
- Card danh mục đọc **img/icon từ ACF term meta** của `product_cat` (seeder đã set sẵn); có thể thay bằng ảnh danh mục mặc định của WooCommerce nếu muốn.
- Biến thể (màu/chất liệu) hiện là ACF text. Muốn chọn được nhiều biến thể → chuyển sang **WooCommerce variable product + attributes**.

> Lưu ý lint: trình soạn thảo có thể cảnh báo "Call to unknown function" cho các hàm WordPress (`home_url`, `get_field`, …). Đây là cảnh báo của linter PHP đứng ngoài WP — chạy trong WordPress vẫn bình thường.
