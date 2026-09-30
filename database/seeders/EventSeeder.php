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
                'title' => 'SAO CONCERT TRẠM 3 - THE STARDOM MUSIC FESTIVAL',
                'slug' => 'sao-concert-tram-sao-3-26418',
                'category' => 'festival', 'city' => 'TP. Hồ Chí Minh', 'city_key' => 'hcm',
                'venue' => 'Vạn Phúc City', 'starts_at' => '2026-10-10 09:00:00',
                'cover_image' => 'images/events/sao-concert-tram-3.png',
                'ticket_name' => 'Giá vé từ Ticketbox', 'price' => 300000,
            ],
            [
                'title' => "'GIỮA MỘT VẠN TOUR (MỞ RỘNG)' - PHÙNG KHÁNH LINH | CHAPTER 5",
                'slug' => 'giua-mot-van-tour-phung-khanh-linh-mo-rong-26459',
                'category' => 'music', 'city' => 'TP. Hồ Chí Minh', 'city_key' => 'hcm',
                'venue' => 'Nhà Thi Đấu Phú Thọ', 'starts_at' => '2026-10-17 19:00:00',
                'cover_image' => 'images/events/giua-mot-van-tour-chapter-5.jpg',
                'ticket_name' => 'Giá vé từ Ticketbox', 'price' => 700000,
            ],
            [
                'title' => 'Tiffany Young: Edge of Calm Tour in Ho Chi Minh City',
                'slug' => 'edge-of-calm-tour-tiffany-young-in-ho-chi-minh-26448',
                'category' => 'music', 'city' => 'TP. Hồ Chí Minh', 'city_key' => 'hcm',
                'venue' => 'Nhà Thi Đấu Nguyễn Du', 'starts_at' => '2026-10-17 18:00:00',
                'cover_image' => 'images/events/tiffany-young-edge-of-calm.jpg',
                'ticket_name' => 'Giá vé từ Ticketbox', 'price' => 1800000,
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

            unset($data['ticket_name'], $data['price']);

            $event = Event::updateOrCreate(['slug' => $data['slug']], $data);
            $event->ticketTypes()->updateOrCreate(['name' => $ticketData['name']], $ticketData);
        }
    }
}
