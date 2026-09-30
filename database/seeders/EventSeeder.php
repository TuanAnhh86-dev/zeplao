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
                'introduction' => html_entity_decode('SAO CONCERT TR&#7840;M 3 mang ch&#7911; &#273;&#7873; THE STARDOM MUSIC FESTIVAL, h&#432;&#7899;ng &#273;&#7871;n m&#7897;t &#273;&#234;m nh&#7841;c n&#259;ng &#273;&#7897;ng v&#7899;i ba s&#226;n kh&#7845;u &#273;&#432;&#7907;c gi&#7899;i thi&#7879;u tr&#234;n k&#234;nh c&#7911;a ban t&#7893; ch&#7913;c. Kh&#244;ng kh&#237; l&#7877; h&#7897;i, h&#7879; th&#7889;ng &#226;m thanh v&#224; c&#225;c m&#224;n tr&#236;nh di&#7877;n li&#234;n ti&#7871;p t&#7841;o n&#234;n m&#7897;t h&#224;nh tr&#236;nh &#226;m nh&#7841;c d&#224;nh cho kh&#225;n gi&#7843; mu&#7889;n h&#242;a m&#236;nh v&#224;o kh&#244;ng gian concert ngo&#224;i tr&#7901;i t&#7841;i V&#7841;n Ph&#250;c City. Th&#244;ng tin v&#7873; ngh&#7879; s&#297; bi&#7875;u di&#7877;n v&#224; quy&#7873;n l&#7907;i theo t&#7915;ng h&#7841;ng v&#233; &#273;&#432;&#7907;c th&#7875; hi&#7879;n trong ph&#7847;n th&#244;ng tin v&#233;.', ENT_QUOTES | ENT_HTML5, 'UTF-8'),
                'ticket_name' => 'Giá vé từ Ticketbox', 'price' => 300000,
            ],
            [
                'title' => "'GIỮA MỘT VẠN TOUR (MỞ RỘNG)' - PHÙNG KHÁNH LINH | CHAPTER 5",
                'slug' => 'giua-mot-van-tour-phung-khanh-linh-mo-rong-26459',
                'category' => 'music', 'city' => 'TP. Hồ Chí Minh', 'city_key' => 'hcm',
                'venue' => 'Nhà Thi Đấu Phú Thọ', 'starts_at' => '2026-12-13 19:00:00',
                'cover_image' => 'images/events/giua-mot-van-tour-chapter-5.jpg',
                'introduction' => html_entity_decode('GI&#7918;A M&#7896;T V&#7840;N TOUR l&#224; live experience &#273;&#432;&#7907;c Ph&#249;ng Kh&#225;nh Linh x&#226;y d&#7921;ng t&#7915; album GI&#7918;A M&#7896;T V&#7840;N NG&#431;&#7900;I. &#194;m nh&#7841;c, &#225;nh s&#225;ng, h&#236;nh &#7843;nh v&#224; c&#225;ch d&#7851;n chuy&#7879;n tr&#234;n s&#226;n kh&#7845;u k&#7871;t n&#7889;i th&#224;nh m&#7897;t m&#7841;ch c&#7843;m x&#250;c v&#7873; y&#234;u th&#432;&#417;ng, m&#7845;t m&#225;t, gi&#7857;ng x&#233; v&#224; tr&#432;&#7903;ng th&#224;nh. Chapter 5 ti&#7871;p t&#7909;c h&#224;nh tr&#236;nh &#7845;y trong m&#7897;t bu&#7893;i di&#7877;n tr&#7921;c ti&#7871;p, n&#417;i kh&#225;n gi&#7843; c&#243; th&#7875; l&#7855;ng nghe c&#225;c ca kh&#250;c c&#7911;a Linh trong kh&#244;ng gian &#273;&#432;&#7907;c d&#224;n d&#7921;ng ri&#234;ng cho tour.', ENT_QUOTES | ENT_HTML5, 'UTF-8'),
                'ticket_name' => 'Giá vé từ Ticketbox', 'price' => 700000,
            ],
            [
                'title' => 'Tiffany Young: Edge of Calm Tour in Ho Chi Minh City',
                'slug' => 'edge-of-calm-tour-tiffany-young-in-ho-chi-minh-26448',
                'category' => 'music', 'city' => 'TP. Hồ Chí Minh', 'city_key' => 'hcm',
                'venue' => 'Nhà Thi Đấu Nguyễn Du', 'starts_at' => '2027-01-09 18:00:00',
                'cover_image' => 'images/events/tiffany-young-edge-of-calm.jpg',
                'introduction' => html_entity_decode('EDGE OF CALM TOUR &#273;&#432;a Tiffany Young tr&#7903; l&#7841;i TP. H&#7891; Ch&#237; Minh trong chuy&#7871;n l&#432;u di&#7877;n solo mang t&#234;n album ph&#242;ng thu &#273;&#7847;u tay c&#249;ng t&#234;n. Kh&#225;n gi&#7843; c&#243; th&#7875; mong &#273;&#7907;i m&#7897;t &#273;&#234;m nh&#7841;c n&#417;i Tiffany k&#7871;t n&#7889;i ch&#7863;ng &#273;&#432;&#7901;ng ho&#7841;t &#273;&#7897;ng solo v&#7899;i nh&#7919;ng ca kh&#250;c m&#7899;i trong album, trong kh&#244;ng gian concert &#273;&#432;&#7907;c thi&#7871;t k&#7871; quanh m&#224;u s&#7855;c v&#224; c&#7843;m x&#250;c c&#7911;a d&#7921; &#225;n. Tiffany Young &#273;&#432;&#7907;c kh&#225;n gi&#7843; qu&#7889;c t&#7871; bi&#7871;t &#273;&#7871;n t&#7915; Girls&#39; Generation v&#224; c&#225;c ho&#7841;t &#273;&#7897;ng &#226;m nh&#7841;c c&#225; nh&#226;n.', ENT_QUOTES | ENT_HTML5, 'UTF-8'),
                'ticket_name' => 'Giá vé từ Ticketbox', 'price' => 1800000,
            ],
            [
                'title' => 'Mr Siro - Encore Extended - Ai Cũng Giấu Trong Lòng Tảng Băng - Hà Nội',
                'slug' => 'mr-siro-encore-extended-ai-cung-giau-trong-long-tang-bang-ha-noi-26333',
                'category' => 'music', 'city' => 'Hà Nội', 'city_key' => 'hanoi',
                'venue' => 'Trung Tâm Hội Nghị Quốc Gia', 'starts_at' => '2026-12-20 19:00:00',
                'cover_image' => 'images/events/mr-siro-encore-extended.jpg',
                'introduction' => html_entity_decode('MR SIRO: ENCORE EXTENDED l&#224; &#273;&#234;m di&#7877;n th&#234;m trong h&#224;nh tr&#236;nh concert AI C&#361;NG GI&#7844;U TRONG L&#210;NG T&#7842;NG B&#258;NG. Ch&#432;&#417;ng tr&#236;nh xoay quanh nh&#7919;ng ca kh&#250;c ballad g&#7855;n v&#7899;i Mr Siro trong vai tr&#242; ca s&#297; v&#224; nh&#7841;c s&#297;, mang &#273;&#7871;n kh&#244;ng gian &#273;&#7875; kh&#225;n gi&#7843; c&#249;ng h&#225;t theo v&#224; l&#7855;ng nghe nh&#7919;ng c&#226;u chuy&#7879;n t&#236;nh c&#7843;m quen thu&#7897;c. Bu&#7893;i di&#7877;n t&#7841;i Trung T&#226;m H&#7897;i Ngh&#7883; Qu&#7889;c Gia l&#224; d&#7883;p &#273;&#7875; ng&#432;&#7901;i h&#226;m m&#7897; g&#7863;p l&#7841;i c&#225;c s&#225;ng t&#225;c c&#7911;a anh trong m&#7897;t &#273;&#234;m nh&#7841;c tr&#7885;n v&#7865;n.', ENT_QUOTES | ENT_HTML5, 'UTF-8'),
                'ticket_name' => 'Giá vé từ Ticketbox', 'price' => 800000,
            ],
            [
                'title' => 'Make It Together - Đinh Mạnh Ninh, Will & Hoàng Tôn',
                'slug' => 'make-it-together-dinh-manh-ninh-will-hoang-ton-nov-2026',
                'category' => 'music', 'city' => 'Hà Nội', 'city_key' => 'hanoi',
                'venue' => 'Trung tâm Nghệ thuật Âu Cơ', 'starts_at' => '2026-12-27 20:00:00',
                'cover_image' => 'images/events/make-it-together-november-2026.jpg',
                'introduction' => html_entity_decode('MAKE IT TOGETHER l&#224; &#273;&#234;m nh&#7841;c do &#272;inh M&#7841;nh Ninh d&#7851;n d&#7855;t, v&#7899;i Will v&#224; Ho&#224;ng T&#244;n trong vai tr&#242; kh&#225;ch m&#7901;i &#273;&#7863;c bi&#7879;t. Ba ngh&#7879; s&#297; mang &#273;&#7871;n nh&#7919;ng m&#224;u gi&#7885;ng v&#224; c&#225; t&#237;nh ri&#234;ng, t&#7841;o n&#234;n m&#7897;t bu&#7893;i di&#7877;n giao thoa gi&#7919;a c&#225;c ca kh&#250;c quen thu&#7897;c v&#224; nh&#7919;ng m&#224;n k&#7871;t h&#7907;p tr&#234;n s&#226;n kh&#7845;u. S&#7921; ki&#7879;n di&#7877;n ra t&#7841;i Trung t&#226;m Ngh&#7879; thu&#7853;t &#194;u C&#417;, H&#224; N&#7897;i; ph&#7847;n th&#244;ng tin v&#233; b&#234;n c&#7841;nh c&#243; c&#225;c m&#7913;c gi&#225; &#273;&#7875; b&#7841;n tham kh&#7843;o.', ENT_QUOTES | ENT_HTML5, 'UTF-8'),
                'ticket_name' => 'Giá vé từ Ticketbox', 'price' => 650000,
            ],
            [
                'title' => 'Tinh Hà "Say Hi" Concert - Đêm 3',
                'slug' => 'tinh-ha-say-hi-dem-3-26590',
                'category' => 'festival', 'city' => 'Hà Nội', 'city_key' => 'hanoi',
                'venue' => 'Trung tâm Triển lãm Việt Nam (VEC)', 'starts_at' => '2027-01-16 19:00:00',
                'cover_image' => 'images/events/tinh-ha-say-hi-dem-3.jpeg',
                'introduction' => html_entity_decode('TINH H&#192; “SAY HI” CONCERT - &#272;&#202;M 3 l&#224; bu&#7893;i concert ti&#7871;p n&#7889;i d&#242;ng s&#7921; ki&#7879;n mang t&#234;n Tinh H&#224; “Say Hi”. Kh&#244;ng gian s&#226;n kh&#7845;u d&#224;nh cho &#226;m nh&#7841;c, nh&#7919;ng m&#224;n tr&#236;nh di&#7877;n tr&#7921;c ti&#7871;p v&#224; s&#7921; t&#432;&#417;ng t&#225;c gi&#7919;a ngh&#7879; s&#297; v&#7899;i kh&#225;n gi&#7843;. H&#236;nh &#7843;nh v&#224; t&#234;n s&#7921; ki&#7879;n g&#7907;i l&#7841;i tinh th&#7847;n tr&#7867; trung c&#7911;a th&#432;&#417;ng hi&#7879;u “Say Hi”; c&#225;c h&#7841;ng v&#233; v&#224; quy&#7873;n l&#7907;i &#273;&#432;&#7907;c li&#7879;t k&#234; ri&#234;ng &#273;&#7875; kh&#225;n gi&#7843; ch&#7885;n khu v&#7921;c ph&#249; h&#7907;p.', ENT_QUOTES | ENT_HTML5, 'UTF-8'),
                'ticket_name' => 'Giá vé từ Ticketbox', 'price' => 800000,
            ],
            [
                'title' => 'The Aura - Không Thể Thay Thế',
                'slug' => 'the-aura-khong-the-thay-the-nov-2026',
                'category' => 'music', 'city' => 'Hà Nội', 'city_key' => 'hanoi',
                'venue' => 'Cung Thể thao Điền kinh Mỹ Đình', 'starts_at' => '2027-01-23 19:30:00',
                'cover_image' => 'images/events/the-aura-november-2026.jpg',
                'introduction' => html_entity_decode('THE AURA - KH&#212;NG TH&#7874; THAY TH&#7870; quy t&#7909; b&#7889;n ngh&#7879; s&#297; Isaac, Jun Ph&#7841;m, Will v&#224; S.T S&#417;n Th&#7841;ch trong m&#7897;t &#273;&#234;m nh&#7841;c t&#7841;i H&#224; N&#7897;i. M&#7895;i ngh&#7879; s&#297; mang theo m&#7897;t m&#224;u s&#7855;c ri&#234;ng, c&#249;ng t&#7841;o n&#234;n nh&#7919;ng ti&#7871;t m&#7909;c v&#224; kho&#7843;nh kh&#7855;c giao l&#432;u d&#224;nh cho kh&#225;n gi&#7843;. Ch&#432;&#417;ng tr&#236;nh di&#7877;n ra t&#7841;i Cung Th&#7875; thao &#272;i&#7873;n kinh M&#7929; &#272;&#236;nh; c&#225;c h&#7841;ng v&#233; &#273;&#432;&#7907;c chia theo khu v&#7921;c v&#224; m&#7913;c gi&#225; &#273;&#7875; b&#7841;n d&#7877; ch&#7885;n tr&#7843;i nghi&#7879;m ph&#249; h&#7907;p.', ENT_QUOTES | ENT_HTML5, 'UTF-8'),
                'ticket_name' => 'Giá vé từ Ticketbox', 'price' => 1250000,
            ],
            [
                'title' => html_entity_decode('&#272;&#7883;a &#272;&#7841;o C&#7911; Chi : Tr&#259;ng Chi&#7871;n Khu', ENT_QUOTES | ENT_HTML5, 'UTF-8'),
                'slug' => 'dia-dao-cu-chi-trang-chien-khu-89666-89666',
                'category' => 'experience', 'city' => html_entity_decode('TP. H&#7891; Ch&#237; Minh', ENT_QUOTES | ENT_HTML5, 'UTF-8'), 'city_key' => 'hcm',
                'venue' => html_entity_decode('&#272;&#7883;a &#273;&#7841;o C&#7911; Chi', ENT_QUOTES | ENT_HTML5, 'UTF-8'), 'starts_at' => '2026-10-24 18:00:00',
                'cover_image' => 'images/events/dia-dao-cu-chi-trang-chien-khu.jpg',
                'introduction' => html_entity_decode('Tr&#259;ng Chi&#7871;n Khu l&#224; ch&#432;&#417;ng tr&#236;nh tham quan v&#224; bi&#7875;u di&#7877;n ban &#273;&#234;m t&#7841;i Khu di t&#237;ch l&#7883;ch s&#7917; &#272;&#7883;a &#273;&#7841;o C&#7911; Chi. L&#7845;y &#225;nh tr&#259;ng l&#224;m b&#7889;i c&#7843;nh, ch&#432;&#417;ng tr&#236;nh &#273;&#432;a ng&#432;&#7901;i xem tr&#7903; l&#7841;i khung c&#7843;nh l&#224;ng qu&#234; C&#7911; Chi trong giai &#273;o&#7841;n 1961&ndash;1964, sau phong tr&#224;o &#272;&#7891;ng Kh&#7903;i. N&#7897;i dung &#273;&#432;&#7907;c x&#226;y d&#7921;ng quanh cu&#7897;c s&#7889;ng th&#432;&#7901;ng ng&#224;y v&#224; tinh th&#7847;n l&#7841;c quan c&#7911;a ng&#432;&#7901;i d&#226;n trong v&#249;ng gi&#7843;i ph&#243;ng.&#10;&#10;Tr&#234;n h&#224;nh tr&#236;nh, kh&#225;ch c&#243; th&#7875; theo d&#245;i c&#225;c c&#7843;nh &#273;&#224;o &#273;&#7883;a &#273;&#7841;o, &#273;an l&#225;t d&#432;&#7899;i tr&#259;ng, thanh ni&#234;n &#273;&#259;ng k&#253; t&#242;ng qu&#226;n, xay l&#250;a, gi&#227; g&#7841;o, h&#7885;p ch&#7907; v&#224; nh&#7919;ng l&#7901;i h&#242; &#273;&#7889;i &#273;&#225;p tr&#234;n &#273;&#7891;ng ru&#7897;ng. C&#225;c ti&#7871;t m&#7909;c v&#259;n c&#244;ng ph&#7909;c v&#7909; b&#7897; &#273;&#7897;i, du k&#237;ch v&#224; ng&#432;&#7901;i d&#226;n &#273;&#432;&#7907;c &#273;&#7863;t c&#7841;nh &#226;m thanh bom, ph&#225;o, m&#225;y bay tu&#7847;n ti&#7875;u, t&#7841;o n&#234;n kh&#244;ng kh&#237; s&#7889;ng &#273;&#7897;ng c&#7911;a m&#7897;t &#273;&#234;m &#7903; chi&#7871;n khu.&#10;&#10;Su&#7845;t di&#7877;n theo l&#7883;ch Ticketbox k&#233;o d&#224;i t&#7915; 18:00 &#273;&#7871;n 20:30. Ban t&#7893; ch&#7913;c cho bi&#7871;t kh&#225;ch tham d&#7921; nh&#7853;n m&#7897;t m&#243;n qu&#224; g&#7855;n v&#7899;i du k&#237;ch C&#7911; Chi. N&#234;n &#273;&#7871;n s&#7899;m &#273;&#7875; c&#243; th&#7901;i gian di chuy&#7875;n v&#224; l&#224;m theo h&#432;&#7899;ng d&#7851;n t&#7841;i khu di t&#237;ch.', ENT_QUOTES | ENT_HTML5, 'UTF-8'),
                'ticket_name' => html_entity_decode('Tr&#259;ng Chi&#7871;n Khu', ENT_QUOTES | ENT_HTML5, 'UTF-8'), 'price' => 399000,
            ],
            [
                'title' => html_entity_decode('THE BROTHERS | &#272;&#7895; Ho&#224;ng Hi&#7879;p x T&#259;ng Ph&#250;c x H&#224; L&#234; x Cheng x B&#236;nh V&#259;n Band', ENT_QUOTES | ENT_HTML5, 'UTF-8'),
                'slug' => 'the-brothers-do-hoang-hiep-tang-phuc-ha-le-cheng-binh-van-band-26364',
                'category' => 'music', 'city' => html_entity_decode('H&#224; N&#7897;i', ENT_QUOTES | ENT_HTML5, 'UTF-8'), 'city_key' => 'hanoi',
                'venue' => html_entity_decode('Trung T&#226;m Ngh&#7879; Thu&#7853;t &#194;u C&#417;', ENT_QUOTES | ENT_HTML5, 'UTF-8'), 'starts_at' => '2026-10-31 20:00:00',
                'cover_image' => 'images/events/the-brothers-ticketbox.jpeg',
                'introduction' => html_entity_decode('THE BROTHERS l&#224; &#273;&#234;m nh&#7841;c quy t&#7909; n&#259;m c&#225;i t&#234;n: &#272;&#7895; Ho&#224;ng Hi&#7879;p, T&#259;ng Ph&#250;c, H&#224; L&#234;, Cheng v&#224; B&#236;nh V&#259;n Band. Theo th&#244;ng tin s&#7921; ki&#7879;n tr&#234;n Ticketbox, ch&#432;&#417;ng tr&#236;nh di&#7877;n ra l&#250;c 20:00 ng&#224;y 31/10/2026 t&#7841;i Trung T&#226;m Ngh&#7879; Thu&#7853;t &#194;u C&#417;, H&#224; N&#7897;i; ngh&#7879; s&#297; Cheng c&#361;ng li&#7879;t k&#234; &#273;&#226;y l&#224; l&#7883;ch ho&#7841;t &#273;&#7897;ng c&#7911;a m&#236;nh.&#10;&#10;Ch&#432;&#417;ng tr&#236;nh ph&#249; h&#7907;p v&#7899;i kh&#225;n gi&#7843; mu&#7889;n th&#432;&#7903;ng th&#7913;c m&#7897;t &#273;&#234;m nh&#7841;c c&#243; nhi&#7873;u ngh&#7879; s&#297; v&#224; ban nh&#7841;c &#273;&#7891;ng h&#224;nh. C&#225;c h&#7841;ng v&#233; &#273;&#432;&#7907;c chia theo khu v&#7921;c ch&#7895; ng&#7891;i, g&#7891;m 1st Row (L&R), RED, YELLOW, PINK, GREEN, BLUE v&#224; PURPLE; b&#7843;ng b&#234;n c&#7841;nh hi&#7875;n th&#7883; gi&#225; c&#7911;a t&#7915;ng h&#7841;ng &#273;&#7875; b&#7841;n d&#7877; so s&#225;nh tr&#432;&#7899;c khi ch&#7885;n. Gi&#225; kh&#7903;i &#273;i&#7875;m Ticketbox c&#244;ng b&#7889; l&#224; 1.000.000 &#273;&#7891;ng.&#10;&#10;Th&#244;ng tin m&#244; t&#7843; chi ti&#7871;t v&#7873; t&#7915;ng ngh&#7879; s&#297;, s&#417; &#273;&#7891; ch&#7895; ng&#7891;i v&#224; quy &#273;&#7883;nh tham d&#7921; c&#243; th&#7875; thay &#273;&#7893;i theo c&#7853;p nh&#7853;t c&#7911;a ban t&#7893; ch&#7913;c. B&#7841;n c&#243; th&#7875; m&#7903; r&#7897;ng ph&#7847;n n&#224;y &#273;&#7875; xem th&#244;ng tin s&#7921; ki&#7879;n, sau &#273;&#243; &#273;&#7889;i chi&#7871;u t&#7915;ng h&#7841;ng v&#233; trong danh s&#225;ch b&#234;n c&#7841;nh.', ENT_QUOTES | ENT_HTML5, 'UTF-8'),
                'ticket_types' => [ ['name' => '1st Row (L&R)', 'price' => 2610000], ['name' => 'RED', 'price' => 2430000], ['name' => 'YELLOW', 'price' => 2160000], ['name' => 'PINK', 'price' => 1800000], ['name' => 'GREEN', 'price' => 1620000], ['name' => 'BLUE', 'price' => 1170000], ['name' => 'PURPLE', 'price' => 1000000] ],
            ],
        ];

        $introductions = [
            'dia-dao-cu-chi-trang-chien-khu-89666-89666' => html_entity_decode('Tr&#259;ng Chi&#7871;n Khu l&#224; ch&#432;&#417;ng tr&#236;nh tham quan ban &#273;&#234;m t&#7841;i &#272;&#7883;a &#273;&#7841;o C&#7911; Chi, t&#225;i hi&#7879;n cu&#7897;c s&#7889;ng c&#7911;a ng&#432;&#7901;i d&#226;n trong v&#249;ng gi&#7843;i ph&#243;ng giai &#273;o&#7841;n 1961&ndash;1964. D&#432;&#7899;i &#225;nh tr&#259;ng, kh&#225;n gi&#7843; theo d&#245;i nh&#7919;ng c&#7843;nh sinh ho&#7841;t nh&#432; &#273;&#224;o &#273;&#7883;a &#273;&#7841;o, &#273;an l&#225;t, xay l&#250;a, gi&#227; g&#7841;o, h&#7885;p ch&#7907; v&#224; thanh ni&#234;n &#273;&#259;ng k&#253; t&#242;ng qu&#226;n; c&#225;c ti&#7871;t m&#7909;c v&#259;n c&#244;ng c&#249;ng &#226;m thanh bom, ph&#225;o, m&#225;y bay l&#224;m s&#7889;ng l&#7841;i kh&#244;ng kh&#237; l&#224;ng qu&#234; th&#7901;i chi&#7871;n. Su&#7845;t di&#7877;n k&#233;o d&#224;i t&#7915; 18:00 &#273;&#7871;n 20:30 v&#224; kh&#225;ch tham d&#7921; &#273;&#432;&#7907;c nh&#7853;n m&#7897;t m&#243;n qu&#224; g&#7855;n v&#7899;i du k&#237;ch C&#7911; Chi.', ENT_QUOTES | ENT_HTML5, 'UTF-8'),
            'the-brothers-do-hoang-hiep-tang-phuc-ha-le-cheng-binh-van-band-26364' => html_entity_decode('THE BROTHERS quy t&#7909; &#272;&#7895; Ho&#224;ng Hi&#7879;p, T&#259;ng Ph&#250;c, H&#224; L&#234;, Cheng v&#224; B&#236;nh V&#259;n Band trong m&#7897;t &#273;&#234;m nh&#7841;c tr&#7921;c ti&#7871;p t&#7841;i Trung T&#226;m Ngh&#7879; Thu&#7853;t &#194;u C&#417;, H&#224; N&#7897;i. N&#259;m c&#225; t&#237;nh &#226;m nh&#7841;c c&#249;ng g&#243;p m&#7863;t tr&#234;n m&#7897;t s&#226;n kh&#7845;u, t&#7841;o n&#234;n cu&#7897;c h&#7865;n d&#224;nh cho kh&#225;n gi&#7843; mu&#7889;n th&#432;&#7903;ng th&#7913;c c&#225;c gi&#7885;ng h&#225;t v&#224; m&#224;n tr&#236;nh di&#7877;n c&#7911;a c&#7843; ban nh&#7841;c. S&#7921; ki&#7879;n c&#243; b&#7843;y khu v&#7921;c v&#233; t&#7915; 1st Row (L&R) &#273;&#7871;n PURPLE, v&#7899;i c&#225;c m&#7913;c gi&#225; &#273;&#432;&#7907;c li&#7879;t k&#234; b&#234;n c&#7841;nh &#273;&#7875; b&#7841;n d&#7877; so s&#225;nh.', ENT_QUOTES | ENT_HTML5, 'UTF-8'),
        ];

        foreach ($events as &$eventData) {
            if (isset($introductions[$eventData['slug']])) {
                $eventData['introduction'] = $introductions[$eventData['slug']];
            }
        }
        unset($eventData);

        foreach ($events as $data) {
            $ticketTypes = $data['ticket_types'] ?? [[
                'name' => $data['ticket_name'],
                'price' => $data['price'],
            ]];

            $ticketUrl = $data['ticket_url'] ?? '';
            unset($data['ticket_url']);

            unset($data['ticket_name'], $data['ticket_types'], $data['price']);

            $event = Event::updateOrCreate(['slug' => $data['slug']], $data);
            $event->update(['ticket_url' => $ticketUrl]);

            $ticketNames = array_column($ticketTypes, 'name');
            $event->ticketTypes()->whereNotIn('name', $ticketNames)->delete();

            foreach ($ticketTypes as $ticketType) {
                $ticketData = array_merge($ticketType, ['quantity' => 0, 'sold' => 0]);
                $event->ticketTypes()->updateOrCreate(['name' => $ticketData['name']], $ticketData);
            }
        }
    }
}
