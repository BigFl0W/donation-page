<?php
require_once __DIR__ . "/config/autoload.php";
use App\Database;

$defaults = [
    'about_hero_title' => "Step Forward <br> <i>Serve The Humanity</i>",
    'about_hero_label' => "About Us",
    'about_hero_desc' => "We are dedicated to providing support and resources to those in need. Our mission is to create a world where everyone has the opportunity to thrive.",
    'about_stat_1_val' => "50K+",
    'about_stat_1_label' => "Lives Impacted",
    'about_stat_2_val' => "12+",
    'about_stat_2_label' => "Active Programs",
    'about_stat_3_val' => "100%",
    'about_stat_3_label' => "Direct Giving",
    'about_stat_4_val' => "15+",
    'about_stat_4_label' => "Years Service",
    'about_story_title' => "Our Journey of Compassion",
    'about_story_lead' => "Founded on the principles of empathy and action, Gracious Charity has grown into a beacon of hope.",
    'about_story_text' => "Gracious Charity started with a simple idea: that small acts of kindness can change the world. Over the years, we have expanded our reach, touching thousands of lives through education, healthcare, and community development. Our team of dedicated volunteers and staff work tirelessly to ensure that every donation makes a real difference.",
    'about_quote_text' => "The best way to find yourself is to lose yourself in the service of others. We believe in the power of collective action to solve the world's most pressing problems.",
    'about_quote_author' => "Gracious Founder",
    'about_quote_role' => "Executive Director",
    'about_time_1_year' => "2010",
    'about_time_1_title' => "The Beginning",
    'about_time_1_desc' => "Gracious Charity was founded with a focus on local community support.",
    'about_time_2_year' => "2015",
    'about_time_2_title' => "Expanding Reach",
    'about_time_2_desc' => "We launched our first national campaign for children's education.",
    'about_time_3_year' => "2018",
    'about_time_3_title' => "Medical Relief",
    'about_time_3_desc' => "Establishment of our mobile clinics providing free healthcare in rural areas.",
    'about_time_4_year' => "2023",
    'about_time_4_title' => "Global Impact",
    'about_time_4_desc' => "Recognized for our international relief efforts and sustainable development goals.",
    'about_img_1' => "assets/images/about/about_1.jpg",
    'about_img_2' => "assets/images/about/about_2.jpg",
    'about_img_3' => "assets/images/about/about_3.jpg",
    'about_story_img' => "assets/images/about/story.jpg",
    'about_founder_img' => "assets/images/about/founder.jpg"
];

foreach ($defaults as $key => $val) {
    Database::execute("INSERT INTO settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = ?", [$key, $val, $val]);
}

echo "About Page settings initialized!\n";
