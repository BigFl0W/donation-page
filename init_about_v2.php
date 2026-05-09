<?php
require_once __DIR__ . '/config/autoload.php';
use App\Database;

$aboutPage = Database::fetchOne("SELECT id FROM pages WHERE slug = 'about-us'");
if (!$aboutPage) {
    Database::execute("INSERT INTO pages (slug, title, status) VALUES ('about-us', 'About Us', 'published')");
    $pageId = Database::lastInsertId();
} else {
    $pageId = $aboutPage['id'];
}

$blocks = [
    'hero_label' => ['About Us', 'text'],
    'hero_title' => ['Step Forward Serve The Humanity Reach Out & Help', 'text'],
    'hero_desc' => ['The secret to happiness lies in helping others...', 'text'],
    'hero_image_1' => ['assets/images/about_img.png', 'image'],
    'hero_image_2' => ['', 'image'],
    'hero_image_3' => ['', 'image'],
    'know_label' => ['Get to Know Us', 'text'],
    'know_title' => ['Let Us Come Together To Make a Difference', 'text'],
    'know_desc' => ['Lorem Ipsum is simply dummy text...', 'text'],
    'skill_1_label' => ['Food Help', 'text'],
    'skill_1_val' => ['67%', 'text'],
    'skill_2_label' => ['Medical Help', 'text'],
    'skill_2_val' => ['85%', 'text'],
    'mission_years' => ['14', 'text'],
    'mission_image' => ['assets/images/about_img_2.jpg', 'image'],
    'mission_text' => ['["Community Empowerment", "Sustainable Development", "Inclusive Growth"]', 'text'],
    'vision_image' => ['assets/images/about_img_2.jpg', 'image'],
    'vision_text' => ['["Global Reach", "Innovation in Giving", "Lasting Impact"]', 'text'],
    'history_image' => ['assets/images/about_img_2.jpg', 'image'],
    'history_text' => ['["Founded in 2010", "Reached 1M+ Lives", "Award Winning NGO"]', 'text'],
    'feature_title' => ['Work As An Intern', 'text'],
    'feature_desc' => ['Sed quia consequuntur agni dolores eos qui ratoluptatem sequi nesciun porquis', 'text'],
    'feature_icon' => ['charity-volunteer_people', 'text'],
    'feature_link' => ['become-volunteers.php', 'text'],
    'contact_phone' => ['+1234567899', 'text']
];

foreach ($blocks as $key => $data) {
    Database::execute(
        "INSERT IGNORE INTO content_blocks (page_id, block_key, block_value, block_label, block_type) 
         VALUES (?, ?, ?, ?, ?)",
        [$pageId, $key, $data[0], ucfirst(str_replace('_', ' ', $key)), $data[1]]
    );
}

echo "About Us blocks initialized successfully.";
