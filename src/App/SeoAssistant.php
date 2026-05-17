<?php
declare(strict_types=1);

namespace App;

final class SeoAssistant
{
    public static function generatePostSeo(array $payload): array
    {
        $title = trim((string) ($payload["title"] ?? ""));
        $category = trim((string) ($payload["category"] ?? ""));
        $excerpt = trim((string) ($payload["excerpt"] ?? ""));
        $content = trim((string) ($payload["content"] ?? ""));
        $brandName = Helpers::brandName();

        $fallback = self::fallbackSeo($title, $category, $excerpt, $content, $brandName);

        $provider = strtolower(trim((string) Env::get("SEO_AI_PROVIDER", "openai")));
        $apiKey = trim((string) Env::get("SEO_AI_API_KEY", ""));
        $model = trim((string) Env::get("SEO_AI_MODEL", ""));
        $enabled = (bool) Env::get("SEO_AI_ENABLED", false);

        if ($model === "") {
            $model = self::defaultModelForProvider($provider);
        }

        if (!$enabled || $apiKey === "" || $model === "") {
            $fallback["source"] = "fallback";
            $fallback["message"] = "AI SEO is not configured yet, so a local draft was generated.";
            return $fallback;
        }

        $baseUrl = rtrim((string) Env::get("SEO_AI_BASE_URL", self::defaultBaseUrlForProvider($provider)), "/");
        $response = self::requestChatCompletion($baseUrl, $apiKey, $model, $title, $category, $excerpt, $content, $brandName);
        if ($response === null) {
            $fallback["source"] = "fallback";
            $fallback["message"] = "AI SEO could not be reached, so a local draft was generated.";
            return $fallback;
        }

        $decoded = self::extractJsonPayload($response);
        if ($decoded === null) {
            $fallback["source"] = "fallback";
            $fallback["message"] = "AI SEO returned an unreadable response, so a local draft was generated.";
            return $fallback;
        }

        $metaTitle = self::truncate(trim((string) ($decoded["meta_title"] ?? $fallback["meta_title"])), 190);
        $metaDescription = self::truncate(trim((string) ($decoded["meta_description"] ?? $fallback["meta_description"])), 255);
        $seoKeywords = self::normalizeCommaList((string) ($decoded["seo_keywords"] ?? $fallback["seo_keywords"]));
        $tags = self::normalizeCommaList((string) ($decoded["tags"] ?? $fallback["tags"]));

        return [
            "meta_title" => $metaTitle !== "" ? $metaTitle : $fallback["meta_title"],
            "meta_description" => $metaDescription !== "" ? $metaDescription : $fallback["meta_description"],
            "seo_keywords" => $seoKeywords !== "" ? $seoKeywords : $fallback["seo_keywords"],
            "tags" => $tags !== "" ? $tags : $fallback["tags"],
            "source" => "ai",
            "message" => "SEO fields were generated with AI assistance.",
        ];
    }

    private static function requestChatCompletion(
        string $baseUrl,
        string $apiKey,
        string $model,
        string $title,
        string $category,
        string $excerpt,
        string $content,
        string $brandName
    ): ?string {
        if (!function_exists("curl_init")) {
            return null;
        }

        $prompt = "You generate SEO metadata for nonprofit blog articles.\n"
            . "Return strict JSON only with keys: meta_title, meta_description, seo_keywords, tags.\n"
            . "Rules:\n"
            . "- meta_title: under 60 characters when possible, compelling, natural, not clickbait.\n"
            . "- meta_description: 140-160 characters when possible.\n"
            . "- seo_keywords: comma-separated keywords.\n"
            . "- tags: comma-separated topical blog tags.\n"
            . "- Use the organization name only when it improves relevance.\n"
            . "- Avoid keyword stuffing.\n\n"
            . "Organization: {$brandName}\n"
            . "Category: {$category}\n"
            . "Title: {$title}\n"
            . "Excerpt: {$excerpt}\n"
            . "Content:\n{$content}";

        $body = [
            "model" => $model,
            "messages" => [
                ["role" => "system", "content" => "You are an expert SEO editor. Always return valid JSON only."],
                ["role" => "user", "content" => $prompt],
            ],
            "temperature" => 0.4,
        ];

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $baseUrl . "/chat/completions",
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($body, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
            CURLOPT_HTTPHEADER => [
                "Authorization: Bearer {$apiKey}",
                "Content-Type: application/json",
            ],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 20,
        ]);

        $raw = curl_exec($ch);
        $status = (int) curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
        curl_close($ch);

        if (!is_string($raw) || $raw === "" || $status < 200 || $status >= 300) {
            return null;
        }

        $decoded = json_decode($raw, true);
        return (string) ($decoded["choices"][0]["message"]["content"] ?? "");
    }

    private static function defaultBaseUrlForProvider(string $provider): string
    {
        return match ($provider) {
            "groq" => "https://api.groq.com/openai/v1",
            default => "https://api.openai.com/v1",
        };
    }

    private static function defaultModelForProvider(string $provider): string
    {
        return match ($provider) {
            "groq" => "llama-3.3-70b-versatile",
            default => "",
        };
    }

    private static function extractJsonPayload(string $value): ?array
    {
        $value = trim($value);
        if ($value === "") {
            return null;
        }

        $decoded = json_decode($value, true);
        if (is_array($decoded)) {
            return $decoded;
        }

        if (preg_match('/\{.*\}/s', $value, $matches) !== 1) {
            return null;
        }

        $decoded = json_decode($matches[0], true);
        return is_array($decoded) ? $decoded : null;
    }

    private static function fallbackSeo(string $title, string $category, string $excerpt, string $content, string $brandName): array
    {
        $plain = trim(preg_replace('/\s+/', ' ', strip_tags($excerpt !== "" ? $excerpt : $content)) ?? "");
        $metaTitle = self::truncate($title !== "" ? "{$title} | {$brandName}" : "{$brandName} Blog", 190);
        $metaDescription = self::truncate($plain !== "" ? $plain : "{$brandName} shares impact stories, outreach updates, and community support initiatives.", 255);
        $keywords = self::buildKeywordList($title, $category, $plain, $brandName);

        return [
            "meta_title" => $metaTitle,
            "meta_description" => $metaDescription,
            "seo_keywords" => implode(", ", $keywords),
            "tags" => implode(", ", array_slice($keywords, 0, 5)),
        ];
    }

    private static function buildKeywordList(string $title, string $category, string $plain, string $brandName): array
    {
        $seed = trim("{$category} {$title} {$plain} {$brandName}");
        $parts = preg_split('/[^a-z0-9]+/i', strtolower($seed)) ?: [];
        $stop = [
            "the", "and", "for", "with", "that", "this", "from", "into", "their", "they", "have", "will", "your",
            "home", "blog", "initiative", "friends", "heart", "welfare", "at", "are", "was", "were", "been", "about",
            "after", "before", "during", "where", "when", "what", "into", "than", "them", "our", "out", "just", "much",
        ];

        $keywords = [];
        foreach ($parts as $part) {
            if ($part === "" || strlen($part) < 4 || in_array($part, $stop, true)) {
                continue;
            }
            $keywords[$part] = true;
        }

        $phrases = [];
        if ($category !== "") {
            $phrases[] = $category;
        }
        if (str_contains(strtolower($plain), "orphanage")) {
            $phrases[] = "orphanage support";
        }
        if (str_contains(strtolower($plain), "children")) {
            $phrases[] = "children outreach";
        }
        if (str_contains(strtolower($plain), "community")) {
            $phrases[] = "community impact";
        }
        if (str_contains(strtolower($plain), "donat")) {
            $phrases[] = "charity donations";
        }
        $phrases[] = "humanitarian outreach";

        $result = [];
        foreach ($phrases as $phrase) {
            $phrase = trim($phrase);
            if ($phrase !== "" && !in_array($phrase, $result, true)) {
                $result[] = $phrase;
            }
        }

        foreach (array_keys($keywords) as $keyword) {
            if (count($result) >= 8) {
                break;
            }
            if (!in_array($keyword, $result, true)) {
                $result[] = $keyword;
            }
        }

        return $result;
    }

    private static function normalizeCommaList(string $value): string
    {
        $items = preg_split('/[,\\n]+/', $value) ?: [];
        $clean = [];
        foreach ($items as $item) {
            $item = trim($item);
            if ($item !== "" && !in_array(mb_strtolower($item), array_map('mb_strtolower', $clean), true)) {
                $clean[] = $item;
            }
        }
        return implode(", ", $clean);
    }

    private static function truncate(string $value, int $limit): string
    {
        $value = trim(preg_replace('/\s+/', ' ', $value) ?? "");
        return mb_strimwidth($value, 0, $limit, "");
    }
}
