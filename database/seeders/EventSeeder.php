<?php

namespace Database\Seeders;

use App\Models\Event;
use Illuminate\Database\Seeder;

class EventSeeder extends Seeder
{
    public function run(): void
    {
        $events = [
            [
                'title' => 'Đêm nhạc Tixtak: First Light', 'slug' => 'tixtak-first-light',
                'category' => 'music', 'city' => 'TP. Hồ Chí Minh', 'city_key' => 'hcm',
                'venue' => 'Nhà thi đấu Phú Thọ', 'starts_at' => '2026-10-10 19:30:00',
                'cover_image' => 'images/events/concert-aurora.svg', 'ticket_name' => 'Vé tiêu chuẩn',
                'price' => 250000, 'quantity' => 5000,
            ],
            [
                'title' => 'Soundwave Festival 2026', 'slug' => 'soundwave-festival-2026',
                'category' => 'festival', 'city' => 'TP. Hồ Chí Minh', 'city_key' => 'hcm',
                'venue' => 'The Global City', 'starts_at' => '2026-10-18 16:00:00',
                'cover_image' => 'images/events/festival-nova.svg', 'ticket_name' => 'Early Bird',
                'price' => 490000, 'quantity' => 8000,
            ],
            [
                'title' => 'Một đêm cùng những bản tình ca', 'slug' => 'dem-tinh-ca-ha-noi',
                'category' => 'music', 'city' => 'Hà Nội', 'city_key' => 'hanoi',
                'venue' => 'Cung Văn hóa Hữu nghị Việt Xô', 'starts_at' => '2026-10-24 20:00:00',
                'cover_image' => 'images/events/acoustic-autumn.svg', 'ticket_name' => 'Vé tiêu chuẩn',
                'price' => 350000, 'quantity' => 2400,
            ],
            [
                'title' => 'Acoustic bên hiên: Mùa thu', 'slug' => 'acoustic-ben-hien-mua-thu',
                'category' => 'music', 'city' => 'Đà Nẵng', 'city_key' => 'danang',
                'venue' => 'Nhà hát Trưng Vương', 'starts_at' => '2026-11-02 19:00:00',
                'cover_image' => 'images/events/acoustic-autumn.svg', 'ticket_name' => 'Vé tiêu chuẩn',
                'price' => 180000, 'quantity' => 1200,
            ],
            [
                'title' => 'Theatre Night: Chuyện phố', 'slug' => 'theatre-night-chuyen-pho',
                'category' => 'theatre', 'city' => 'Hà Nội', 'city_key' => 'hanoi',
                'venue' => 'Nhà hát Tuổi Trẻ', 'starts_at' => '2026-11-08 19:30:00',
                'cover_image' => 'images/events/theatre-night.svg', 'ticket_name' => 'Vé tiêu chuẩn',
                'price' => 220000, 'quantity' => 700,
            ],
            [
                'title' => 'Indie Weekend: Những ngày xanh', 'slug' => 'indie-weekend-nhung-ngay-xanh',
                'category' => 'festival', 'city' => 'TP. Hồ Chí Minh', 'city_key' => 'hcm',
                'venue' => 'Sala Convention Center', 'starts_at' => '2026-11-15 16:30:00',
                'cover_image' => 'images/events/festival-nova.svg', 'ticket_name' => 'Early Bird',
                'price' => 299000, 'quantity' => 4000,
            ],
            [
                'title' => 'Live in Da Lat: Chạm vào mây', 'slug' => 'live-in-da-lat-cham-vao-may',
                'category' => 'music', 'city' => 'Đà Lạt', 'city_key' => 'dalat',
                'venue' => 'Quảng trường Lâm Viên', 'starts_at' => '2026-11-21 18:00:00',
                'cover_image' => 'images/events/concert-aurora.svg', 'ticket_name' => 'Vé tiêu chuẩn',
                'price' => 390000, 'quantity' => 3000,
            ],
            [
                'title' => 'Đêm nhạc cuối năm: Hẹn nhau nhé', 'slug' => 'dem-nhac-cuoi-nam-hen-nhau-nhe',
                'category' => 'music', 'city' => 'TP. Hồ Chí Minh', 'city_key' => 'hcm',
                'venue' => 'SECC, Quận 7', 'starts_at' => '2026-12-12 20:00:00',
                'cover_image' => 'images/events/theatre-night.svg', 'ticket_name' => 'Vé tiêu chuẩn',
                'price' => 450000, 'quantity' => 6000,
            ],
        ];

        foreach ($events as $data) {
            $ticketData = [
                'name' => $data['ticket_name'],
                'price' => $data['price'],
                'quantity' => $data['quantity'],
                'sold' => 0,
            ];

            unset($data['ticket_name'], $data['price'], $data['quantity']);

            $event = Event::firstOrCreate(['slug' => $data['slug']], $data);
            $event->ticketTypes()->firstOrCreate(['name' => $ticketData['name']], $ticketData);
        }
    }
}
