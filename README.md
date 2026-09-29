# Zeplao - Shoe Shop

Website bán giày trực tuyến được xây dựng bằng Laravel, giao diện lấy cảm hứng từ Nike.

## Công nghệ sử dụng

- Backend: Laravel 12
- Frontend: Blade + Tailwind CSS + Alpine.js
- Database: MySQL
- Thanh toán: VNPay
- Public local: ngrok

## Tính năng chính

- Quản lý sản phẩm (size, màu, tồn kho)
- Giỏ hàng
- Đặt hàng & thanh toán VNPay
- Quản lý đơn hàng (Admin duyệt → Đang giao → Hoàn thành)
- Phân quyền Admin / Customer

## Cài đặt

```bash
# Clone project
git clone https://github.com/TuanAnhh86-dev/zeplao.git
cd zeplao

# Cài đặt package
composer install
npm install && npm run build

# Chạy migration
php artisan migrate

# Chạy server
php artisan serve