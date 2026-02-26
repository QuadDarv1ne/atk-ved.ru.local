<?php

declare(strict_types=1);

namespace ATKVed\Services;

/**
 * Сервис для работы с данными компании
 *
 * @package ATKVed\Services
 * @since   3.5.0
 */
final class CompanyService
{
    /**
     * Кэш данных компании
     *
     * @var array<string, mixed>|null
     */
    private ?array $companyInfo = null;

    /**
     * Получение данных компании
     *
     * @return array<string, mixed>
     */
    public function getInfo(): array
    {
        if ($this->companyInfo !== null) {
            return $this->companyInfo;
        }

        $founded = 2018;
        $years   = (int) date('Y') - $founded;

        $this->companyInfo = [
            'name'        => 'АТК ВЭД',
            'description' => __('Товары для маркетплейсов из Китая оптом. Полный цикл работы от поиска до доставки с гарантией качества.', 'atk-ved'),
            'founded'     => $founded,
            'years'       => $years,
            'deliveries'  => 1000,
            'clients'     => 1500,
            'rating'      => 4.9,
            'phone'       => get_theme_mod('atk_ved_phone', ''),
            'email'       => get_theme_mod('atk_ved_email', ''),
            'address'     => get_theme_mod('atk_ved_address', ''),
        ];

        return $this->companyInfo;
    }

    /**
     * Получение социальных ссылок
     *
     * @return array<string, string>
     */
    public function getSocialLinks(): array
    {
        static $links = null;

        if ($links !== null) {
            return $links;
        }

        $keys  = ['whatsapp', 'telegram', 'vk', 'instagram', 'youtube'];
        $links = [];

        foreach ($keys as $key) {
            $value = get_theme_mod('atk_ved_' . $key, '');
            if ($value && $this->isSafeUrl($value)) {
                $links[$key] = esc_url($value);
            }
        }

        return $links;
    }

    /**
     * Получение trust badges
     *
     * @return array<int, array<string, string>>
     */
    public function getTrustBadges(): array
    {
        $info = $this->getInfo();

        return [
            [
                'icon' => 'trophy',
                'text' => $info['years'] . ' ' . $this->pluralize($info['years'], 'год', 'года', 'лет') . ' ' . __('на рынке', 'atk-ved'),
            ],
            [
                'icon' => 'truck',
                'text' => $info['deliveries'] . '+ ' . __('доставок', 'atk-ved'),
            ],
            [
                'icon' => 'star',
                'text' => $info['rating'] . '/5 ' . __('рейтинг', 'atk-ved'),
            ],
            [
                'icon' => 'shield',
                'text' => __('Гарантия качества', 'atk-ved'),
            ],
        ];
    }

    /**
     * Склонение числительных
     *
     * @param int    $number Число
     * @param string $one    Форма для 1
     * @param string $few    Форма для 2-4
     * @param string $many   Форма для 5+
     * @return string
     */
    private function pluralize(int $number, string $one, string $few, string $many): string
    {
        $mod10  = $number % 10;
        $mod100 = $number % 100;

        if ($mod100 >= 11 && $mod100 <= 19) {
            return $many;
        }

        if ($mod10 === 1) {
            return $one;
        }

        if ($mod10 >= 2 && $mod10 <= 4) {
            return $few;
        }

        return $many;
    }

    /**
     * Проверка безопасности URL
     *
     * @param string $url URL для проверки
     * @return bool
     */
    private function isSafeUrl(string $url): bool
    {
        $scheme = parse_url($url, PHP_URL_SCHEME);
        return in_array(strtolower((string) $scheme), ['https', 'http', 'tg'], true);
    }
}
