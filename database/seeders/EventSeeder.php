<?php

namespace Database\Seeders;

use App\Models\Event;
use Illuminate\Database\Seeder;

class EventSeeder extends Seeder
{
    public function run(): void
    {
        Event::whereIn('slug', [
            'tixtak-countdown-night-2026',
            'winter-lights-live-2027',
        ])->delete();
        
        $events = [
            [
                'title' => 'SAO CONCERT TRẠM 3 - THE STARDOM MUSIC FESTIVAL',
                'slug' => 'sao-concert-tram-sao-3-26418',
                'category' => 'festival', 'city' => 'TP. Hồ Chí Minh', 'city_key' => 'hcm',
                'venue' => 'Vạn Phúc City', 'starts_at' => '2026-12-06 19:00:00',
                'cover_image' => 'images/events/sao-concert-tram-3.png',
                'ticket_name' => 'Giá vé từ Ticketbox', 'price' => 300000,
            ],
            [
                'title' => "'GIỮA MỘT VẠN TOUR (MỞ RỘNG)' - PHÙNG KHÁNH LINH | CHAPTER 5",
                'slug' => 'giua-mot-van-tour-phung-khanh-linh-mo-rong-26459',
                'category' => 'music', 'city' => 'TP. Hồ Chí Minh', 'city_key' => 'hcm',
                'venue' => 'Nhà Thi Đấu Phú Thọ', 'starts_at' => '2026-12-13 19:00:00',
                'cover_image' => 'images/events/giua-mot-van-tour-chapter-5.jpg',
                'ticket_name' => 'Giá vé từ Ticketbox', 'price' => 700000,
            ],
            [
                'title' => 'Tiffany Young: Edge of Calm Tour in Ho Chi Minh City',
                'slug' => 'edge-of-calm-tour-tiffany-young-in-ho-chi-minh-26448',
                'category' => 'music', 'city' => 'TP. Hồ Chí Minh', 'city_key' => 'hcm',
                'venue' => 'Nhà Thi Đấu Nguyễn Du', 'starts_at' => '2027-01-09 18:00:00',
                'cover_image' => 'images/events/tiffany-young-edge-of-calm.jpg',
                'ticket_name' => 'Giá vé từ Ticketbox', 'price' => 1800000,
            ],
            [
                'title' => 'Mr Siro - Encore Extended - Ai Cũng Giấu Trong Lòng Tảng Băng - Hà Nội',
                'slug' => 'mr-siro-encore-extended-ai-cung-giau-trong-long-tang-bang-ha-noi-26333',
                'category' => 'music', 'city' => 'Hà Nội', 'city_key' => 'hanoi',
                'venue' => 'Trung Tâm Hội Nghị Quốc Gia', 'starts_at' => '2026-12-20 19:00:00',
                'cover_image' => 'images/events/mr-siro-encore-extended.jpg',
                'ticket_name' => 'Giá vé từ Ticketbox', 'price' => 800000,
            ],
            [
                'title' => 'Make It Together - Đinh Mạnh Ninh, Will & Hoàng Tôn',
                'slug' => 'make-it-together-dinh-manh-ninh-will-hoang-ton-nov-2026',
                'category' => 'music', 'city' => 'Hà Nội', 'city_key' => 'hanoi',
                'venue' => 'Trung tâm Nghệ thuật Âu Cơ', 'starts_at' => '2026-12-27 20:00:00',
                'cover_image' => 'images/events/make-it-together-november-2026.jpg',
                'ticket_name' => 'Giá vé từ Ticketbox', 'price' => 650000,
            ],
            [
                'title' => 'Tinh Hà "Say Hi" Concert - Đêm 3',
                'slug' => 'tinh-ha-say-hi-dem-3-26590',
                'category' => 'festival', 'city' => 'Hà Nội', 'city_key' => 'hanoi',
                'venue' => 'Trung tâm Triển lãm Việt Nam (VEC)', 'starts_at' => '2027-01-16 19:00:00',
                'cover_image' => 'images/events/tinh-ha-say-hi-dem-3.jpeg',
                'ticket_name' => 'Giá vé từ Ticketbox', 'price' => 800000,
            ],
            [
                'title' => 'The Aura - Không Thể Thay Thế',
                'slug' => 'the-aura-khong-the-thay-the-nov-2026',
                'category' => 'music', 'city' => 'Hà Nội', 'city_key' => 'hanoi',
                'venue' => 'Cung Thể thao Điền kinh Mỹ Đình', 'starts_at' => '2027-01-23 19:30:00',
                'cover_image' => 'images/events/the-aura-november-2026.jpg',
                'ticket_name' => 'Giá vé từ Ticketbox', 'price' => 1250000,
            ],
            [
                'title' => 'Địa Đạo Củ Chi : Trăng Chiến Khu',
                'slug' => 'dia-dao-cu-chi-trang-chien-khu-89666-89666',
                'category' => 'experience', 'city' => 'TP. Hồ Chí Minh', 'city_key' => 'hcm',
                'venue' => 'Địa đạo Củ Chi', 'starts_at' => '2026-10-24 18:00:00',
                'cover_image' => 'images/events/dia-dao-cu-chi-trang-chien-khu.jpg',
                'ticket_name' => 'Trăng Chiến Khu', 'price' => 399000,
            ],
            [
                'title' => 'THE BROTHERS | Đỗ Hoàng Hiệp x Tăng Phúc x Hà Lê x Cheng x Bình Văn Band',
                'slug' => 'the-brothers-do-hoang-hiep-tang-phuc-ha-le-cheng-binh-van-band-26364',
                'category' => 'music', 'city' => 'Hà Nội', 'city_key' => 'hanoi',
                'venue' => 'Trung Tâm Nghệ Thuật Âu Cơ', 'starts_at' => '2026-10-31 20:00:00',
                'cover_image' => 'images/events/the-brothers-ticketbox.jpeg',
                'ticket_name' => 'Vé phổ thông', 'price' => 1000000,
            ],
        ];

        foreach ($events as $data) {
            $ticketData = [
                'name' => $data['ticket_name'],
                'price' => $data['price'],
                // Ticketbox publishes starting prices, but not remaining stock in the listing.
                'quantity' => 0,
                'sold' => 0,
            ];

            $ticketUrl = $data['ticket_url'] ?? '';
            unset($data['ticket_url']);

            unset($data['ticket_name'], $data['price']);

            $event = Event::updateOrCreate(['slug' => $data['slug']], $data);
            $event->update(['ticket_url' => $ticketUrl]);
            $event->ticketTypes()->updateOrCreate(['name' => $ticketData['name']], $ticketData);
        }
    }
}
