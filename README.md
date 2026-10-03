# Tixtak

Tixtak là ứng dụng đặt vé sự kiện được xây dựng bằng Laravel 12. Khách hàng có thể duyệt sự kiện, chọn hạng vé và theo dõi đơn đã đặt. Quản trị viên quản lý sự kiện, số lượng vé và xác nhận hoặc hủy đơn.

## Công nghệ

- PHP 8.2+ và Laravel 12
- Blade, Tailwind CSS 4 và Vite
- SQLite mặc định; có thể cấu hình MySQL trong `.env`
- Mã xác minh đăng ký và đặt lại mật khẩu gửi qua email

## Chức năng

### Khách hàng

- Đăng ký bằng mã xác minh email, đăng nhập và đặt lại mật khẩu
- Xem sự kiện, thông tin địa điểm, sơ đồ và hạng vé
- Đặt tối đa 10 vé mỗi hạng trong một yêu cầu; hệ thống kiểm tra tồn kho khi ghi nhận đơn
- Vé được giữ 10 phút cho đơn chờ xác nhận; đơn hết hạn tự nhả vé về kho
- Theo dõi đơn vé trên trang tổng quan

### Quản trị viên

- Bảng tổng quan về sự kiện, sức chứa, số vé đã bán và đơn gần đây
- Số đơn chờ xử lý, doanh thu từ đơn đã xác nhận, tìm kiếm và lọc danh sách
- Tạo, chỉnh sửa và xóa sự kiện
- Quản lý hạng vé, giá và sức chứa; sức chứa không thể thấp hơn số vé đã bán hoặc đang được giữ
- Xem đơn đặt vé, xác nhận đơn hoặc hủy đơn và trả vé về kho

## Cài đặt

Yêu cầu PHP 8.2+, Composer, Node.js và npm.

```bash
composer install
```

Tạo `.env` từ tệp mẫu nếu dự án chưa có cấu hình môi trường:

```powershell
Copy-Item .env.example .env
```

Sau khi tạo `.env`, tạo khóa ứng dụng:

```bash
php artisan key:generate
```

Cấu hình cơ sở dữ liệu trong `.env`. Mặc định dự án dùng SQLite; tạo tệp nếu chưa có:

```powershell
New-Item -ItemType File -Force database/database.sqlite
```

Sau đó chạy migration, dữ liệu mẫu và biên dịch giao diện:

```bash
php artisan migrate --seed
npm install
npm run build
```

Khởi động máy chủ:

```bash
php artisan serve
```

Trong lúc phát triển giao diện, chạy `npm run dev` ở một terminal khác. `composer run dev` cũng chạy tiến trình scheduler để tự hủy đơn giữ vé đã hết hạn mỗi phút. Trên máy chủ, cấu hình Laravel Scheduler chạy `php artisan schedule:run` mỗi phút.

## Email và mã xác minh

Thiết lập `MAIL_*` trong `.env` để gửi OTP thật. Cấu hình mẫu dùng mailer `log`, phù hợp phát triển cục bộ; thư được ghi vào log ứng dụng thay vì gửi đi. Cache và session mặc định dùng database, vì vậy cần chạy migration trước khi dùng.

## Tài khoản mẫu

Seeder tạo dữ liệu sự kiện và tài khoản demo. Xem `database/seeders/DemoUsersSeeder.php` để biết thông tin đăng nhập mẫu; hãy đổi mật khẩu trước khi dùng môi trường chia sẻ hoặc triển khai.

## Lệnh hữu ích

```bash
php artisan migrate:fresh --seed
php artisan route:list
npm run dev
npm run build
```

`migrate:fresh` xóa toàn bộ dữ liệu hiện có trước khi tạo lại cơ sở dữ liệu.
