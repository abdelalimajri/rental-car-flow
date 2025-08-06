<?php
// src/DataFixtures/Provider/MoroccanProvider.php
namespace App\DataFixtures\Provider;

class MoroccanProvider
{
    /**
     * Génère un numéro de téléphone marocain valide
     */
    public function moroccanPhoneNumber(): string
    {
        $prefixes = [
            // Fixes
            '522', '523', '524', '525', '528', '529', // Casablanca/Mohammedia
            '537', '538', // Rabat/Salé
            '524', '544', // Marrakech
            '535', '555', // Fès/Meknès
            '539', '559', // Tanger/Tétouan
            '528', '548', // Agadir
            
            // Mobiles
            '600', '601', '602', '603', '604', '605', '606', '607', '608', '609',
            '610', '611', '612', '613', '614', '615', '616', '617', '618', '619',
            '620', '621', '622', '623', '624', '625', '626', '627', '628', '629',
            '630', '631', '632', '633', '634', '635', '636', '637', '638', '639',
            '640', '641', '642', '643', '644', '645', '646', '647', '648', '649',
            '650', '651', '652', '653', '654', '655', '656', '657', '658', '659',
            '660', '661', '662', '663', '664', '665', '666', '667', '668', '669',
            '670', '671', '672', '673', '674', '675', '676', '677', '678', '679',
            '680', '681', '682', '683', '684', '685', '686', '687', '688', '689',
            '690', '691', '692', '693', '694', '695', '696', '697', '698', '699'
        ];
        
        $prefix = $prefixes[array_rand($prefixes)];
        $number = str_pad((string)random_int(100000, 999999), 6, '0', STR_PAD_LEFT);
        
        return '+212' . $prefix . $number;
    }

    /**
     * Génère une adresse marocaine réaliste
     */
    public function moroccanAddress(): string
    {
        $streetTypes = ['Avenue', 'Boulevard', 'Rue', 'Place', 'Allée'];
        $streetNames = [
            'Mohammed V', 'Hassan II', 'Mohammed VI', 'Allal Ben Abdellah',
            'de la République', 'de l\'Indépendance', 'des FAR', 'Zerktouni',
            'Abdelkrim Khattabi', 'Ibn Sina', 'Al Massira', 'de France',
            'Prince Moulay Abdellah', 'des Almohades', 'Youssef Ben Tachfine'
        ];
        
        $streetType = $streetTypes[array_rand($streetTypes)];
        $streetName = $streetNames[array_rand($streetNames)];
        $number = random_int(1, 999);
        
        return "$number $streetType $streetName";
    }

    /**
     * Génère un nom d'agence marocaine
     */
    public function agencyName(): string
    {
        $prefixes = ['RentCar', 'AutoMaroc', 'LocationPlus', 'CarRental', 'VoitureExpress'];
        $suffixes = ['Centre', 'Express', 'Premium', 'City', 'Royal', 'Atlas', 'Sahara'];
        
        $cities = [
            'Casablanca', 'Rabat', 'Marrakech', 'Fès', 'Tanger', 'Agadir', 
            'Meknès', 'Oujda', 'Tétouan', 'Safi', 'El Jadida', 'Kénitra'
        ];
        
        $prefix = $prefixes[array_rand($prefixes)];
        $city = $cities[array_rand($cities)];
        $suffix = $suffixes[array_rand($suffixes)];
        
        return "$prefix $city $suffix";
    }

    /**
     * Génère un slug à partir d'un nom
     */
    public function slugify(string $text): string
    {
        // Remplacer les caractères spéciaux
        $text = transliterator_transliterate('Any-Latin; Latin-ASCII; [\u0080-\u7fff] remove', $text);
        $text = strtolower($text);
        $text = preg_replace('/[^a-z0-9]+/', '-', $text);
        $text = trim($text, '-');
        
        return $text;
    }

    /**
     * Génère des heures d'ouverture réalistes
     */
    public function businessHours(): array
    {
        $variations = [
            // Horaires standards
            [
                'monday' => ['09:00', '18:00'],
                'tuesday' => ['09:00', '18:00'],
                'wednesday' => ['09:00', '18:00'],
                'thursday' => ['09:00', '18:00'],
                'friday' => ['09:00', '18:00'],
                'saturday' => ['09:00', '13:00'],
                'sunday' => null
            ],
            // Horaires étendus
            [
                'monday' => ['08:00', '19:00'],
                'tuesday' => ['08:00', '19:00'],
                'wednesday' => ['08:00', '19:00'],
                'thursday' => ['08:00', '19:00'],
                'friday' => ['08:00', '19:00'],
                'saturday' => ['08:00', '17:00'],
                'sunday' => ['10:00', '15:00']
            ],
            // Horaires compacts
            [
                'monday' => ['10:00', '17:00'],
                'tuesday' => ['10:00', '17:00'],
                'wednesday' => ['10:00', '17:00'],
                'thursday' => ['10:00', '17:00'],
                'friday' => ['10:00', '17:00'],
                'saturday' => ['10:00', '14:00'],
                'sunday' => null
            ]
        ];
        
        return $variations[array_rand($variations)];
    }

    /**
     * Génère un username à partir du prénom et nom
     */
    public function generateUsername(string $firstName, string $lastName): string
    {
        $firstName = strtolower(transliterator_transliterate('Any-Latin; Latin-ASCII', $firstName));
        $lastName = strtolower(transliterator_transliterate('Any-Latin; Latin-ASCII', $lastName));
        
        return $firstName . '.' . $lastName . '.' . random_int(100, 999);
    }

    /**
     * Retourne un élément aléatoire d'un tableau
     */
    public function randomElement(array $array): mixed
    {
        return $array[array_rand($array)];
    }

    /**
     * Encode un mot de passe avec le hasher Symfony
     */
    public function encodePassword(string $plainPassword): string
    {
        // Cette méthode sera utilisée par Alice avec le PasswordHasher
        return $plainPassword; // Alice va automatiquement hasher avec le service
    }
}